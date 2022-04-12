<?php

namespace Rapidez\MirasvitAdvancedSeoSuite\Models;

use Rapidez\Core\Models\Category;
use Rapidez\Core\Models\Model;
use Rapidez\Core\Models\Product;
use Rapidez\Core\Models\Scopes\IsActiveScope;
use Rapidez\Core\Facades\Rapidez;

class Template extends Model
{
    protected $table = 'mst_seo_content_template';

    protected static function booted()
    {
        static::addGlobalScope(new IsActiveScope());
    }

    public static function content(Model $model, string $field)
    {
        if ($model instanceof Product) {
            $type = 1;
            $replaces = [
                '[product_name]'  => $model->name,
                '[category_name]' => '',
            ];
        }

        if ($model instanceof Category) {
            $type = 2;
            $replaces = [
                '[category_name]' => $model->name,
            ];
        }

        if (!isset($type)) {
            return;
        }

        $value = optional(self::firstWhere('rule_type', $type))->{$field};
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
}
