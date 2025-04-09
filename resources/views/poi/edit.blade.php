@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')
<div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">POI เพิ่มสถานที่</h2>

    <label class="block text-sm text-gray-600">Link Google (Optional)</label>
    <input id= "googleMapLink" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="Link Google">
    <span id="googleLink-error" class="text-red-500 text-sm hidden">ลิงก์ไม่ถูกต้อง</span>

    <label class="block text-sm text-gray-600">ละติจูด</label>
    <input id="lat" name="lat" type="text" oninput="functions.inputChanged()" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ละติจูด" value="{{ $show->poi_gps_lat }}">

    <label class="block text-sm text-gray-600">ลองจิจูด</label>
    <input id="lng" name="lng" type="text" oninput="functions.inputChanged()" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ลองจิจูด" value="{{ $show->poi_gps_lng }}">

    <div class="w-full h-48 bg-gray-200 rounded-lg mb-3">
        <img src="your-map-image-url.png" alt="Map" class="w-full h-full object-cover rounded-lg">
    </div>

    <label class="block text-sm text-gray-600">รหัสไปรษณีย์</label>
    <input id="postal_code" name="postal_code" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="รหัสไปรษณีย์" >

    <label class="block text-sm text-gray-600">จังหวัด</label>
    <input id="province" name="province" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="จังหวัด">

    <label class="block text-sm text-gray-600">อำเภอ</label>
    <input id="district" name="amphoe" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="อำเภอ">

    <label class="block text-sm text-gray-600">ตำบล</label>
    <input id="sub_district" name="
    district" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ตำบล">

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

    <script type="module">

        const googleMapLinkInput = document.getElementById('googleMapLink');
        let functions = {};

        function log(...args) {
            let date = `[${Date.now()}]`;

            console.log(date, ...args);
        }

        const {
            Map
        } = await google.maps.importLibrary("maps");
        const {
            AdvancedMarkerElement,
            PinElement
        } = await google.maps.importLibrary("marker");
        let map, MapMarker;

        functions.initMap = async function() {
            const position = {
                lat: 13.2855079,
                lng: 100.9246009
            };
            
            map = new Map(document.getElementById("map"), {
                zoom: 15,
                center: position,
                mapId: "DEMO_MAP_ID",
            });

            const pinBackground = new PinElement({
                glyph: "⭐",
                glyphColor: "white",
                scale: 1.5
            });
            MapMarker = new google.maps.marker.AdvancedMarkerElement({
                position: position,
                map: map,
                content: pinBackground.element,
                gmpDraggable: false,
            });
        }

        functions.setMapPosition = function(lat, lng) {
            const position = {
                lat: parseFloat(lat),
                lng: parseFloat(lng)
            };
            // console.log(position);
            
            map.setCenter(position);
            MapMarker.position = position;
        }

        functions.inputChanged = function() {
            // รับค่าที่กรอกใน input
            var latChanged = document.getElementById('lat').value;
            var lngChanged = document.getElementById('lng').value;
             console.log(latChanged,lngChanged);
            console.log(parseFloat(latChanged),parseFloat(lngChanged));

            functions.setMapPosition(latChanged, lngChanged);
        }
        function validateForm() {
            const isComplete = requiredFields.every(id => {
                const input = document.getElementById(id);
                return input && input.value.trim() !== '';
            });

            submitButton.disabled = !isComplete;
            submitButton.classList.toggle('bg-green-700', isComplete);
            submitButton.classList.toggle('bg-gray-400', !isComplete);
            submitButton.classList.toggle('cursor-not-allowed', !isComplete);
        }
         // Fetch lat/lng from Google Map link
         googleMapLinkInput.addEventListener('blur', async () => {
            const url = googleMapLinkInput.value.trim();
            if (!url) return;

            try {
                const response = await fetch(`{{ route('handleConversion') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        url
                    })
                });

                const data = await response.json();
                console.log(response.ok, data.lat, data.lng);

                if (response.ok && data.lat && data.lng) {
                    document.getElementById('lat').value = data.lat;
                    document.getElementById('lng').value = data.lng;
                    document.getElementById('googleLink-error').classList.add('hidden');
                    window.functions.setMapPosition(data.lat, data.lng);
                    // validateForm();
                } else {
                    throw new Error('Invalid data');
                }
            } catch (err) {
                console.error('Error fetching lat/lng:', err);
                document.getElementById('googleLink-error').classList.remove('hidden');
                document.getElementById('lat').value = '';
                document.getElementById('lng').value = '';
            }
        });

        googleMapLinkInput.addEventListener('input', () => {
            document.getElementById('googleLink-error').classList.add('hidden');
        });

    
        functions.initMap();
        window.functions = functions;
        functions.setMapPosition('{{ $show->poi_gps_lat }}', '{{ $show->poi_gps_lng }}');
           
    </script>
    <!-- prettier-ignore -->
<script>(g => { var h, a, k, p = "The Google Maps JavaScript API", c = "google", l = "importLibrary", q = "__ib__", m = document, b = window; b = b[c] || (b[c] = {}); var d = b.maps || (b.maps = {}), r = new Set, e = new URLSearchParams, u = () => h || (h = new Promise(async (f, n) => { await (a = m.createElement("script")); e.set("libraries", [...r] + ""); for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]); e.set("callback", c + ".maps." + q); a.src = `https://maps.${c}apis.com/maps/api/js?` + e; d[q] = f; a.onerror = () => h = n(Error(p + " could not load.")); a.nonce = m.querySelector("script[nonce]")?.nonce || ""; m.head.append(a) })); d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n)) })
    ({ key: "AIzaSyCIqpKnIfAIP48YujVFbBISkubwaQNdIME", v: "weekly" });</script>

<script>
    $(document).ready(function () {
        $.Thailand({
            database: '{{ asset('assets/js/db.json') }}',
            database_type: 'json',
            $district: $('#district'),
            $amphoe: $('#amphoe'),
            $province: $('#province'),
            $zipcode: $('#zipcode'),

            onDataFill: function (data) {
                console.info('Data Filled', data);
            },

            onLoad: function () {
                console.info('Thailand.js Autocomplete ready ✔️');
            }
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