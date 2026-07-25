<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Homepage extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Retrieve the singleton Homepage instance.
     */
    public static function getSingleton(): self
    {
        return self::firstOrFail();
    }

    public function getLogoSrcAttribute()
    {
        return asset(config('app.logo'));
    }
}
