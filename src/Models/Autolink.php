<?php

namespace Rapidez\MirasvitAdvancedSeoSuite\Models;

use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Model;
use Rapidez\Core\Models\Scopes\IsActiveScope;

class Autolink extends Model
{
    protected $table = 'mst_seoautolink_link';

    protected const CONFIG_LINKS_LIMIT = 'seoautolink/autolink/links_limit_per_page';

    protected const CONFIG_STOP_KEYWORD_PROCESSING = 'seoautolink/autolink/stop_keyword_processing';

    protected static function booted()
    {
        static::addGlobalScope(new IsActiveScope());
    }

    public static function replace(string $text): string
    {
        $limit = (int) Rapidez::config(self::CONFIG_LINKS_LIMIT, -1);
        $avoidDuplicateUrls = (bool) Rapidez::config(self::CONFIG_STOP_KEYWORD_PROCESSING, 0);
        $counter = 0;
        $usedUrls = [];

        self::all()->each(function ($autolink) use (&$text, &$counter, &$usedUrls, $limit, $avoidDuplicateUrls) {
            if ($limit > -1 && $counter >= $limit) {
                return false;
            }

            $url = url($autolink->url);

            if ($avoidDuplicateUrls && in_array($url, $usedUrls)) {
                return;
            }

            $remaining = $limit > -1 ? max($limit - $counter, 0) : -1;
            $link = " <a href=\"$url\" target=\"$autolink->url_target\" title=\"$autolink->url_title\">$autolink->keyword</a> ";

            $text = preg_replace('/ '.$autolink->keyword.' (?!([^<]+)?>)/i', $link, $text, $remaining, $count);

            $counter += $count;

            if ($count > 0) {
                $usedUrls[] = $url;
            }
        });

        return $text;
    }
}
