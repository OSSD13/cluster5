@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')
    <style>
        /* style for error validate */
        .error-input-style {
            border: 2px solid #F02801;
        }
    </style>
    <form method="POST" action="{{ route('poi.insert') }}" name="poiForm">


        @csrf
        <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">POI เพิ่มสถานที่</h2>

            <label class="block text-sm text-gray-600">Link Google (Optional)</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3 " placeholder="Link Google">

            <label class="block text-sm text-gray-600">ละติจูด</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-1" placeholder="ละติจูด"
                name="latitude" value="{{ old('latitude') }}" onkeyup="checkForm()" id="latitude">
            <span id="latitude-error" class="text-red-500 text-sm hidden">กรุณาใส่ละติจูดให้ถูกต้อง (เช่น 13.7563)</span>

            <label class="block text-sm text-gray-600">ลองจิจูด</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-1" placeholder="ลองจิจูด"
                name="longitude" value="{{ old('longitude') }}" onkeyup="checkForm()" id="longitude">
            <span id="longitude-error" class="text-red-500 text-sm hidden">กรุณาใส่ลองจิจูดให้ถูกต้อง (เช่น 100.5018)</span>



            <div class="w-full h-48 bg-gray-200 rounded-lg mb-3">
                <img src="your-map-image-url.png" alt="Map" class="w-full h-full object-cover rounded-lg">
            </div>

            <label class="block text-sm text-gray-600">รหัสไปรษณีย์</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3 " placeholder="รหัสไปรษณีย์"
                name="postal_code" value="{{ old('postal_code') }}" onkeyup="checkForm()">
            <span id="postal-error" class="text-red-500 text-sm hidden">กรุณาใส่รหัสไปรษณีย์ให้ถูกต้อง (5 หลัก)</span>

            <label class="block text-sm text-gray-600">จังหวัด</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3 " placeholder="จังหวัด"
                name="province" value="{{ old('province') }}" onkeyup="checkForm()">


            <label class="block text-sm text-gray-600">อำเภอ</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3 " placeholder="อำเภอ"
                name="district" value="{{ old('district') }}" onkeyup="checkForm()">
            <span id="province-error" class="text-red-500 text-sm hidden">กรุณากรอกจังหวัดให้ถูกต้อง</span>


            <label class="block text-sm text-gray-600">ตำบล</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ตำบล"
                name="sub_district" value="{{ old('sub_district') }}" onkeyup="checkForm()">


            <label class="block text-sm text-gray-600">ที่อยู่</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3 " placeholder="ที่อยู่"
                name="address" value="{{ old('address') }}" onkeyup="checkForm()">



            <label class="block text-sm text-gray-600">ชื่อ</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3 " placeholder="ชื่อ" name="name"
                value="{{ old('name') }}" onkeyup="checkForm()">


            <label class="block text-sm text-gray-600">ประเภท</label>
            <select class="w-full p-2 border border-gray-300 rounded-lg mb-3 @error('type') error-input-style @enderror"
                name="type" onchange="checkForm()">
                <option class="hidden" value="" disabled {{ old('type') == '' ? 'selected' : '' }}>เลือกประเภทสถานที่
                </option>
                <option value="1" {{ old('type') == '1' ? 'selected' : '' }}>1</option>
            </select>





            <div class="flex justify-between">
                <a href="{{ route('poi.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg text-center">
                    ยกเลิก
                </a>

                <button class="px-4 py-2 bg-gray-500 text-white rounded-lg cursor-not-allowed" disabled="disabled"
                    id="saveButton">บันทึก</button>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                checkForm(); // เรียกฟังก์ชันตรวจสอบเมื่อหน้าโหลด
            });

            function checkForm() {
                // ดึงฟอร์มทั้งหมด
                var form = document.forms['poiForm'];

                // ดึงค่าของฟิลด์ทั้งหมด
                var latitude = form.elements['latitude'];
                var longitude = form.elements['longitude'];
                var postal_code = form.elements['postal_code'];
                var province = form.elements['province'];
                var district = form.elements['district'];
                var sub_district = form.elements['sub_district'];
                var address = form.elements['address'];
                var name = form.elements['name'];
                var type = form.elements['type'];

                // ดึงข้อความ caution
                var cautionMessage = document.querySelector("label[name='caution']");

                // ฟังก์ชันตรวจสอบและเพิ่ม/ลบคลาส error
                function validateField(field) {
                    if (field.value.trim() === "") {
                        field.classList.add("error-input-style");
                    } else {
                        field.classList.remove("error-input-style");
                    }
                }

                // ตรวจสอบฟิลด์ทั้งหมด
                validateField(latitude);
                validateField(longitude);
                validateField(postal_code);
                validateField(province);
                validateField(district);
                validateField(sub_district);
                validateField(address);
                validateField(name);
                validateField(type);

                // ตรวจสอบว่าฟิลด์ทั้งหมดไม่ว่าง
                if (
                    latitude.value.trim() !== "" &&
                    longitude.value.trim() !== "" &&
                    postal_code.value.trim() !== "" &&
                    province.value.trim() !== "" &&
                    district.value.trim() !== "" &&
                    sub_district.value.trim() !== "" &&
                    address.value.trim() !== "" &&
                    name.value.trim() !== "" &&
                    type.value.trim() !== ""
                ) {
                    // เปิดใช้งานปุ่ม
                    document.getElementById("saveButton").disabled = false;
                    document.getElementById("saveButton").classList.remove("cursor-not-allowed", "bg-gray-500");
                    document.getElementById("saveButton").classList.add("bg-green-700", "cursor-pointer");

                    // ซ่อนข้อความ caution
                    cautionMessage.classList.add("hidden");
                } else {
                    // ปิดใช้งานปุ่ม
                    document.getElementById("saveButton").disabled = true;
                    document.getElementById("saveButton").classList.remove("bg-green-700", "cursor-pointer");
                    document.getElementById("saveButton").classList.add("cursor-not-allowed", "bg-gray-500");

                    // แสดงข้อความ caution
                    cautionMessage.classList.remove("hidden");
                }
            }
        </script>



        @if (session('success'))
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    Swal.fire({
                        title: "{{ session('success') }}",
                        icon: "success",
                        showConfirmButton: true,
                        confirmButtonColor: "#1c7d32",
                        confirmButtonText: "ยืนยัน"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('poi.index') }}";
                        }
                    });
                });
            </script>
        @endif

@endsection