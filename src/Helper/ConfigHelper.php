<?php

namespace TwentyTwoDigital\CashierFastspring\Helper;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class ConfigHelper
{
    public static function getBillableModelRelationalKey(): string
    {

        $billableModel = Config::get('services.fastspring.model', 'App\Models\User');
        $modelName = Str::singular(class_basename($billableModel));
        return Str::snake($modelName).'_id';
    }
}
