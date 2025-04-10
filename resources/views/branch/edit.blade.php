@extends('layouts.main')

@section('title', 'Branch')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto mb-5">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">แก้ไขสาขา</h2>
        </div>
    </div>
    <form method="POST" action="{{ route('api.branch.edit') }}" name="branchForm" id="branchForm" autocomplete="off">
        @csrf
        <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">รายละเอียดสาขา</h2>

            <input type="hidden" name="bs_id" value="{{ $branch->bs_id }}">

            <label class="block text-sm text-gray-600">Link Google (Optional)</label>
            <input type="text" id="googleMapLink" class="w-full p-2 border border-gray-300 rounded-lg mb-3"
                placeholder="Link Google">
            <span id="googleLink-error" class="text-red-500 text-sm hidden">ลิงก์ไม่ถูกต้อง</span>

            <label class="block text-sm text-gray-600">ละติจูด</label>
            <input type="text" name="poi_gps_lat" id="latitude" value="{{ $branch->poi_gps_lat }}"
                class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ละติจูด">

            <label class="block text-sm text-gray-600">ลองจิจูด</label>
            <input type="text" name="poi_gps_lng" id="longitude" value="{{ $branch->poi_gps_lng }}"
                class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ลองจิจูด">

            <div class="w-full h-48 bg-gray-200 rounded-lg mb-3">
                <div id="map" class="w-full h-48 rounded-lg"></div>
            </div>

            <label class="block text-sm text-gray-600">รหัสไปรษณีย์</label>
            <input type="text" name="zipcode" id="zipcode" value="{{ $branch->zipcode }}"
                class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="รหัสไปรษณีย์">

            <label class="block text-sm text-gray-600">จังหวัด</label>
            <input type="text" name="province" id="province" value="{{ $branch->province }}"
                class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="จังหวัด">

            <label class="block text-sm text-gray-600">อำเภอ</label>
            <input type="text" name="amphoe" id="amphoe" value="{{ $branch->amphoe }}"
                class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="อำเภอ">

            <label class="block text-sm text-gray-600">ตำบล</label>
            <input type="text" name="district" id="district" value="{{ $branch->district }}"
                class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ตำบล">

            <label class="block text-sm text-gray-600">ที่อยู่</label>
            <input type="text" name="address" id='address' value="{{ $branch->bs_address }}"
                class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ที่อยู่">

            <label class="block text-sm text-gray-600">ชื่อ</label>
            <input type="text" name="name" id='name' value="{{ $branch->bs_name }}"
                class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ชื่อ">

            <label class="block text-sm text-gray-600">รายละเอียด</label>
            <input type="text" name="detail" id='detail' value="{{ $branch->bs_detail }}"
                class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="รายละเอียดเพิ่มเติม">

            <div class="flex justify-between">
                <a href="{{ route('branch.index') }}">
                    <button type="button"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg cursor-pointer">ยกเลิก</button>
                </a>
                <button type="submit" class="px-4 py-2 bg-green-700 text-white rounded-lg cursor-pointer"
                    id="saveButton">บันทึก</button>
            </div>
        </div>
    </form>
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


        const form = document.getElementById('branchForm');
        const submitButton = document.getElementById('saveButton');
        const googleMapLinkInput = document.getElementById('googleMapLink');
        const allFields = [
            'latitude',
            'longitude',
            'zipcode',
            'province',
            'amphoe',
            'district',
            'address',
            'name'
        ];
        const requiredFields = ['latitude', 'longitude', 'zipcode', 'province', 'amphoe', 'district', 'address', 'name'];


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
                poi_gps_lat: form.latitude.value,
                poi_gps_lng: form.longitude.value,
                zipcode: form.zipcode.value,
                province: form.province.value,
                district: form.district.value,
                amphoe: form.amphoe.value,
                address: form.address.value,
                name: form.name.value,
                detail: form.detail.value || null, // Optional field
                bs_id: form.bs_id.value,
            };

            submitButton.disabled = true;
            submitButton.innerText = 'กำลังบันทึก...';

            try {
                const response = await fetch(`{{ route('api.branch.edit') }}`, {
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
                        window.location.href = "{{ route('branch.index') }}";
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
        functions.setMapPosition('{{ $branch->poi_gps_lat }}', '{{ $branch->poi_gps_lng }}');
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


@endsection
