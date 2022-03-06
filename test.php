<?php

use FloFaber\Bencode\Bencode;
require_once("Bencode.php");

function pass(){
  echo ": Passed\n";
}

function fail($msg = ""){
  echo ": Failed ($msg)\n";
}

// @ToDo: Decoding Tests

echo "Encoding string: ";
try{
  if(Bencode::encode("This is a string") === "16:This is a string"){
    pass();
  }else{
    fail();
  }
}catch (Exception $e){
  fail($e->getMessage());
}

echo "Encoding integer: ";
try{
  if(Bencode::encode(1337) === "i1337e"){
    pass();
  }else{
    fail();
  }
}catch (Exception $e){
  fail($e->getMessage());
}


echo "Encoding list: ";
try{
  if(Bencode::encode([ "beetlejuice", "beetlejuice", "beetlejuice" ]) === "l11:beetlejuice11:beetlejuice11:beetlejuicee"){
    pass();
  }else{
    fail();
  }
}catch (Exception $e){
  fail($e->getMessage());
}


echo "Encoding dictionary: ";
try{
  if(Bencode::encode([ "pear" => "tracker", "banana" => "papaya", "apple" => "google" ]) === "d4:pear:7:tracker6:banana:6:papaya5:apple:6:googlee"){
    pass();
  }else{
    fail();
  }
}catch (Exception $e){
  fail($e->getMessage());
}

echo "\n";
