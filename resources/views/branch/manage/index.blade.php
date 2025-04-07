@extends('layouts.main')

@section('title', 'Branch')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto mb-5">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-700">จัดการสาขา - </h2>
        </div>
    </div>
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto mb-5">
        <div class="flex flex-col space-y-2 text-left">
            <label class="font-medium text-gray-800 text-sm">ชื่อสถานที่</label>
            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm"
                value="${branch.name}" readonly>

            <label class="font-medium text-gray-800 text-sm">ประเภท</label>
            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm"
                value="${branch.type}" readonly>

            <label class="font-medium text-gray-800 text-sm">จังหวัด</label>
            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm"
                value="${branch.province}" readonly>

            <label class="font-medium text-gray-800 text-sm">วันที่เพิ่ม</label>
            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm"
                value="17 ก.ย. 2568" readonly>

            <label class="font-medium text-gray-800 text-sm">เพิ่มโดย</label>
            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm"
                value="jeng@gmail.com" readonly>
        </div>
    </div>



    <div class="flex flex-col gap-4">
        {{-- report card --}}

        {{-- stat cards --}}
        <div class="flex flex-row gap-4">
            <div class="flex-1 bg-white shadow-md rounded-lg flex flex-col p-4 gap-4">
                <div class="">ยอดพัสดุทั้งหมด</div>
                <div class="flex justify-around items-center">
                    <span class="icon-[streamline--upload-box-1-solid] text-4xl text-trinary"
                        id='thisMonthTotalPackageIcon'></span>
                    <span class="text-2xl text-bold text-trinary" id='thisMonthTotalPackageNumber'></span>ชิ้น
                </div>
                <div class="text-success text-sm text-end" id='thisMonthTotalPackagePercentParent'>
                    <span class="icon-[line-md--arrow-up]" id='thisMonthTotalPackageArrow'></span>
                    <span id='thisMonthTotalPackagePercent'></span>%
                </div>
            </div>
        </div>

        <div class="flex flex-row gap-4">
            <div class="flex-1 bg-white shadow-md rounded-lg flex flex-col p-4 gap-4">
                <div class="">ยอดรายได้ทั้งหมด</div>
                <div class="flex justify-around items-center">
                    <span class="icon-[tabler--coin-filled] text-4xl text-trinary" id='thisMonthTotalMoneyIcon'></span>
                    <span class="text-2xl text-bold text-trinary" id='thisMonthTotalMoneyNumber'></span>บาท
                </div>
                <div class="text-success text-sm text-end" id='thisMonthTotalMoneyPercentParent'>
                    <span class="icon-[line-md--arrow-up]" id='thisMonthTotalMoneyArrow'></span>
                    <span id='thisMonthTotalMoneyPercent'></span>%
                </div>
            </div>
        </div>


        <div class="bg-purpur shadow-md rounded-lg p-6 flex flex-col">
            <canvas id="branchVSprofit"></canvas>
        </div>
        <div class="flex flex-col gap-4">
            <div class="flex flex-row gap-4">
                <div id="minCard" class="flex-1 shadow-md rounded-lg flex flex-col p-4 gap-2 text-red-dark">
                    <div class="">Min</div>
                    <div class="flex justify-center items-center text-bold gap-2">
                        <span id="minValue" class="text-2xl text-bold">0</span>บาท
                    </div>
                    <div id="minChange" class="text-sm text-end">
                        <span id="minArrow" class="icon-[line-md--arrow-down]"></span>
                        <span id="minPercent">0</span>%
                    </div>
                </div>
                <div id="maxCard" class="flex-1 shadow-md rounded-lg flex flex-col p-4 gap-2 text-success">
                    <div class="">Max</div>
                    <div class="flex justify-center items-center text-bold gap-2">
                        <span id="maxValue" class="text-2xl text-bold">0</span>บาท
                    </div>
                    <div id="maxChange" class="text-sm text-end">
                        <span id="maxArrow" class="icon-[line-md--arrow-up]"></span>
                        <span id="maxPercent">0</span>%
                    </div>
                </div>
            </div>
            <div class="flex flex-row gap-4">
                <div id="stdCard" class="flex-1 shadow-md rounded-lg flex flex-col p-4 gap-2 text-primary-dark">
                    <div class="">Standard Deviation</div>
                    <div class="flex justify-center items-center text-bold gap-2">
                        <span id="stdValue" class="text-2xl text-bold">0</span>บาท
                    </div>
                    <div id="stdChange" class="text-sm text-end">
                        <span id="stdArrow" class="icon-[line-md--arrow-down]"></span>
                        <span id="stdPercent">0</span>%
                    </div>
                </div>
                <div id="avgCard" class="flex-1 shadow-md rounded-lg flex flex-col p-4 gap-2 text-primary-dark">
                    <div class="">Average</div>
                    <div class="flex justify-center items-center text-bold gap-2">
                        <span id="avgValue" class="text-2xl text-bold">0</span>บาท
                    </div>
                    <div id="avgChange" class="text-sm text-end">
                        <span id="avgArrow" class="icon-[line-md--arrow-down]"></span>
                        <span id="avgPercent">0</span>%
                    </div>
                </div>
            </div>
        </div>





    </div>



    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto mb-5 mt-5">
        <div class="flex flex-col space-y-2 text-left max-w-xs">
            <label class="font-medium text-gray-700 text-sm">เดือน</label>
            <select class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm">
                <option>กุมภาพันธ์ - 2568</option>
            </select>

            <label class="font-medium text-gray-700 text-sm">จำนวนกล่อง</label>
            <input type="number" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm">

            <label class="font-medium text-gray-700 text-sm">ยอดเงิน</label>
            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm">

            <button
                class="w-full h-10 text-white border border-gray-400 font-medium rounded-md shadow-md hover:bg-blue-700 transition" style="background-color: #3062B8">
                เพิ่มรายการ
            </button>

            <p class="text-sm text-gray-600 mt-2">ผลลัพธ์ 302 รายการ</p>
        </div>
    </div>

    <!-- Pagination Controls -->
    <div class="overflow-visible">
        <table class="w-full mt-5 border-collapse rounded-lg overflow-hidden table-fixed ">
            <thead class="text-gray-800" style="background-color: #B5CFF5">
                <tr>
                    <th class="py-3 px-4 w-13 text-left">ID</th>
                    <th class="py-3 px-4 text-left whitespace-nowrap">ชื่อสาขา</th>
                    <th class="py-3 px-4 text-left whitespace-nowrap">จังหวัด</th>
                    <th class="py-3 px-4 text-left whitespace-nowrap">เพิ่มโดย</th>
                    <th class="py-3 px-1 w-7 text-center"></th>
                </tr>
            </thead>
            <tbody id="tableBody" class="bg-white divide-y divide-gray-200"></tbody>
        </table>
    </div>

    <!-- Pagination Controls -->
    <div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>

@endsection


@section('script')
    <script>
        let branches = [
            { id: 1, name: "บางแสน", type: "ร้านอาหาร", province: "ชลบุรี" },
            { id: 2, name: "อุดรธานี", type: "ร้านกาแฟ", province: "อุดรธานี" },
            { id: 3, name: "ศรีราชา", type: "ร้านขนม", province: "ชลบุรี" },
            { id: 4, name: "พัทยา", type: "ผับบาร์", province: "ชลบุรี" },
            { id: 5, name: "เซนทรัล", type: "ศูนย์การค้า", province: "ชลบุรี" },
            { id: 6, name: "ท่าพระ", type: "ตลาด", province: "ขอนแก่น" },
            { id: 7, name: "กรุงเทพฯ", type: "ร้านอาหาร", province: "กรุงเทพมหานคร" },
            { id: 8, name: "ปราจีนบุรี", type: "ร้านกาแฟ", province: "ปราจีนบุรี" },
            { id: 9, name: "ฉะเชิงเทรา", type: "ตลาด", province: "ฉะเชิงเทรา" },
            { id: 10, name: "สระบุรี", type: "ร้านขนม", province: "สระบุรี" },
            { id: 11, name: "แหลมแท่น", type: "ที่เที่ยว", province: "ชลบุรีหหหหหหหหหหห" }
        ]; // Your existing data
        let currentPage = 1;
        const rowsPerPage = 25;
        let currentSort = { column: null, ascending: true };

        function renderTable() {
            const tableBody = document.getElementById("tableBody");
            tableBody.innerHTML = "";

            const start = (currentPage - 1) * rowsPerPage;
            const paginatedData = branches.slice(start, start + rowsPerPage);


            paginatedData.forEach((branch) => {
                const row = document.createElement("tr");
                row.innerHTML = `
                        <td class="py-3 px-4 w-16">${branch.id}</td>
                        <td class="py-3 px-4 truncate">${branch.name}</td>
                        <td class="py-3 px-4 w-32 truncate">${branch.type}</td>
                        <td class="py-3 px-4 w-32 truncate">${branch.province}</td>
                        <td class="py-3 px-1 w-10 text-center relative">
                            <button class="cursor-pointer" onclick="toggleMenu(event, ${branch.id})">&#8230;</button>
                            <div id="menu-${branch.id}" class="hidden absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-32 z-50 p-2 space-y-2">
                                <button class="block w-full px-4 py-2 border border-gray-400 text-white rounded-lg shadow-md hover:bg-blue-700 whitespace-nowrap cursor-pointer" style="background-color: #3062B8" onclick="viewDetail(${branch.id})">ดูรายละเอียด</button>
                                <button class="block w-full px-4 py-2 text-white border border-gray-400 rounded-lg shadow-md hover:bg-blue-700 cursor-pointer" style="background-color: #3062B8" 
                                onclick="edit(${branch.id})">แก้ไข</button>
                                <button class="block w-full px-4 py-2 text-white bg-red-600 border border-gray-400 rounded-lg shadow-md hover:bg-red-700 cursor-pointer" style="background-color: #CF3434" onclick="deleteBranch(${branch.id})">ลบ</button>
                            </div>
                        </td>
                    `;
                tableBody.appendChild(row);
            });

            renderPagination();
        }
       
        function renderPagination() {
            const pagination = document.getElementById("pagination");
            pagination.innerHTML = ""; // Clear previous pagination

            const totalPages = Math.ceil(branches.length / rowsPerPage);

            // Previous button
            const prevBtn = document.createElement("button");
            prevBtn.innerHTML = '<span class="icon-[material-symbols--chevron-left-rounded]"></span>';
            prevBtn.className = `px-3 py-1 ${currentPage === 1 ? "text-gray-400 cursor-not-allowed" : "text-blue-600 cursor-pointer"} text-5xl`;
            prevBtn.disabled = currentPage === 1;
            prevBtn.onclick = () => goToPage(currentPage - 1);
            pagination.appendChild(prevBtn);

            // Display first page button if needed
            if (currentPage > 3) {
                const firstBtn = document.createElement("button");
                firstBtn.innerText = "1";
                firstBtn.className = `px-4 py-2 mx-1 rounded-lg text-base font-semibold bg-white border border-gray-300 text-black cursor-pointer`;
                firstBtn.onclick = () => goToPage(1);
                pagination.appendChild(firstBtn);
                pagination.appendChild(document.createTextNode("..."));
            }

            // Display middle page numbers
            for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
                const btn = document.createElement("button");
                btn.innerText = i;
                btn.className = `px-4 py-2 mx-1 rounded-lg text-base font-semibold 
                                         ${i === currentPage ? "bg-blue-600 text-white " : "bg-white border border-gray-300 text-black cursor-pointer"}`;
                btn.onclick = () => goToPage(i);
                pagination.appendChild(btn);
            }

            // Display last page button if needed
            if (currentPage < totalPages - 2) {
                pagination.appendChild(document.createTextNode("..."));
                const lastBtn = document.createElement("button");
                lastBtn.innerText = totalPages;
                lastBtn.className = `px-4 py-2 mx-1 rounded-lg text-base font-semibold bg-white border border-gray-300 text-black cursor-pointer`;
                lastBtn.onclick = () => goToPage(totalPages);
                pagination.appendChild(lastBtn);
            }

            // Next button
            const nextBtn = document.createElement("button");
            nextBtn.innerHTML = '<span class="icon-[material-symbols--chevron-right-rounded]"></span>';
            nextBtn.className = `px-3 py-1 ${currentPage === totalPages ? "text-gray-400 cursor-not-allowed" : "text-blue-600 cursor-pointer"} text-5xl`;
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.onclick = () => goToPage(currentPage + 1);
            pagination.appendChild(nextBtn);
        }


        function goToPage(pageNumber) {
            currentPage = pageNumber;
            renderTable();
        }

        function toggleMenu(event, id) {
            event.stopPropagation();
            document.querySelectorAll("[id^=menu-]").forEach(menu => menu.classList.add("hidden"));
            document.getElementById(`menu-${id}`).classList.toggle("hidden");
        }
        document.addEventListener("click", () => {
            document.querySelectorAll("[id^=menu-]").forEach(menu => menu.classList.add("hidden"));
        });


        function viewDetail(id) {
            const branch = branches.find(item => item.id === id);

            Swal.fire({
                html: `
                        <div class="flex flex-col text-3xl mb-6 mt-4">
                        <b class=text-gray-800 >รายละเอียดข้อมูล POI</b>
                        </div>
                            <div class="flex flex-col space-y-2 text-left ">
                                <label class="font-medium text-gray-800 text-sm">ชื่อสถานที่</label>
                                <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${branch.name}" readonly>

                                <label class="font-medium text-gray-800 text-sm">ประเภท</label>
                                <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${branch.type}" readonly>

                                <label class="font-medium text-gray-800 text-sm">จังหวัด</label>
                                <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${branch.province}" readonly>

                                <label class="font-medium text-gray-800 text-sm">วันที่เพิ่ม</label>
                                <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="17 ก.ย. 2568" readonly>

                                <label class="font-medium text-gray-800 text-sm">เพิ่มโดย</label>
                                <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="jeng@gmail.com" readonly>
                            </div>
                            `,
                customClass: {
                    popup: 'custom-popup'
                },
                confirmButtonText: "ยืนยัน",
                confirmButtonColor: "#2D8C42",
            });
        }
        function edit(id) {
            const branch = branches.find(item => item.id === id);

            Swal.fire({
                html: `
                    <div class="flex flex-col text-3xl  mb-6 mt-4">
                        <b class=text-gray-800 >แก้ไขยอดขาย</b>
                        </div>
                    <!-- ฟอร์ม -->
                    <div class="fflex flex-col items-center mt-4 space-y-4 text-left w-full max-w-md mx-auto">
                    <div class="w-full">
                        <label class="block text-gray-800 text-sm mb-1">เดือน</label>
                        <select class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm">
                            <option>กุมภาพันธ์ - 2568</option>
                        </select>
                    </div>
                    
                    <div class="w-full">
                        <label class="block text-gray-800 text-sm mb-1">จำนวน</label>
                        <input type="number" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="482">
                    </div>

                    <div class="w-full">
                        <label class="block text-gray-800 text-sm mb-1">ยอดเงิน</label>
                        <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="234,454">
                    </div>
                    </div>
        
                    </div>
                        `,
                customClass: {
                    popup: 'custom-popup'
                },
                confirmButtonText: "ยืนยัน",
                confirmButtonColor: "#2D8C42",
            });
        }

        function editBranch(id) { alert(`แก้ไขข้อมูลของ ID ${id}`); }
        function deleteBranch(id) {
            Swal.fire({
                title: "ลบสถานที่ที่สนใจ",
                text: "คุณต้องการลบสถานที่ที่สนใจ ใช่หรือไม่",
                icon: "warning",
                iconColor: "#d33",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "ยืนยัน",
                cancelButtonText: "ยกเลิก"
            }).then((result) => {
                if (result.isConfirmed) {
                    // ลบรายการออกจากอาร์เรย์
                    branches = branches.filter(branch => branch.id !== id);

                    // อัปเดตตาราง
                    renderTable();

                    // แจ้งเตือนว่าลบสำเร็จ
                    Swal.fire({
                        title: "ลบแล้ว!",
                        text: "สถานที่ที่สนใจถูกลบเรียบร้อย",
                        icon: "success"
                    });
                }
            });
        }


        renderTable();

    </script>

    <script>
        function updateCardData(data) {
            const {
                min,
                max,
                std,
                avg,
                minChange,
                maxChange,
                stdChange,
                avgChange
            } = data;

            // Update Min Card
            document.getElementById('minValue').textContent = min.toLocaleString();
            document.getElementById('minPercent').textContent = minChange.toFixed(2);
            updateCardStyle('minCard', 'minArrow', minChange);

            // Update Max Card
            document.getElementById('maxValue').textContent = max.toLocaleString();
            document.getElementById('maxPercent').textContent = maxChange.toFixed(2);
            updateCardStyle('maxCard', 'maxArrow', maxChange);

            // Update Std Card
            document.getElementById('stdValue').textContent = std.toLocaleString();
            document.getElementById('stdPercent').textContent = stdChange.toFixed(2);
            updateCardStyle('stdCard', 'stdArrow', stdChange);

            // Update Avg Card
            document.getElementById('avgValue').textContent = avg.toLocaleString();
            document.getElementById('avgPercent').textContent = avgChange.toFixed(2);
            updateCardStyle('avgCard', 'avgArrow', avgChange);
        }

        function updateCardStyle(cardId, arrowId, change) {
            const card = document.getElementById(cardId);
            const arrow = document.getElementById(arrowId);

            card.classList.remove('bg-red-light', 'bg-green', 'bg-lightblue');
            arrow.classList.remove('icon-[line-md--arrow-up]', 'icon-[line-md--arrow-down]');

            if (change > 0) {
                card.classList.add('bg-green');
                arrow.classList.add('icon-[line-md--arrow-up]');
            } else {
                card.classList.add('bg-red-light');
                arrow.classList.add('icon-[line-md--arrow-down]');
            }
        }
    </script>
    <script>
        function handleMonthChange() {
            const selectedMonth = document.getElementById('timePeriod').value;
            console.log('Selected month:', selectedMonth);
            // Add your logic here to handle the month change event
            getBranchReport();
            buildRegionTable();
        }
    </script>

    <script>
        function handleSubordinateChange() {
            const selectedValue = document.getElementById('subordinateSelect').value;
            console.log('Selected subordinate:', selectedValue);
            // Add your logic here to handle the change event
            getBranchReport();
            buildRegionTable();
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetch('/api/getSubordinate')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('subordinateSelect');
                    data.forEach(subordinate => {
                        const option = document.createElement('option');
                        option.value = subordinate.user_id;
                        option.textContent = `${subordinate.role_name} - ${subordinate.name}`;
                        select.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching subordinates:', error));
        });
    </script>

    <script>
        // fetch data from API /api/getBranchReport
        // search param user_id, date

        function getBranchReport() {
            const userId = document.getElementById('subordinateSelect') ?
                document.getElementById('subordinateSelect').value :
                            {{ session()->get('user')->user_id }};
            const date = document.getElementById('timePeriod') ?
                document.getElementById('timePeriod').value :
                new Date().toISOString().slice(0, 7); // Ensure YYYY-MM format

            fetch(`/api/getBranchReport?user_id=${userId}&date=${date}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Branch report:', data);

                    const branchCount = data.branch_count;
                    const branches = data.branches;

                    let allMonthlySales = {};
                    let thisMonthTotalMoneyRange = {};
                    let maxRange = 0;
                    let selectedMonth = date.slice(0, 7); // Extract YYYY-MM format

                    // Calculate max sales amount only for the selected month
                    branches.forEach(b => {
                        let monthlySales = b.monthly_sales || {};
                        if (monthlySales[selectedMonth]) {

                            let salesAmount = parseFloat(monthlySales[selectedMonth]?.sales_amount || 0);
                            maxRange = Math.max(maxRange, salesAmount);
                        }
                    });

                    // Determine bin size: at least 1000, at most 20 bins
                    let step = Math.ceil(Math.max(1000, maxRange / 20));
                    let numBins = Math.ceil(maxRange / step);

                    // Ensure there are at most 20 bins
                    if (numBins > 20) {
                        step = Math.ceil(maxRange / 20);
                        numBins = 20;
                    }

                    let chartLabels = [];
                    let chartData = {};

                    // Initialize bins to 0
                    for (let i = 0; i <= maxRange; i += step) {
                        if (i === 0) {
                            chartLabels.push("0");
                        } else {
                            chartLabels.push(`${Math.round(i / 1000)}k`);
                        }
                        chartData[i] = 0;
                    }

                    // Fill in the sales data only for the selected month
                    branches.forEach(b => {
                        console.log(1, b)
                        let monthlySales = b.monthly_sales || {};
                        if (monthlySales[selectedMonth]) {
                            let salesAmount = parseFloat(monthlySales[selectedMonth]?.sales_amount || 0);
                            let range = Math.floor(salesAmount / step) * step;
                            chartData[range] += 1;
                        }
                    });

                    // Convert chartData object to an array for Chart.js
                    let chartValues = Object.keys(chartData).map(key => chartData[key]);

                    console.log("Chart Labels:", chartLabels);
                    console.log("Chart Data:", chartValues);


                    const ctx = document.getElementById('branchVSprofit').getContext('2d');
                    if (window.branchChart) {
                        window.branchChart.destroy();
                    }
                    window.branchChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: chartLabels,
                            datasets: [{
                                label: 'จำนวนสาขา', // "Number of Branches"
                                data: chartValues,
                                backgroundColor: '#F846E1',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: Math.max(...chartValues) + 5
                                }
                            }
                        }
                    });

                    // Summing up monthly sales data
                    branches.forEach(b => {
                        let monthlySales = b.monthly_sales;
                        Object.entries(monthlySales).forEach(([key, value]) => {
                            if (allMonthlySales[key]) {
                                allMonthlySales[key]['sales_amount'] += parseFloat(value[
                                    'sales_amount']);
                                allMonthlySales[key]['sales_package_amount'] += parseFloat(value[
                                    'sales_package_amount']);
                            } else {
                                allMonthlySales[key] = {
                                    'sales_amount': parseFloat(value['sales_amount']),
                                    'sales_package_amount': parseFloat(value[
                                        'sales_package_amount'])
                                };
                            }
                        });
                    });

                    console.log('Filtered Monthly Sales:', allMonthlySales);

                    let thisMonthTotalPackage = allMonthlySales[selectedMonth]?.sales_package_amount ?? 0;
                    let thisMonthTotalSales = allMonthlySales[selectedMonth]?.sales_amount ?? 0;

                    let lastMonth = new Date(new Date(date).setMonth(new Date(date).getMonth() - 1)).toISOString()
                        .slice(0, 7);
                    let lastMonthTotalPackage = allMonthlySales[lastMonth]?.sales_package_amount ?? 0;
                    let lastMonthTotalSales = allMonthlySales[lastMonth]?.sales_amount ?? 0;

                    let packageChange = lastMonthTotalPackage > 0 ? ((thisMonthTotalPackage - lastMonthTotalPackage) /
                        lastMonthTotalPackage) * 100 : 0;
                    let salesChange = lastMonthTotalSales > 0 ? ((thisMonthTotalSales - lastMonthTotalSales) /
                        lastMonthTotalSales) * 100 : 0;

                    document.getElementById('thisMonthTotalPackageNumber').textContent = thisMonthTotalPackage
                        .toLocaleString();
                    document.getElementById('thisMonthTotalPackagePercent').textContent = packageChange.toFixed(2);
                    document.getElementById('thisMonthTotalMoneyNumber').textContent = thisMonthTotalSales
                        .toLocaleString();
                    document.getElementById('thisMonthTotalMoneyPercent').textContent = salesChange.toFixed(2);

                    updateIndicator('thisMonthTotalPackage', packageChange);
                    updateIndicator('thisMonthTotalMoney', salesChange);

                    let salesArray = [];
                    let lastMonthSalesArray = [];

                    branches.forEach(branch => {
                        if (branch.monthly_sales[selectedMonth]) {
                            salesArray.push(parseFloat(branch.monthly_sales[selectedMonth].sales_amount || 0));
                        }
                        if (branch.monthly_sales[lastMonth]) {
                            lastMonthSalesArray.push(parseFloat(branch.monthly_sales[lastMonth].sales_amount ||
                                0));
                        }
                    });

                    // Calculate statistics
                    let min = Math.min(...salesArray);
                    let max = Math.max(...salesArray);
                    let avg = salesArray.reduce((a, b) => a + b, 0) / salesArray.length || 0;
                    let std = Math.sqrt(salesArray.map(x => Math.pow(x - avg, 2)).reduce((a, b) => a + b, 0) /
                        salesArray.length) || 0;

                    // Calculate changes compared to last month
                    let lastMin = Math.min(...lastMonthSalesArray);
                    let lastMax = Math.max(...lastMonthSalesArray);
                    let lastAvg = lastMonthSalesArray.reduce((a, b) => a + b, 0) / lastMonthSalesArray.length || 0;
                    let lastStd = Math.sqrt(lastMonthSalesArray.map(x => Math.pow(x - lastAvg, 2)).reduce((a, b) => a +
                        b, 0) / lastMonthSalesArray.length) || 0;

                    let minChange = lastMin > 0 ? ((min - lastMin) / lastMin) * 100 : 0;
                    let maxChange = lastMax > 0 ? ((max - lastMax) / lastMax) * 100 : 0;
                    let avgChange = lastAvg > 0 ? ((avg - lastAvg) / lastAvg) * 100 : 0;
                    let stdChange = lastStd > 0 ? ((std - lastStd) / lastStd) * 100 : 0;

                    // Update cards
                    updateCardData({
                        min,
                        max,
                        std,
                        avg,
                        minChange,
                        maxChange,
                        stdChange,
                        avgChange
                    });


                })
                .catch(error => console.error('Error fetching branch report:', error));
        }

        function updateIndicator(prefix, change) {
            const icon = document.getElementById(`${prefix}Icon`);
            const arrow = document.getElementById(`${prefix}Arrow`);
            const percentParent = document.getElementById(`${prefix}PercentParent`);

            icon.classList.remove('text-success', 'text-danger');
            arrow.classList.remove('icon-[line-md--arrow-up]', 'icon-[line-md--arrow-down]');
            percentParent.classList.remove('text-success', 'text-danger');

            if (change > 0) {
                icon.classList.add('text-success');
                arrow.classList.add('icon-[line-md--arrow-up]');
                percentParent.classList.add('text-success');
            } else {
                icon.classList.add('text-danger');
                arrow.classList.add('icon-[line-md--arrow-down]');
                percentParent.classList.add('text-danger');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            console.log("Fetching report...");
            getBranchReport();
        });
    </script>
@endsection