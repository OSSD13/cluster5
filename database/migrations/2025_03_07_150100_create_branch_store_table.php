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
            $table->string('bs_map_id'); // foreign key ******************
            $table->string('bs_user_id'); // foreign key ******************
            $table->string('bs_sales_id'); // foreign key ******************
            $table->string('bs_name');
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
