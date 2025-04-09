@extends('layouts.main')

@section('title', 'Branch')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto mb-5">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">แก้ไขสาขา</h2>
        </div>
    </div>
    <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">รายละเอียดสาขา</h2>

        <label class="block text-sm text-gray-600">Link Google (Optional)</label>
        <input type="text" id="googleLink" class="w-full p-2 border border-gray-300 rounded-lg mb-3"
            placeholder="Link Google">


        <label class="block text-sm text-gray-600">ละติจูด</label>
        <input type="text" id="latitude" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ละติจูด"
            value="{{ $branch->poi_gps_lat }}">
        <!-- <pre>{{ json_encode($branch, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre> -->


        <label class="block text-sm text-gray-600">ลองจิจูด</label>
        <input type="text" id="longitude" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ลองจิจูด"
            value="{{ $branch->poi_gps_lng }}">

        <div class="w-full h-48 bg-gray-200 rounded-lg mb-3">
            <div id="map" class="w-full h-48"></div>
        </div>


        <label class="block text-sm text-gray-600">รหัสไปรษณีย์</label>
        <input type="text" id="zipcode" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="รหัสไปรษณีย์"
            value="{{ $branch->zipcode }}">

        <label class="block text-sm text-gray-600">จังหวัด</label>
        <input type="text" id="province" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="จังหวัด"
            value="{{ $branch->province }}">

        <label class="block text-sm text-gray-600">อำเภอ</label>
        <input type="text" id="amphoe" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="อำเภอ"
            value="{{ $branch->amphoe }}">

        <label class="block text-sm text-gray-600">ตำบล</label>
        <input type="text" id="district" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ตำบล"
            value="{{ $branch->district }}">

        <label class="block text-sm text-gray-600">ที่อยู่</label>
        <input type="text" id="address" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ที่อยู่"
            value="{{ $branch->bs_address }}">

        <label class="block text-sm text-gray-600">ชื่อสาขา</label>
        <input type="text" id="branchName" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ชื่อสาขา"
            value="{{ $branch->bs_name }}">

        <div class="flex justify-between">
            <a href="{{ route('branch.index') }}">
                <button class="px-4 py-2 bg-gray-500 text-white rounded-lg cursor-pointer">ยกเลิก</button>
            </a>
            <button class="px-4 py-2 bg-green-700 text-white rounded-lg cursor-pointer" id="saveButton"
                disabled>บันทึก</button>
        </div>
    </div>
@endsection

@section('script')
    <script>
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

    </script>
    <script>
        function validateForm() {
            const fields = [
                document.getElementById("googleLink").value,
                document.getElementById("latitude").value,
                document.getElementById("longitude").value,
                document.getElementById("zipcode").value,
                document.getElementById("province").value,
                document.getElementById("amphoe").value,
                document.getElementById("district").value,
                document.getElementById("address").value,
            ];
            const isFormValid = fields.every(field => field.trim() !== "");
            document.getElementById("saveButton").disabled = !isFormValid;
        }

        document.querySelectorAll("input, select").forEach(input => {
            input.addEventListener("input", validateForm);
        });

        document.getElementById("saveButton").addEventListener("click", function () {
            // Add your save logic here
            // Show SweetAlert
            Swal.fire({
                title: "เพิ่มสำเร็จ",
                icon: "success",
                showConfirmButton: true,
                confirmButtonColor: "#1c7d32",
                confirmButtonText: "ยืนยัน"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to the branch index page
                    window.location.href = "{{ route('branch.index') }}";
                }
            });
        });

        // Initial validation call on page load to disable save button if form is empty
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

        functions.initMap = async function () {
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

        functions.setMapPosition = function (lat, lng) {
            const position = {
                lat: parseFloat(lat),
                lng: parseFloat(lng)
            };
            map.setCenter(position);
            MapMarker.position = position;
        }


        functions.initMap();
        window.functions = functions;
        functions.setMapPosition('{{ $branch->poi_gps_lat }}', '{{ $branch->poi_gps_lng }}');
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

            // Optional: log changes
            $('#amphoe').on('change', function () {
                console.log('ตำบล', this.value);
            });
            $('#district').on('change', function () {
                console.log('อำเภอ', this.value);
            });
            $('#province').on('change', function () {
                console.log('จังหวัด', this.value);
            });
            $('#zipcode').on('change', function () {
                console.log('รหัสไปรษณีย์', this.value);
            });
        });
    </script>
@endsection