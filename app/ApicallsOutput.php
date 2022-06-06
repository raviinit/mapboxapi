<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApicallsOutput extends Model
{
    public $table = 'apicallsoutput';

    protected $fillable = [
        'name', 'latitude', 'longitude'
    ];


}
