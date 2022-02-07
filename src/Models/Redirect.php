<?php

namespace Rapidez\MirasvitAdvancedSeoSuite\Models;

use Rapidez\Core\Models\Model;
use Rapidez\Core\Models\Scopes\IsActiveScope;
use Rapidez\Core\Models\Scopes\ForCurrentStoreScope;

class Redirect extends Model
{
    protected $table = 'mst_seo_redirect';
    
    protected $primaryKey = 'redirect_id';

    protected static function booted()
    {
        static::addGlobalScope(new IsActiveScope());
        static::addGlobalScope(new ForCurrentStoreScope());
    }
}
