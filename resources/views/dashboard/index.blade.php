@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
    <div class="flex flex-col gap-4">
        {{-- report card --}}
        <div class="mt-8 bg-white shadow-md rounded-lg p-6 flex flex-col gap-3 ">
            <h3 class="text-lg font-bold text-center">รายงาน</h3>
            <div class="flex items-center gap-4">
                <label for="timePeriod" class=" text-base font-bold text-black">ช่วงเวลา</label>
                <input type="month" id="timePeriod" name="timePeriod"
                    class="p-2 flex-1 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    onchange="handleMonthChange()" value="{{ \Carbon\Carbon::now()->format('Y-m') }}">
            </div>
            <script>
                function handleMonthChange() {
                    const selectedMonth = document.getElementById('timePeriod').value;
                    console.log('Selected month:', selectedMonth);
                    // Add your logic here to handle the month change event
                    getBranchReport();
                    buildRegionTable();
                }
            </script>

            @unless (session()->get('user')->role_name === 'sale')
                <div class="flex items-center gap-4">
                    <label for="timePeriod" class=" text-base font-bold text-black">พนักงาน</label>
                    <select
                        class="p-2 w-full flex-1 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        id="subordinateSelect" onchange="handleSubordinateChange()">
                        <option value="{{ session()->get('user')->user_id }}">{{ session()->get('user')->role_name }} -
                            {{ session()->get('user')->name }}
                        </option>
                    </select>
                </div>

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
                    document.addEventListener('DOMContentLoaded', function() {
                        fetch('{{ route('api.report.getSubordinate') }}')
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
            @endunless

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

                    fetch(`{{ route('api.report.getBranchReport') }}?user_id=${userId}&date=${date}`)
                        .then(response => response.json())
                        .then(data => {
                            console.log('Branch report:', data);

                            let branchCount = data.branch_count;
                            const branches = data.branches;
                            totalItems = branchCount;
                            console.log(totalItems);

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
                                        backgroundColor: '#3366C0',
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

                            let packageChange = lastMonthTotalPackage > 0 && isFinite(thisMonthTotalPackage) && isFinite(
                                    lastMonthTotalPackage) ?
                                ((thisMonthTotalPackage - lastMonthTotalPackage) / lastMonthTotalPackage) * 100 :
                                0;
                            let salesChange = lastMonthTotalSales > 0 && isFinite(thisMonthTotalSales) && isFinite(
                                    lastMonthTotalSales) ?
                                ((thisMonthTotalSales - lastMonthTotalSales) / lastMonthTotalSales) * 100 :
                                0;

                            document.getElementById('thisMonthTotalPackageNumber').textContent = thisMonthTotalPackage
                                .toLocaleString();
                            document.getElementById('thisMonthTotalPackagePercent').textContent = Math.abs(packageChange)
                                .toFixed(2);
                            document.getElementById('thisMonthTotalMoneyNumber').textContent = thisMonthTotalSales
                                .toLocaleString();
                            document.getElementById('thisMonthTotalMoneyPercent').textContent = Math.abs(salesChange).toFixed(
                            2);

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
                            let min = salesArray.length > 0 && salesArray.every(isFinite) ? Math.min(...salesArray) : 0;
                            let max = salesArray.length > 0 && salesArray.every(isFinite) ? Math.max(...salesArray) : 0;
                            let avg = salesArray.length > 0 && salesArray.every(isFinite) ?
                                salesArray.reduce((a, b) => a + b, 0) / salesArray.length :
                                0;
                            let std = salesArray.length > 0 && salesArray.every(isFinite) ?
                                Math.sqrt(salesArray.map(x => Math.pow(x - avg, 2)).reduce((a, b) => a + b, 0) / salesArray
                                    .length) :
                                0;

                            // Calculate changes compared to last month
                            let lastMin = lastMonthSalesArray.length > 0 && lastMonthSalesArray.every(isFinite) ? Math.min(...
                                lastMonthSalesArray) : 0;
                            let lastMax = lastMonthSalesArray.length > 0 && lastMonthSalesArray.every(isFinite) ? Math.max(...
                                lastMonthSalesArray) : 0;
                            let lastAvg = lastMonthSalesArray.length > 0 && lastMonthSalesArray.every(isFinite) ?
                                lastMonthSalesArray.reduce((a, b) => a + b, 0) / lastMonthSalesArray.length :
                                0;
                            let lastStd = lastMonthSalesArray.length > 0 && lastMonthSalesArray.every(isFinite) ?
                                Math.sqrt(lastMonthSalesArray.map(x => Math.pow(x - lastAvg, 2)).reduce((a, b) => a + b, 0) /
                                    lastMonthSalesArray.length) :
                                0;

                            let minChange = lastMin > 0 && isFinite(min) && isFinite(lastMin) ?
                                ((min - lastMin) / lastMin) * 100 :
                                0;
                            let maxChange = lastMax > 0 && isFinite(max) && isFinite(lastMax) ?
                                ((max - lastMax) / lastMax) * 100 :
                                0;
                            let avgChange = lastAvg > 0 && isFinite(avg) && isFinite(lastAvg) ?
                                ((avg - lastAvg) / lastAvg) * 100 :
                                0;
                            let stdChange = lastStd > 0 && isFinite(std) && isFinite(lastStd) ?
                                ((std - lastStd) / lastStd) * 100 :
                                0;

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

                document.addEventListener('DOMContentLoaded', function() {
                    console.log("Fetching report...");
                    getBranchReport();
                });
            </script>

        </div>
        {{-- stat cards --}}
        <div class="flex flex-row gap-4">
            <div class="flex-1 bg-white shadow-md rounded-lg flex flex-col p-4 gap-4">
                <div class="font-bold">ยอดพัสดุทั้งหมด (ชิ้น)</div>
                <div class="flex justify-around items-center">
                    <span class="icon-[streamline--upload-box-1-solid] text-trinary flex-grow: 3 font-medium"
                        style="font-size: 55px; flex-grow :1" id='thisMonthTotalPackageIcon'></span>
                    <span class="font-bold text-trinary flex-grow " style="font-size: 28px "
                        id="thisMonthTotalPackageNumber"></span>
                </div>
                <div class="text-success text-base text-end font-medium " id='thisMonthTotalPackagePercentParent'>
                    <span class="icon-[line-md--arrow-up]" id='thisMonthTotalPackageArrow'></span>
                    <span id='thisMonthTotalPackagePercent'></span>% จากก่อนหน้านี้
                </div>
            </div>
        </div>

        <div class="flex flex-row gap-4">
            <div class="flex-1 bg-green shadow-md rounded-lg flex flex-col p-4 gap-4">
                <div class="font-bold">ยอดรายได้ทั้งหมด (บาท) </div>
                <div class="flex justify-around items-center">
                    <span class="icon-[tabler--coin-filled] text-green-600 flex-grow font-medium"
                        style="font-size: 75px; flex-grow :1.5" id='thisMonthTotalMoneyIcon'></span>
                    <span class="font-bold text-green-600 flex-grow " style="font-size: 28px;"
                        id='thisMonthTotalMoneyNumber'></span>
                </div>
                <div class="text-success text-sm text-end" id='thisMonthTotalMoneyPercentParent'>
                    <span class="icon-[line-md--arrow-up]" id='thisMonthTotalMoneyArrow'></span>
                    <span id='thisMonthTotalMoneyPercent'></span>% จากก่อนหน้านี้
                </div>
            </div>
        </div>

        <!--  -->
        <div class="bg-purpur shadow-md rounded-lg p-6 flex flex-col" style="background-color:rgb(229, 238, 255)">
            <canvas id="branchVSprofit"></canvas>
        </div>
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
                <div id="maxCard" class="flex-1 shadow-md rounded-lg flex flex-col p-4 gap-2 text-success "
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
                <div id="stdCard" class="flex-1 shadow-md rounded-lg flex flex-col p-4 gap-2 text-primary-dark "
                    style="background-color: #FAEAFF;">
                    <div class="font-bold" style="font-size: 14px; color:black;">Standard Deviation (บาท)</div>
                    <div class="flex justify-center items-center text-bold gap-2" style="color: #DA25BF;">
                        <span id="stdValue" class="text-2xl font-bold" style="font-size: 20px">0</span>

                    </div>
                    <div id="stdChange" class="text-base text-end text-bold " style="color: #DA25BF;">
                        <span id="stdArrow" class="icon-[line-md--arrow-down]"></span>
                        <span id="stdPercent">0</span>%
                    </div>
                </div>
                <div id="avgCard" class="flex-1 shadow-md rounded-lg flex flex-col p-4 gap-2 text-primary-dark"
                    style="background-color: #FAEAFF;">
                    <div class="font-bold" style="font-size: 14px; color: black;">Average (บาท)</div>
                    <div class="flex justify-center items-center text-bold  text-base gap-2" style="color: #DA25BF;">
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
        <!--  -->
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
                document.getElementById('minPercent').textContent = Math.abs(minChange).toFixed(2);
                document.getElementById('minArrow').classList.remove('icon-[line-md--arrow-up]', 'icon-[line-md--arrow-down]');
                document.getElementById('minArrow').classList.add(minChange > 0 ? 'icon-[line-md--arrow-up]' :
                    'icon-[line-md--arrow-down]');

                // Update Max Card
                document.getElementById('maxValue').textContent = max.toLocaleString();
                document.getElementById('maxPercent').textContent = Math.abs(maxChange).toFixed(2);
                document.getElementById('maxArrow').classList.remove('icon-[line-md--arrow-up]', 'icon-[line-md--arrow-down]');
                document.getElementById('maxArrow').classList.add(maxChange > 0 ? 'icon-[line-md--arrow-up]' :
                    'icon-[line-md--arrow-down]');

                // Update Std Card
                document.getElementById('stdValue').textContent = std.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                document.getElementById('stdPercent').textContent = Math.abs(stdChange).toFixed(2);
                updateCardStyle('stdCard', 'stdArrow', stdChange);


                // Update Avg Card
                document.getElementById('avgValue').textContent = avg.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                document.getElementById('avgPercent').textContent = Math.abs(avgChange).toFixed(2);
                updateCardStyle('avgCard', 'avgArrow', avgChange);

            }

            function updateCardStyle(cardId, arrowId, change) {
                const card = document.getElementById(cardId);
                const arrow = document.getElementById(arrowId);

                if (change > 0) {
                    card.classList.add('bg-green');
                    arrow.classList.add('icon-[line-md--arrow-up]', 'text-success');
                } else {
                    card.classList.add('bg-red-light');
                    arrow.classList.add('icon-[line-md--arrow-down]', 'text-danger');
                }

            }
        </script>

        <!-- กรอบหัวเรื่องภูมิภาค -->
        <div class="w-full bg-lightblue shadow-md rounded-lg p-4 flex justify-center items-center"
            style="background-color: #B8E0F8">
            <div id="regionTitle" class="text-primary-dark font-bold text-4xl break-words whitespace-normal text-center">
                ภูมิภาค
            </div>
        </div>


        <!-- ปุ่มย้อนกลับวางใต้กรอบ -->
        <div class="mt-2 w-full">
            <button id="regionTableBack" class="w-full px-4 py-2 bg-primary-dark text-white rounded hidden text-center">
                ย้อนกลับ
            </button>
        </div>

        <h3 class="text-left px-2" id='regionBranchCount'></h3>
        <div class="overflow-x-auto w-full">
            <table class="table-auto w-full min-w-full divide-y divide-gray-200 rounded-lg" id="regionTable">
                <thead class="bg-lightblue" style="background-color: #B6D2FF">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-center font-medium text-gray-500 uppercase tracking-wider align-middle rounded-tl-lg"
                            style="color: black;">#</th>
                        <th scope="col"
                            class="py-3 text-left text-base font-medium text-gray-500 uppercase tracking-wider"
                            style="color: black; text-align: left; padding-left: 1rem; font-weight: 500;">
                            จังหวัด</th>
                        <th scope="col"
                            class="py-3 text-left text-base font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg"
                            style="color: black; text-align: left; padding-left: 1rem; font-weight: 500;">
                            จำนวนสาขา</th>
                        <th scope="col" class="py-3" id="regionBranchCount"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="regionTableBody">
                </tbody>
            </table>
        </div>



        <script>
            let region = null;
            let province = null;
            let branches = [];
            let currentPage = 1;
            let totalItems = 100; // Total number of items (you will fetch this from the server)
            const rowsPerPage = 10; // Number of items per page

            function renderPagination(type = 'region', region, province, page = 1) {
                console.log(totalItems);

                const pagination = document.getElementById("pagination");
                pagination.innerHTML = "";

                const totalPages = Math.ceil(totalItems / rowsPerPage);
                const maxVisible = 1; // Number of pages before and after the current page to display
                let startPage = Math.max(1, currentPage - maxVisible);
                let endPage = Math.min(totalPages, currentPage + maxVisible);

                if (totalPages <= 1) return; // If only one page, no pagination needed

                // Function to create a page button
                const createPageButton = (page, isActive = false) => {
                    const btn = document.createElement("button");
                    btn.innerText = page;
                    btn.className =
                        `min-w-[36px] h-10 px-3 mx-1 rounded-lg text-sm font-medium ${isActive ? "bg-blue-600 text-white" : "bg-white border border-gray-300 text-black hover:bg-gray-100"}`;
                    btn.onclick = () => goToPage(type, region, province, page); // Pass region and province too
                    return btn;
                };

                // Function to create ellipsis button ("...")
                const createEllipsis = () => {
                    const btn = document.createElement("button");
                    btn.innerText = "...";
                    btn.className = "px-3 text-gray-500 hover:text-black rounded hover:bg-gray-100";
                    btn.onclick = () => {
                        Swal.fire({
                            title: "Go to page...",
                            input: "number",
                            inputLabel: `Enter page number (1 - ${totalPages})`,
                            inputAttributes: {
                                min: 1,
                                max: totalPages,
                                step: 1
                            },
                            showCancelButton: true,
                            confirmButtonText: "Go!",
                            confirmButtonColor: "#3062B8",
                            inputValidator: (value) => {
                                if (!value || isNaN(value)) return "Please enter a valid number.";
                                if (value < 1 || value > totalPages)
                                    return `Page number must be between 1 and ${totalPages}.`;
                                return null;
                            }
                        }).then(result => {
                            if (result.isConfirmed) goToPage(type, region, province, parseInt(result.value));
                        });
                    };
                    return btn;
                };

                // Previous button
                const prevBtn = document.createElement("button");
                prevBtn.innerHTML = "&lt;";
                prevBtn.className =
                    `min-w-[40px] h-10 px-3 mx-1 rounded-lg text-xl font-bold ${currentPage === 1 ? "text-gray-300 bg-white border border-gray-200 cursor-not-allowed" : "text-blue-600 bg-white border border-gray-300 hover:bg-blue-50"}`;
                prevBtn.disabled = currentPage === 1;
                prevBtn.onclick = () => goToPage(type, region, province, currentPage - 1);
                pagination.appendChild(prevBtn);

                // Display first page button and ellipsis if necessary
                if (startPage > 1) {
                    pagination.appendChild(createPageButton(1));
                    if (startPage > 2) pagination.appendChild(createEllipsis());
                }

                // Display page numbers in the current range
                for (let i = startPage; i <= endPage; i++) {
                    pagination.appendChild(createPageButton(i, i === currentPage));
                }

                // Display last page button and ellipsis if necessary
                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) pagination.appendChild(createEllipsis());
                    pagination.appendChild(createPageButton(totalPages));
                }

                // Next button
                const nextBtn = document.createElement("button");
                nextBtn.innerHTML = "&gt;";
                nextBtn.className =
                    `min-w-[40px] h-10 px-3 mx-1 rounded-lg text-xl font-bold ${currentPage === totalPages ? "text-gray-300 bg-white border border-gray-200 cursor-not-allowed" : "text-blue-600 bg-white border border-gray-300 hover:bg-blue-50"}`;
                nextBtn.disabled = currentPage === totalPages;
                nextBtn.onclick = () => goToPage(type, region, province, currentPage + 1);
                pagination.appendChild(nextBtn);
            }

            // Function to navigate to a specific page
            function goToPage(type = 'region', region = null, province = null, pageNumber) {
                currentPage = pageNumber;

                if (type == 'region') {
                    buildRegionTable(pageNumber); // This should fetch data for the current page
                } else if (type == 'province') {
                    buildProvinceTable(region, pageNumber); // This should fetch data for the current page
                } else if (type == 'branches') {
                    buildBranchesTable(region, province, pageNumber); // This should fetch data for the current page
                }
                renderPagination(type, region, province); // Re-render pagination
            }


            // Call renderPagination whenever you need to update the pagination (e.g., after fetching data)


            function buildRegionTable(page = 1) {
                currentPage = page; // Ensure currentPage is set correctly
                totalItems = 100;
                // fetch /api/getRegionBranch
                // example response
                // {
                //     "distinct_regions": [
                //         "SOUTH",
                //         "CENTRAL",
                //         "WEST",
                //         "NORTHEAST",
                //         "EAST",
                //         "NORTH"
                //     ],
                //     "branch_count_by_region": [{
                //             "region": "NORTHEAST",
                //             "branch_count": 79
                //         },
                //         {
                //             "region": "CENTRAL",
                //             "branch_count": 64
                //         },
                //         {
                //             "region": "EAST",
                //             "branch_count": 8
                //         },
                //         {
                //             "region": "NORTH",
                //             "branch_count": 17
                //         },
                //         {
                //             "region": "SOUTH",
                //             "branch_count": 25
                //         },
                //         {
                //             "region": "WEST",
                //             "branch_count": 8
                //         }
                //     ]
                // }

                const regions = {
                    'NORTH': 'ภาคเหนือ',
                    'NORTHEAST': 'ภาคตะวันออกเฉียงเหนือ',
                    'WEST': 'ภาคตะวันตก',
                    'CENTRAL': 'ภาคกลาง',
                    'EAST': 'ภาคตะวันออก',
                    'SOUTH': 'ภาคใต้',
                };

                const date = document.getElementById('timePeriod') ?
                    document.getElementById('timePeriod').value :
                    new Date().toISOString().slice(0, 7); // Ensure YYYY-MM format
                const user_id = document.getElementById('subordinateSelect') ?
                    document.getElementById('subordinateSelect').value :
                    {{ session()->get('user')->user_id }}
                fetch('{{ route('api.report.getRegionBranch') }}?' + new URLSearchParams({
                        date,
                        user_id,
                        page: page || currentPage
                    }).toString())
                    .then(response => response.json())
                    .then(data => {
                        console.log('Branches Data:', data);
                        totalItems = data.branch_count;

                        console.log('Region Branch Data:', data);
                        clearTableBody();
                        const regionTableBody = document.getElementById('regionTableBody');
                        regionTableBody.innerHTML = ''; // Clear existing data
                        data.branch_count_by_region.forEach((region, index) => {
                            let row = `<tr class="cursor-pointer" onclick="buildProvinceTable('${region.region}')">
                                                        <td class="px-6 py-2 whitespace-nowrap">${index + 1}</td>
                                                        <td class="px-3 py-2 whitespace-nowrap">${regions[region.region]}</td>
                                                        <td class="px-3 py-2 whitespace-nowrap text-center">${region.branch_count}</td>
                                                        <td class="px-3 py-2 whitespace-nowrap text-center"><span class="icon-[material-symbols--chevron-right-rounded]"></span></td>
                                                     </tr>`;
                            regionTableBody.innerHTML += row;
                        });
                        let branchCount = data.branch_count_by_region.reduce((acc, region) => acc + region.branch_count, 0);
                        document.getElementById("regionBranchCount").textContent = `สาขาทั้งหมด ${branchCount} สาขา`;
                        // Render full branch list if available
                        if (data.branches) {
                            const tableBody = document.getElementById('tableBody');
                            tableBody.innerHTML = ''; // Clear previous rows
                            data.branches.forEach((branch, index) => {
                                console.log(branch)
                                let row = `
                                    <tr class="hover:bg-gray-100">
                                        <td class="py-2 px-2 text-center text-xs whitespace-nowrap">${branch.branchId}</td>
                                        <td class="py-2 px-2 text-xs whitespace-normal break-words max-w-[150px]" title="${branch.branchName}">
                                            ${branch.branchName}
                                        </td>
                                        <td class="py-2 px-2 text-right text-xs whitespace-nowrap ${
                                            branch.branchSaleChange > 0 ? 'text-green-600' :
                                            branch.branchSaleChange < 0 ? 'text-red-600' : 'text-black'
                                        }">
                                            ${
                                                typeof branch.branchSaleChange === 'number'
                                                    ? (branch.branchSaleChange > 0 ? '+ ' : branch.branchSaleChange < 0 ? '- ' : '') +
                                                    Math.abs(branch.branchSaleChange).toFixed(2) + '%'
                                                    : '-'
                                            }
                                        </td>

                                        <td class="py-2 px-2 text-center text-xs whitespace-nowrap">
                                            <span class="px-3 py-1 text-white rounded-full ${branch.saleAdded ? "bg-green-500" : "bg-red-500"}">
                                                ${branch.saleAdded ? "เพิ่มแล้ว" : "ยังไม่เพิ่ม"}
                                            </span>
                                        </td>
                                    </tr>
                                `;
                                tableBody.innerHTML += row;
                            });
                        }
                        hideBackButton();
                        showRegionTable();
                        setBackButtonOnClick(() => {
                            buildRegionTable();
                        });
                        renderPagination('region', region); // Render pagination for region table
                    }).catch(error => console.error('Error fetching region branch data:', error));


            }


            function buildProvinceTable(region, page = 1) {
                currentPage = page;
                const regions = {
                    'NORTH': 'ภาคเหนือ',
                    'NORTHEAST': 'ภาคตะวันออกเฉียงเหนือ',
                    'WEST': 'ภาคตะวันตก',
                    'CENTRAL': 'ภาคกลาง',
                    'EAST': 'ภาคตะวันออก',
                    'SOUTH': 'ภาคใต้',
                };

                // เปลี่ยนชื่อหัวเรื่อง (ใหญ่) เป็นชื่อภาค
                const regionTitle = document.getElementById('regionTitle');
                regionTitle.textContent = regions[region];

                // ✅ ใช้ฟอนต์ขนาดใหญ่เสมอ
                regionTitle.classList.remove('text-2xl');
                regionTitle.classList.add('text-4xl');

                // ✅ เปลี่ยนชื่อหัวตารางจาก "ภูมิภาค" เป็น "จังหวัด"
                const regionHeader = document.querySelector('#regionTable thead th:nth-child(2)');
                if (regionHeader) {
                    regionHeader.textContent = 'จังหวัด';
                }

                const date = document.getElementById('timePeriod') ?
                    document.getElementById('timePeriod').value :
                    new Date().toISOString().slice(0, 7);

                const user_id = document.getElementById('subordinateSelect') ?
                    document.getElementById('subordinateSelect').value :
                    {{ session()->get('user')->user_id }};

                fetch('{{ route('api.report.getRegionBranch') }}?' + new URLSearchParams({
                        region,
                        date,
                        user_id,
                        page: page || currentPage
                    }).toString())
                    .then(response => response.json())
                    .then(data => {
                        console.log('Province Branch Data:', data);
                        totalItems = data.branch_count;

                        clearTableBody();

                        const provinceTableBody = document.getElementById('regionTableBody');
                        provinceTableBody.innerHTML = ''; // Clear existing data

                        data.branch_count_by_province.forEach((province, index) => {
                            let row = `
                                            <tr class="cursor-pointer" onclick="buildBranchesTable('${region}', '${province.province}')">
                                                <td class="px-6 py-2 text-center align-middle whitespace-nowrap">${index + 1}</td>
                                                <td class="px-6 py-2 whitespace-nowrap">${province.province}</td>
                                                <td class="px-6 py-2 text-center whitespace-nowrap">${province.branch_count}</td>
                                                <td class="px-3 py-2 text-center whitespace-nowrap">
                                                    <span class="icon-[material-symbols--chevron-right-rounded]"></span>
                                                </td>
                                            </tr>`;
                            provinceTableBody.innerHTML += row;
                        });

                        let branchCount = data.branch_count_by_province.reduce((acc, p) => acc + p.branch_count, 0);
                        document.getElementById("regionBranchCount").textContent = `สาขาทั้งหมด ${branchCount} สาขา`;
                        // Render full branch list if available
                        if (data.branches) {
                            const tableBody = document.getElementById('tableBody');
                            tableBody.innerHTML = ''; // Clear previous rows

                            data.branches.forEach((branch, index) => {
                                let row = `
                                        <tr class="hover:bg-gray-100">
                                            <td class="py-2 px-2 text-center text-xs whitespace-nowrap">${branch.branchId}</td>
                                            <td class="py-2 px-2 text-xs whitespace-normal break-words max-w-[150px]" title="${branch.branchName}">
                                                ${branch.branchName}
                                            </td>
                                            <td class="py-2 px-2 text-right text-xs whitespace-nowrap ${
                                                branch.branchSaleChange > 0 ? 'text-green-600' :
                                                branch.branchSaleChange < 0 ? 'text-red-600' : 'text-black'
                                            }">
                                                ${
                                                    typeof branch.branchSaleChange === 'number'
                                                        ? (branch.branchSaleChange > 0 ? '+ ' : branch.branchSaleChange < 0 ? '- ' : '') +
                                                        Math.abs(branch.branchSaleChange).toFixed(2) + '%'
                                                        : '-'
                                                }
                                            </td>

                                            <td class="py-2 px-2 text-center text-xs whitespace-nowrap">
                                                <span class="px-3 py-1 text-white rounded-full ${branch.saleAdded ? "bg-green-500" : "bg-red-500"}">
                                                    ${branch.saleAdded ? "เพิ่มแล้ว" : "ยังไม่เพิ่ม"}
                                                </span>
                                            </td>
                                        </tr>
                                    `;
                                tableBody.innerHTML += row;
                            });
                        }
                        showBackButton();
                        showRegionTable();
                        setBackButtonOnClick(() => {
                            buildRegionTable();
                        });
                        renderPagination('province', region, ); // Render pagination for province table
                    })
                    .catch(error => console.error('Error fetching province branch data:', error));
            }



            function buildBranchesTable(region, province, page = 1) {
                currentPage = page;
                // fetch /api/getRegionBranch?region=SOUTH&province=กระบี่
                // example response
                // {
                //     "branches": [{
                //             "branchId": 74,
                //             "branchName": "Mrs. Concepcion Cremin DVM",
                //             "branchProvince": "กระบี่",
                //             "branchSaleChange": 88.00888230940048,
                //             "saleAdded": true
                //         },
                //         {
                //             "branchId": 8,
                //             "branchName": "Angus VonRueden",
                //             "branchProvince": "กระบี่",
                //             "branchSaleChange": -65.5929781923279,
                //             "saleAdded": true
                //         },
                //         {
                //             "branchId": 187,
                //             "branchName": "Ivy Russel",
                //             "branchProvince": "กระบี่",
                //             "branchSaleChange": -38.20272520633531,
                //             "saleAdded": true
                //         }
                //     ],
                //     "branch_count": 3
                // }

                const date = document.getElementById('timePeriod') ?
                    document.getElementById('timePeriod').value :
                    new Date().toISOString().slice(0, 7); // Ensure YYYY-MM format

                const user_id = document.getElementById('subordinateSelect') ?
                    document.getElementById('subordinateSelect').value :
                    {{ session()->get('user')->user_id }};

                fetch('{{ route('api.report.getRegionBranch') }}?' + new URLSearchParams({
                        region,
                        province,
                        date,
                        user_id,
                        page: page || currentPage
                    }).toString())
                    .then(response => response.json())
                    .then(data => {
                        console.log('Branches Data:', data);
                        totalItems = data.branch_count;
                        clearTableBody();

                        const tableBody = document.getElementById('tableBody');
                        tableBody.innerHTML = ''; // Clear existing data

                        data.branches.forEach((branch, index) => {
                            let row = `
                                <tr class="hover:bg-gray-100">
                                    <td class="py-2 px-2 text-center text-xs whitespace-nowrap">${branch.branchId}</td>
                                    <td class="py-2 px-2 text-xs whitespace-normal break-words max-w-[150px]" title="${branch.branchName}">
                                        ${branch.branchName}
                                    </td>
                                    <td class="py-2 px-2 text-right text-xs whitespace-nowrap ${
                                        branch.branchSaleChange > 0 ? 'text-green-600' :
                                        branch.branchSaleChange < 0 ? 'text-red-600' : 'text-black'
                                    }">
                                        ${
                                            typeof branch.branchSaleChange === 'number'
                                                ? (branch.branchSaleChange > 0 ? '+ ' : branch.branchSaleChange < 0 ? '- ' : '') +
                                                Math.abs(branch.branchSaleChange).toFixed(2) + '%'
                                                : '-'
                                        }
                                    </td>

                                    <td class="py-2 px-2 text-center text-xs whitespace-nowrap">
                                        <span class="px-3 py-1 text-white rounded-full ${branch.saleAdded ? "bg-green-500" : "bg-red-500"}">
                                            ${branch.saleAdded ? "เพิ่มแล้ว" : "ยังไม่เพิ่ม"}
                                        </span>
                                    </td>
                                </tr>
                                `;

                            tableBody.innerHTML += row;
                        });

                        // เปลี่ยนชื่อหัวเรื่องจากชื่อภูมิภาคเป็นชื่อจังหวัด
                        const regionTitle = document.getElementById('regionTitle');
                        regionTitle.textContent = province;

                        hideRegionTable(); // ซ่อนตารางภูมิภาค
                        showBackButton(); // แสดงปุ่มย้อนกลับ

                        setBackButtonOnClick(() => {
                            buildProvinceTable(region); // ย้อนกลับไปตารางจังหวัด
                        });

                        // อัปเดตจำนวนสาขา
                        document.getElementById("regionBranchCount").textContent = `สาขาทั้งหมด ${data.branch_count} สาขา`;
                        renderPagination('branches', region, province); // Render pagination for branches table
                    })
                    .catch(error => console.error('Error fetching branches data:', error));
            }

            function showBackButton() {
                const backButton = document.getElementById('regionTableBack');
                backButton.classList.remove('hidden');
            }

            function hideBackButton() {
                const backButton = document.getElementById('regionTableBack');
                backButton.classList.add('hidden');
            }

            function hideRegionTable() {
                const regionTable = document.getElementById('regionTable');
                regionTable.classList.add('hidden');
            }

            function showRegionTable() {
                const regionTable = document.getElementById('regionTable');
                regionTable.classList.remove('hidden');
            }

            function clearTableBody() {
                const tableBody = document.getElementById('tableBody');
                tableBody.innerHTML = ''; // Clear existing data
            }

            function setBackButtonOnClick(func) {
                const backButton = document.getElementById('regionTableBack');
                backButton.onclick = function() {
                    // เปลี่ยนชื่อหัวเรื่องกลับเป็น "ภูมิภาค"
                    const regionTitle = document.getElementById('regionTitle');
                    regionTitle.textContent = "ภูมิภาค";
                    regionTitle.classList.remove('text-2xl'); // ลบฟอนต์เล็ก
                    regionTitle.classList.add('text-4xl'); // คืนฟอนต์ใหญ่

                    // เรียกฟังก์ชันที่กำหนดไว้ (เช่น buildProvinceTable หรือ buildRegionTable)
                    func();
                };
            }


            document.addEventListener('DOMContentLoaded', function() {
                buildRegionTable();
            });
        </script>

        <div class="overflow-x-auto w-full">
            <table class="table-auto w-full border-collapse rounded-lg text-sm" id="branchTable"
                style="table-layout: fixed;">
                <thead class="bg-blue-500 text-white" style="background-color: #B6D2FF">
                    <tr>
                        <th class="px-2 py-2 text-center text-xs rounded-tl-lg" style="min-width: 40px; width: 10%;">ID</th>
                        <th class="px-2 py-2 text-left text-xs" style="min-width: 150px; width: 40%;">ชื่อสาขา</th>
                        <th class="px-2 py-2 text-right text-xs" style="min-width: 80px; width: 25%;">ยอดขาย</th>
                        <th class="px-2 py-2 text-center text-xs rounded-tr-lg" style="min-width: 100px; width: 25%;">เพิ่มยอด</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="py-2 px-2 text-center text-xs whitespace-nowrap">1</td>
                        <td class="py-2 px-2 text-xs whitespace-normal break-words max-w-[150px]"
                            title="Prof. Sabryna Tromp Sr.">
                            Prof. Sabryna Tromp Sr.
                        </td>
                        <td class="py-2 px-2 text-right text-xs whitespace-nowrap">-39.06</td>
                        <td class="py-2 px-2 text-center text-xs whitespace-nowrap">
                            <span class="bg-green-500 text-white px-4 py-1 rounded-full">เพิ่มแล้ว</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>


        <!-- Pagination Controls -->
        <div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>



    </div>
    <style>
        .truncate-cell {
            max-width: 150px;
            /* กำหนดความกว้างสูงสุดของเซลล์ */
            overflow: hidden;
            /* ซ่อนข้อความที่เกิน */
            text-overflow: ellipsis;
            /* เพิ่ม ... เมื่อข้อความยาวเกิน */
            white-space: nowrap;
            /* ป้องกันการตัดบรรทัด */
        }

        table {
            boarder-radius: 12px;
            overflow: hidden;
        }
    </style>
@endsection

@section('script')
    <script></script>
@endsection
