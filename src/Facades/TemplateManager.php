<?php

namespace BrandStudio\Page\Facades;

use Illuminate\Support\Facades\Facade;

class TemplateManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'brandstudio_templatemanager';
    }

}
