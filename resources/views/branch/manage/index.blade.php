@extends('layouts.main')

@section('title', 'Manage Branch')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto mb-5">
        <h2 class="text-2xl font-bold text-gray-800">จัดการสาขา - {{ $branch->bs_name ?? 'ไม่พบข้อมูลสาขา' }}</h2>
    </div>

    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto mb-5">
        <div class="flex flex-col space-y-2 text-left">
            <label class="font-medium text-gray-700 text-sm">ชื่อสถานที่</label>
            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="{{ $branch->bs_name }}" readonly>
            <label class="font-medium text-gray-700 text-sm">ประเภท</label>
            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="{{ $branch->poit_name }}" readonly>
            <label class="font-medium text-gray-700 text-sm">จังหวัด</label>
            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="{{ $branch->province }}" readonly>
            <label class="font-medium text-gray-700 text-sm">วันที่เพิ่ม</label>
            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="{{ \Carbon\Carbon::parse($branch->created_at)->format('d M Y') }}" readonly>
            <label class="font-medium text-gray-700 text-sm">เพิ่มโดย</label>
            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="{{ $branch->bs_manager_email }}" readonly>
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
            <tbody id="salesTableBody" class="bg-white divide-y divide-gray-200"></tbody>
        </table>
    </div>

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
        const params = new URLSearchParams({ bs_id: branchId, page, limit: rowsPerPage });
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
    const parentCell = event.currentTarget.closest("td");

    if (activeMenuId === id && !menu.classList.contains("hidden")) {
        menu.classList.add("hidden");
        activeMenuId = null;
        return;
    }

    activeMenuId = id;
    menu.innerHTML = `
        <button class="block w-full px-4 py-2 text-white border border-gray-400 bg-blue-600 rounded-lg hover:bg-blue-700 whitespace-nowrap" style="background-color: #3062B8"  onclick="document.getElementById('contextMenu').classList.add('hidden'); viewSale(${id})">ดูรายละเอียด</button>
        <button class="block w-full px-4 py-2 text-white border border-gray-400 bg-blue-600 rounded-lg hover:bg-blue-700" style="background-color: #3062B8"  onclick="document.getElementById('contextMenu').classList.add('hidden'); editSale(${id})">แก้ไข</button>
        <button class="block w-full px-4 py-2 text-white border border-gray-400 bg-red-600 rounded-lg hover:bg-red-700" style="background-color: #CF3434" onclick="document.getElementById('contextMenu').classList.add('hidden'); deleteSale(${id})">ลบ</button>
    `;

    menu.classList.remove("hidden");
    const top = parentCell.offsetTop + parentCell.offsetHeight;
    const left = parentCell.offsetLeft + parentCell.offsetWidth - menu.offsetWidth;
    menu.style.top = `${top}px`;
    menu.style.left = `${left}px`;
}


    function formatThaiDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('th-TH', { year: 'numeric', month: 'short' });
    }

    function formatThaiMonth(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString("th-TH", {
            month: "long"
        });
    }

    function viewSale(id) {
        const sale = sales.find(item => item.sales_id === id);
        if (!sale) {
            Swal.fire("ไม่พบข้อมูล", "รายการที่เลือกไม่มีอยู่ในระบบ", "error");
            return;
        }

        Swal.fire({
            html: `
                <div class="flex flex-col text-2xl font-bold text-center mb-6 mt-2">
                    รายละเอียดข้อมูลสินค้า
                </div>
                <div class="flex flex-col space-y-3 text-left">
                    <div class="w-full">
                        <label class="font-medium text-gray-800 text-sm">เดือน</label>
                        <input type="text" class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow-sm" 
                            value="${formatThaiMonth(sale.sales_month)}" readonly>
                    </div>
                    <div class="w-full">
                        <label class="font-medium text-gray-800 text-sm">จำนวน</label>
                        <input type="text" class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow-sm" 
                            value="${sale.sales_package_amount ?? '-'}" readonly>
                    </div>
                    <div class="w-full">
                        <label class="font-medium text-gray-800 text-sm">ยอดเงิน (บาท)</label>
                        <input type="text" class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow-sm" 
                            value="${parseFloat(sale.sales_amount).toLocaleString('th-TH', { minimumFractionDigits: 2 })}" readonly>
                    </div>
                    <div class="w-full">
                        <label class="font-medium text-gray-800 text-sm">วันที่เพิ่ม</label>
                        <input type="text" class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow-sm" 
                            value="${formatThaiDate(sale.sales_month)}" readonly>
                    </div>
                    <div class="w-full">
                        <label class="font-medium text-gray-800 text-sm">เพิ่มโดย</label>
                        <input type="text" class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow-sm" 
                            value="${sale.manager_email ?? '-'}" readonly>
                    </div>
                </div>
            `,
            confirmButtonText: "ยืนยัน",
            confirmButtonColor: "#2D8C42",
            customClass: {
                popup: 'custom-popup'
            }
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        if (branchId !== null) fetchSales();
    });

    document.addEventListener("click", () => {
        const menu = document.getElementById("contextMenu");
        if (!menu.classList.contains("hidden")) {
            menu.classList.add("hidden");
            activeMenuId = null;
        }
    });
    function editSale(id) {
    const sale = sales.find(item => item.sales_id === id);
    if (!sale) {
        Swal.fire("ไม่พบข้อมูล", "รายการที่เลือกไม่มีอยู่ในระบบ", "error");
        return;
    }

    // สร้าง options เดือนในรูปแบบ กุมภาพันธ์ - 2568
    const monthOptions = [
        { value: "2025-01-01", label: "มกราคม - 2568" },
        { value: "2025-02-01", label: "กุมภาพันธ์ - 2568" },
        { value: "2025-03-01", label: "มีนาคม - 2568" },
        { value: "2025-04-01", label: "เมษายน - 2568" },
        { value: "2025-05-01", label: "พฤษภาคม - 2568" },
    ];

    const selectedMonth = new Date(sale.sales_month).toISOString().split("T")[0];

    Swal.fire({
        html: `
            <div class="flex flex-col items-center mb-4">
                <span class="iconify" data-icon="material-symbols:edit" data-width="60" data-height="60"></span>
                <div class="text-xl font-bold mt-2 mb-4">แก้ไขยอด</div>
            </div>

            <div class="flex flex-col space-y-3 text-left">
                <label class="text-sm font-semibold text-gray-700">เดือน</label>
                <select id="editMonth" class="swal2-select">
                    ${monthOptions.map(opt => 
                        `<option value="${opt.value}" ${opt.value === selectedMonth ? 'selected' : ''}>${opt.label}</option>`
                    ).join("")}
                </select>

                <label class="text-sm font-semibold text-gray-700">จำนวน</label>
                <input id="editBox" type="number" class="swal2-input" placeholder="กรอกจำนวนกล่อง" value="${sale.sales_package_amount || ''}">

                <label class="text-sm font-semibold text-gray-700">ยอดเงิน</label>
                <input id="editAmount" type="text" class="swal2-input" placeholder="กรอกยอดเงิน" value="${parseFloat(sale.sales_amount).toLocaleString()}">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: "ยืนยัน",
        cancelButtonText: "ยกเลิก",
        confirmButtonColor: "#2D8C42",
        cancelButtonColor: "#6B7280",
        customClass: {
            actions: "mt-6 flex justify-between w-full px-4",
            confirmButton: "w-full",
            cancelButton: "w-full"
        },
        preConfirm: () => {
            const sales_amount = parseFloat(document.getElementById("editAmount").value.replace(/,/g, ''));
            const sales_package_amount = parseInt(document.getElementById("editBox").value);
            const sales_month = document.getElementById("editMonth").value;

            if (isNaN(sales_amount) || isNaN(sales_package_amount)) {
                Swal.showValidationMessage("กรุณากรอกจำนวนและยอดเงินให้ถูกต้อง");
                return false;
            }

            return fetch(`{{ route('api.sales.edit') }}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    sales_id: id,
                    sales_amount,
                    sales_package_amount,
                    sales_month
                })
            }).then(res => res.json())
              .then(result => {
                if (result.status !== "success") {
                    throw new Error(result.message || "เกิดข้อผิดพลาด");
                }
                return result;
              }).catch(error => {
                Swal.showValidationMessage(`ผิดพลาด: ${error.message}`);
              });
        }
    }).then(result => {
        if (result.isConfirmed) {
            Swal.fire("สำเร็จ!", "อัปเดตข้อมูลเรียบร้อยแล้ว", "success");
            fetchSales(currentPage);
        }
    });
}

function deleteSale(id) {
    Swal.fire({
        title: "ลบยอดขาย",
        text: "คุณต้องการลบรายการนี้ใช่ไหม?",
        icon: "warning",
        iconColor: "#d33",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "ยืนยัน",
        cancelButtonText: "ยกเลิก"
    }).then(result => {
        if (result.isConfirmed) {
            fetch(`{{ route('api.sales.delete') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ sales_id: id })
            })
            .then(res => res.json())
            .then(result => {
                if (result.status === 'success') {
                    Swal.fire("ลบแล้ว!", "รายการถูกลบเรียบร้อย", "success");
                    fetchSales(currentPage);
                } else {
                    Swal.fire("ผิดพลาด", result.message || "ไม่สามารถลบได้", "error");
                }
            })
            .catch(() => {
                Swal.fire("ผิดพลาด", "เกิดข้อผิดพลาดในการเชื่อมต่อ", "error");
            });
        }
    });
}

</script>
@endsection