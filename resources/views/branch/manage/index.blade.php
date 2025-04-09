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




    <div class="bg-white shadow-md rounded-lg p-6 w-full max-w-md mx-auto mb-5">
    <!-- Dropdown เดือน -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-800 mb-1">เดือน</label>
        <select id="saleMonth"
            class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </select>
    </div>

    <!-- จำนวนกล่อง -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-800 mb-1">จำนวนกล่อง</label>
        <input id="saleBox" type="number" placeholder="กรอกจำนวนกล่อง"
            class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
    </div>

    <!-- ยอดเงิน -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-800 mb-1">ยอดเงิน</label>
        <input id="saleAmount" type="number" placeholder="กรอกยอดเงิน"
            class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
    </div>

    <!-- ปุ่ม -->
    <div class="text-center">
        <button onclick="addSale()" type="button"
            class="bg-[#3062B8] hover:bg-[#204A90] text-white font-semibold text-sm py-2 px-4 rounded-md shadow-md transition-all duration-200">
            เพิ่มรายการ
        </button>
    </div>

    <!-- ข้อความผลลัพธ์ -->
    <div class="text-sm text-gray-700 mt-3" id="resultCount">
        ผลลัพธ์ 0 รายการ
    </div>
</div>




<!-- กราฟและการ์ดสถิติยอดขาย -->


<div class="flex flex-col gap-4">
    <div class="flex flex-row gap-4">
        <div id="minCard" class="flex-1 shadow-md rounded-lg flex flex-col p-4 gap-2 text-red-dark"
             style="background-color: #F2DDD4;">
            <div class="font-bold" style="font-size: 14px; color: black;">Min (บาท)</div>
            <div class="flex justify-center items-center text-bold gap-2">
                <span id="minValue" class="text-2xl font-bold" style="font-size: 20px">0</span>
            </div>
            <div id="minChange" class="text-sm text-end">
                <span id="minArrow" class="icon-[line-md--arrow-down]"></span>
                <span id="minPercent">0</span>%
            </div>
        </div>

        <div id="maxCard" class="flex-1 shadow-md rounded-lg flex flex-col p-4 gap-2 text-success"
             style="background-color: #D6F2D4;">
            <div class="font-bold" style="font-size: 14px; color: black;">Max (บาท)</div>
            <div class="flex justify-center items-center text-bold gap-2">
                <span id="maxValue" class="text-2xl font-bold" style="font-size: 20px">0</span>
            </div>
            <div id="maxChange" class="text-sm text-end">
                <span id="maxArrow" class="icon-[line-md--arrow-up]"></span>
                <span id="maxPercent">0</span>%
            </div>
        </div>
    </div>

    <div class="flex flex-row gap-4">
        <div id="stdCard" class="flex-1 shadow-md rounded-lg flex flex-col p-4 gap-2 text-primary-dark"
             style="background-color: #FAEAFF;">
            <div class="font-bold" style="font-size: 14px; color:black;">Standard Deviation (บาท)</div>
            <div class="flex justify-center items-center text-bold gap-2" style="color: #DA25BF;">
                <span id="stdValue" class="text-2xl font-bold" style="font-size: 20px">0</span>
            </div>
            <div id="stdChange" class="text-base text-end text-bold" style="color: #DA25BF;">
                <span id="stdArrow" class="icon-[line-md--arrow-down]"></span>
                <span id="stdPercent">0</span>%
            </div>
        </div>

        <div id="avgCard" class="flex-1 shadow-md rounded-lg flex flex-col p-4 gap-2 text-primary-dark"
             style="background-color: #FAEAFF;">
            <div class="font-bold" style="font-size: 14px; color: black;">Average (บาท)</div>
            <div class="flex justify-center items-center text-bold text-base gap-2 mt-5" style="color: #DA25BF;">
                <span id="avgValue" class="text-2xl font-bold" style="font-size: 20px">0</span>
                <span class="text-2xl font-bold" style="font-size: 16px">บาท</span>
            </div>
            <div id="avgChange" class="text-base text-end text-bold" style="color: #DA25BF;">
                <span id="avgArrow" class="icon-[line-md--arrow-down]"></span>
                <span id="avgPercent">0</span>%
            </div>
        </div>
    </div>
</div>






<script>
document.addEventListener("DOMContentLoaded", function () {
    fetchBranchSalesStats();
});

async function fetchBranchSalesStats() {
    try {
        const response = await fetch(`{{ route('api.sales.query') }}?bs_id={{ $branch->bs_id }}&limit=1000`);
        const result = await response.json();
        const data = result.data || [];
        const salesAmounts = data.map(s => parseFloat(s.sales_amount));

        if (salesAmounts.length === 0) return;

        const min = Math.min(...salesAmounts);
        const max = Math.max(...salesAmounts);
        const avg = salesAmounts.reduce((a, b) => a + b, 0) / salesAmounts.length;
        const std = Math.sqrt(salesAmounts.map(x => Math.pow(x - avg, 2)).reduce((a, b) => a + b, 0) / salesAmounts.length);

        document.getElementById('minValue').textContent = min.toLocaleString(undefined, { minimumFractionDigits: 2 });
        document.getElementById('maxValue').textContent = max.toLocaleString(undefined, { minimumFractionDigits: 2 });
        document.getElementById('avgValue').textContent = avg.toLocaleString(undefined, { minimumFractionDigits: 2 });
        document.getElementById('stdValue').textContent = std.toLocaleString(undefined, { minimumFractionDigits: 2 });

        const bins = Array(10).fill(0);
        const maxSale = Math.max(...salesAmounts);
        const step = maxSale / bins.length;
        salesAmounts.forEach(amount => {
            const index = Math.min(Math.floor(amount / step), bins.length - 1);
            bins[index]++;
        });

        const labels = bins.map((_, i) => `${Math.round(i * step / 1000)}k`);
        const ctx = document.getElementById("branchSalesChart").getContext("2d");
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: "จำนวนสาขา",
                    data: bins,
                    backgroundColor: "#3366C0"
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

    } catch (error) {
        console.error("Error fetching stats:", error);
    }
}
</script>


<table class="w-full mt-5 border-collapse rounded-lg overflow-hidden ">
            <thead class="text-gray-800 text-md" style="background-color: #B5CFF5">
                <tr>
                    <th class="py-3 px-4 text-left">เดือน</th>
                    <th class="py-3 px-4 text-right">ยอดเงิน</th>
                    <th class="py-3 px-4 text-right">เพิ่มโดย</th>
                    <th class="py-3 px-4 text-right"></th>
                </tr>
            </thead>
            <tbody id="salesTableBody" class="bg-white divide-y divide-gray-200"></tbody>
        </table>

    <div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>
    <div id="contextMenu" class="hidden absolute bg-white shadow-lg rounded-lg w-32 z-50 p-2 space-y-2"></div>
@endsection

@section('script')

<script>
    document.addEventListener("DOMContentLoaded", async () => {
        const select = document.getElementById("saleMonth");

        // ดึงข้อมูลยอดขายปัจจุบัน
        let existingMonths = [];

        try {
            const response = await fetch(`{{ route('api.sales.query') }}?bs_id={{ $branch->bs_id }}&limit=1000`);
            const result = await response.json();
            existingMonths = (result.data || []).map(s => s.sales_month.slice(0, 7));
        } catch (err) {
            console.error("Error fetching existing sales:", err);
        }

        // สร้างตัวเลือก 12 เดือนย้อนหลัง
        const now = new Date();
        for (let i = 0; i < 12; i++) {
            const date = new Date(now.getFullYear(), now.getMonth() - i, 1);
            const value = date.toISOString().slice(0, 10);
            const ym = date.toISOString().slice(0, 7);

            if (!existingMonths.includes(ym)) {
                const month = date.toLocaleString('th-TH', {
                    year: 'numeric',
                    month: 'long'
                });
                const option = document.createElement("option");
                option.value = value;
                option.textContent = `${month}`;
                select.appendChild(option);
            }
        }

        // ถ้าไม่มีให้เลือก
        if (select.options.length === 0) {
            const opt = document.createElement("option");
            opt.value = "";
            opt.textContent = "ไม่มีเดือนที่สามารถเพิ่มได้";
            opt.disabled = true;
            opt.selected = true;
            select.appendChild(opt);
        }
    });
</script>
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
            document.addEventListener("DOMContentLoaded", updateResultCount);
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
                    updateResultCount();
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


        function editSale(id) {
    const sale = sales.find(item => item.sales_id === id);
    if (!sale) {
        Swal.fire("ไม่พบข้อมูล", "รายการที่เลือกไม่มีอยู่ในระบบ", "error");
        return;
    }

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
        <div class="flex flex-col items-center mb-2">
            <span class="iconify text-black" data-icon="mdi:pen" data-width="60" data-height="60"></span>
            <div class="text-lg font-bold text-black mt-2 mb-4">แก้ไขยอด</div>
        </div>

        <div class="flex flex-col space-y-3 text-left">

            <div>
                <label class="text-sm text-gray-700 mb-1 block">เดือน</label>
                <select id="editMonth"
                    class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow">
                    ${monthOptions.map(opt =>
                        `<option value="${opt.value}" ${opt.value === selectedMonth ? 'selected' : ''}>${opt.label}</option>`
                    ).join("")}
                </select>
            </div>

            <div>
                <label class="text-sm text-gray-700 mb-1 block">จำนวน</label>
                <input id="editBox" type="number"
                    class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow"
                    placeholder="จำนวนกล่อง" value="${sale.sales_package_amount ?? ''}">
            </div>

            <div>
                <label class="text-sm text-gray-700 mb-1 block">ยอดเงิน</label>
                <input id="editAmount" type="text"
                    class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow"
                    placeholder="ยอดเงิน" value="${parseFloat(sale.sales_amount).toLocaleString()}">
            </div>
        </div>
    `,
    showCancelButton: true,
    confirmButtonText: "ยืนยัน",
    cancelButtonText: "ยกเลิก",
    confirmButtonColor: "#2D8C42", // เขียว
    cancelButtonColor: "#6B7280",  // เทา
    customClass: {
        actions: "mt-6 flex justify-between w-full px-4",
        confirmButton: "ml-auto w-[45%] text-white bg-green-700 hover:bg-green-800 rounded-md font-semibold py-2",
        cancelButton: "w-[45%] text-white bg-gray-500 hover:bg-gray-600 rounded-md font-semibold py-2",
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
        })
        .then(res => res.json())
        .then(result => {
            if (result.status !== "success") {
                throw new Error(result.message || "เกิดข้อผิดพลาด");
            }
            return result;
        })
        .catch(error => {
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

async function addSale() {
        const month = document.getElementById("saleMonth").value;
        const box = parseInt(document.getElementById("saleBox").value);
        const amount = parseFloat(document.getElementById("saleAmount").value);

        if (isNaN(box) || isNaN(amount)) {
            Swal.fire("กรุณากรอกจำนวนกล่องและยอดเงินให้ถูกต้อง");
            return;
        }

        try {
            const response = await fetch(`{{ route('api.sales.create') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    sales_branch_id: {{ $branch->bs_id }},
                    sales_package_amount: box,
                    sales_amount: amount,
                    sales_month: month
                })
            });

            const result = await response.json();

            if (result.status === 'success') {
                Swal.fire("สำเร็จ", "เพิ่มรายการเรียบร้อยแล้ว", "success");
                fetchSales(currentPage); // รีโหลดตาราง
                updateResultCount();     // อัปเดตจำนวนผลลัพธ์
            } else {
                Swal.fire("เกิดข้อผิดพลาด", result.message || "ไม่สามารถเพิ่มข้อมูลได้", "error");
            }
        } catch (error) {
            console.error(error);
            Swal.fire("ผิดพลาด", "ไม่สามารถเชื่อมต่อ API", "error");
        }
    }

    async function updateResultCount() {
        try {
            const params = new URLSearchParams({
                bs_id: {{ $branch->bs_id }},
                page: 1,
                limit: 1 // ขอข้อมูล 1 รายการพอ เพื่อดูว่า total เท่าไหร่
            });

            const response = await fetch(`{{ route('api.sales.query') }}?${params.toString()}`);
            const result = await response.json();

            const total = result.total || 0;
            document.getElementById("resultCount").innerText = `ผลลัพธ์ ${total} รายการ`;
        } catch (error) {
            console.error("Error updating result count:", error);
        }
        document.addEventListener("DOMContentLoaded", updateResultCount);
    }

    // โหลด count เมื่อเข้าเพจ
    document.addEventListener("DOMContentLoaded", updateResultCount);

    </script>
@endsection