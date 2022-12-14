<?php

namespace App\Utils;

class SampleClass
{
  /**
   * 引数で指定された長さのランダムな文字列を返す
   */
  public function randomStr(int $length = 10)
  {
    return \Str::random($length);
  }
}