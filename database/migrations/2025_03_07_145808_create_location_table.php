<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Location;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id('location_id');
            $table->string('district');
            $table->string('amphoe');
            $table->string('province');
            $table->string('zipcode');
            $table->string('district_code');
            $table->string('amphoe_code');
            $table->string('province_code');
            $table->string('region');
        });

        // insert data from raw database
        // database/raw/raw_location_database.json

        $json = File::get(path: database_path('/raw/raw_location_database.json'));
        $data = json_decode($json);
        // Prepare an array of rows to be inserted
        $locations = [];
        foreach ($data as $obj) {
            $locations[] = [
                'district' => $obj->district,
                'amphoe' => $obj->amphoe,
                'province' => $obj->province,
                'zipcode' => $obj->zipcode,
                'district_code' => $obj->district_code,
                'amphoe_code' => $obj->amphoe_code,
                'province_code' => $obj->province_code,
                'region' => $obj->region,
            ];
        }

        // Insert all rows at once
        Location::insert($locations);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
