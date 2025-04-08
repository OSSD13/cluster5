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
        Schema::create('sales', function (Blueprint $table) {
            $table->id("sales_id");
            $table->double("sales_amount");
            $table->integer('sales_package_amount');
            $table->date('sales_month');
            $table->unsignedBigInteger('sales_bs_id');
            // foreign key
            $table->foreign('sales_bs_id')->references('bs_id')->on('branch_stores');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
