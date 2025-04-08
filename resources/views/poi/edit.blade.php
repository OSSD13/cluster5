@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')
<div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">POI เพิ่มสถานที่</h2>

    <label class="block text-sm text-gray-600">Link Google (Optional)</label>
    <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="Link Google">

    <label class="block text-sm text-gray-600">ละติจูด</label>
    <input id="latitude" name="latitude" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ละติจูด" value="{{ $show->poi_gps_lat }}">

    <label class="block text-sm text-gray-600">ลองจิจูด</label>
    <input id="longitude" name="longitude" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ลองจิจูด" value="{{ $show->poi_gps_lng }}">

    <div class="w-full h-48 bg-gray-200 rounded-lg mb-3">
        <img src="your-map-image-url.png" alt="Map" class="w-full h-full object-cover rounded-lg">
    </div>

    <label class="block text-sm text-gray-600">รหัสไปรษณีย์</label>
    <input id="postal_code" name="postal_code" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="รหัสไปรษณีย์" >

    <label class="block text-sm text-gray-600">จังหวัด</label>
    <input id="province" name="province" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="จังหวัด">

    <label class="block text-sm text-gray-600">อำเภอ</label>
    <input id="district" name="district" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="อำเภอ">

    <label class="block text-sm text-gray-600">ตำบล</label>
    <input id="sub_district" name="sub_district" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ตำบล">

    <label class="block text-sm text-gray-600">ที่อยู่</label>
    <input id="address" name="address" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ที่อยู่" >

    <label class="block text-sm text-gray-600">ชื่อ</label>
    <input id="name" name="name" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ชื่อ" value="{{ $show->poi_name }}">

    <label class="block text-sm text-gray-600">ประเภท</label>
    <select id="type" name="type" class="w-full p-2 border border-gray-300 rounded-lg mb-3">
        <option>เลือกประเภทสถานที่</option>
    </select>

    <div class="flex justify-between">
        <a href="{{ route('poi.index') }}">
                <button class="px-4 py-2 bg-gray-500 text-white rounded-lg cursor-pointer">ยกเลิก</button>
        </a>
        <button class="px-4 py-2 bg-green-700 text-white rounded-lg cursor-pointer" id="saveButton">บันทึก</button>
    </div>
</div>
@endsection
@section('script')
<script>
    // ฟังก์ชันเพื่อเริ่มต้นการแสดงแผนที่
    function initMap() {
        const initialPosition = {
            lat: 13.7358,  // ค่าละติจูดเริ่มต้น
            lng: 100.5231 // ค่าลองจิจูดเริ่มต้น
        };

        // สร้างแผนที่บน div ที่มี id="map"
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 15,
            center: initialPosition,  // ตั้งศูนย์แผนที่ตรงตำแหน่งเริ่มต้น
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        // สร้าง Marker
        const marker = new google.maps.Marker({
            position: initialPosition,
            map: map,
            title: "ตำแหน่งของคุณ",
            draggable: true
        });

        // ฟังก์ชันการอัพเดตค่าละติจูดและลองจิจูดจากการลาก Marker
        google.maps.event.addListener(marker, 'dragend', function(event) {
            document.getElementById('latitude').value = event.latLng.lat();
            document.getElementById('longitude').value = event.latLng.lng();
        });
    }

    // โหลด Google Maps API พร้อมกับการเรียกใช้งานฟังก์ชัน initMap
    function loadGoogleMapsAPI() {
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap`;
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }

    window.onload = loadGoogleMapsAPI;
</script>
@endsection