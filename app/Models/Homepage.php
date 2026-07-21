<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Homepage extends Model
{
    protected $guarded = [];

    /**
     * Retrieve the singleton Homepage instance.
     */
    public static function getSingleton(): self
    {
        return self::firstOrFail();
    }
}

