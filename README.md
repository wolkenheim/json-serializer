# JSON Serializer

This is proof of concept or tutorial project. I´ve been thinking lately: how is JSON made? It´s a 
string representation of data. I use it every day.

My question is: How does this
```
class User
{
    public function __construct(
        public string $name
    ){ }
}
```
become this
```
'{"name" : "Matt"}
```

How does it work under the hood? Could I do it myself? 

I´ve been using a few libraries and approaches yet. That is mostly Spring Boot with the bundled Jackson Object Mapper.
This is the approach I was aiming for. Simple Java classes (Plain Old Java Object - POJO) can get transformed to JSON.
There are a few annotations that can be used like @JsonIgnore. So the corresponding class attribute is not used. If you
haven´t seen Jackson yet - here is a brief [introduction](https://www.baeldung.com/jackson-annotations)

Just to be on the safe side. Programming languages invent their own lingo for the same concepts. In PHP
[Attribute](https://www.php.net/manual/en/language.attributes.overview.php), introduced in PHP8, is what Annotation 
would be in Java: metadata on class field. Annotations do exist in PHP as well as docblock comments. Docblock 
Annotations were used prior to Attributes, were not part of the standard library, and can be replaced by 
native attributes.
Any class member variables are called [Property](https://www.php.net/manual/en/language.oop5.properties.php). 
That would be an "Attribute" in Java to maximize the confusion.

There is already a PHP library that does that, mostly. It is 
[symfony/serializer](https://symfony.com/doc/current/components/serializer.html)
It can be configured with a lot of options. However, it is quite big.
The other main library is [schmittjoh/serializer](https://github.com/schmittjoh/serializer)

It´s not the goal to compete with them. Hundreds of hours of work went into those libraries.
I want to build something smaller. In a small project I will never ever need XML parsing. Or Content negotiation. 
It will be always be JSON parsing. And I´m not aiming for the best library that handles all edge cases. But a proof 
of concept that will work.

Is there an alternative approach to solve this? Yes, your classes can implement the interface 
[jsonSerializable](https://www.php.net/manual/en/jsonserializable.jsonserialize.php)
This works out of the box and no libraries are needed. 

What I don´t like about this approach:
* It bloats your classes. The base of OOP is to favor composition - instead of implementing all functionality
in the class itself
* A lot of writing. You need to define a mapping for all attributes in your classes
* Type information is lost. All you can read is the name of the keys. But not their type
* How do you guarantee that e.g. DateTime objects are always represented in the same format?

This is however a good option if you have a lot of custom transformations and renaming of keys. The `symfony/serializer`
and Jackson approach is a **rule based approach**. You define global and custom rules how certain type of data is mapped
according to these rules.

## Project statement
Let´s build a JSON Serializer that takes a Plain Old PHP Object (POPO) and encodes it as JSON string. The
output data might not the same as the input. It should require zero dependencies (except development) and use the 
standard library only. It should use PHP8.0 and 8.1 features. No backward compatibility.

The result should be a library package. No webserver is needed and development can be done inside phpunit.

## Part 1: The Basics
The simplest encoding strategy I can think of is `json_encode(new User("Matt"))`. 

This will work and result in the 
`'{"name":"Matt"}'`. If requirements are simple enough and I guess this is the case for many projects. This is a 1:1 mapping
of input format to output format. When thinking about the first feature to implement, the `#[JsonIgnore]` Attribute, the
auto magic is not working anymore. First we need to know the properties and there values. We also need to know which
property has which Attribute. Then this information needs to be mapped to a custom data structure and that would be most
likely an array. The following steps need to happen:

1. Read object and find out which properties to use
2. If there is a `#[JsonIgnore]` Attribute, do not use the property in the JSON format
3. Map the transformed to an array / normalize the object
4. Encode array to JSON string

I introduced an interface Normalize `public function normalize(mixed $data): mixed;`. This could be used for all kind of
data. There could be an ArrayNormalizer and so on. In this project there should be only the ObjectNormalizer. All other
input data cannot be processed and will throw a TypeNotObjectException. In general the Serializer class needs to pick a
normalization strategy according to the type of the input data. There will be only objects, so that is easy.
The object normalizer will transform the object to an array for now using type casting: `return (array) $data`

Let´s run the class: `vendor/bin/phpunit ./tests/Unit/JsonSerializer/JsonSerializerTest.php`. This works

## Part 2: Entering Reflection
Now this is not very impressive so far. All we got is the same as before just with more classes. Now comes the interesting 
challenge: getting the types and attributes out of the objects. `get_object_vars()` will retrieve properties, but not the
attributes. ReflectionClass can be initialized with `new \ReflectionClass($data)` and traversed with `$class->getProperties()
The idea is: extract a set of rules from the metadata of the class for each property. Then use these rules to process the
object and map it to a normalized array.
I introduced the PropertyRule class, which represents the set of rules for one class property. The PropertyRuleMapper 
extracts those rules.

But first one thing: visibility. This has not been an issue yet with only one public variable. I´ll throw in a protected 
class property. Reflection gives us a handle to all properties, but we should not try to access it´s values. This will 
result in the following exception

`Error: Cannot access protected property Tests\Unit\JsonSerializer\Domain\User::$hidden`

That´s why there needs to be a "isIgnored" method in the PropertyRuleMapper. Protected and Private properties will not 
be processed. This method will come in handy in a minute for another purpose. This works, the test still runs and gives 
the same result.

Now we set up the project to extract information from the input classes using reflection.

## Part 3: Attributes
Now for the [attributes](https://www.php.net/manual/en/language.attributes.overview.php). Add a JsonIgnore attribute 
class and use it on a new property in the User class. Nothing happens, test fails. Of course there is no logic yet 
involved.
We need to define a rule. Let´s use the isIgnored() method for that. Loop through all attributes of the property 
`$property->getAttributes()`and if JsonIgnore is found, do not use that property. Believe it or not - this is 
the implementation of the first, working annotation. Quick win.

Now for some reasoning for the next attributes.`JsonProperty("new_name")` will use a custom name for the given class 
property. So the key 'myName' will be represented as 'new_name' in its JSON format. 
`JsonSerialize(CustomFieldFormat::class)` will use a custom class to process the value. Hence, we have 
attributes that mutate the given key or its value. Both attributes have in common that they need to store information,
both of string type. It´s serializedName for JsonProperty and className for JsonSerialize.

I added the getJsonName() method to extract the new Name property and add it to a new attribute in the PropertyRule 
object. 

Nothing is happening though. We just extracted the rule but didn´t implement any functionality yet. buildNormalizedArray()
in ObjectNormalizer class needs to handle this. The new getKey() method will handle this. If the jsonName attribute is 
set, use it. If not, return the properties name. This works. Output is now `'{"name":"Matt","different_name":"Waititi"}'`

Now before we continue, let´s spice things up a bit. We haven´t talked about complex types yet. So far we had only 
string types. Scalar value in general (string, bool, int, float) are easy when it comes to JSON. No action is needed.
This is different for objects. Let´s introduce a UserStatus enum.

This will break the application as the ObjectNormalizer has no way how to handle that type. We need an EnumFormat class
for that case. This needs to go to the UserStatus property in the User class for now.
```
#[JsonSerialize(EnumFormat::class)]
public UserStatus $status,
```
This works. Now the whole serialization process is extendable. Need a custom mapping for an object? Just implement 
the format interface and hook into the process with the JsonSerialize attribute.

## Part 4: Default formatting for certain types
All works fine, there is a problem though. Format classes have to be used for complex types. That means a lot of 
writing attributes in classes. Not ideal. So it would be good to have a default formatting strategy for those types.
DateTime class is another candidate here. Dates have to be parsed. We chose a global ISO8601 option for now.
Most methods in PropertyRuleMapper take currently ReflectionProperty as an argument. A refactoring can be made here
to a new ReflectionPropertyMapper class.

I added an DateTime property to the Dummy User class in tests as well. This works. No annotation - global DateTime format
is picked and formats the date property successfully without an attribute: `"createdAt":"2022-01-22T00:00:00+0000"` We
can also remove the attribute for the Enum property in the User class. It is still getting formatted. 

Before all the checks for types where hard coded in the source code. Now there is an $defaultStrategyClassMappings array.
This would be the first step in a refactoring towards a configuration object. At the end users of the library could 
inject their own class definitions for default formatting options in a map-styled array.

But first tests need to be refactored. A lot of the functionality moved down to the classes, that extract information 
out of Reflection properties. There are already a few cases here and each one should get a single unit test. I worked
so far with a global testing strategy: check the resulting string. This is fine for very simple beginnings but the 
project has already gotten far way to complex for that.

## Part 5: Extract Getters
So far we are only extracting public properties. And this is fine as protected and private ones should be not publicly
available. But the usual pattern that exists is to keep all properties private or protected and add Getters and Setters.
So in fact a `private string $hiddenName` will have its corresponding `getHiddenName() : string` method. We used 
Reflection to get information on properties so far. Now we need information on methods as well.

There are some crucial changes now. Before we could roughly say that the resulting array would be of the same size
or smaller as the number of attributes. Public ones minus private / protected ones. That also meant our input was always
the value of an attribute. Now the input can be a method. Currently, the ObjectNormalizer retrieves the value with 
this call `$data->{$propertyRule->name}`. This will no longer work as we either have a property or a method call. 
At this point I tried callables (not a valid type for a class property) and came to Closures. I stopped there as, this 
was getting far way to complex for a simple call. The easiest thing to do this, is to introduce a new Enum to 
differentiate between both cases. Also, PropertyRule "name" is a bit vague. I decided to call it accessName for now.
jsonName has to change as well. It cannot be null anymore but will always carry the final name of the output key in 
json. 

## Conclusion
Time for wrapping it up. I learned a lot while doing this project. And I know a lot more what I don´t know. So here
are the things I didn´t cover:

I did not handle nested types or custom classes as attributes. What if class User has the attribute Product? The 
normalizer should be able to handle that case in a real life situation. I did not handle **union types**. 

I learned that the ReflectionClass treats "string|null" not as a union but type 
string that is nullable. A union would be "string|int". There could be a lot of interesting edge cases here. Even more
challenging is type "mixed", the PHP equivalent to the "any" type in TypeScript. Reflection won´t help here, methods
to extract the type from a variable are needed. That would be [getType()](https://www.php.net/manual/en/function.gettype) 
. There is a catch. The types it returns are not the same as used by the Reflection classes. This function is quite 
old, but there is a new alternative out for PHP8 [get_debug_type()](https://www.php.net/manual/en/function.get-debug-type.php)
This would be needed especially to tackle the tricky cases of **arrays**. Here a plethora of problems arise which I 
did not cover. There is another catch here: Reflection has also to handle the deserialization and denormalization
process. In that case it could make sense to inspect a class without any data in it. Therefore, it would make sense 
to keep all the value based type inspection out. 

Keep in mind that reflection is quite inefficient. In this project every time an object gets processed the 
reflection for its underlying class is done, again and again. This is only necessary in case of the event when 
the underlying class changes. There are no file watchers in PHP but in a production setup all the reflection 
processing could get cached. At least this is what the symfony/serializer offers. This leads to the question of
caching and this is bound most likely to a framework. Same goes for configuration which needs some kind of class 
and environment loading library. 

The goal of this project was to keep things agnostic from any framework and simple. As I said: I learned a lot by doing
this, and I hope you too. Thank you for reading.

