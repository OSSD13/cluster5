@extends('layouts.main')

@section('title', 'Branch')

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


    <div class="bg-white shadow-md rounded-lg p-4 my-6">
        <h3 class="text-lg font-bold mb-2">ยอดขายย้อนหลัง 12 เดือน</h3>
        <canvas id="branchSalesChart" height="200"></canvas>
    </div>

    <!-- กราฟและการ์ดสถิติยอดขาย -->


    <div class="flex flex-col gap-4 mb-4">
        <div class="flex flex-row gap-4">
            <div id="minCard"
                class="flex-1 flex justify-center items-center shadow-md rounded-lg  flex-col p-4 gap-2 text-red-dark min-h-32"
                style="background-color: #F2DDD4;">
                <div class="font-bold" style="font-size: 14px; color: black;">Min (บาท)</div>
                <div class="flex justify-center items-center text-bold gap-2">
                    <span id="minValue" class="text-2xl font-bold" style="font-size: 20px">0</span>
                </div>
            </div>

            <div id="maxCard"
                class="flex-1 flex justify-center items-center shadow-md rounded-lg  flex-col p-4 gap-2 text-success min-h-32"
                style="background-color: #D6F2D4;">
                <div class="font-bold" style="font-size: 14px; color: black;">Max (บาท)</div>
                <div class="flex justify-center items-center text-bold gap-2">
                    <span id="maxValue" class="text-2xl font-bold" style="font-size: 20px">0</span>
                </div>
            </div>
        </div>

        <div class="flex flex-row gap-4">
            <div id="stdCard"
                class="flex-1 flex justify-center items-center shadow-md rounded-lg  flex-col p-4 gap-2 text-primary-dark min-h-32"
                style="background-color: #FAEAFF;">
                <div class="font-bold text-center" style="font-size: 14px; color:black;">Standard<br>Deviation (บาท)</div>
                <div class="flex justify-center items-center text-bold gap-2" style="color: #DA25BF;">
                    <span id="stdValue" class="text-2xl font-bold" style="font-size: 20px">0</span>
                </div>
            </div>

            <div id="avgCard"
                class="flex-1 flex justify-center items-center shadow-md rounded-lg  flex-col p-4 gap-2 text-primary-dark min-h-32"
                style="background-color: #FAEAFF;">
                <div class="font-bold" style="font-size: 14px; color: black;">Average (บาท)</div>
                <div class="flex justify-center items-center text-bold text-base gap-2" style="color: #DA25BF;">
                    <span id="avgValue" class="text-2xl font-bold" style="font-size: 20px">0</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function fetchBranchSalesStats() {
            const chartEl = document.getElementById("branchSalesChart");
            const minEl = document.getElementById("minValue");
            const maxEl = document.getElementById("maxValue");
            const avgEl = document.getElementById("avgValue");
            const stdEl = document.getElementById("stdValue");

            try {
                const response = await fetch(`{{ route('api.sales.query') }}?bs_id={{ $branch->bs_id }}`);
                const result = await response.json();

                const sales = result.data || [];
                const salesAmounts = sales
                    .map(s => parseFloat(s.sales_amount))
                    .filter(amount => !isNaN(amount) && isFinite(amount));

                if (salesAmounts.length === 0) {
                    minEl.textContent = maxEl.textContent = avgEl.textContent = stdEl.textContent = "0.00";
                    return;
                }

                // 📊 Calculate stats
                const sum = salesAmounts.reduce((a, b) => a + b, 0);
                const avg = sum / salesAmounts.length;
                const min = Math.min(...salesAmounts);
                const max = Math.max(...salesAmounts);
                const std = Math.sqrt(salesAmounts.reduce((acc, val) => acc + Math.pow(val - avg, 2), 0) / salesAmounts.length);

                // 💡 Format stats
                const format = (val) => val.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                minEl.textContent = format(min);
                maxEl.textContent = format(max);
                avgEl.textContent = format(avg);
                stdEl.textContent = format(std);

                // 🎯 Build monthly sales chart
                // Filter เฉพาะ 12 เดือนรวมเดือนปัจจุบัน
                const now = new Date();
                const last12Months = Array.from({ length: 12 }, (_, i) => {
                    const d = new Date(now.getFullYear(), now.getMonth() - 11 + i, 1);
                    return d.toISOString().slice(0, 7); // yyyy-mm
                });


                const monthlyTotals = {};
                last12Months.forEach(month => monthlyTotals[month] = 0);

                sales.forEach(sale => {
                    const month = sale.sales_month.slice(0, 7);
                    if (monthlyTotals.hasOwnProperty(month)) {
                        monthlyTotals[month] += parseFloat(sale.sales_amount);
                    }
                });

                const labels = last12Months.map(m => {
                    const [y, mo] = m.split("-");
                    return new Date(y, mo - 1).toLocaleString("th-TH", {
                        month: "short",
                        year: "numeric"
                    });
                });

                const values = last12Months.map(m => monthlyTotals[m]);

                if (window.branchMonthlyChart) {
                    window.branchMonthlyChart.destroy();
                }

                const ctx = chartEl.getContext("2d");
                window.branchMonthlyChart = new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels,
                        datasets: [{
                            label: "ยอดขายรายเดือน (บาท)",
                            data: values,
                            backgroundColor: "#4F77BE"
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => value.toLocaleString()
                                }
                            }
                        }
                    }
                });

            } catch (error) {
                console.error("🔥 Error fetching branch sales stats:", error);
            }
        }


        async function drawLast12MonthsChart() {
            const ctx = document.getElementById("branchSalesChart").getContext("2d");

            try {
                const response = await fetch(`{{ route('api.sales.query') }}?bs_id={{ $branch->bs_id }}&limit=1000`);
                const result = await response.json();
                const data = result.data || [];

                // ✅ สร้าง array ของ 12 เดือน (รวมเดือนปัจจุบัน)
                const now = new Date();
                const last12Months = Array.from({ length: 12 }, (_, i) => {
                    const d = new Date(now.getFullYear(), now.getMonth() - i +1, 1);
                    return d.toISOString().slice(0, 7);
                }).reverse(); // reverse ให้เรียงจากเก่า -> ใหม่

                const monthlyTotals = {};
                last12Months.forEach(month => {
                    monthlyTotals[month] = 0;
                });

                data.forEach(sale => {
                    const month = sale.sales_month.slice(0, 7);
                    if (monthlyTotals.hasOwnProperty(month)) {
                        monthlyTotals[month] += parseFloat(sale.sales_amount);
                    }
                });

                const labels = last12Months.map(m => {
                    const [y, mo] = m.split("-");
                    return new Date(y, mo - 1).toLocaleString("th-TH", {
                        month: "short",
                        year: "numeric"
                    });
                });

                const values = last12Months.map(m => monthlyTotals[m]);

                if (window.branchMonthlyChart) {
                    window.branchMonthlyChart.destroy();
                }

                window.branchMonthlyChart = new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels,
                        datasets: [{
                            label: "ยอดขายรายเดือน (บาท)",
                            data: values,
                            backgroundColor: "#4F77BE"
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => value.toLocaleString()
                                }
                            }
                        }
                    }
                });

            } catch (err) {
                console.error("Error drawing monthly chart:", err);
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            fetchBranchSalesStats();
            drawLast12MonthsChart();
        });
    </script>

    <div class="bg-white shadow-md rounded-lg p-6 w-full max-w-md mx-auto relative mb-4">
        @if (session('user')->role_name === 'sale')
            <div class="absolute inset-0 rounded-lg bg-opacity-70 backdrop-blur-sm flex items-center justify-center z-10">
                <p class="text-center text-red-600 font-semibold">คุณไม่มีสิทธิ์ในการเพิ่มข้อมูล</p>
            </div>
        @endif

        <!-- Dropdown เดือน -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-800 mb-1">เดือน</label>
            <select id="saleMonth"
                class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                {{ session('user')->role_name === 'sale' ? 'disabled' : '' }}>
            </select>
        </div>

        <!-- จำนวนกล่อง -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-800 mb-1">จำนวนกล่อง</label>
            <input id="saleBox" type="number" placeholder="กรอกจำนวนกล่อง"
                class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                {{ session('user')->role_name === 'sale' ? 'disabled' : '' }} />
        </div>

        <!-- ยอดเงิน -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-800 mb-1">ยอดเงิน</label>
            <input id="saleAmount" type="number" placeholder="กรอกยอดเงิน"
                class="w-full h-10 px-3 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                {{ session('user')->role_name === 'sale' ? 'disabled' : '' }} />
        </div>

        <!-- ปุ่ม -->
        <div class="text-center">
            <button onclick="addSale()" type="button"
                class="bg-[#3062B8] hover:bg-[#204A90] text-white font-semibold text-sm py-2 px-4 rounded-md shadow-md transition-all duration-200"
                {{ session('user')->role_name === 'sale' ? 'disabled' : '' }}>
                เพิ่มรายการ
            </button>
        </div>

        <!-- ข้อความผลลัพธ์ -->
        <div class="text-sm text-gray-700 mt-3" id="resultCount">
            ผลลัพธ์ 0 รายการ
        </div>
    </div>


    <table class="w-full mt-5 border-collapse rounded-lg">
        <thead class="text-gray-800 text-md rounded-lg" style="background-color: #B5CFF5">
            <tr>
                <th class="py-3 px-4 text-left rounded-tl-lg">เดือน</th>
                <th class="py-3 px-4 text-right">ยอดเงิน</th>
                <th class="py-3 px-4 text-right">เพิ่มโดย</th>
                <th class="py-3 px-4 text-right rounded-tr-lg"></th>
            </tr>
        </thead>
        <tbody id="salesTableBody" class="bg-white divide-y divide-gray-200"></tbody>
    </table>

    <div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>
    <div id="contextMenu" class="hidden absolute bg-white shadow-lg rounded-lg w-32 z-50 p-2 space-y-2"></div>
@endsection


@section('script')

    <script>
        async function regenerateSaleMonthOptions() {
            const select = document.getElementById("saleMonth");
            select.innerHTML = "";

            let existingMonths = [];
            try {
                const response = await fetch(`{{ route('api.sales.query') }}?bs_id={{ $branch->bs_id }}&limit=1000`);
                const result = await response.json();
                existingMonths = (result.data || []).map(s => s.sales_month.slice(0, 7));
            } catch (err) {
                console.error("Error fetching existing sales:", err);
            }

            const now = new Date();
            let added = false;

            for (let i = 0; i < 12; i++) {
                const date = new Date(now.getFullYear(), now.getMonth() - i, 15); // ใช้วันที่ 15
                const ym = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
                const fullDate = `${ym}-01`;

                if (!existingMonths.includes(ym)) {
                    const month = date.toLocaleString('th-TH', {
                        year: 'numeric',
                        month: 'long'
                    });
                    const option = document.createElement("option");
                    option.value = fullDate;
                    option.textContent = `${month}`;
                    select.appendChild(option);
                    added = true;
                }
            }

            if (!added) {
                const opt = document.createElement("option");
                opt.value = "";
                opt.textContent = "ไม่มีเดือนที่สามารถเพิ่มได้";
                opt.disabled = true;
                opt.selected = true;
                select.appendChild(opt);
            }
        }

        document.addEventListener("DOMContentLoaded", async () => {
            await regenerateSaleMonthOptions();
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", async () => {
            const select = document.getElementById("saleMonth");

            // ดึงข้อมูลยอดขายปัจจุบัน
            let existingMonths = [];

            try {
                const response = await fetch(
                    `{{ route('api.sales.query') }}?bs_id={{ $branch->bs_id }}&limit=1000`);
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
            const params = new URLSearchParams({
                bs_id: branchId,
                page,
                limit: rowsPerPage
            });
            try {
                const response = await fetch(`{{ route('api.sales.query') }}?${params.toString()}`);
                const result = await response.json();

                // 🔽 เรียงจากเดือนล่าสุด -> เก่าสุด
                sales = (result.data || []).sort((a, b) => new Date(b.sales_month) - new Date(a.sales_month));
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
                                            <td class="py-3 px-4 text-left">${parseFloat(sale.sales_amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                                            <td class="py-3 px-4 text-left">${sale.manager_name}</td>
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
            const top = parentCell.offsetTop + parentCell.offsetHeight - 120; // ลดลงมานิด (4px)
            const left = parentCell.offsetLeft + parentCell.offsetWidth - menu.offsetWidth;
            console.log(top, left)
            menu.style.top = `${top}px`;
            menu.style.left = `${left}px`;
        }


        function formatThaiDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'short'
            });
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
        function formatFullThaiDate(dateStr) {
            const date = new Date(dateStr);

            // ถ้า dateStr ไม่ valid
            if (isNaN(date.getTime())) return "-";

            const day = date.getDate();
            const month = date.toLocaleString("th-TH", { month: "long" });
            const year = date.getFullYear() + 543;

            return `${day} ${month} ${year}`;
        }

        function editSale(id) {
            const sale = sales.find(item => item.sales_id === id);
            if (!sale) {
                Swal.fire("ไม่พบข้อมูล", "รายการที่เลือกไม่มีอยู่ในระบบ", "error");
                return;
            }
            console.log(sale, new Date(sale.sales_month))

            Swal.fire({
                html: `
                                        <div class="flex flex-col items-center mb-1">
                                            <span class="iconify" data-icon="material-symbols-light:edit-square-rounded" data-width="70" data-height="70"></span>
                                        </div>
                                    <div class="text-xl font-bold mt-2 mb-4">แก้ไขยอด</div>
                                </div>

                                <div class="flex flex-col space-y-3 text-left">
                                    <label class="text-gray-800 text-sm">เดือน</label>
                                    <input id="editMonth" type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" readonly value="${formatFullThaiDate(sale.sales_month)}">

                                    <label class="text-gray-800 text-sm">จำนวน</label>
                                    <input id="editBox" type="number" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" placeholder="กรอกจำนวนกล่อง" value="${sale.sales_package_amount || ''}">

                                    <label class="text-gray-800 text-sm">ยอดเงิน</label>
                                    <input id="editAmount" type="number" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" placeholder="กรอกยอดเงิน" value="${sale.sales_amount}">
                                </div>
                            `,
                showCancelButton: true,
                confirmButtonText: "ยืนยัน",
                cancelButtonText: "ยกเลิก",
                confirmButtonColor: "#2D8C42",
                cancelButtonColor: "#6B7280",
                preConfirm: () => {
                    const sales_amount_raw = document.getElementById("editAmount").value.replace(/,/g, '')
                        .trim();
                    const sales_amount = parseFloat(sales_amount_raw);
                    const sales_package_amount = parseInt(document.getElementById("editBox").value.trim());
                    const sales_month = sale.sales_month;

                    if (!sales_month) {
                        Swal.showValidationMessage("กรุณาเลือกเดือนให้ถูกต้อง");
                        return false;
                    }

                    if (isNaN(sales_amount) || isNaN(sales_package_amount)) {
                        Swal.showValidationMessage("กรุณากรอกจำนวนและยอดเงินให้ถูกต้อง");
                        return false;
                    }

                    if (sales_amount < 0 || sales_package_amount < 0) {
                        Swal.showValidationMessage("จำนวนและยอดเงินต้องไม่ติดลบ");
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
                    fetchBranchSalesStats();
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
                        body: JSON.stringify({
                            sales_id: id
                        })
                    })
                        .then(res => res.json())
                        .then(async result => {
                            if (result.status === 'success') {
                                Swal.fire("ลบแล้ว!", "รายการถูกลบเรียบร้อย", "success");
                                await fetchSales(currentPage);
                                await updateResultCount();
                                await fetchBranchSalesStats();

                                // ✅ อัปเดต dropdown ให้เพิ่มเดือนนั้นกลับมาได้
                                await regenerateSaleMonthOptions();

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


        function renderPagination(total) {
            const pagination = document.getElementById("pagination");
            pagination.innerHTML = "";

            const totalPages = Math.ceil(total / rowsPerPage);
            const maxVisible = 1;
            let startPage = Math.max(1, currentPage - maxVisible);
            let endPage = Math.min(totalPages, currentPage + maxVisible);

            if (totalPages <= 1) return;

            const createPageButton = (page, isActive = false) => {
                const btn = document.createElement("button");
                btn.innerText = page;
                btn.className =
                    `min-w-[36px] h-10 px-3 mx-1 rounded-lg text-sm font-medium ${isActive ? "bg-blue-600 text-white" : "bg-white border border-gray-300 text-black hover:bg-gray-100"}`;
                btn.onclick = () => goToPage(page);
                return btn;
            };

            const createEllipsis = () => {
                const btn = document.createElement("button");
                btn.innerText = "...";
                btn.className = "px-3 text-gray-500 hover:text-black rounded hover:bg-gray-100";
                btn.onclick = () => {
                    Swal.fire({
                        title: "ไปยังหน้าที่...",
                        input: "number",
                        inputLabel: `กรอกหมายเลขหน้า (1 - ${totalPages})`,
                        inputAttributes: {
                            min: 1,
                            max: totalPages,
                            step: 1
                        },
                        showCancelButton: true,
                        confirmButtonText: "ไปเลย!",
                        confirmButtonColor: "#3062B8",
                        inputValidator: (value) => {
                            if (!value || isNaN(value)) return "กรุณากรอกตัวเลข";
                            if (value < 1 || value > totalPages)
                                return `หน้าต้องอยู่ระหว่าง 1 ถึง ${totalPages}`;
                            return null;
                        }
                    }).then(result => {
                        if (result.isConfirmed) goToPage(parseInt(result.value));
                    });
                };
                return btn;
            };

            const prevBtn = document.createElement("button");
            prevBtn.innerHTML = "&lt;";
            prevBtn.className =
                `min-w-[40px] h-10 px-3 mx-1 rounded-lg text-xl font-bold ${currentPage === 1 ? "text-gray-300 bg-white border border-gray-200 cursor-not-allowed" : "text-blue-600 bg-white border border-gray-300 hover:bg-blue-50"}`;
            prevBtn.disabled = currentPage === 1;
            prevBtn.onclick = () => goToPage(currentPage - 1);
            pagination.appendChild(prevBtn);

            if (startPage > 1) {
                pagination.appendChild(createPageButton(1));
                if (startPage > 2) pagination.appendChild(createEllipsis());
            }

            for (let i = startPage; i <= endPage; i++) {
                pagination.appendChild(createPageButton(i, i === currentPage));
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) pagination.appendChild(createEllipsis());
                pagination.appendChild(createPageButton(totalPages));
            }

            const nextBtn = document.createElement("button");
            nextBtn.innerHTML = "&gt;";
            nextBtn.className =
                `min-w-[40px] h-10 px-3 mx-1 rounded-lg text-xl font-bold ${currentPage === totalPages ? "text-gray-300 bg-white border border-gray-200 cursor-not-allowed" : "text-blue-600 bg-white border border-gray-300 hover:bg-blue-50"}`;
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.onclick = () => goToPage(currentPage + 1);
            pagination.appendChild(nextBtn);
        }

        function goToPage(pageNumber) {
            currentPage = pageNumber;
            fetchSales(currentPage); // เปลี่ยนเป็น fetchSales แทน
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

                    // ✅ เคลียร์ฟอร์ม
                    document.getElementById("saleBox").value = "";
                    document.getElementById("saleAmount").value = "";
                    regenerateSaleMonthOptions();

                    // ✅ อัปเดตตาราง & count
                    await fetchSales(currentPage);
                    await updateResultCount();
                    await fetchBranchSalesStats(); // เพิ่มบรรทัดนี้
                    await drawLast12MonthsChart(); // เพิ่มบรรทัดนี้

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