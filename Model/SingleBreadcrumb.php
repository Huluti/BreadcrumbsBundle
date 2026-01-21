<?php

namespace Huluti\BreadcrumbsBundle\Model;

class SingleBreadcrumb
{
    public $translationParameters;

    public function __construct(public $text = '', public $url = '', array $translationParameters = [], public $translate = true)
    {
        $this->translationParameters = $translationParameters;
    }
}
