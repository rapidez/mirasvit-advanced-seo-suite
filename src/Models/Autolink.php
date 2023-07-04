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
        $limit = Rapidez::config('seoautolink/autolink/links_limit_per_page', false);
        $counter = 0;
        self::all()->each(function ($autolink) use (&$text, &$counter, $limit) {
            $url = url($autolink->url);
            $link = " <a href=\"$url\" target=\"$autolink->url_target\" title=\"$autolink->url_title\">$autolink->keyword</a> ";
            $text = preg_replace('/ '.$autolink->keyword.' (?!([^<]+)?>)/i', $link, $text, -1, $count);
            $counter += $count;
            if ($limit && $counter > $limit) {
                return false;
            }
        });

        return $text;
    }
}
