@extends('layouts.main')

@section('title', 'Manage Branch')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto mb-5">
        <h2 class="text-2xl font-bold text-gray-800">จัดการสาขา - {{ $branch->bs_name ?? 'ไม่พบข้อมูลสาขา' }}</h2>
    </div>

    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto mb-5">
        <div class="flex flex-col space-y-2 text-left">
            <label class="font-medium text-gray-700 text-sm">ชื่อสถานที่</label>
            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm"
                value="{{ $branch->bs_name }}" readonly>
            {{-- <pre>{{ json_encode($branch, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre> --}}

            <label class="font-medium text-gray-700 text-sm">ประเภท</label>
            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm"
                value="{{ $branch->poit_name }}" readonly>

            <label class="font-medium text-gray-700 text-sm">จังหวัด</label>
            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm"
                value="{{ $branch->province }}" readonly>

            <label class="font-medium text-gray-700 text-sm">วันที่เพิ่ม</label>
            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm"
                value="{{ \Carbon\Carbon::parse($branch->created_at)->format('d M Y') }}" readonly>

            <label class="font-medium text-gray-700 text-sm">เพิ่มโดย</label>
            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm"
                value="{{ $branch->bs_manager_email }}" readonly>
        </div>
    </div>


    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto mb-5">
        {{-- Dropdown: เดือน --}}
        <div>
            <label class="block text-sm font-medium text-gray-800 mb-1">เดือน</label>
            <select
                class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option>มกราคม - 2568</option>
                <option selected>กุมภาพันธ์ - 2568</option>
                <option>มีนาคม - 2568</option>
            </select>
        </div>
        {{-- Input: จำนวนกล่อง --}}
        <div>
            <label class="block text-sm font-medium text-gray-800 mb-1">จำนวนกล่อง</label>
            <input type="number"
                class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="กรอกจำนวนกล่อง">
        </div>
        {{-- Input: ยอดเงิน --}}
        <div>
            <label class="block text-sm font-medium text-gray-800 mb-1">ยอดเงิน</label>
            <input type="number"
                class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="กรอกยอดเงิน">
        </div>
        {{-- ปุ่มเพิ่มรายการ --}}
        <div>
            <button
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm py-2 rounded-md shadow-md transition">
                เพิ่มรายการ
            </button>
        </div>
        {{-- ข้อความผลลัพธ์ --}}
        <div class="text-sm text-gray-700">
            ผลลัพธ์ 302 รายการ
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto mb-5">
        <table class="w-full border-collapse rounded-lg overflow-hidden">
            <thead class="bg-blue-500 text-white">
                <tr>
                    <th class="py-3 px-4 text-left">เดือน</th>
                    <th class="py-3 px-4 text-right">ยอดเงิน</th>
                    <th class="py-3 px-4 text-right">เพิ่มโดย</th>
                    <th class="py-3 px-4 text-right"></th>
                </tr>
            </thead>
            <tbody id="salesTableBody" class="bg-white divide-y divide-gray-200">
                <!-- Sales data will be dynamically added here -->
            </tbody>
        </table>
    </div>

    <div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>
@endsection

@section('script')
    <script>
        let sales = [];
        let currentPage = 1;
        const rowsPerPage = 10;
        const branchId = {{ $branch->bs_id ?? 'null' }}; // <- bs_id คือคีย์ในฐานข้อมูล

        async function fetchSales(page = 1) {
            const params = new URLSearchParams({
                bs_id: branchId,
                page: page,
                limit: rowsPerPage
            });

            try {
                const response = await fetch(`{{ route('api.sales.query') }}?${params.toString()}`);
                const result = await response.json();
                sales = result.data || [];
                currentPage = result.page || 1;
                renderSalesTable();
                renderPagination(result.total || 0);
            } catch (error) {
                console.error("Error fetching sales data:", error);
            }
        }

        function renderSalesTable() {
            const tableBody = document.getElementById("salesTableBody");
            tableBody.innerHTML = "";

            if (sales.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-gray-500">ไม่พบข้อมูล</td></tr>`;
                return;
            }

            sales.forEach(sale => {
                const row = document.createElement("tr");
                const monthLabel = new Date(sale.sales_month).toLocaleDateString('th-TH', {
                    year: 'numeric',
                    month: 'short'
                });
                row.innerHTML = `
                <td class="py-3 px-4">${monthLabel}</td>
                <td class="py-3 px-4 text-right">${parseFloat(sale.sales_amount).toLocaleString()}</td>
                <td class="py-3 px-4 text-right">${sale.manager_name}</td>
                <td class="py-3 px-1 w-7 text-center">&#8230;</td>
            `;
                tableBody.appendChild(row);
            });
        }

        function renderPagination(totalItems) {
            const pagination = document.getElementById("pagination");
            pagination.innerHTML = "";

            const totalPages = Math.ceil(totalItems / rowsPerPage);
            if (totalPages <= 1) return;

            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement("button");
                btn.textContent = i;
                btn.className = `px-3 py-2 mx-1 rounded-lg text-sm font-semibold ${
                i === currentPage ? "bg-blue-600 text-white" : "bg-white border border-gray-300 text-black"
            }`;
                btn.onclick = () => fetchSales(i);
                pagination.appendChild(btn);
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            if (branchId !== null) {
                fetchSales();
            } else {
                console.warn("ไม่พบ branchId");
            }
        });

        function formatThaiDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('th-TH', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        }
    </script>
@endsection
