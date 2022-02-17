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

There is already a library that does that, mostly. It is https://symfony.com/doc/current/components/serializer.html
It can be configured with a lot of options.

Just to be on the save side. Programming languages invent their own lingo for the same concepts. In PHP
[Attribute](https://www.php.net/manual/en/language.attributes.overview.php) is what Annotation would be in Java: metadata
on class field. And class member variables are called [Property](https://www.php.net/manual/en/language.oop5.properties.php)

I wanted something simpler. In a small project I will never ever need XML parsing. Or Content negotiation. It will be
always be JSON parsing. And I´m not aiming for the best library that handles all edge cases. But a proof of concept
that will work.

Mission statement: build a JSON Serializer that takes a Plain Old PHP Object (POPO) and encodes it as JSON string.

This is a library project. No webserver is needed and development can be done inside phpunit.

Chapter 1: 
The most simple encoding strategy I can think of is `json_encode(new User("Matt"))`. This will work and result in the 
`'{"name":"Matt"}'`. If requirements are simple enough and I guess this is the case for many projects. This is a 1:1 mapping
of input format to output format. 

