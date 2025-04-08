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
    <h2 class="text-2xl font-bold text-gray-800 mb-4">POI เพิ่มสถานที่</h2>

    <label class="block text-sm text-gray-600">Link Google (Optional)</label>
    <input type="text" id="googleLink" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="Link Google">

    <label class="block text-sm text-gray-600">ละติจูด</label>
    <input type="text" id="latitude" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ละติจูด">

    <label class="block text-sm text-gray-600">ลองจิจูด</label>
    <input type="text" id="longitude" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ลองจิจูด">

    <div class="w-full h-48 bg-gray-200 rounded-lg mb-3">
        <img src="your-map-image-url.png" alt="Map" class="w-full h-full object-cover rounded-lg">
    </div>

    <label class="block text-sm text-gray-600">รหัสไปรษณีย์</label>
    <input type="text" id="zipcode" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="รหัสไปรษณีย์">

    <label class="block text-sm text-gray-600">จังหวัด</label>
    <input type="text" id="province" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="จังหวัด">

    <label class="block text-sm text-gray-600">อำเภอ</label>
    <input type="text" id="amphoe" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="อำเภอ">

    <label class="block text-sm text-gray-600">ตำบล</label>
    <input type="text" id="district" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ตำบล">

    <label class="block text-sm text-gray-600">ที่อยู่</label>
    <input type="text" id="address" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ที่อยู่">

    <label class="block text-sm text-gray-600">ชื่อ</label>
    <input type="text" id="name" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ชื่อ">

    <label class="block text-sm text-gray-600">ประเภท</label>
    <select id="type" class="w-full p-2 border border-gray-300 rounded-lg mb-3">
        <option>เลือกประเภทสถานที่</option>
    </select>

    <div class="flex justify-between">
        <a href="{{ route('branch.index') }}">
                <button class="px-4 py-2 bg-gray-500 text-white rounded-lg cursor-pointer">ยกเลิก</button>
        </a>
        <button class="px-4 py-2 bg-green-700 text-white rounded-lg cursor-pointer" id="saveButton" disabled>บันทึก</button>
    </div>
</div>
@endsection

@section('script')
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
        document.getElementById("name").value,
    ];
    const isFormValid = fields.every(field => field.trim() !== "");
    
    const saveButton = document.getElementById("saveButton");

    // หากข้อมูลครบให้เปิดปุ่มและทำให้ปุ่มเป็นสีเขียว หากข้อมูลไม่ครบให้ปิดและปุ่มเป็นสีเทา
    saveButton.disabled = !isFormValid;

    if (isFormValid) {
        saveButton.style.backgroundColor = '#38A169'; // สีเขียว
    } else {
        saveButton.style.backgroundColor = '#6B7280'; // สีเทา
    }
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
@endsection