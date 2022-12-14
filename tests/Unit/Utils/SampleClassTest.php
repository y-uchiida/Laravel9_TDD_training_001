<?php

namespace Tests\Unit\Utils;

use App\Utils\SampleClass;
use PHPUnit\Framework\TestCase;

class SampleClassTest extends TestCase
{
    /** @test */
    public function randomStr_引数を指定しない場合10文字を返す()
    {
        $sample = new SampleClass;
        $str = $sample->randomStr();

        $this->assertSame(10, strlen($str));
    }

    /** @test */
    public function randomStr_引数で指定した文字数を返す()
    {
        $sample = new SampleClass;
        $str_8 = $sample->randomStr(8);
        $str_100 = $sample->randomStr(100);

        $this->assertSame(8, strlen($str_8));
        $this->assertSame(100, strlen($str_100));
    }

    /** @test */
    public function randomStr_ランダムな文字列を返す()
    {
        $sample = new SampleClass;

        $str_1 = $sample->randomStr();
        $str_2 = $sample->randomStr();

        $this->assertFalse($str_1 === $str_2);
    }
}