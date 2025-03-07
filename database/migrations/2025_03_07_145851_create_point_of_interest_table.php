<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('point_of_interests', function (Blueprint $table) {
            $table->id('poi_id');
            $table->string('poi_name');
            $table->string('type');
            $table->double('gps_lat');
            $table->double('gps_lng');
            $table->string('address');
            $table->bigInteger('location_id')->unsigned();
            $table->foreign('location_id')->references('location_id')->on('locations');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_of_interests');
    }
};
