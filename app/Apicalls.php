<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Apicalls extends Model
{
    public $table = 'apicalls'; 

    protected $fillable = [
        'name', 'format'
    ];

}
