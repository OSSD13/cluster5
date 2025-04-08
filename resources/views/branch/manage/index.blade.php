@extends('layouts.main')

@section('title', 'Manage Branch')

@section('content')
<div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto mb-5">
<h2 class="text-2xl font-bold text-gray-800">จัดการสาขา - {{ $branch->bs_name ?? 'ไม่พบข้อมูลสาขา' }}</h2>
</div>

<div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto mb-5">
    <div class="flex flex-col space-y-2 text-left">
        <label class="font-medium text-gray-800 text-sm">ชื่อสาขา</label>
        <input type="text" id="branchName" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" readonly>

        <label class="font-medium text-gray-800 text-sm">จังหวัด</label>
        <input type="text" id="branchProvince" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" readonly>
    </div>
</div>

<div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto mb-5">
    <h3 class="text-lg font-bold text-gray-800 mb-3">ยอดขาย</h3>
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
    const branchId = 1; // Replace with the actual branch ID

    async function fetchSales(page = 1) {
        const params = new URLSearchParams({
            branch_id: branchId,
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
            tableBody.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-gray-500">ไม่พบข้อมูล</td></tr>`;
            return;
        }

        sales.forEach(sale => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td class="py-3 px-4">${new Date(sale.sales_month).toLocaleDateString('th-TH', { month: 'short' })}</td>
                <td class="py-3 px-4 text-right">${sale.sales_amount.toLocaleString()}</td>
                <td class="py-3 px-4 text-right">${sale.manager_name.toLocaleString()}</td>
                <th class="py-3 px-1 w-7 text-center">&#8230;</th>
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
        fetchSales();
    });

    
</script>
@endsection