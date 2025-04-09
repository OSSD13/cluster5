@extends('layouts.main')

@section('title', 'Branch')

@section('content')
<div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto mb-5">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">แก้ไขสาขา</h2>
    </div>
</div>

<div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">รายละเอียดสาขา</h2>

    <input type="hidden" name="bs_id" value="{{ $branch->bs_id }}">

    <label class="block text-sm text-gray-600">Link Google (Optional)</label>
    <input type="text" id="googleMapLink" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="Link Google">

    <label class="block text-sm text-gray-600">ละติจูด</label>
    <input type="text" name="poi_gps_lat" id="latitude" value="{{ $branch->poi_gps_lat }}" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ละติจูด">

    <label class="block text-sm text-gray-600">ลองจิจูด</label>
    <input type="text" name="poi_gps_lng" id="longitude" value="{{ $branch->poi_gps_lng }}" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ลองจิจูด">

    <div class="w-full h-48 bg-gray-200 rounded-lg mb-3">
        <div id="map" class="w-full h-48 rounded-lg"></div>
    </div>

    <label class="block text-sm text-gray-600">รหัสไปรษณีย์</label>
    <input type="text" name="zipcode" id="zipcode" value="{{ $branch->zipcode }}" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="รหัสไปรษณีย์">

    <label class="block text-sm text-gray-600">จังหวัด</label>
    <input type="text" name="province" id="province" value="{{ $branch->province }}" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="จังหวัด">

    <label class="block text-sm text-gray-600">อำเภอ</label>
    <input type="text" name="amphoe" id="amphoe" value="{{ $branch->amphoe }}" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="อำเภอ">

    <label class="block text-sm text-gray-600">ตำบล</label>
    <input type="text" name="district" id="district" value="{{ $branch->district }}" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ตำบล">

    <label class="block text-sm text-gray-600">ที่อยู่</label>
    <input type="text" name="address" value="{{ $branch->bs_address }}" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ที่อยู่">

    <label class="block text-sm text-gray-600">ชื่อ</label>
    <input type="text" name="name" value="{{ $branch->bs_name }}" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ชื่อ">

    <label class="block text-sm text-gray-600">รายละเอียด</label>
    <input type="text" name="detail" value="{{ $branch->bs_detail }}" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="รายละเอียดเพิ่มเติม">

    <label class="block text-sm text-gray-600">ประเภท</label>
        <select id="poi_type" name="poi_type" class="w-full p-2 border border-gray-300 rounded-lg mb-3">
            <!-- ตัวเลือกจะเติมด้วย JS -->
        </select>

    <div class="flex justify-between">
        <a href="{{ route('branch.index') }}">
            <button class="px-4 py-2 bg-gray-500 text-white rounded-lg cursor-pointer">ยกเลิก</button>
        </a>
        <button class="px-4 py-2 bg-green-700 text-white rounded-lg cursor-pointer" id="saveButton">บันทึก</button>
    </div>
</div>
@endsection

@section('script')
<script>
document.getElementById("saveButton").addEventListener("click", async function () {
    const payload = {
        bs_id: document.querySelector('input[name="bs_id"]').value,
        name: document.querySelector('input[name="name"]').value,
        address: document.querySelector('input[name="address"]').value,
        detail: document.querySelector('input[name="detail"]').value,
    };

    try {
        const res = await fetch("{{ route('api.branch.edit') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (data.status === "success") {
            Swal.fire("สำเร็จ", "แก้ไขเรียบร้อย", "success").then(() => {
                window.location.href = "{{ route('branch.index') }}";
            });
        } else {
            Swal.fire("ผิดพลาด", data.message ?? 'เกิดข้อผิดพลาดในการแก้ไข', "error");
        }
    } catch (err) {
        console.error("❌ Error:", err);
        Swal.fire("ผิดพลาด", "ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้", "error");
    }
});
</script>

<script type="module">
    let functions = {};

    const { Map } = await google.maps.importLibrary("maps");
    const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary("marker");

    let map, MapMarker;

    functions.initMap = async function () {
        const position = {
            lat: parseFloat({{ $branch->poi_gps_lat }}),
            lng: parseFloat({{ $branch->poi_gps_lng }})
        };
        map = new Map(document.getElementById("map"), {
            zoom: 15,
            center: position,
            mapId: "DEMO_MAP_ID",
        });

        const pin = new PinElement({ glyph: "📍", glyphColor: "white", scale: 1.5 });
        MapMarker = new AdvancedMarkerElement({
            position: position,
            map: map,
            content: pin.element,
            gmpDraggable: false
        });
    };

    functions.setMapPosition = function(lat, lng) {
        const position = { lat: parseFloat(lat), lng: parseFloat(lng) };
        map.setCenter(position);
        MapMarker.position = position;
    }

    functions.initMap();
    window.functions = functions;
</script>

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
    });
</script>
<script>
document.addEventListener("DOMContentLoaded", async () => {
    await loadPoiTypes(); // โหลดประเภท
});

async function loadPoiTypes() {
    const select = document.getElementById("poi_type");
    const currentType = `{{ $branch->poit_type }}`;

    try {
        const res = await fetch(`{{ route('api.poit.query.all') }}`);
        const data = await res.json();

        (data.data || []).forEach(poit => {
            const option = document.createElement("option");
            option.value = poit.poit_type;
            option.textContent = `${poit.poit_icon ?? ''} ${poit.poit_name}`;
            if (poit.poit_type === currentType) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    } catch (err) {
        console.error("❌ ไม่สามารถโหลดประเภท POI:", err);
    }
}
</script>

<script>(g => { var h, a, k, p = "The Google Maps JavaScript API", c = "google", l = "importLibrary", q = "__ib__", m = document, b = window; b = b[c] || (b[c] = {}); var d = b.maps || (b.maps = {}), r = new Set, e = new URLSearchParams, u = () => h || (h = new Promise(async (f, n) => { await (a = m.createElement("script")); e.set("libraries", [...r] + ""); for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]); e.set("callback", c + ".maps." + q); a.src = `https://maps.${c}apis.com/maps/api/js?` + e; d[q] = f; a.onerror = () => h = n(Error(p + " could not load.")); a.nonce = m.querySelector("script[nonce]")?.nonce || ""; m.head.append(a) })); d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n)) })
    ({ key: "AIzaSyCIqpKnIfAIP48YujVFbBISkubwaQNdIME", v: "weekly" });</script>
@endsection