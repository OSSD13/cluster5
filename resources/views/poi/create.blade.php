@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')
    <style>
        /* style for error validate */
        .error-input-style {
            border: 2px solid #F02801;
        }
    </style>
    <form method="POST" action="{{ route('poi.create') }}" name="poiForm">
        @csrf
        <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">POI เพิ่มสถานที่</h2>

            <label class="block text-sm text-gray-600">Link Google (Optional)</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3 " placeholder="Link Google">

            <label class="block text-sm text-gray-600">ละติจูด</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-1" placeholder="ละติจูด"
                name="latitude" value="{{ old('latitude') }}" id="latitude">
            <span id="latitude-error" class="text-red-500 text-sm hidden">กรุณาใส่ละติจูดให้ถูกต้อง (เช่น 13.7563)</span>

            <label class="block text-sm text-gray-600">ลองจิจูด</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-1" placeholder="ลองจิจูด"
                name="longitude" value="{{ old('longitude') }}" id="longitude">
            <span id="longitude-error" class="text-red-500 text-sm hidden">กรุณาใส่ลองจิจูดให้ถูกต้อง (เช่น 100.5018)</span>



            <div class="w-full h-48 bg-gray-200 rounded-lg mb-3">
                <img src="your-map-image-url.png" alt="Map" class="w-full h-full object-cover rounded-lg">
            </div>

            <label class="block text-sm text-gray-600">รหัสไปรษณีย์</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3 " placeholder="รหัสไปรษณีย์"
                name="postal_code" value="{{ old('postal_code') }}" id="postal_code">
            <span id="postal-error" class="text-red-500 text-sm hidden">กรุณาใส่รหัสไปรษณีย์ให้ถูกต้อง (5 หลัก)</span>

            <label class="block text-sm text-gray-600">จังหวัด</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3 " placeholder="จังหวัด"
                name="province" value="{{ old('province') }}" id="province">


            <label class="block text-sm text-gray-600">อำเภอ</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3 " placeholder="อำเภอ"
                name="district" value="{{ old('district') }}" id="district">
            <span id="province-error" class="text-red-500 text-sm hidden">กรุณากรอกจังหวัดให้ถูกต้อง</span>


            <label class="block text-sm text-gray-600">ตำบล</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3" placeholder="ตำบล"
                name="sub_district" value="{{ old('sub_district') }}" id="sub_district">


            <label class="block text-sm text-gray-600">ที่อยู่</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3 " placeholder="ที่อยู่"
                name="address" value="{{ old('address') }}" id="address">



            <label class="block text-sm text-gray-600">ชื่อ</label>
            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg mb-3 " placeholder="ชื่อ" name="name"
                value="{{ old('name') }}" id="name">


            <label class="block text-sm text-gray-600">ประเภท</label>
            <select class="w-full p-2 border border-gray-300 rounded-lg mb-3 "
                name="type" id="type">
                <option class="hidden" value="" disabled {{ old('type') == '' ? 'selected' : '' }}>เลือกประเภทสถานที่
                </option>
                <option value="โรงพยาบาล" {{ old('type') == '1' ? 'selected' : '' }}>โรงพยาบาล</option>
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
                    const formData = {
                        latitude: form.latitude.value,
                        longitude: form.longitude.value,
                        postal_code: form.postal_code.value,
                        province: form.province.value,
                        district: form.district.value,
                        sub_district: form.sub_district.value,
                        address: form.address.value,
                        name: form.name.value,
                        type: form.type.value,
                    };

                };

                submitButton.disabled = true;
                submitButton.innerText = 'กำลังบันทึก...';

                try {
                    const response = await fetch(`{{ route('api.poi.create') }}`, {
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
                            window.location.href = "{{ route('poi.index') }}";
                        });
                    } else if (data.errors) {
                        displayValidationErrors(data.errors);
                    } else {
                        Swal.fire("เกิดข้อผิดพลาด", data.message || "ไม่สามารถบันทึกข้อมูลได้",
                            "error");
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
                    poi_latitude: 'latitude',
                    poi_longitude: 'longitude',
                    poi_postal_code: 'postal_code',
                    poi_province: 'province',
                    poi_district: 'district',
                    poi_sub_district: 'sub_district',
                    poi_address: 'address',
                    poi_name: 'name',
                    poi_type: 'type'
                };
                return map[field] || field;
            }

            submitButton.disabled = true;
            submitButton.classList.add('bg-gray-400', 'cursor-not-allowed'); // optional visual cue

            const requiredFields = ['latitude', 'longitude', 'postal_code', 'province', 'district', 'sub_district', 'address', 'name', 'type'];

            function validateForm() {
                const isComplete = requiredFields.every(id => {
                    const input = document.getElementById(id);
                    return input && input.value.trim() !== '';
                });

                submitButton.disabled = !isComplete;

                if (isComplete) {
                    submitButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
                    submitButton.classList.add('bg-green-700');
                } else {
                    submitButton.classList.remove('bg-green-700');
                    submitButton.classList.add('bg-gray-400', 'cursor-not-allowed');
                }
            }

            // Listen for input changes
            requiredFields.forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    input.addEventListener('input', validateForm);
                }
            });

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