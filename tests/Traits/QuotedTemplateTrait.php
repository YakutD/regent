<?php

namespace MultihandED\Regent\Tests\Traits;

use MultihandED\Regent\Facades\Regent;

trait QuotedTemplateTrait
{
    protected function getTemplates(): array
    {
        $delimiterDefault = Regent::getDelimiterDefault();

        $template = "test{$delimiterDefault}Pattern";
        $templateQuoted = preg_quote($template, $delimiterDefault);

        return [$template, $templateQuoted];
    }
}