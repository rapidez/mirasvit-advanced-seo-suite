<?php

namespace Rapidez\MirasvitAdvancedSeoSuite\Models;

use Rapidez\Core\Models\Model;
use Rapidez\Core\Models\Scopes\IsActiveScope;

class Redirect extends Model
{
    protected $table = 'mst_seo_redirect';

    protected static function booted()
    {
        static::addGlobalScope(new IsActiveScope);
    }
}
