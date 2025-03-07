<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Point_of_interest extends Model
{

    /** @use HasFactory<\Database\Factories\Point_of_interestFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'gps',
        'poi_name'
    ];
    //
}
