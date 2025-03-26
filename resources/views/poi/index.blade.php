@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')
    <!-- <form method="POST" action="{{ route('logout') }}">
        @csrf -->
        <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-3">
        <h2 class="text-lg font-bold">POI จัดการสถานที่ที่สนใจ</h2>
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            สร้าง POI
        </button>
    </div>

    <!-- Search Input -->
    <input type="text" placeholder="ค้นหาสถานที่ที่สนใจ" class="w-full p-2 border border-gray-300 rounded mb-3">

    <!-- Dropdowns -->
    <div class="mb-3">
        <label class="block text-gray-600 mb-1">ประเภท</label>
        <select class="w-full p-2 border border-gray-300 rounded">
            <option>ประเภทสถานที่</option>
        </select>
    </div>
    
    <div class="mb-3">
        <label class="block text-gray-600 mb-1">จังหวัด</label>
        <select class="w-full p-2 border border-gray-300 rounded">
            <option>จังหวัด</option>
        </select>
    </div>

    <!-- Result Count -->
    <p class="text-gray-700">ผลลัพธ์ 302 รายการ</p>
</div>

<table class="w-full mt-5 border-collapse rounded-lg overflow-hidden" id="locationTable">
    <thead class="bg-blue-500 text-white">
        <tr>
            <th class="py-3 px-4 text-left">ID</th>
            <th class="py-3 px-4 text-left">ชื่อสถานที่</th>
            <th class="py-3 px-4 text-left">ประเภท</th>
            <th class="py-3 px-4 text-left">จังหวัด</th>
            <th class="py-3 px-4 text-left">⋮</th> <!-- Kebab Bar -->
        </tr>
    </thead>
    <tbody id="tableBody" class="bg-white divide-y divide-gray-200">
        <tr>
            <td class="py-3 px-4">1</td>
            <td class="py-3 px-4 ">หาดบางแสน</td>
            <td class="py-3 px-4">ชายหาด</td>
            <td class="py-3 px-4 text-wrap">ชลบุรี</td>
            <td class="py-3 px-4 text-center">
                <button class="text-gray-500 hover:text-black text-xl">⋮</button>
            </td>
        </tr>
        <tr>
            <td class="py-3 px-4">2</td>
            <td class="py-3 px-4">มหาวิทยาลัย</td>
            <td class="py-3 px-4">สถานศึกษา</td>
            <td class="py-3 px-4">ชลบุรี</td>
            <td class="py-3 px-4 text-center">
                <button class="text-gray-500 hover:text-black text-xl">⋮</button>
            </td>
        </tr>
        <tr>
            <td class="py-3 px-4">3</td>
            <td class="py-3 px-4">สถานีตำรวจ</td>
            <td class="py-3 px-4">ราชการ</td>
            <td class="py-3 px-4">ชลบุรี</td>
            <td class="py-3 px-4 text-center">
                <button class="text-gray-500 hover:text-black text-xl">⋮</button>
            </td>
        </tr>
        <tr>
            <td class="py-3 px-4">4</td>
            <td class="py-3 px-4">แหลมทอง</td>
            <td class="py-3 px-4">ห้าง</td>
            <td class="py-3 px-4">ชลบุรี</td>
            <td class="py-3 px-4 text-center">
                <button class="text-gray-500 hover:text-black text-xl">⋮</button>
            </td>
        </tr>
        <tr>
            <td class="py-3 px-4">5</td>
            <td class="py-3 px-4">โรงพยาบาล</td>
            <td class="py-3 px-4">ราชการ</td>
            <td class="py-3 px-4">ชลบุรี</td>
            <td class="py-3 px-4 text-center">
                <button class="text-gray-500 hover:text-black text-xl">⋮</button>
            </td>
        </tr>
    </tbody>
</table>



    <!-- </form> -->
@endsection
