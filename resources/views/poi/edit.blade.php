@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')
    <style>
        .error-input-style {
            border: 2px solid #F02801;
        }
    </style>

    <form method="POST" action="{{ route('api.poi.edit') }}" name="poiForm" id="poiForm" autocomplete="off">
        @csrf
        <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">POI แก้ไขสถานที่</h2>

            <input type="hidden" name="poi_id" value="{{ $poi->poi_id }}">

            <label class="block text-sm text-gray-600">Link Google (Optional)</label>
            <input type="text" id="googleMapLink" name="googleMapLink"
                class="w-full p-2 border border-gray-300 rounded-lg mb-1" placeholder="Link Google">
            <span id="googleLink-error" class="text-red-500 text-sm hidden">ลิงก์ไม่ถูกต้อง</span>
            {{-- <div class="bg-gray-100 p-4 rounded-lg mb-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">POI Data</h3>
                <pre class="bg-gray-200 p-2 rounded-lg text-sm text-gray-800 overflow-auto">{{ json_encode($poi, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>

            @if ($locations)
                <div class="bg-gray-100 p-4 rounded-lg mb-4">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">locations Data</h3>
                    <pre class="bg-gray-200 p-2 rounded-lg text-sm text-gray-800 overflow-auto">{{ json_encode($locations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif --}}

            <label class="block text-sm text-gray-600">ละติจูด</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-1" placeholder="ละติจูด"
                name="latitude" value="{{ $poi->poi_gps_lat }}" id="latitude" inputmode="decimal">
            <span class="text-red-500 text-sm hidden" id="error-latitude"></span>

            <label class="block text-sm text-gray-600">ลองจิจูด</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-1" placeholder="ลองจิจูด"
                name="longitude" value="{{ $poi->poi_gps_lng }}" id="longitude" inputmode="decimal">
            <span class="text-red-500 text-sm hidden" id="error-longitude"></span>

            <div class="w-full h-48 bg-gray-200 rounded-lg mb-3">
                <div id="map" class="w-full h-48"></div>
            </div>

            <label class="block text-sm text-gray-600">รหัสไปรษณีย์</label>
            <input type="text"
                class="w-full p-2 border border-gray-300 rounded-lg mb-1 {{ !$locations ? 'bg-gray-200 cursor-not-allowed' : '' }}"
                placeholder="รหัสไปรษณีย์" name="zipcode" value="{{ $locations ? $locations->zipcode : '' }}" id="zipcode"
                pattern="\d{5}" inputmode="numeric" {{ !$locations ? 'disabled' : '' }}>
            <span class="text-red-500 text-sm hidden" id="error-zipcode"></span>

            <label class="block text-sm text-gray-600">จังหวัด</label>
            <input type="text"
                class="w-full p-2 border border-gray-300 rounded-lg mb-1 {{ !$locations ? 'bg-gray-200 cursor-not-allowed' : '' }}"
                placeholder="จังหวัด" name="province" value="{{ $locations ? $locations->province : '' }}" id="province"
                {{ !$locations ? 'disabled' : '' }}>
            <span class="text-red-500 text-sm hidden" id="error-province"></span>

            <label class="block text-sm text-gray-600">อำเภอ</label>
            <input type="text"
                class="w-full p-2 border border-gray-300 rounded-lg mb-1 {{ !$locations ? 'bg-gray-200 cursor-not-allowed' : '' }}"
                placeholder="อำเภอ" name="amphoe" value="{{ $locations ? $locations->amphoe : '' }}" id="amphoe"
                {{ !$locations ? 'disabled' : '' }}>
            <span class="text-red-500 text-sm hidden" id="error-amphoe"></span>

            <label class="block text-sm text-gray-600">ตำบล</label>
            <input type="text"
                class="w-full p-2 border border-gray-300 rounded-lg mb-1 {{ !$locations ? 'bg-gray-200 cursor-not-allowed' : '' }}"
                placeholder="ตำบล" name="district" value="{{ $locations ? $locations->district : '' }}" id="district"
                {{ !$locations ? 'disabled' : '' }}>
            <span class="text-red-500 text-sm hidden" id="error-district"></span>

            <label class="block text-sm text-gray-600">ที่อยู่</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-1" placeholder="ที่อยู่"
                name="address" value="{{ $poi->poi_address }}" id="address">
            <span class="text-red-500 text-sm hidden" id="error-address"></span>

            <label class="block text-sm text-gray-600">ชื่อ</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-1" placeholder="ชื่อ"
                name="name" value="{{ $poi->poi_name }}" id="name">
            <span class="text-red-500 text-sm hidden" id="error-name"></span>
            <label class="block text-sm text-gray-600">ประเภท</label>
            <select class="w-full p-2 border border-gray-300 rounded-lg mb-3" name="type" id="type">
                <option class="hidden" disabled {{ old('type') == '' ? 'selected' : '' }}>เลือกประเภทสถานที่
                </option>
                @foreach ($poiTypes as $type)
                    <option value="{{ $type->poit_type }}"
                        {{ (old('type') ?? $poi->poi_type) == $type->poit_type ? 'selected' : '' }}>
                        {{ $type->poit_icon }} {{ $type->poit_name }}
                    </option>
                @endforeach
            </select>
            <span class="text-red-500 text-sm hidden" id="error-type"></span>

            <div class="flex justify-between">
                <a href="{{ route('poi.index') }}"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg text-center">ยกเลิก</a>
                <button type="submit" class="px-4 py-2 bg-gray-500 text-white rounded-lg cursor-not-allowed" disabled
                    id="saveButton">บันทึก</button>
            </div>
        </div>
    </form>

    @if (session('success'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "{{ session('success') }}",
                    icon: "success",
                    showConfirmButton: true,
                    confirmButtonColor: "#1c7d32",
                    confirmButtonText: "ยืนยัน"
                }).then(() => {
                    window.location.href = "{{ route('poi.index') }}";
                });
            });
        </script>
    @endif
@endsection


@section('script')
    <script>
        let initialFormState = {};

        function captureInitialFormState() {
            const inputs = form.querySelectorAll("input, select");
            inputs.forEach(input => {
                initialFormState[input.name] = input.value;
            });
        }

        function hasFormChanged() {
            const inputs = form.querySelectorAll("input, select");
            return Array.from(inputs).some(input => {
                const original = (initialFormState[input.name] ?? '').trim();
                const current = input.value.trim();
                return original !== current;
            });
        }



        const form = document.getElementById('poiForm');
        const submitButton = document.getElementById('saveButton');
        const googleMapLinkInput = document.getElementById('googleMapLink');
        const allFields = [
            'latitude', 'longitude', 'zipcode', 'province', 'district', 'amphoe', 'address', 'name', 'type'
        ];
        const requiredFields = ['latitude', 'longitude', 'name', 'type'];

        if ({{ $locations ? 'true' : 'false' }}) {
            requiredFields.push('zipcode', 'province', 'district', 'amphoe', 'address');
        }

        // Validate form completeness
        function validateForm() {
            const isComplete = requiredFields.every(id => {
                const input = document.getElementById(id);
                return input && input.value.trim() !== '';
            });

            const changed = hasFormChanged();

            submitButton.disabled = !(isComplete && changed);
            submitButton.classList.toggle('bg-green-700', isComplete && changed);
            submitButton.classList.toggle('bg-gray-400', !(isComplete && changed));
            submitButton.classList.toggle('cursor-not-allowed', !(isComplete && changed));
        }


        // Attach input listeners for validation
        allFields.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.addEventListener('input', validateForm);
            }
        });

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

                if (response.ok && data.lat && data.lng) {
                    document.getElementById('latitude').value = data.lat;
                    document.getElementById('longitude').value = data.lng;
                    document.getElementById('googleLink-error').classList.add('hidden');
                    window.functions.setMapPosition(data.lat, data.lng);
                    validateForm();
                } else {
                    throw new Error('Invalid data');
                }
            } catch (err) {
                document.getElementById('googleLink-error').classList.remove('hidden');
                document.getElementById('latitude').value = '';
                document.getElementById('longitude').value = '';
            }
        });

        googleMapLinkInput.addEventListener('input', () => {
            document.getElementById('googleLink-error').classList.add('hidden');
        });

        // Submit form via API
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = {
                latitude: form.latitude.value,
                longitude: form.longitude.value,
                zipcode: form.zipcode.value,
                province: form.province.value,
                district: form.district.value,
                amphoe: form.amphoe.value,
                address: form.address.value,
                name: form.name.value,
                type: form.type.value,
                poi_id: form.poi_id.value,
            };

            submitButton.disabled = true;
            submitButton.innerText = 'กำลังบันทึก...';

            try {
                const response = await fetch(`{{ route('api.poi.edit') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    Swal.fire({
                        title: 'สำเร็จ!',
                        text: data.message || 'บันทึกข้อมูลสำเร็จ',
                        icon: 'success',
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#1c7d32',
                    }).then(() => {
                        window.location.href = "{{ route('poi.index') }}";
                    });
                } else if (data.errors) {
                    displayValidationErrors(data.errors);
                } else {
                    Swal.fire("เกิดข้อผิดพลาด", data.message || "ไม่สามารถบันทึกข้อมูลได้", "error");
                }
            } catch (err) {
                Swal.fire("ข้อผิดพลาด", "ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้", "error");
            }

            submitButton.disabled = false;
            submitButton.innerText = 'บันทึก';
        });

        function displayValidationErrors(errors) {
            for (const field in errors) {
                const inputName = convertApiFieldToInputName(field);
                const errorDiv = document.getElementById(`error-${inputName}`);
                if (errorDiv) {
                    errorDiv.textContent = errors[field][0];
                    errorDiv.classList.remove('hidden');
                }
            }
        }

        function convertApiFieldToInputName(field) {
            const map = {
                poi_latitude: 'latitude',
                poi_longitude: 'longitude',
                poi_zipcode: 'zipcode',
                poi_province: 'province',
                poi_district: 'district',
                poi_amphoe: 'amphoe',
                poi_address: 'address',
                poi_name: 'name',
                poi_type: 'type'
            };
            return map[field] || field;
        }

        // Initial validation state
        document.addEventListener("DOMContentLoaded", () => {
            captureInitialFormState(); // ✅ Save the initial form state
            validateForm(); // ✅ Run validation once
        });
    </script>

    <script type="module">
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
            map.setCenter(position);
            MapMarker.position = position;
        }

        functions.initMap();
        functions.setMapPosition('{{ $poi->poi_gps_lat }}', '{{ $poi->poi_gps_lng }}');
        document.getElementById('latitude').addEventListener('input', () => {
            const lat = parseFloat(document.getElementById('latitude').value);
            const lng = parseFloat(document.getElementById('longitude').value);
            if (!isNaN(lat) && !isNaN(lng)) {
                functions.setMapPosition(lat, lng);
            }
        });

        document.getElementById('longitude').addEventListener('input', () => {
            const lat = parseFloat(document.getElementById('latitude').value);
            const lng = parseFloat(document.getElementById('longitude').value);
            if (!isNaN(lat) && !isNaN(lng)) {
                functions.setMapPosition(lat, lng);
            }
        });
        window.functions = functions;
    </script>
    <!-- prettier-ignore -->
<script>(g => { var h, a, k, p = "The Google Maps JavaScript API", c = "google", l = "importLibrary", q = "__ib__", m = document, b = window; b = b[c] || (b[c] = {}); var d = b.maps || (b.maps = {}), r = new Set, e = new URLSearchParams, u = () => h || (h = new Promise(async (f, n) => { await (a = m.createElement("script")); e.set("libraries", [...r] + ""); for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]); e.set("callback", c + ".maps." + q); a.src = `https://maps.${c}apis.com/maps/api/js?` + e; d[q] = f; a.onerror = () => h = n(Error(p + " could not load.")); a.nonce = m.querySelector("script[nonce]")?.nonce || ""; m.head.append(a) })); d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n)) })
    ({ key: "AIzaSyCIqpKnIfAIP48YujVFbBISkubwaQNdIME", v: "weekly" });</script>

    <script>
        $(document).ready(function() {
            $.Thailand({
                database: '{{ asset('assets/js/db.json') }}',
                database_type: 'json',

                $district: $('#district'),
                $amphoe: $('#amphoe'),
                $province: $('#province'),
                $zipcode: $('#zipcode'),

                onDataFill: function(data) {
                    console.info('Data Filled', data);
                },

                onLoad: function() {
                    console.info('Thailand.js Autocomplete ready ✔️');
                }
            });

            // Optional: log changes
            $('#amphoe').on('change', function() {
                console.log('ตำบล', this.value);
            });
            $('#district').on('change', function() {
                console.log('อำเภอ', this.value);
            });
            $('#province').on('change', function() {
                console.log('จังหวัด', this.value);
            });
            $('#zipcode').on('change', function() {
                console.log('รหัสไปรษณีย์', this.value);
            });
        });

        
    </script>
    <script>
    document.getElementById("poiForm").addEventListener("submit", function (e) {
        const lat = document.getElementById("latitude").value.trim();
        const lng = document.getElementById("longitude").value.trim();

        // เช็คว่าค่าไม่ใช่ตัวเลข
        if (isNaN(lat) || isNaN(lng) || lat === '' || lng === '') {
            e.preventDefault(); // ป้องกันการ submit
            Swal.fire({
                icon: "error",
                title: "พิกัดไม่ถูกต้อง",
                text: "กรุณากรอก Latitude และ Longitude เป็นตัวเลขเท่านั้น",
                confirmButtonColor: "#d33",
                confirmButtonText: "ตกลง"
            });
        }
    });
</script>


@endsection