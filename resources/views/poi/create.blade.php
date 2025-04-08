@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')
    <style>
        .error-input-style {
            border: 2px solid #F02801;
        }
    </style>

    <form method="POST" action="{{ route('poi.create') }}" name="poiForm" id="poiForm">
        @csrf
        <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">POI เพิ่มสถานที่</h2>

            <label class="block text-sm text-gray-600">Link Google (Optional)</label>
            <input type="text" id="googleMapLink" name="googleMapLink"
                class="w-full p-2 border border-gray-300 rounded-lg mb-1" placeholder="Link Google">
            <span id="googleLink-error" class="text-red-500 text-sm hidden">ลิงก์ไม่ถูกต้อง</span>

            <label class="block text-sm text-gray-600">ละติจูด</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-1" placeholder="ละติจูด"
                name="latitude" value="{{ old('latitude') }}" id="latitude">
            <span id="latitude-error" class="text-red-500 text-sm hidden">กรุณาใส่ละติจูดให้ถูกต้อง (เช่น 13.7563)</span>

            <label class="block text-sm text-gray-600">ลองจิจูด</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-1" placeholder="ลองจิจูด"
                name="longitude" value="{{ old('longitude') }}" id="longitude">
            <span id="longitude-error" class="text-red-500 text-sm hidden">กรุณาใส่ลองจิจูดให้ถูกต้อง (เช่น 100.5018)</span>

            <div class="w-full h-48 bg-gray-200 rounded-lg mb-3">
                <div id="map" class="w-full h-48"></div>
            </div>

            <label class="block text-sm text-gray-600">รหัสไปรษณีย์</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-1" placeholder="รหัสไปรษณีย์"
                name="postal_code" value="{{ old('postal_code') }}" id="postal_code">
            <span id="postal-error" class="text-red-500 text-sm hidden">กรุณาใส่รหัสไปรษณีย์ให้ถูกต้อง (5 หลัก)</span>

            <label class="block text-sm text-gray-600">จังหวัด</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-1" placeholder="จังหวัด"
                name="province" value="{{ old('province') }}" id="province">

            <label class="block text-sm text-gray-600">อำเภอ</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-1" placeholder="อำเภอ"
                name="district" value="{{ old('district') }}" id="district">
            <span id="province-error" class="text-red-500 text-sm hidden">กรุณากรอกจังหวัดให้ถูกต้อง</span>

            <label class="block text-sm text-gray-600">ตำบล</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-1" placeholder="ตำบล"
                name="sub_district" value="{{ old('sub_district') }}" id="sub_district">

            <label class="block text-sm text-gray-600">ที่อยู่</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-1" placeholder="ที่อยู่"
                name="address" value="{{ old('address') }}" id="address">

            <label class="block text-sm text-gray-600">ชื่อ</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-1" placeholder="ชื่อ"
                name="name" value="{{ old('name') }}" id="name">

            <label class="block text-sm text-gray-600">ประเภท</label>
            <select class="w-full p-2 border border-gray-300 rounded-lg mb-3" name="type" id="type">
                <option class="hidden" value="" disabled {{ old('type') == '' ? 'selected' : '' }}>เลือกประเภทสถานที่
                </option>
                <option value="โรงพยาบาล" {{ old('type') == 'โรงพยาบาล' ? 'selected' : '' }}>โรงพยาบาล</option>
            </select>

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
        const form = document.getElementById('poiForm');
        const submitButton = document.getElementById('saveButton');
        const googleMapLinkInput = document.getElementById('googleMapLink');
        const requiredFields = ['latitude', 'longitude', 'postal_code', 'province', 'district', 'sub_district', 'address',
            'name', 'type'
        ];

        // Validate form completeness
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

        // Attach input listeners for validation
        requiredFields.forEach(id => {
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
                postal_code: form.postal_code.value,
                province: form.province.value,
                district: form.district.value,
                sub_district: form.sub_district.value,
                address: form.address.value,
                name: form.name.value,
                type: form.type.value,
            };

            submitButton.disabled = true;
            submitButton.innerText = 'กำลังบันทึก...';

            try {
                const response = await fetch(`{{ route('api.poi.create') }}`, {
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
                poi_postal_code: 'postal_code',
                poi_province: 'province',
                poi_district: 'district',
                poi_sub_district: 'sub_district',
                poi_address: 'address',
                poi_name: 'name',
                poi_type: 'type'
            };
            return map[field] || field;
        }

        // Initial validation state
        validateForm();
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
        window.functions = functions;
    </script>
    <!-- prettier-ignore -->
<script>(g => { var h, a, k, p = "The Google Maps JavaScript API", c = "google", l = "importLibrary", q = "__ib__", m = document, b = window; b = b[c] || (b[c] = {}); var d = b.maps || (b.maps = {}), r = new Set, e = new URLSearchParams, u = () => h || (h = new Promise(async (f, n) => { await (a = m.createElement("script")); e.set("libraries", [...r] + ""); for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]); e.set("callback", c + ".maps." + q); a.src = `https://maps.${c}apis.com/maps/api/js?` + e; d[q] = f; a.onerror = () => h = n(Error(p + " could not load.")); a.nonce = m.querySelector("script[nonce]")?.nonce || ""; m.head.append(a) })); d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n)) })
    ({ key: "AIzaSyCIqpKnIfAIP48YujVFbBISkubwaQNdIME", v: "weekly" });</script>

    <script>
        $.Thailand({
            database: './jquery.Thailand.js/database/db.json',

            $district: $('#demo1 [name="district"]'),
            $amphoe: $('#demo1 [name="amphoe"]'),
            $province: $('#demo1 [name="province"]'),
            $zipcode: $('#demo1 [name="zipcode"]'),

            onDataFill: function(data) {
                console.info('Data Filled', data);
            },

            onLoad: function() {
                console.info('Autocomplete is ready!');
                $('#loader, .demo').toggle();
            }
        });

        // watch on change

        $('#demo1 [name="district"]').change(function() {
            console.log('ตำบล', this.value);
        });
        $('#demo1 [name="amphoe"]').change(function() {
            console.log('อำเภอ', this.value);
        });
        $('#demo1 [name="province"]').change(function() {
            console.log('จังหวัด', this.value);
        });
        $('#demo1 [name="zipcode"]').change(function() {
            console.log('รหัสไปรษณีย์', this.value);
        });
    </script>
@endsection
