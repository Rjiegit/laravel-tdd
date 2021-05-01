<?php

namespace Tests\Unit\Translator;

use App\Translator\CustomSlugTranslator;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class CustomSlugTranslatorTest extends TestCase
{
    public function test_it_can_translate_chinese_to_english()
    {
        $translator = new CustomSlugTranslator();

        $result = $translator->translate('英語 英語');

        $this->assertEquals("english-english", Str::lower($result));
    }

}
