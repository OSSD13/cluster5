@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')
    <form id="poiForm">
        @csrf
        <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-2xl font-bold text-gray-700 mb-4">POIT เพิ่มประเภทสถานที่</h2>

            <!-- ประเภทสถานที่ -->
            <label class="block text-sm text-gray-600">ประเภทสถานที่ที่สนใจ</label>
            <input type="text" name="poiType" id="poiType"
                class="w-full p-2 border border-gray-300 rounded-lg"
                placeholder="ประเภทสถานที่">
            <div class="text-red-500 text-sm mb-3 px-2" id="error-poiType"></div>

            <!-- ชื่อสถานที่ -->
            <label class="block text-sm text-gray-600">ชื่อสถานที่ที่สนใจ</label>
            <input type="text" name="poiName" id="poiName"
                class="w-full p-2 border border-gray-300 rounded-lg"
                placeholder="ชื่อสถานที่">
            <div class="text-red-500 text-sm mb-3 px-2" id="error-poiName"></div>

            <!-- Icon -->
            <label class="block text-sm text-gray-600">Icon</label>
            <div class="relative mb-3">
                <input type="text" name="icon" id="iconInput" readonly
                    class="w-full p-2 border border-gray-300 rounded-lg"
                    placeholder="เลือกอีโมจิ">
                <button type="button" id="emojiButton"
                    class="absolute inset-y-0 right-0 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-r-lg">😀</button>
            </div>
            <div id="emojiPickerContainer" class="hidden">
                <emoji-picker class="w-full light"></emoji-picker>
            </div>
            <div class="text-red-500 text-sm mb-3 px-2" id="error-icon"></div>

            <!-- สี -->
            <label class="block text-sm text-gray-600">สี</label>
            <div class="relative mb-3 flex items-center">
                <input type="text" name="color" id="colorInput"
                    class="flex-grow p-2 border border-gray-300 rounded-l-lg"
                    placeholder="สี (Hex)">
                <input type="color" id="colorPicker" class="w-0 h-0" value="#ffffff">
                <button type="button" id="colorButton" class="h-full px-4 py-2 text-white rounded-r-lg"
                    style="background-color: #888">🎨</button>
            </div>
            <div class="text-red-500 text-sm mb-3 px-2" id="error-color"></div>

            <!-- รายละเอียด -->
            <label class="block text-sm text-gray-600">รายละเอียดสถานที่ที่สนใจ</label>
            <input type="text" name="poiDetails" id="poiDetails"
                class="w-full p-2 border border-gray-300 rounded-lg mb-3"
                placeholder="รายละเอียด">
            <div class="text-red-500 text-sm mb-3 px-2" id="error-poiDetails"></div>

            <!-- ปุ่มบันทึกและยกเลิก -->
            <div class="flex justify-between">
                <a href="{{ route('poi.type.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg">ยกเลิก</a>
                <button type="submit" class="px-4 py-2 bg-green-700 text-white rounded-lg">บันทึก</button>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Emoji Picker
            const emojiButton = document.getElementById('emojiButton');
            const emojiPickerContainer = document.getElementById('emojiPickerContainer');
            const iconInput = document.getElementById('iconInput');

            emojiButton.addEventListener('click', () => {
                emojiPickerContainer.classList.toggle('hidden');
            });

            emojiPickerContainer.querySelector('emoji-picker').addEventListener('emoji-click', event => {
                iconInput.value = event.detail.unicode;
                emojiPickerContainer.classList.add('hidden');
            });

            document.addEventListener('click', (event) => {
                if (!emojiPickerContainer.contains(event.target) && event.target !== emojiButton) {
                    emojiPickerContainer.classList.add('hidden');
                }
            });

            // Color Picker
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

            // API Submit & Validation
            const form = document.getElementById('poiForm');
            const submitButton = form.querySelector('button[type="submit"]');

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                // Clear previous errors
                form.querySelectorAll('[id^="error-"]').forEach(el => {
                    el.textContent = '';
                });

                const formData = {
                    poit_type: form.poiType.value,
                    poit_name: form.poiName.value,
                    poit_icon: form.iconInput.value,
                    poit_color: form.colorInput.value,
                    poit_detail: form.poiDetails.value,
                };

                submitButton.disabled = true;
                submitButton.innerText = 'กำลังบันทึก...';

                try {
                    const response = await fetch(`{{ route('api.poit.create') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(formData)
                    });

                    const data = await response.json();

                    if (response.ok && data.status === 'success') {
                        Swal.fire({
                            title: 'สำเร็จ!',
                            text: data.message || 'บันทึกข้อมูลสำเร็จ',
                            icon: 'success',
                            confirmButtonText: 'ตกลง',
                            confirmButtonColor: '#1c7d32',
                        }).then(() => {
                            window.location.href = "{{ route('poi.type.index') }}";
                        });
                    } else if (data.errors) {
                        displayValidationErrors(data.errors);
                    } else {
                        Swal.fire("เกิดข้อผิดพลาด", data.message || "ไม่สามารถบันทึกข้อมูลได้", "error");
                    }
                } catch (err) {
                    Swal.fire("ข้อผิดพลาด", "ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้", "error");
                }

                submitButton.disabled = false;
                submitButton.innerText = 'บันทึก';
            });

            function displayValidationErrors(errors) {
                for (const field in errors) {
                    const inputName = convertApiFieldToInputName(field);
                    const errorDiv = document.getElementById(`error-${inputName}`);
                    if (errorDiv) {
                        errorDiv.textContent = errors[field][0];
                    }
                }
            }

            function convertApiFieldToInputName(field) {
                const map = {
                    poit_type: 'poiType',
                    poit_name: 'poiName',
                    poit_icon: 'icon',
                    poit_color: 'color',
                    poit_detail: 'poiDetails',
                };
                return map[field] || field;
            }
        });
    </script>
@endsection
