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
        Schema::create('branch_stores', function (Blueprint $table) {
            $table->id('bs_id');
            $table->string('bs_name');
            $table->string('bs_detail')->nullable();
            $table->string('bs_address');
            $table->bigInteger('bs_poi_id')->unsigned();
            $table->foreign('bs_poi_id')->references('poi_id')->on('point_of_interests');
            $table->bigInteger('bs_manager')->unsigned();
            $table->foreign('bs_manager')->references('user_id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_stores');
    }
};
