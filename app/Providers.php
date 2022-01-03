<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Providers extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'providers';

    protected $fillable = [
        'provider',
        'ggr',
        'games',
        'disabled',
        'img',
    ];
}
