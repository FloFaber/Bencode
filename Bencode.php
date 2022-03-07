<?php

namespace FloFaber\Bencode;

use Exception;

// Starting with PHP 8.1 this is a built-in function
if(!function_exists("array_is_list")){
  function array_is_list(array $array) : bool {
    $i = 0;
    foreach ($array as $k => $v) {
      if ($k !== $i++) {
        return false;
      }
    }
    return true;
  }
}

class Decoder
{

  private int $len = 0;
  private int $offset = 0;
  private string $data = "";

  /**
   * @throws Exception
   */
  public function decode($data)
  {

    $this->data = $data;
    $this->len = strlen($this->data);

    $ret = [];


    while ($this->offset < $this->len - 1) {
      array_push($ret, $this->decode_next());
    }
    if (count($ret) === 1) {
      $ret = $ret[0];
    }
    return $ret;
  }

  /**
   * @throws Exception
   */
  private function decode_next()
  {
    $char = $this->data[$this->offset];
    if (is_numeric($char)) {
      return $this->decode_string();
    } elseif ($char === "d") {
      return $this->decode_dict();
    } elseif ($char === "i") {
      return $this->decode_int();
    } elseif ($char === "l") {
      return $this->decode_list();
    } else {
      throw new Exception("Unexpected character at offset $this->offset: $char");
    }
  }


  /**
   * @throws Exception
   */
  // i1337e
  private function decode_int(): int
  {
    $e = strpos($this->data, "e", $this->offset);
    if ($e === false) {
      throw new Exception("Unterminated integer at offset $this->offset");
    }

    $n = "";
    $this->offset++;
    while ($this->offset < $e) {
      $n .= $this->data[$this->offset];
      $this->offset++;
    }
    $this->offset++;

    return (int)$n;
  }

  /**
   * @throws Exception
   */
  private function decode_string(): string
  {
    $len = "";

    // 4:pear

    // first get the length of the upcoming string
    while ($this->offset < $this->len) {
      if (is_numeric($this->data[$this->offset])) {
        $len .= $this->data[$this->offset];
      } else {
        break;
      }
      $this->offset++;
    }

    $len = (int)$len;

    // skip ":"
    $this->offset++;

    // then get read from current offset to offset + len
    $stop = $this->offset + $len;
    if ($stop > $this->len) {
      throw new Exception("String longer than length of data at offset $this->offset");
    }

    $s = "";
    while ($this->offset < $stop) {
      $s .= $this->data[$this->offset];
      $this->offset++;
    }

    return $s;
  }

  /**
   * @throws Exception
   */
  private function decode_dict(): array
  {

    $ret = [];
    while ($this->offset < $this->len) {

      $k = $this->decode_next();
      $v = $this->decode_next();

      $ret[$k] = $v;

      if ($this->data[$this->offset] === "e") {
        $this->offset++;
        break;
      }
    }
    return $ret;

  }

  /**
   * @throws Exception
   */
  private function decode_list(): array
  {
    $this->offset++;
    $ret = [];
    while ($this->offset < $this->len) {
      array_push($ret, $this->decode_next());
      if ($this->data[$this->offset] === "e") {
        $this->offset++;
        break;
      }
    }
    return $ret;
  }

}

class Encoder
{

  /**
   * @throws Exception
   */
  public function encode($x): string
  {
    switch (gettype($x)) {
      case "string":
        return $this->encode_string($x);
      case "integer":
        return $this->encode_int((int)$x);
      case "array":
        return $this->encode_array((array)$x);
      default:
        throw new Exception("Unknown type: " . gettype($x));
    }
  }

  private function encode_string(string $str): string
  {
    return strlen($str) . ":" . $str;
  }

  private function encode_int(int $num): string
  {
    return "i" . $num . "e";
  }

  /**
   * @throws Exception
   */
  private function encode_array(array $arr): string
  {
    if (array_is_list($arr)) {
      return $this->encode_list($arr);
    } else {
      $x = "";
      foreach ($arr as $key => $value) {
        $k = $this->encode($key);
        $v = $this->encode($value);
        $x .= $k . $v;
      }
      return "d" . $x . "e";
    }
  }

  /**
   * @throws Exception
   */
  private function encode_list(array $list): string
  {
    $x = "";
    foreach ($list as $item) {
      $x .= $this->encode($item);
    }
    return "l" . $x . "e";
  }

}


class Bencode
{
  /**
   * @throws Exception
   */
  public function decode($data)
  {
    return (new Decoder())->decode($data);
  }

  /**
   * @throws Exception
   */
  public function encode($data): string
  {
    return (new Encoder())->encode($data);
  }
}
