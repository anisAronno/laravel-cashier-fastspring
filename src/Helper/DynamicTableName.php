<?php

namespace TwentyTwoDigital\CashierFastspring\Helper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;

class DynamicTableName
{
    public static function getTableName($model): string
    {
         return Config::get('services.fastspring.'.Str::snake($model).'_table_name');
    }
}
