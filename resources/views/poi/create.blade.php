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
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ละติจูด"
                name="latitude" value="{{ old('latitude') }}" onkeyup="checkForm()">


            <label class="block text-sm text-gray-600">ลองจิจูด</label>
            <input type="text"
                class="w-full p-2 border border-gray-300 rounded-lg mb-3 @error('longitude') error-input-style @enderror"
                placeholder="ลองจิจูด" name="longitude" value="{{ old('longitude') }}">
            @error('longitude')
                <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
            @enderror

            <div class="w-full h-48 bg-gray-200 rounded-lg mb-3">
                <img src="your-map-image-url.png" alt="Map" class="w-full h-full object-cover rounded-lg">
            </div>

            <label class="block text-sm text-gray-600">รหัสไปรษณีย์</label>
            <input type="text"
                class="w-full p-2 border border-gray-300 rounded-lg mb-3 @error('postal_code') error-input-style @enderror"
                placeholder="รหัสไปรษณีย์" name="postal_code" value="{{ old('postal_code') }}">
            @error('postal_code')
                <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
            @enderror

            <label class="block text-sm text-gray-600">จังหวัด</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3 @error('province') error-input-style
            @enderror" placeholder="จังหวัด" name="province" value="{{ old('province') }}">
            @error('province')
                <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
            @enderror

            <label class="block text-sm text-gray-600">อำเภอ</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3 @error('district') error-input-style
            @enderror" placeholder="อำเภอ" name="district" value="{{ old('district') }}">
            @error('district')
                <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
            @enderror

            <label class="block text-sm text-gray-600">ตำบล</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3 @error('sub_district') error-input-style
            @enderror" placeholder="ตำบล" name="sub_district" value="{{ old('sub_district') }}">
            @error('sub_district')
                <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
            @enderror

            <label class="block text-sm text-gray-600">ที่อยู่</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3 @error('address') error-input-style
            @enderror" placeholder="ที่อยู่" name="address" value="{{ old('address') }}">
            @error('address')
                <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
            @enderror


            <label class="block text-sm text-gray-600">ชื่อ</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3 @error('name') error-input-style
            @enderror" placeholder="ชื่อ" name="name" value="{{ old('name') }}">
            @error('name')
                <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
            @enderror

            <label class="block text-sm text-gray-600">ประเภท</label>
            <select class="w-full p-2 border border-gray-300 rounded-lg mb-3 @error('type') error-input-style @enderror"
                name="type">
                <option class="hidden " value="" disabled {{ old('type') == '' ? 'selected' : '' }}>เลือกประเภทสถานที่
                </option>
                <option value="1" {{ old('type') == '1' ? 'selected' : '' }}>1</option>
            </select>
            @error('type')
                <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
            @enderror


            <div class="flex justify-between">
                <a href="{{ route('poi.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg text-center">
                    ยกเลิก
                </a>

                <button class="px-4 py-2 bg-gray-500 text-white rounded-lg cursor-not-allowed" disabled="disabled"
                    id="saveButton">บันทึก</button>
            </div>
        </div>

        <script>
            function checkForm() {
                // ดึงค่าของฟิลด์ latitude
                var latitude = document.forms['poiForm'].elements['latitude'].value;

                // ตรวจสอบว่าฟิลด์ latitude ไม่ว่างเปล่า
                if (latitude.trim() !== "") {
                    // เปิดใช้งานปุ่ม
                    document.getElementById("saveButton").disabled = false;
                    document.getElementById("saveButton").classList.remove("cursor-not-allowed", "bg-gray-500");
                    document.getElementById("saveButton").classList.add("bg-green-700", "cursor-pointer");
                } else {
                    // ปิดใช้งานปุ่ม
                    document.getElementById("saveButton").disabled = true;
                    document.getElementById("saveButton").classList.remove("bg-green-700", "cursor-pointer");
                    document.getElementById("saveButton").classList.add("cursor-not-allowed", "bg-gray-500");
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