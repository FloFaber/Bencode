# Bencode
Simple PHP Bencode library without dependecies inside a single PHP File.

# Requirements
PHP >= 7.4

# How to use it
``` PHP
use FloFaber\Bencode\Bencode;
require_once("Bencode.php");

// Encoding an integer
Bencode::encode(1337); // "i1337e"

// Encoding a string
Bencode::encode("bencoded string"); // "15:bencoded string"

// Encoding arrays
Bencode::encode([ "fruits" => [ "pear", "apple", "banana" ]]); // "d6:fruitsl4:pear5:apple6:bananaee"


// Decoding a list
Bencode::decode("l11:beetlejuice11:beetlejuice11:beetlejuicee"); // [ "beetlejuice", "beetlejuice", "beetlejuice" ]

// Decoding a dictionary
Bencode::decode("d6:fruitsl4:pear5:apple6:bananae10:vegetablesl6:potato5:onionee"); // [ "fruits" => [ "pear", "apple", "banana" ], "vegetables" => [ "potato", "onion" ]]

```
