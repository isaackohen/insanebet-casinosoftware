<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Currency extends Model
{
    protected $collection = 'currencies';

    protected $connection = 'mongodb';

    /* Old -- I dont think data is double needed fillable and on casts
    protected $fillable = [
        'currency', 'data'
    ];*/

    protected $fillable = [
        'currency',
    ];

    protected $casts = [
        'data' => 'json',
    ];
}
