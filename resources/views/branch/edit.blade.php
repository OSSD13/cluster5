@extends('layouts.main')

@section('title', 'Branch')

@section('content')
<div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto mb-5">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">แก้ไขสาขา</h2>
    </div>
</div>

<div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">POI เพิ่มสถานที่</h2>

    <!-- Hidden ID -->
    <input type="hidden" name="bs_id" value="{{ $branch->bs_id }}">

    <label class="block text-sm text-gray-600">Link Google (Optional)</label>
    <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="Link Google">

    <label class="block text-sm text-gray-600">ละติจูด</label>
    <input type="text" name="poi_gps_lat" value="{{ $branch->poi_gps_lat }}" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ละติจูด">

    <label class="block text-sm text-gray-600">ลองจิจูด</label>
    <input type="text" name="poi_gps_lng" value="{{ $branch->poi_gps_lng }}" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ลองจิจูด">


    <label class="block text-sm text-gray-600">รหัสไปรษณีย์</label>
    <input type="text" name="zipcode" value="{{ $branch->zipcode }}" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="รหัสไปรษณีย์">

    <label class="block text-sm text-gray-600">จังหวัด</label>
    <input type="text" name="province" value="{{ $branch->province }}" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="จังหวัด">

    <label class="block text-sm text-gray-600">อำเภอ</label>
    <input type="text" name="amphoe" value="{{ $branch->amphoe }}" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="อำเภอ">

    <label class="block text-sm text-gray-600">ตำบล</label>
    <input type="text" name="district" value="{{ $branch->district }}" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ตำบล">

    <label class="block text-sm text-gray-600">ที่อยู่</label>
    <input type="text" name="address" value="{{ $branch->bs_address }}" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ที่อยู่">

    <label class="block text-sm text-gray-600">ชื่อ</label>
    <input type="text" name="name" value="{{ $branch->bs_name }}" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ชื่อ">

    <label class="block text-sm text-gray-600">รายละเอียด</label>
    <input type="text" name="detail" value="{{ $branch->bs_detail }}" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="รายละเอียดเพิ่มเติม">

    <label class="block text-sm text-gray-600">ประเภท</label>
    <select class="w-full p-2 border border-gray-300 rounded-lg mb-3">
        <option selected>{{ $branch->poit_name }}</option>
    </select>

    <div class="flex justify-between">
        <a href="{{ route('branch.index') }}">
            <button class="px-4 py-2 bg-gray-500 text-white rounded-lg cursor-pointer">ยกเลิก</button>
        </a>
        <button class="px-4 py-2 bg-green-700 text-white rounded-lg cursor-pointer" id="saveButton">บันทึก</button>
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
@endsection
