<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

<<<<<<<< HEAD:app/Models/Point_of_interests.php
class Point_of_interests extends Model
{
    /** @use HasFactory<\Database\Factories\Point_of_interestsFactory> */
========
class PointOfInterest extends Model
{
    /** @use HasFactory<\Database\Factories\PointOfInterestFactory> */
>>>>>>>> origin/develop:app/Models/PointOfInterest.php
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'poi_id',
        'poi_name',
        'poi_type',
        'gps_lat',
        'gps_lng',
        'address',
        'location_id',
    ];

    public function getPOIId(): int
    {
        return $this->poi_id;

    }
}
