<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('point_of_interest_type', function (Blueprint $table) {
            $table->string('poit_type')->primary()->unique();
            $table->string('poit_name');
            $table->string('poit_icon')->nullable();
            $table->string('poit_color')->nullable();
            $table->string('poit_description')->nullable();
            $table->timestamps();
        });

        Log::info('Reading files from directory: ' . database_path('raw/thailand-poi-geojson'));
        $files = File::files(database_path('raw/thailand-poi-geojson'));
        $geojsonFiles = collect($files)
            ->filter(fn($file) => Str::endsWith($file->getFilename(), '.geojson'))
            ->groupBy(fn($file) => preg_replace('/(-v\d+)?\.geojson$/', '', $file->getFilename()))
            ->map(fn($group) => $group->sortByDesc(function ($file) {
                preg_match('/-v(\d+)\.geojson$/', $file->getFilename(), $matches);
                return $matches[1] ?? PHP_INT_MIN;
            })->first());
        Log::info('Filtered GeoJSON files: ' . json_encode($geojsonFiles->keys()));

        $defaultValues = [
            "amphoe-center" => ["poit_name" => "ศูนย์กลางอำเภอ", "poit_icon" => "🏛", "poit_color" => "#1E90FF", "poit_description" => "ศูนย์กลางอำเภอ", 'created_at' => now(), 'updated_at' => now()],
            "bank" => ["poit_name" => "ธนาคาร", "poit_icon" => "🏦", "poit_color" => "#008000", "poit_description" => "ธนาคารและสถานที่ทำธุรกรรมทางการเงิน", 'created_at' => now(), 'updated_at' => now()],
            "beach" => ["poit_name" => "ชายหาด", "poit_icon" => "🏖", "poit_color" => "#FF8C00", "poit_description" => "ชายหาดและสถานที่พักผ่อนริมทะเล", 'created_at' => now(), 'updated_at' => now()],
            "building-landmark" => ["poit_name" => "อาคารสำคัญ", "poit_icon" => "🏢", "poit_color" => "#A9A9A9", "poit_description" => "อาคารและสถานที่สำคัญ", 'created_at' => now(), 'updated_at' => now()],
            "cave" => ["poit_name" => "ถ้ำ", "poit_icon" => "🕳️", "poit_color" => "#654321", "poit_description" => "ถ้ำและสถานที่สำรวจใต้ดิน", 'created_at' => now(), 'updated_at' => now()],
            "changwat-center" => ["poit_name" => "ศูนย์กลางจังหวัด", "poit_icon" => "🏙", "poit_color" => "#000000", "poit_description" => "ศูนย์กลางจังหวัด", 'created_at' => now(), 'updated_at' => now()],
            "college-and-university" => ["poit_name" => "วิทยาลัยและมหาวิทยาลัย", "poit_icon" => "🎓", "poit_color" => "#2E8B57", "poit_description" => "วิทยาลัยและมหาวิทยาลัย", 'created_at' => now(), 'updated_at' => now()],
            "court-center" => ["poit_name" => "ศาล", "poit_icon" => "⚖", "poit_color" => "#FF6347", "poit_description" => "ศาลและสถานที่พิจารณาคดี", 'created_at' => now(), 'updated_at' => now()],
            "department-of-lands" => ["poit_name" => "กรมที่ดิน", "poit_icon" => "🏤", "poit_color" => "#6B8E23", "poit_description" => "กรมที่ดินและหน่วยงานราชการที่เกี่ยวข้อง", 'created_at' => now(), 'updated_at' => now()],
            "gas-station" => ["poit_name" => "ปั๊มน้ำมัน", "poit_icon" => "⛽", "poit_color" => "#FF7F50", "poit_description" => "ปั๊มน้ำมันและสถานีเติมเชื้อเพลิง", 'created_at' => now(), 'updated_at' => now()],
            "health-center" => ["poit_name" => "ศูนย์สุขภาพ", "poit_icon" => "🏥", "poit_color" => "#FF69B4", "poit_description" => "ศูนย์สุขภาพและสถานพยาบาล", 'created_at' => now(), 'updated_at' => now()],
            "hospital" => ["poit_name" => "โรงพยาบาล", "poit_icon" => "🏨", "poit_color" => "#DC143C", "poit_description" => "โรงพยาบาลและสถานพยาบาลขนาดใหญ่", 'created_at' => now(), 'updated_at' => now()],
            "hotel" => ["poit_name" => "โรงแรม", "poit_icon" => "🏩", "poit_color" => "#FFD700", "poit_description" => "โรงแรมและที่พัก", 'created_at' => now(), 'updated_at' => now()],
            "jail" => ["poit_name" => "เรือนจำ", "poit_icon" => "🚔", "poit_color" => "#2F4F4F", "poit_description" => "สถานกักขังและเรือนจำ", 'created_at' => now(), 'updated_at' => now()],
            "lake" => ["poit_name" => "ทะเลสาบ", "poit_icon" => "🏞", "poit_color" => "#00BFFF", "poit_description" => "ทะเลสาบและแหล่งน้ำธรรมชาติ", 'created_at' => now(), 'updated_at' => now()],
            "masjid" => ["poit_name" => "มัสยิด", "poit_icon" => "🕌", "poit_color" => "#228B22", "poit_description" => "มัสยิดและสถานที่ประกอบศาสนาอิสลาม", 'created_at' => now(), 'updated_at' => now()],
            "mountain" => ["poit_name" => "ภูเขา", "poit_icon" => "⛰", "poit_color" => "#B8860B", "poit_description" => "ภูเขาและพื้นที่สูง", 'created_at' => now(), 'updated_at' => now()],
            "police-center" => ["poit_name" => "ศูนย์ตำรวจ", "poit_icon" => "🚓", "poit_color" => "#00008B", "poit_description" => "สถานีตำรวจและหน่วยงานบังคับใช้กฎหมาย", 'created_at' => now(), 'updated_at' => now()],
            "police-stop" => ["poit_name" => "จุดตรวจตำรวจ", "poit_icon" => "🛑", "poit_color" => "#FF0000", "poit_description" => "จุดตรวจของตำรวจ", 'created_at' => now(), 'updated_at' => now()],
            "rural-road-center" => ["poit_name" => "ศูนย์ถนนชนบท", "poit_icon" => "🛤", "poit_color" => "#808080", "poit_description" => "ศูนย์ควบคุมถนนชนบท", 'created_at' => now(), 'updated_at' => now()],
            "samnak-song" => ["poit_name" => "สำนักสงฆ์", "poit_icon" => "⛩", "poit_color" => "#D2691E", "poit_description" => "สำนักสงฆ์และสถานปฏิบัติธรรม", 'created_at' => now(), 'updated_at' => now()],
            "school" => ["poit_name" => "โรงเรียน", "poit_icon" => "🏫", "poit_color" => "#FF1493", "poit_description" => "โรงเรียนและสถานศึกษาระดับพื้นฐาน", 'created_at' => now(), 'updated_at' => now()],
            "stone-and-hole" => ["poit_name" => "หินและโพรง", "poit_icon" => "🪨", "poit_color" => "#8B4513", "poit_description" => "สถานที่ที่มีหินและโพรงธรรมชาติ", 'created_at' => now(), 'updated_at' => now()],
            "temple" => ["poit_name" => "วัด", "poit_icon" => "🏯", "poit_color" => "#DAA520", "poit_description" => "วัดและสถานที่ประกอบศาสนาพุทธ", 'created_at' => now(), 'updated_at' => now()],
            "tesaban" => ["poit_name" => "เทศบาล", "poit_icon" => "🏘", "poit_color" => "#5F9EA0", "poit_description" => "เทศบาลและศูนย์บริหารท้องถิ่น", 'created_at' => now(), 'updated_at' => now()],
            "view-point" => ["poit_name" => "จุดชมวิว", "poit_icon" => "🔭", "poit_color" => "#7B68EE", "poit_description" => "จุดชมวิวและสถานที่ท่องเที่ยวเชิงธรรมชาติ", 'created_at' => now(), 'updated_at' => now()],
            "waterfall" => ["poit_name" => "น้ำตก", "poit_icon" => "🌊", "poit_color" => "#00CED1", "poit_description" => "น้ำตกและสถานที่ท่องเที่ยวทางธรรมชาติ", 'created_at' => now(), 'updated_at' => now()],
            "chedi" => ["poit_name" => "เจดีย์", "poit_icon" => "🛕", "poit_color" => "#C0C0C0", "poit_description" => "เจดีย์และสถูป", 'created_at' => now(), 'updated_at' => now()],
            "church" => ["poit_name" => "โบสถ์", "poit_icon" => "⛪", "poit_color" => "#8B0000", "poit_description" => "โบสถ์และสถานที่ทางศาสนาคริสต์", 'created_at' => now(), 'updated_at' => now()],
            "dam" => ["poit_name" => "เขื่อน", "poit_icon" => "🏗", "poit_color" => "#4169E1", "poit_description" => "เขื่อนและอ่างเก็บน้ำ", 'created_at' => now(), 'updated_at' => now()],
            "geotour" => ["poit_name" => "ท่องเที่ยวธรณี", "poit_icon" => "🌍", "poit_color" => "#32CD32", "poit_description" => "แหล่งท่องเที่ยวทางธรณีวิทยา", 'created_at' => now(), 'updated_at' => now()],
            "health-care-station" => ["poit_name" => "สถานีอนามัย", "poit_icon" => "🏪", "poit_color" => "#FFDAB9", "poit_description" => "สถานีอนามัยและสถานพยาบาลชุมชน", 'created_at' => now(), 'updated_at' => now()],
            "hotspring" => ["poit_name" => "น้ำพุร้อน", "poit_icon" => "♨", "poit_color" => "#B22222", "poit_description" => "น้ำพุร้อนและบ่อน้ำแร่", 'created_at' => now(), 'updated_at' => now()],
            "island" => ["poit_name" => "เกาะ", "poit_icon" => "🏝", "poit_color" => "#ADFF2F", "poit_description" => "เกาะและพื้นที่ทางทะเล", 'created_at' => now(), 'updated_at' => now()],
            "kaeng" => ["poit_name" => "แก่ง", "poit_icon" => "🚤", "poit_color" => "#87CEEB", "poit_description" => "แก่งและพื้นที่น้ำไหลแรง", 'created_at' => now(), 'updated_at' => now()],
            "mining" => ["poit_name" => "เหมือง", "poit_icon" => "⛏", "poit_color" => "#696969", "poit_description" => "เหมืองและแหล่งทรัพยากรธรรมชาติ", 'created_at' => now(), 'updated_at' => now()],
            "pratath" => ["poit_name" => "พระธาตุ", "poit_icon" => "📿", "poit_color" => "#FFB6C1", "poit_description" => "พระธาตุและสถานที่ศักดิ์สิทธิ์", 'created_at' => now(), 'updated_at' => now()],
            "shine" => ["poit_name" => "ศาลเจ้า", "poit_icon" => "🌟", "poit_color" => "#FFA07A", "poit_description" => "ศาลเจ้าและสถานที่ศักดิ์สิทธิ์", 'created_at' => now(), 'updated_at' => now()],
            "susan" => ["poit_name" => "สุสาน", "poit_icon" => "🏵", "poit_color" => "#A52A2A", "poit_description" => "สุสานและสถานที่ฝังศพ", 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($geojsonFiles as $file) {
            $poiType = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $poiType = preg_replace('/^\d+-\d+-(.*?)(-v\d+)?$/', '$1', $poiType);
            $defaults = $defaultValues[$poiType] ?? ['poit_name' => $poiType, 'poit_icon' => null, 'poit_color' => null, 'poit_description' => null, 'created_at' => now(), 'updated_at' => now()];
            Log::info('Inserting POI Type: ' . $poiType);
            DB::table('point_of_interest_type')->updateOrInsert(['poit_type' => $poiType], values: $defaults);
        }

        // create default dependency types
        $defaultDependencyTypes = [
            "branch" => ["poit_name" => "สาขา", "poit_icon" => "🏢", "poit_color" => "#0000FF", "poit_description" => "สาขา", 'created_at' => now(), 'updated_at' => now()],
        ];
        foreach ($defaultDependencyTypes as $type => $defaults) {
            DB::table('point_of_interest_type')->updateOrInsert(['poit_type' => $type], $defaults);
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_of_interest_type');
    }
};
