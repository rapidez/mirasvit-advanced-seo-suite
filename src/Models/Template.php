<?php

namespace Rapidez\MirasvitAdvancedSeoSuite\Models;

use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Category;
use Rapidez\Core\Models\Model;
use Rapidez\Core\Models\Product;
use Rapidez\Core\Models\Scopes\IsActiveScope;

class Template extends Model
{
    protected $table = 'mst_seo_content_template';

    protected static function booted()
    {
        static::addGlobalScope(new IsActiveScope());
    }

    public static function content(Model $model, string $field)
    {
        $type = self::getModelType($model);

        if (!$type) {
            return;
        }

        $value = optional(self::firstWhere('rule_type', $type))->{$field};

        $replaces = [];
        foreach (self::getReplaceKeys($value) as $key) {
            $replaces[$key] = self::getReplaceValue($model, $key);
        }

        $value = strtr($value, array_merge($replaces ?? [], [
            '[store_name]'          => Rapidez::config('general/store_information/name'),
            '[store_phone]'         => Rapidez::config('general/store_information/phone'),
            '[store_email]'         => Rapidez::config('trans_email/ident_general/email'),
            '[store_street_line_1]' => Rapidez::config('general/store_information/street_line1'),
            '[store_street_line_2]' => Rapidez::config('general/store_information/street_line2'),
            '[store_city]'          => Rapidez::config('general/store_information/city'),
            '[store_postcode]'      => Rapidez::config('general/store_information/postcode'),
        ]));

        // Remove double spaces
        return preg_replace('/\s+/', ' ', $value);
    }

    protected static function getModelType(Model $model)
    {
        return match (true) {
            $model instanceof Product  => 1,
            $model instanceof Category => 2
        };
    }

    protected static function getReplaceKeys(string $value)
    {
        preg_match_all('/\[(.*?)\]/', $value, $matches);

        return $matches[0];
    }

    protected static function getReplaceValue(Model $model, string $replaceKey)
    {
        $replace = match (true) {
            $model instanceof Product  => str_replace('product_', '', $replaceKey),
            $model instanceof Category => str_replace('category_', '', $replaceKey)
        };

        return $model->{str_replace(['[', ']'], '', $replace)} ?? null;
    }
}
