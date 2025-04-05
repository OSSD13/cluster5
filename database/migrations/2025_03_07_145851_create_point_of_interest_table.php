<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('point_of_interests', function (Blueprint $table) {
            $table->id('poi_id');
            $table->string('poi_name');
            $table->string('poi_type');
            $table->foreign('poi_type')->references('poit_type')->on('point_of_interest_type');
            $table->double('poi_gps_lat');
            $table->double('poi_gps_lng');
            $table->string('poi_address')->nullable();
            $table->bigInteger('poi_location_id')->unsigned()->nullable();
            $table->foreign('poi_location_id')->references('location_id')->on('locations');

            $table->timestamps();
        });

        // read directory database_path('/raw/thailand-poi-geojson')
        // then filter for .geojson files
        // some files will ends with -v2 or -v3
        // ignore lower version and only use the highest version
        // read the file and insert the data into the database
        // use the filename as the poi_type
        // use the feature.properties.name as the poi_name
        // use the feature.geometry.coordinates[1] as the poi_gps_lat
        // use the feature.geometry.coordinates[0] as the poi_gps_lng

        Log::info('Reading files from directory: ' . database_path('/raw/thailand-poi-geojson'));
        $files = File::files(database_path('/raw/thailand-poi-geojson'));

        Log::info('Filtering for .geojson files');
        $geojsonFiles = collect($files)
            ->filter(fn($file) => Str::endsWith($file->getFilename(), '.geojson'))
            ->groupBy(fn($file) => preg_replace('/(-v\d+)?\.geojson$/', '', $file->getFilename()))
            ->map(fn($group) => $group->sortByDesc(function ($file) {
                preg_match('/-v(\d+)\.geojson$/', $file->getFilename(), $matches);
                return $matches[1] ?? PHP_INT_MIN;
            })->first());
        Log::info('GeoJSON files to be processed: ' . $geojsonFiles->implode(', '));
        Log::info('Processing each geojson file');
<<<<<<< HEAD
=======

>>>>>>> origin/develop
        foreach ($geojsonFiles as $file) {
            Log::info('Reading file: ' . $file->getFilename());
            $data = json_decode(File::get($file), true);
            $poiType = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $poiType = preg_replace('/^\d+-\d+-(.*?)(-v\d+)?$/', '$1', $poiType);

            Log::info('Preparing data for POI type: ' . $poiType);
            $poiData = [];
            foreach ($data['features'] as $feature) {
                $poiData[] = [
                    'poi_name' => $feature['properties']['name'],
                    'poi_type' => $poiType,
                    'poi_gps_lat' => $feature['geometry']['coordinates'][1],
                    'poi_gps_lng' => $feature['geometry']['coordinates'][0],
                    'poi_address' => $feature['properties']['address'] ?? null,
                    'poi_location_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            Log::info('Found ' . count($poiData) . ' POIs in file: ' . $file->getFilename());

            // Chunk the data into smaller parts
<<<<<<< HEAD
            $chunks = array_chunk($poiData, 1000);
=======
            $chunks = array_chunk($poiData, 7000);
>>>>>>> origin/develop
            DB::transaction(function () use ($chunks, $poiType) {
                foreach ($chunks as $chunk) {
                    DB::table('point_of_interests')->insert($chunk);
                    Log::info('Inserted ' . count($chunk) . ' POIs for type: ' . $poiType);
                }
            });
            Log::info(message: 'Done inserting ' . count($poiData) . ' POIs for type: ' . $poiType);

        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_of_interests');
    }
};
