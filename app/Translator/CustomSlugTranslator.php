<?php


namespace App\Translator;


class CustomSlugTranslator implements Translator
{

    public function translate($sentence)
    {
        return 'english-english';
    }
}
