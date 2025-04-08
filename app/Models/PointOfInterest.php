<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PointOfInterest extends Model
{
   /** @use HasFactory<\Database\Factories\PointOfInterestFactory> */
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
    protected $table = 'point_of_interests';

    public function getPOIId(): int
    {
        return $this->poi_id;

    }
}