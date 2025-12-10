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
        $counter = 0;
        $usedUrls = [];

        self::all()->each(function ($autolink) use (&$text, &$counter, &$usedUrls, $limit) {
            if (self::shouldStopProcessing($counter, $limit)) {
                return false;
            }

            $url = url($autolink->url);

            if (self::shouldSkipDuplicateUrl($url, $usedUrls)) {
                return;
            }

            $remaining = self::getRemainingReplacements($autolink, $limit, $counter);
            $link = " <a href=\"$url\" target=\"$autolink->url_target\" title=\"$autolink->url_title\">$autolink->keyword</a> ";

            $text = preg_replace('/ '.$autolink->keyword.' (?!([^<]+)?>)/i', $link, $text, $remaining, $count);

            $counter += $count;

            if ($count > 0) {
                $usedUrls[] = $url;
            }
        });

        return $text;
    }

    protected static function shouldStopProcessing(int $counter, int $limit): bool
    {
        return $limit > -1 && $counter >= $limit;
    }

    protected static function shouldSkipDuplicateUrl(string $url, array $usedUrls): bool
    {
        return (bool) Rapidez::config(self::CONFIG_STOP_KEYWORD_PROCESSING, false) && in_array($url, $usedUrls);
    }

    /**
     * Determine the maximum number of replacements allowed for this autolink.
     * Respects both the global page limit and the keyword-specific limit by returning the lowest of the two.
     * Returns -1 for unlimited replacements.
     */
    protected static function getRemainingReplacements(Autolink $autolink, int $globalLimit, int $counter): int
    {
        $keywordLimit = (int) ($autolink->max_replacements ?: -1);
        $globalRemaining = $globalLimit > -1 ? max($globalLimit - $counter, 0) : -1;

        return match (true) {
            $globalRemaining === 0 => 0,
            $globalRemaining > -1 && $keywordLimit > -1 => min($globalRemaining, $keywordLimit),
            $globalRemaining > -1 => $globalRemaining,
            $keywordLimit > -1 => $keywordLimit,
            default => -1,
        };
    }
}
