# JSON Serializer

So I´ve been thinking: how is JSON made? It´s a string representation of data. I use it every day.

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

How does it work under the hood? Could I do it myself? That was one topic to explore.

I´ve been using a few libraries and approaches yet. That is mostly Spring Boot with the bundled Jackson Object Mapper.
This is the approach I was aiming for. Simple Java classes (Plain Old Java Object - POJO) can get transformed to JSON.
There are a few annotations that can be used like @JsonIgnore. So the corresponding class attribute is not used.

Just to be on the safe side. Programming languages invent their own lingo for the same concepts. In PHP
[Attribute](https://www.php.net/manual/en/language.attributes.overview.php), introduced in PHP8, is what Annotation would be in Java: metadata
on class field. Annotations do exist in PHP as well as docblock comments. Docblock Annotations were used prior to Attributes, were not part of the 
standard library, and can be replaced by native Attributes.
Any class member variables are called [Property](https://www.php.net/manual/en/language.oop5.properties.php). That would
be an "Attribute" in Java to maximize the confusion.

There is already a PHP library that does that, mostly. It is https://symfony.com/doc/current/components/serializer.html
It can be configured with a lot of options. However, it is quite big. 

I wanted something simpler. In a small project I will never ever need XML parsing. Or Content negotiation. It will be
always be JSON parsing. And I´m not aiming for the best library that handles all edge cases. But a proof of concept
that will work.

Is there an alternative approach so solve this? Yes, your classes can implement the interface [jsonSerializable](https://www.php.net/manual/en/jsonserializable.jsonserialize.php)
This works out of the box and no libraries are needed. 

What I don´t like about this approach:
* It bloats your class files. 
* A lot of writing. You need to define a mapping for all attributes in your classes
* Type information is lost. All you can read is the name of the keys. But not their type.
* How do you guarantee that e.g. DateTime objects are always represented in the same format?

This is however a good option if you have a lot of custom transformations and renaming of keys. The `symfony/serializer`
and Jackson approach is a **rule based approach**. You define global and custom rules how certain type of data is mapped
according to these rules.

## Mission statement
build a JSON Serializer that takes a Plain Old PHP Object (POPO) and encodes it as JSON string. The
output data is not the same as the input. It should require zero dependencies (except development). I should use native PHP8.0 and 8.1 features.

This is a library project. No webserver is needed and development can be done inside phpunit.

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

Let´s run the class: `vendor/bin/phpunit ./tests/Unit/JsonSerializer/JsonSerializerTest.php`

