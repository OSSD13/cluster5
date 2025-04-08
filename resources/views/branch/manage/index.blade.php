@extends('layouts.main')

@section('title', 'Manage Branch')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto mb-5">
        <h2 class="text-2xl font-bold text-gray-800">จัดการสาขา - {{ $branch->bs_name ?? 'ไม่พบข้อมูลสาขา' }}</h2>
    </div>

<div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto mb-5">
    <div>
        <label class="block text-sm font-medium text-gray-800 mb-1">เดือน</label>
        <select class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option>มกราคม - 2568</option>
            <option selected>กุมภาพันธ์ - 2568</option>
            <option>มีนาคม - 2568</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-800 mb-1">จำนวนกล่อง</label>
        <input type="number" class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="กรอกจำนวนกล่อง">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-800 mb-1">ยอดเงิน</label>
        <input type="number" class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="กรอกยอดเงิน">
    </div>
    <div>
        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm py-2 rounded-md shadow-md transition">
            เพิ่มรายการ
        </button>
    </div>
    <div class="text-sm text-gray-700">
        ผลลัพธ์ 302 รายการ
    </div>
</div>

            <label class="font-medium text-gray-700 text-sm">ประเภท</label>
            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm"
                value="{{ $branch->poit_name }}" readonly>

<div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>

<div id="contextMenu" class="hidden absolute bg-white shadow-lg rounded-lg w-32 z-50 p-2 space-y-2"></div>
@endsection

@section('script')
<script>
    let sales = [];
    let currentPage = 1;
    const rowsPerPage = 10;
    const branchId = {{ $branch->bs_id ?? 'null' }};
    let activeMenuId = null;

    async function fetchSales(page = 1) {
        const params = new URLSearchParams({ branch_id: branchId, page: page, limit: rowsPerPage });
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
            const monthLabel = formatThaiDate(sale.sales_month);
            row.innerHTML = `
                <td class="py-3 px-4">${monthLabel}</td>
                <td class="py-3 px-4 text-right">${parseFloat(sale.sales_amount).toLocaleString()}</td>
                <td class="py-3 px-4 text-right">${sale.manager_name}</td>
                <td class="py-3 px-1 w-7 text-center relative">
                    <button onclick="toggleMenu(event, ${sale.sales_id})">&#8230;</button>
                </td>
            `;
                tableBody.appendChild(row);
            });
        }

    function toggleMenu(event, id) {
        event.stopPropagation();
        const menu = document.getElementById("contextMenu");
        const button = event.currentTarget;
        const parentCell = button.closest('td');
        if (activeMenuId === id && !menu.classList.contains("hidden")) {
            menu.classList.add("hidden");
            activeMenuId = null;
            return;
        }
        activeMenuId = id;
        menu.innerHTML = `
            <button class="block w-full px-4 py-2 text-white border border-gray-400 bg-blue-600 rounded-lg hover:bg-blue-700 whitespace-nowrap" style="background-color: #3062B8" onclick="viewSale(${id})">ดูรายละเอียด</button>
            <button class="block w-full px-4 py-2 text-white border border-gray-400 bg-blue-600 rounded-lg hover:bg-blue-700" style="background-color: #3062B8" onclick="editSale(${id})">แก้ไข</button>
            <button class="block w-full px-4 py-2 text-white border border-gray-400 bg-red-600 rounded-lg hover:bg-red-700" style="background-color: #CF3434" style="background-color: #CF3434" onclick="deleteSale(${id})">ลบ</button>
        `;
        menu.classList.remove("hidden");
        const top = parentCell.offsetTop + parentCell.offsetHeight;
        const left = parentCell.offsetLeft + parentCell.offsetWidth - menu.offsetWidth;
        menu.style.top = `${top}px`;
        menu.style.left = `${left}px`;
    }

    document.addEventListener("click", function () {
        const menu = document.getElementById("contextMenu");
        if (!menu.classList.contains("hidden")) {
            menu.classList.add("hidden");
            activeMenuId = null;
        }
    });

    function renderPagination(totalItems) {
        const pagination = document.getElementById("pagination");
        pagination.innerHTML = "";
        const totalPages = Math.ceil(totalItems / rowsPerPage);
        if (totalPages <= 1) return;
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement("button");
            btn.textContent = i;
            btn.className = `px-3 py-2 mx-1 rounded-lg text-sm font-semibold ${i === currentPage ? "bg-blue-600 text-white" : "bg-white border border-gray-300 text-black"}`;
            btn.onclick = () => fetchSales(i);
            pagination.appendChild(btn);
        }

    function formatThaiDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('th-TH', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    function viewSale(id) {
    const sale = sales.find(item => item.sales_id === id);
    if (!sale) {
        Swal.fire("ไม่พบข้อมูล", "รายการที่เลือกไม่มีอยู่ในระบบ", "error");
        return;
    }

    Swal.fire({
        html: `
            <div class="flex flex-col text-3xl mb-6 mt-4">
                <b class="text-gray-800">รายละเอียดยอดขาย</b>
            </div>

            <div class="flex flex-col space-y-2 text-left">
                <div class="w-full">
                    <label class="font-medium text-gray-800 text-sm">เดือน</label>
                    <input type="text" class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow-sm" 
                        value="${formatThaiDate(sale.sales_month)}" readonly>
                </div>

                <div class="w-full">
                    <label class="font-medium text-gray-800 text-sm">จำนวนกล่อง</label>
                    <input type="text" class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow-sm" 
                        value="${sale.sales_package_amount ?? '-'}" readonly>
                </div>

                <div class="w-full">
                    <label class="font-medium text-gray-800 text-sm">ยอดเงิน (บาท)</label>
                    <input type="text" class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow-sm" 
                        value="${parseFloat(sale.sales_amount).toLocaleString()}" readonly>
                </div>

                <div class="w-full">
                    <label class="font-medium text-gray-800 text-sm">วันที่เพิ่ม</label>
                    <input type="text" class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow-sm" 
                        value="${formatThaiDate(sale.sales_month)}" readonly>
                </div>

                <div class="w-full">
                    <label class="font-medium text-gray-800 text-sm">เพิ่มโดย</label>
                    <input type="text" class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow-sm" 
                        value="${sale.manager_name ?? '-'}" readonly>
                </div>
            </div>
        `,
        confirmButtonText: "ปิด",
        confirmButtonColor: "#2D8C42",
        customClass: {
            popup: 'custom-popup'
        }
    });
}

    function editSale(id) {
        alert('แก้ไข #' + id);
    }
    
    function deleteSale(id) {
    Swal.fire({
        title: "ลบรายการยอดขาย",
        text: "คุณต้องการลบรายการนี้ใช่หรือไม่?",
        icon: "warning",
        iconColor: "#d33",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3062B8",
        confirmButtonText: "ยืนยัน",
        cancelButtonText: "ยกเลิก"
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ route('api.sales.delete') }}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ sales_id: id })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    sales = sales.filter(s => s.sales_id !== id);
                    renderSalesTable();
                    Swal.fire("ลบแล้ว!", "รายการถูกลบเรียบร้อย", "success");
                } else {
                    Swal.fire("เกิดข้อผิดพลาด", data.message || "ไม่สามารถลบข้อมูลได้", "error");
                }
            });
        }
    });
}


    document.addEventListener("DOMContentLoaded", () => {
        if (branchId !== null) fetchSales();
    });
</script>
@endsection
