<?php

namespace Rapidez\MirasvitAdvancedSeoSuite\Models;

use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Model;
use Rapidez\Core\Models\Scopes\IsActiveScope;

class Autolink extends Model
{
    protected $table = 'mst_seoautolink_link';

    protected static function booted()
    {
        static::addGlobalScope(new IsActiveScope());
    }

    public static function replace(string $text): string
    {
        $limit = (int) Rapidez::config('seoautolink/autolink/links_limit_per_page', -1);
        $counter = 0;

        self::all()->each(function ($autolink) use (&$text, &$counter, $limit) {
            $url = url($autolink->url);
            $link = " <a href=\"$url\" target=\"$autolink->url_target\" title=\"$autolink->url_title\">$autolink->keyword</a> ";

            if ($limit > -1 && $counter >= $limit) {
                return false;
            }

            $remaining = $limit > -1 ? max($limit - $counter, 0) : -1;
            $text = preg_replace('/ '.$autolink->keyword.' (?!([^<]+)?>)/i', $link, $text, $remaining, $count);
            $counter += $count;
        });

        return $text;
    }
}
