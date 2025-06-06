<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PointOfInterestType extends Model
{
    /** @use HasFactory<\Database\Factories\PointOfInterestTypeFactory> */
    use HasFactory;

    protected $primaryKey = 'poit_type';
    public $incrementing = false;
    protected $keyType = 'string';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'poit_type',
        'poit_name',
        'poit_icon',
        'poit_color',
        'poit_description'
    ];
    protected $table = 'point_of_interest_type';
    // public function getPOIId(): int
    // {
    //     return $this->poi_id;

    // }
}