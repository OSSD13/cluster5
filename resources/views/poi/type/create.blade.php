@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')
<div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-700 mb-4">POI เพิ่มสถานประเภที่</h2>

    <!-- ประเภทสถานที่ที่สนใจ -->
    <label class="block text-sm text-gray-600">ประเภทสถานที่ที่สนใจ</label>
    <input type="text" id="poiType" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ประเภทสถานที่">

    <!-- ชื่อสถานที่ที่สนใจ -->
    <label class="block text-sm text-gray-600">ชื่อสถานที่ที่สนใจ</label>
    <input type="text" id="poiName" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ชื่อสถานที่">

    <!-- Icon -->
    <label class="block text-sm text-gray-600">Icon</label>
    <div class="relative mb-3">
        <input type="text" readonly id="iconInput" class="w-full p-2 border border-gray-300 rounded-lg" placeholder="เลือกอีโมจิ">
        <button type="button" id="emojiButton" class="absolute inset-y-0 right-0 px-4 py-2 cursor-pointer bg-primary-dark hover:bg-primary-light text-white rounded-r-lg">😀</button>
    </div>
    <div id="emojiPickerContainer" class="hidden">
        <emoji-picker class="w-full light"></emoji-picker>
    </div>

    <!-- สี -->
    <label class="block text-sm text-gray-600">สี</label>
    <input type="text" id="colorInput" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="สี">

    <!-- รายละเอียดสถานที่ที่สนใจ -->
    <label class="block text-sm text-gray-600">รายละเอียดสถานที่ที่สนใจ</label>
    <input type="text" id="poiDetails" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="รายละเอียด">

    <!-- ปุ่มบันทึกและยกเลิก -->
    <div class="flex justify-between">
        <a href="{{ route('poi.type.index') }}">
            <button class="px-4 py-2 bg-gray-500 text-white rounded-lg cursor-pointer">ยกเลิก</button>
        </a>
        <button class="px-4 py-2 bg-green-700 text-white rounded-lg cursor-pointer" id="saveButton">บันทึก</button>
    </div>
</div>
@endsection


@section('script')
<script>
  document.addEventListener('DOMContentLoaded', () => {
        const emojiButton = document.getElementById('emojiButton');
        const emojiPickerContainer = document.getElementById('emojiPickerContainer');
        const iconInput = document.getElementById('iconInput');

        // แสดงหรือซ่อน Emoji Picker เมื่อคลิกที่ปุ่ม
        emojiButton.addEventListener('click', () => {
            emojiPickerContainer.classList.toggle('hidden');
        });

        // แทรกอีโมจิที่เลือกลงในช่องป้อนข้อมูล
        emojiPickerContainer.querySelector('emoji-picker').addEventListener('emoji-click', event => {
            iconInput.value = event.detail.unicode;
            emojiPickerContainer.classList.add('hidden');
        });

        // ซ่อน Emoji Picker เมื่อคลิกภายนอก
        document.addEventListener('click', (event) => {
            if (!emojiPickerContainer.contains(event.target) && event.target !== emojiButton) {
                emojiPickerContainer.classList.add('hidden');
            }
        });
    });
</script>
@endsection
