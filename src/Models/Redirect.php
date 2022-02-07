<?php

namespace Rapidez\MirasvitAdvancedSeoSuite\Models;

use Rapidez\Core\Models\Model;
use Rapidez\Core\Models\Scopes\IsActiveScope;
use Rapidez\Core\Models\Scopes\ForCurrentStoreScope;

class Redirect extends Model
{
    /**
     * The primary key for the model.
     * @var string
     */
    protected $primaryKey = 'redirect_id';

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'mst_seo_redirect';

    protected static function booted()
    {
        static::addGlobalScope(new IsActiveScope());
        static::addGlobalScope(new ForCurrentStoreScope());
    }
}
