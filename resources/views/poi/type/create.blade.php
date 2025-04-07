@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')
    <!-- <style>
        .error-input-style {
            border: 2px solid #F02801;
        }
    </style>
    
    <form method="POST" action="{{ route('poi.type.insert') }}">
        @csrf
        <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-2xl font-bold text-gray-700 mb-4">POIT เพิ่มประเภทสถานที่</h2>

            <!-- ประเภทสถานที่ -->
            <label class="block text-sm text-gray-600">ประเภทสถานที่ที่สนใจ</label>
            <input type="text" name="poiType" id="poiType"
                class="w-full p-2 border border-gray-300 rounded-lg mb-3 @error('poiType') error-input-style @enderror"
                value="{{ old('poiType') }}" placeholder="ประเภทสถานที่">
            @error('poiType')
                <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
            @enderror

            <!-- ชื่อสถานที่ -->
            <label class="block text-sm text-gray-600">ชื่อสถานที่ที่สนใจ</label>
            <input type="text" name="poiName" id="poiName"
                class="w-full p-2 border border-gray-300 rounded-lg mb-3 @error('poiName') error-input-style @enderror"
                value="{{ old('poiName') }}" placeholder="ชื่อสถานที่">
            @error('poiName')
                <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
            @enderror

            <!-- Icon -->
            <label class="block text-sm text-gray-600">Icon</label>
            <div class="relative mb-3">
                <input type="text" name="icon" id="iconInput" readonly
                    class="w-full p-2 border border-gray-300 rounded-lg @error('icon') error-input-style @enderror"
                    value="{{ old('icon') }}" placeholder="เลือกอีโมจิ">
                <button type="button" id="emojiButton"
                    class="absolute inset-y-0 right-0 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-r-lg">😀</button>
            </div>
            <div id="emojiPickerContainer" class="hidden">
                <emoji-picker class="w-full light"></emoji-picker>
            </div>
            @error('icon')
                <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
            @enderror

            <!-- สี -->
<!-- สี -->
<label class="block text-sm text-gray-600">สี</label>
<div class="relative mb-3 flex items-center">
    <!-- input สี (hex) -->
    <input type="text" id="colorInput"
    class="w-full p-2 border border-gray-300 rounded-lg @error('color') error-input-style @enderror"
    placeholder="สี" name="color" value="{{ old('color') }}" >

    <!-- ปุ่ม color picker -->
    <button type="button" id="colorButton"
        class="absolute inset-y-0 right-0 px-4 py-2 cursor-pointer
         rounded-r-lg"
        name="color" style="background-color: {{ old('color', '#9e9e9e') }};">🎨</button>
</div>

<!-- ซ่อนตัวเลือกสีไว้ใต้ form -->
<input type="color" id="colorPicker" class="hidden" value="{{ old('color', '#9e9e9e') }}">

@error('color')
    <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
@enderror


            <!-- รายละเอียดสถานที่ที่สนใจ -->
            <label class="block text-sm text-gray-600">รายละเอียดสถานที่ที่สนใจ</label>
            <input type="text" name="poiDetails" id="poiDetails"
                class="w-full p-2 border border-gray-300 rounded-lg mb-3 @error('poiDetails') error-input-style @enderror"
                value="{{ old('poiDetails') }}" placeholder="รายละเอียด">
            @error('poiDetails')
                <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
            @enderror

            <!-- ปุ่มบันทึกและยกเลิก -->
            <div class="flex justify-between">
                <a href="{{ route('poi.type.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg">ยกเลิก</a>
                <button type="submit" class="px-4 py-2 bg-green-700 text-white rounded-lg">บันทึก</button>
            </div>
        </div>
    </form>

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
                        window.location.href = "{{ route('poi.type.index') }}";
                    }
                });
            });
        </script>
    @endif
@endsection



<!-- Color picker -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const colorInput = document.getElementById("colorInput");
        const colorButton = document.getElementById("colorButton");
        const colorPicker = document.getElementById("colorPicker");

        colorButton.style.backgroundColor = colorInput.value || "#9e9e9e";

        // เมื่อเลือกสีจาก Color Picker
        colorPicker.addEventListener("input", function () {
            colorInput.value = colorPicker.value;
            colorButton.style.backgroundColor = colorPicker.value;
        });

        // Color Picker
        document.addEventListener("DOMContentLoaded", function () {
            const colorInput = document.getElementById("colorInput");
            const colorButton = document.getElementById("colorButton");
            const colorPicker = document.getElementById("colorPicker");

            colorPicker.addEventListener("input", function () {
                colorInput.value = colorPicker.value;
                colorButton.style.backgroundColor = colorPicker.value;
            });

            colorInput.addEventListener("input", function () {
                colorButton.style.backgroundColor = colorInput.value;
            });

            colorButton.addEventListener("click", function () {
                colorPicker.click();
            });
        });
    </script>
@endsection
