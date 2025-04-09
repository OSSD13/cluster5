@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')
<div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">POI แก้ไขสถานที่</h2>

    <input type="hidden" id="poi_id" value="{{ $show->id }}">

    <label class="block text-sm text-gray-600">Link Google (Optional)</label>
    <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="Link Google">

    <label class="block text-sm text-gray-600">ละติจูด</label>
    <input id="lat" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" value="{{ $show->poi_gps_lat }}">

    <label class="block text-sm text-gray-600">ลองจิจูด</label>
    <input id="lng" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" value="{{ $show->poi_gps_lng }}">

    <div class="w-full h-48 bg-gray-200 rounded-lg mb-3">
        <div id="map" class="w-full h-48"></div>
    </div>

    <label class="block text-sm text-gray-600">รหัสไปรษณีย์</label>
    <input id="zipcode" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" value="">

    <label class="block text-sm text-gray-600">จังหวัด</label>
    <input id="province" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" value="">

    <label class="block text-sm text-gray-600">อำเภอ</label>
    <input id="district" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" value="">

    <label class="block text-sm text-gray-600">ตำบล</label>
    <input id="amphoe" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" value="">

    <label class="block text-sm text-gray-600">ที่อยู่</label>
    <input id="address" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" value="{{ $show->poi_address }}">

    <label class="block text-sm text-gray-600">ชื่อ</label>
    <input id="name" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" value="{{ $show->poi_name }}">

    <label class="block text-sm text-gray-600">ประเภท</label>
    <select id="type" class="w-full p-2 border border-gray-300 rounded-lg mb-3">
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
    $(document).ready(function () {
        // Load Types from API
        $.getJSON('/api/poi/types', function(data) {
            data.forEach(function(item) {
                $('#type').append(`<option value="${item.point_type}">${item.point_type}</option>`);
            });

            $('#type').val('{{ $show->type->point_type ?? '' }}');
        });

        // Save Button Click
        $('#saveButton').on('click', function () {
            $('.error-input-style').removeClass('error-input-style'); // Reset error border

            let data = {
                id: $('#poi_id').val(),
                lat: $('#lat').val(),
                lng: $('#lng').val(),
                zipcode: $('#zipcode').val(),
                province: $('#province').val(),
                district: $('#district').val(),
                amphoe: $('#amphoe').val(),
                address: $('#address').val(),
                name: $('#name').val(),
                type: $('#type').val(),
            };

            $.ajax({
                url: '/api/poi/edit',
                type: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (res) {
                    alert(res.message);
                    window.location.href = '{{ route("poi.index") }}';
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(key => {
                            $('#' + key).addClass('error-input-style');
                        });
                        alert('กรุณากรอกข้อมูลให้ครบถ้วน');
                    } else {
                        alert(xhr.responseJSON.message || 'เกิดข้อผิดพลาด');
                    }
                }
            });
        });

        // Thailand.js
        $.Thailand({
            database: '{{ asset('assets/js/db.json') }}',
            database_type: 'json',
            $district: $('#district'),
            $amphoe: $('#amphoe'),
            $province: $('#province'),
            $zipcode: $('#zipcode'),
        });
    });
</script>

{{-- Google Maps --}}
<script type="module">
    const { Map } = await google.maps.importLibrary("maps");
    const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary("marker");

    const position = {
        lat: parseFloat('{{ $show->poi_gps_lat }}'),
        lng: parseFloat('{{ $show->poi_gps_lng }}')
    };

    const map = new Map(document.getElementById("map"), {
        zoom: 15,
        center: position,
        mapId: "DEMO_MAP_ID"
    });

    const pinBackground = new PinElement({
        glyph: "⭐",
        glyphColor: "white",
        scale: 1.5
    });

    const MapMarker = new google.maps.marker.AdvancedMarkerElement({
        position: position,
        map: map,
        content: pinBackground.element,
        gmpDraggable: false
    });
</script>

   <!-- prettier-ignore -->
<script>(g => { var h, a, k, p = "The Google Maps JavaScript API", c = "google", l = "importLibrary", q = "__ib__", m = document, b = window; b = b[c] || (b[c] = {}); var d = b.maps || (b.maps = {}), r = new Set, e = new URLSearchParams, u = () => h || (h = new Promise(async (f, n) => { await (a = m.createElement("script")); e.set("libraries", [...r] + ""); for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]); e.set("callback", c + ".maps." + q); a.src = `https://maps.${c}apis.com/maps/api/js?` + e; d[q] = f; a.onerror = () => h = n(Error(p + " could not load.")); a.nonce = m.querySelector("script[nonce]")?.nonce || ""; m.head.append(a) })); d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n)) })
    ({ key: "AIzaSyCIqpKnIfAIP48YujVFbBISkubwaQNdIME", v: "weekly" });</script>

<script>
    document.getElementById('lat').addEventListener('input', updateMapPosition);
document.getElementById('lng').addEventListener('input', updateMapPosition);

function updateMapPosition() {
    const newLat = parseFloat(document.getElementById('lat').value);
    const newLng = parseFloat(document.getElementById('lng').value);

    if (!isNaN(newLat) && !isNaN(newLng)) {
        const newPos = { lat: newLat, lng: newLng };
        map.setCenter(newPos);
        MapMarker.position = newPos;
    }
}

</script>

<script>
    $(document).ready(function () {
        $.Thailand({
            database: '{{ asset('assets/js/db.json') }}',
            database_type: 'json',

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