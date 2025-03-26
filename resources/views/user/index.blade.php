@extends('layouts.main')

@section('title', 'จัดการสมาชิก')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto border-4 border-purple-500">
        <!-- Header -->
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-lg font-bold">จัดการสมาชิก</h2>
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                สร้างสมาชิก
            </button>
        </div>

        <!-- Search & Filters -->
        <input type="text" placeholder="ค้นหาสมาชิก" class="w-full p-2 border border-gray-300 rounded mb-3">
        
        <div class="mb-3">
            <label class="block text-gray-600 mb-1">Sale Supervisor</label>
            <select class="w-full p-2 border border-gray-300 rounded bg-gray-100" disabled>
                <option>แสดงสมาชิก</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="block text-gray-600 mb-1">บทบาท</label>
            <select class="w-full p-2 border border-gray-300 rounded bg-gray-100" disabled>
                <option>ค้นหาด้วยตำแหน่ง</option>
            </select>
        </div>

        <!-- Result Count -->
        <p class="text-gray-700">ผลลัพธ์ 302 รายการ</p>
    </div>

@php
    $members = [
        ['id' => 1, 'name' => 'พีระพัท', 'email' => 'per@gmail.com', 'role' => 'Sale'],
        ['id' => 2, 'name' => 'กานต์', 'email' => 'knn@gmail.com', 'role' => 'CEO'],
        ['id' => 3, 'name' => 'อิทธิ์', 'email' => 'itt@gmail.com', 'role' => 'Sale'],
        ['id' => 4, 'name' => 'เจษฎา', 'email' => 'jess@gmail.com', 'role' => 'Sale'],
        ['id' => 5, 'name' => 'บุญมี', 'email' => 'bun@gmail.com', 'role' => 'Sale Sup.'],
    ];
@endphp

<table class="w-full max-w-md mx-auto mt-5 border-collapse rounded-lg overflow-hidden" id="locationTable">
    <thead class="bg-blue-500 text-white">
        <tr>
            <th class="p-2 text-left">ID</th>
            <th class="p-2 text-left">ชื่อ</th>
            <th class="p-2 text-left">อีเมล</th>
            <th class="p-2 text-left">บทบาท</th>
            <th class="p-2 text-left">⋮</th> <!-- Kebab Bar -->
        </tr>
    </thead>
    <tbody id="tableBody" class="bg-white divide-y divide-gray-200">
        @foreach ($members as $member)
        <tr>
            <td class="p-2 text-center">{{ $member['id'] }}</td>
            <td class="p-2 text-left">{{ $member['name'] }}</td>
            <td class="p-2 text-left">{{ $member['email'] }}</td>
            <td class="p-2 text-left">{{ $member['role'] }}</td>
            <td class="p-2 text-center">
                <button class="text-gray-500 hover:text-black text-xl">⋮</button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

    <!-- Pagination -->
    <div class="flex items-center justify-center mt-4 space-x-2 border-dotted border-purple-500 p-2 rounded-lg">
        <button class="px-3 py-1 bg-gray-300 text-gray-600 rounded-full">&lt;</button>
        <button class="px-3 py-1 bg-blue-600 text-white rounded-full font-bold">1</button>
        <button class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full">2</button>
        <button class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full">3</button>
        <button class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full">4</button>
        <button class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full">5</button>
        <button class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full">6</button>
        <button class="px-3 py-1 bg-gray-300 text-gray-600 rounded-full">&gt;</button>
    </div>
@endsection