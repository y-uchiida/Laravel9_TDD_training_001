<?php

namespace App\Utils;

// Unit テストでの呼び出しでエラーにならないように、クラスを指定する
use Illuminate\Support\Str;

class SampleClass
{
  /**
   * 引数で指定された長さのランダムな文字列を返す
   */
  public function randomStr(int $length = 10)
  {
    return Str::random($length);
  }
}