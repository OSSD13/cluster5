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
            function isValidCoordinate(value, type = "lat") {
                const floatVal = parseFloat(value);
                if (isNaN(floatVal)) return false;
                return (type === "lat") ? floatVal >= -90 && floatVal <= 90 : floatVal >= -180 && floatVal <= 180;
            }

            function isValidPostalCode(value) {
                return /^\d{5}$/.test(value);
            }

            function isValidText(value) {
                // ตรวจสอบว่ามีเฉพาะตัวอักษรไทย อังกฤษ และเว้นวรรค (ถ้าอยากละเอียดกว่านี้ก็แจ้งได้)
                return /^[\u0E00-\u0E7Fa-zA-Z\s]+$/.test(value);
            }

            function checkForm() {
                var form = document.forms['poiForm'];

                var latitude = form.elements['latitude'].value.trim();
                var longitude = form.elements['longitude'].value.trim();
                var postal_code = form.elements['postal_code'].value.trim();
                var province = form.elements['province'].value.trim();
                var district = form.elements['district'].value.trim();
                var sub_district = form.elements['sub_district'].value.trim();
                var address = form.elements['address'].value.trim();
                var name = form.elements['name'].value.trim();
                var type = form.elements['type'].value.trim();

                // ตรวจค่าที่ต้องมีรูปแบบ
                var isLatValid = isValidCoordinate(latitude, "lat");
                var isLonValid = isValidCoordinate(longitude, "lon");
                var isPostalValid = isValidPostalCode(postal_code);
                var isProvinceValid = isValidText(province);
                var isDistrictValid = isValidText(district);
                var isSubDistrictValid = isValidText(sub_district);
                var isAddressValid = address !== "";
                var isNameValid = isValidText(name);

                // Error message + red border toggle
                toggleError("latitude", isLatValid);
                toggleError("longitude", isLonValid);
                toggleError("postal_code", isPostalValid, "postal-error");
                toggleError("province", isProvinceValid, "province-error");
                toggleError("district", isDistrictValid, "district-error");
                toggleError("sub_district", isSubDistrictValid, "sub-district-error");
                toggleError("address", isAddressValid, "address-error");
                toggleError("name", isNameValid, "name-error");

                // ตรวจสอบทั้งหมดก่อนเปิดปุ่ม
                if (
                    isLatValid &&
                    isLonValid &&
                    isPostalValid &&
                    isProvinceValid &&
                    isDistrictValid &&
                    isSubDistrictValid &&
                    isAddressValid &&
                    isNameValid &&
                    type !== ""
                ) {
                    document.getElementById("saveButton").disabled = false;
                    document.getElementById("saveButton").classList.remove("cursor-not-allowed", "bg-gray-500");
                    document.getElementById("saveButton").classList.add("bg-green-700", "cursor-pointer");
                } else {
                    document.getElementById("saveButton").disabled = true;
                    document.getElementById("saveButton").classList.remove("bg-green-700", "cursor-pointer");
                    document.getElementById("saveButton").classList.add("cursor-not-allowed", "bg-gray-500");
                }
            }

            function toggleError(fieldId, isValid, errorId = null) {
                const input = document.getElementById(fieldId);
                const errorSpan = document.getElementById(errorId || `${fieldId}-error`);

                if (!isValid) {
                    input.classList.add("error-input-style");
                    errorSpan.classList.remove("hidden");
                } else {
                    input.classList.remove("error-input-style");
                    errorSpan.classList.add("hidden");
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