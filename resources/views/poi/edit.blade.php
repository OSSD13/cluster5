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
    <input id="longitude" name="longitude" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ลองจิจูด">

    <div class="w-full h-48 bg-gray-200 rounded-lg mb-3">
        <img src="your-map-image-url.png" alt="Map" class="w-full h-full object-cover rounded-lg">
    </div>

    <label class="block text-sm text-gray-600">รหัสไปรษณีย์</label>
    <input id="postal_code" name="postal_code" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="รหัสไปรษณีย์">

    <label class="block text-sm text-gray-600">จังหวัด</label>
    <input id="province" name="province" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="จังหวัด">

    <label class="block text-sm text-gray-600">อำเภอ</label>
    <input id="district" name="district" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="อำเภอ">

    <label class="block text-sm text-gray-600">ตำบล</label>
    <input id="sub_district" name="sub_district" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ตำบล">

    <label class="block text-sm text-gray-600">ที่อยู่</label>
    <input id="address" name="address" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ที่อยู่">

    <label class="block text-sm text-gray-600">ชื่อ</label>
    <input id="name" name="name" type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ชื่อ">

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
document.getElementById("saveButton").addEventListener("click", function() {
    // แสดง SweetAlert
    Swal.fire({
        title: "เเก้ไขสำเร็จ",
        icon: "success",
        showConfirmButton: true,
        confirmButtonColor: "#1c7d32",
        confirmButtonText: "ยืนยัน"
    }).then((result) => {
        if (result.isConfirmed) {
            // เปลี่ยนหน้าไปที่ poi.index
            window.location.href = "{{ route('poi.index') }}";
        }
    });
});
</script>
@endsection