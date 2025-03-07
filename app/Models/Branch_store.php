<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch_store extends Model
{
    
    /** @use HasFactory<\Database\Factories\Branch_storeFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'bs_map_id',
        'bs_user_id',
        'bs_sales_id',
        'bs_name'
    ];
    //
}
