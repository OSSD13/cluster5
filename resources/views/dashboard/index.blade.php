@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
    <div class="flex flex-col gap-4">
        {{-- report card --}}
        <div class="mt-2 bg-white shadow-md rounded-lg p-6 flex flex-col gap-3 ">
            <h3 class="text-2xl font-bold text-center text-gray-800">รายงาน</h3>
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
                            {{ session()->get('user')->name }}</option>
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

                document.addEventListener('DOMContentLoaded', function() {
                    console.log("Fetching report...");
                    getBranchReport();
                });
            </script>

        </div>
        {{-- stat cards --}}
        <div class="flex flex-row gap-4">
            <div class="flex-1 bg-white shadow-md rounded-lg flex flex-col p-4 gap-4">
                <div class="">ยอดพัสดุทั้งหมด</div>
                <div class="flex justify-around items-center">
                    <span class="icon-[streamline--upload-box-1-solid] text-trinary flex-grow" style="font-size: 55px;" id='thisMonthTotalPackageIcon'></span>
                    <span class="font-bold text-trinary flex-grow text-right" style="font-size: 28px " id="thisMonthTotalPackageNumber"></span>
                    <span class="font-medium text-xl flex-grow-0 text-right p-4">ชิ้น</span>
                </div>
                <div class="text-success text-base text-end font-medium " id='thisMonthTotalPackagePercentParent'>
                    <span class="icon-[line-md--arrow-up]" id='thisMonthTotalPackageArrow'></span>
                    <span id='thisMonthTotalPackagePercent'></span>% จากก่อนหน้านี้
                </div>
            </div>
        </div>

        <div class="flex flex-row gap-4">
            <div class="flex-1 bg-green shadow-md rounded-lg flex flex-col p-4 gap-2">
                <div class="">ยอดรายได้ทั้งหมด</div>
                <div class="flex justify-around items-center">
                    <span class="icon-[tabler--coin-filled] text-green-600 flex-grow" style="font-size: 75px; flex-grow :3" id='thisMonthTotalMoneyIcon'></span>
                    <span class="font-bold text-green-600 flex-grow" style="font-size: 28px;" id='thisMonthTotalMoneyNumber'></span>
                    <span class="font-black text-xl flex-grow-0 text-right p-4 text-green-600">บาท</span>
                </div>
                <div class="text-success text-base font-medium text-end" id='thisMonthTotalMoneyPercentParent'>
                    <span class="icon-[line-md--arrow-up]" id='thisMonthTotalMoneyArrow'></span>
                    <span id='thisMonthTotalMoneyPercent'></span>% จากก่อนหน้านี้
                </div>
            </div>
        </div>

<!--  -->
        <div class="bg-purpur shadow-md rounded-lg p-6 flex flex-col">
            <canvas id="branchVSprofit"></canvas>
        </div>
        <div class="flex flex-col gap-4">
            <div class="flex flex-row gap-4">
                <div id="minCard" class="flex-1 shadow-md rounded-lg flex flex-col p-4 gap-2 text-red-dark" style="background-color: #F2DDD4;">
                    <div class="font-bold" style="font-size: 13px; color: black;"  >Min</div>
                    <div class="flex justify-center items-center text-bold gap-2">
                        <span id="minValue" class="text-2xl font-bold" style="font-size: 18px">0</span>
                        <span class="text-2xl font-bold" style="font-size: 16px">บาท</span>
                    </div>
                    <div id="minChange" class="text-sm text-end">
                        <span id="minArrow" class="icon-[line-md--arrow-down]"></span>
                        <span id="minPercent">0</span>%
                    </div>
                </div>
                <div id="maxCard" class="flex-1 shadow-md rounded-lg flex flex-col p-4 gap-2 text-success " style="background-color: #D6F2D4;">
                    <div class="font-bold" style="font-size: 13px; color: black;">Max</div>
                    <div class="flex justify-center items-center text-bold gap-2">
                        <span id="maxValue" class="text-2xl font-bold" style="font-size: 18px">0</span>
                        <span class="text-2xl font-bold" style="font-size: 16px">บาท</span>
                    </div>
                    <div id="maxChange" class="text-sm text-end">
                        <span id="maxArrow" class="icon-[line-md--arrow-up]"></span>
                        <span id="maxPercent">0</span>%
                    </div>
                </div>
            </div>
            <div class="flex flex-row gap-4">
                <div id="stdCard" class="flex-1 shadow-md rounded-lg flex flex-col p-4 gap-2 text-primary-dark " style="background-color: #FAEAFF;">
                    <div class="font-bold" style="font-size: 13px; color:black;" >Standard Deviation</div>
                    <div class="flex justify-center items-center text-bold gap-2" style ="color: #DA25BF;">
                        <span id="stdValue" class="text-2xl font-bold" style="font-size: 18px">0</span>
                        <span class="text-2xl font-bold" style="font-size: 16px">บาท</span>
                    </div>
                    <div id="stdChange" class="text-sm text-end "style ="color: #DA25BF;"> 
                        <span id="stdArrow" class="icon-[line-md--arrow-down]" ></span>
                        <span id="stdPercent">0</span>%
                    </div>
                </div>
                <div id="avgCard" class="flex-1 shadow-md rounded-lg flex flex-col p-4 gap-2 text-primary-dark" style="background-color: #FAEAFF;">
                    <div class="font-bold" style="font-size: 13px; color: black;" >Average</div>
                    <div class="flex justify-center items-center text-bold gap-2"style ="color: #DA25BF;">
                        <span id="avgValue" class="text-2xl font-bold" style="font-size: 18px">0</span>
                        <span class="text-2xl font-bold" style="font-size: 16px">บาท</span>
                    </div>
                    <div id="avgChange" class="text-sm text-end" style ="color: #DA25BF;">
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
                document.getElementById('minPercent').textContent = minChange.toFixed(2);
                updateCardStyle('minCard', 'minArrow', minChange);

                // Update Max Card
                document.getElementById('maxValue').textContent = max.toLocaleString();
                document.getElementById('maxPercent').textContent = maxChange.toFixed(2);
                updateCardStyle('maxCard', 'maxArrow', maxChange);

                // Update Std Card
                document.getElementById('stdValue').textContent = std.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('stdPercent').textContent = stdChange.toFixed(2);
            updateCardStyle('stdCard', 'stdArrow', stdChange);


                // Update Avg Card
                document.getElementById('avgValue').textContent = avg.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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

        <div class="grid grid-cols-3  gap-4  bg-lightblue shadow-md rounded-lg p-4 " style="background-color: #B8E0F8">
            <div class='col-span-1'>
                <button id="regionTableBack" class="cursor-pointer px-4 py-2 bg-primary-dark text-white rounded"
                    onclick="">ย้อนกลับ</button>
            </div>
            <div class="flex justify-center text-primary-dark text-4xl font-bold col-span-1 whitespace-nowrap">
                 ภูมิภาค
            </div>
        </div>

        <h3 class="text-left px-2" id='regionBranchCount'></h3>
        <div style="resize: both; overflow: auto; max-width: 100%;">
        <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden" id="regionTable">
            <thead class="bg-lightblue" style="background-color: #B6D2FF">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-base font-medium text-gray-500 uppercase tracking-wider" style="color: black">#</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-base font-medium text-gray-500 uppercase tracking-wider" style="color: black">ภูมิภาค</th>
                    <th scope="col"
                        class="px-7 py-3 text-left text-base font-medium text-gray-500 uppercase tracking-wider hitespace-nowrap" style="color: black">จำนวนสาขา</th>
                    <th scope="col" class="px-6 py-3" id="regionBranchCount"></th>
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

            function buildRegionTable() {
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
                fetch('/api/getRegionBranch?' + new URLSearchParams({
                        date,
                        user_id
                    }).toString())
                    .then(response => response.json())
                    .then(data => {
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
                        hideBackButton();
                        showRegionTable();
                        setBackButtonOnClick(() => {
                            buildRegionTable();
                        });
                    }).catch(error => console.error('Error fetching region branch data:', error));
            }

            function buildProvinceTable(region) {
                // fetch /api/getRegionBranch?region=SOUTH
                // example response
                // {
                //     "distinct_provinces": [
                //         "กระบี่",
                //         "ชุมพร",
                //         "ตรัง",
                //         "นครศรีธรรมราช",
                //         "นราธิวาส",
                //         "ปัตตานี",
                //         "พังงา",
                //         "พัทลุง",
                //         "ภูเก็ต",
                //         "ยะลา",
                //         "ระนอง",
                //         "สงขลา",
                //         "สตูล",
                //         "สุราษฎร์ธานี"
                //     ],
                //     "branch_count_by_province": [{
                //             "province": "กระบี่",
                //             "branch_count": 3
                //         },
                //         {
                //             "province": "ชุมพร",
                //             "branch_count": 2
                //         },
                //         {
                //             "province": "ตรัง",
                //             "branch_count": 1
                //         },
                //         {
                //             "province": "นครศรีธรรมราช",
                //             "branch_count": 2
                //         },
                //         {
                //             "province": "ปัตตานี",
                //             "branch_count": 5
                //         },
                //         {
                //             "province": "ภูเก็ต",
                //             "branch_count": 1
                //         },
                //         {
                //             "province": "สุราษฎร์ธานี",
                //             "branch_count": 11
                //         }
                //     ]
                // }
                const date = document.getElementById('timePeriod') ?
                    document.getElementById('timePeriod').value :
                    new Date().toISOString().slice(0, 7); // Ensure YYYY-MM format
                const user_id = document.getElementById('subordinateSelect') ?
                    document.getElementById('subordinateSelect').value :
                    {{ session()->get('user')->user_id }}
                fetch('/api/getRegionBranch?' + new URLSearchParams({
                        region,
                        date,
                        user_id
                    }).toString())
                    .then(response => response.json())
                    .then(data => {
                        console.log('Province Branch Data:', data);
                        clearTableBody();
                        const provinceTableBody = document.getElementById('regionTableBody');
                        provinceTableBody.innerHTML = ''; // Clear existing data
                        data.branch_count_by_province.forEach((province, index) => {
                            let row = `<tr class="cursor-pointer" onclick="buildBranchesTable('${region}', '${province.province}')">
                                <td class="px-6 py-2 whitespace-nowrap">${index + 1}</td>
                                <td class="px-6 py-2 whitespace-nowrap">${province.province}</td>
                                <td class="px-6 py-2 whitespace-nowrap text-center">${province.branch_count}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-center"><span class="icon-[material-symbols--chevron-right-rounded]"></span></td>
                            </tr>`;
                            provinceTableBody.innerHTML += row;
                        });
                        let branchCount = data.branch_count_by_province.reduce((acc, region) => acc + region.branch_count,
                            0);
                        document.getElementById("regionBranchCount").textContent = `สาขาทั้งหมด ${branchCount} สาขา`;
                        showBackButton();
                        showRegionTable();
                        setBackButtonOnClick(() => {
                            buildRegionTable();
                        });
                    })
                    .catch(error => console.error('Error fetching province branch data:', error));
            }

            function buildBranchesTable(region, province) {
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
                    {{ session()->get('user')->user_id }}
                fetch('/api/getRegionBranch?' + new URLSearchParams({
                        region,
                        province,
                        date,
                        user_id
                    }).toString())
                    .then(response => response.json())
                    .then(data => {
                        console.log('Branches Data:', data);
                        clearTableBody();
                        const tableBody = document.getElementById('tableBody');
                        tableBody.innerHTML = ''; // Clear existing data
                        data.branches.forEach((branch, index) => {
                            let row = `<tr class="hover:bg-gray-100">
                                <td class="py-3 px-4 whitespace-nowrap">${branch.branchId}</td>
                                <td class="py-3 px-4 whitespace-nowrap">${branch.branchName}</td>
                                <td class="py-3 px-4 whitespace-nowrap">${branch.branchSaleChange.toFixed(2)}</td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="px-3 py-1 text-white rounded-full ${branch.saleAdded ? "bg-green-500" : "bg-red-500"}">
                                        ${branch.saleAdded ? "เพิ่มแล้ว" : "ยังไม่เพิ่ม"}
                                    </span>
                                </td>
                            </tr>`;
                            tableBody.innerHTML += row;
                        });
                        hideRegionTable();
                        showBackButton();
                        setBackButtonOnClick(() => {
                            buildProvinceTable(region);
                        });
                        document.getElementById("regionBranchCount").textContent = `สาขาทั้งหมด ${data.branch_count} สาขา`;
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
                    func();
                };
            }


            document.addEventListener('DOMContentLoaded', function() {
                buildRegionTable();
            });
        </script>

        <table class="w-full border-collapse rounded-lg overflow-hidden" id="branchTable" >
            <thead class="bg-blue-500 text-white" style="background-color: #B6D2FF">
                <tr>
                    <th class="py-3 px-4 text-left" style="color: black">ID</th>
                    <th class="py-3 px-4 text-left" style="color: black">ชื่อสาขา</th>
                    
                    <th class="py-3 px-4 text-left cursor-pointer" onclick="sortTable('sales')" style="color: black">
                        ยอดขาย ⬍
                    </th>
                    <th class="py-3 px-4 text-left cursor-pointer" onclick="sortTable('status')" style="color: black">
                        เพิ่มยอด ⬍
                    </th>
                </tr>
            </thead>
            <tbody id="tableBody" class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="py-3 px-4 whitespace-nowrap overflow-hidden text-ellipsis">1</td>
                    <td class="py-3 px-4 whitespace-nowrap overflow-hidden text-ellipsis">สาขา A</td>
                    
                    <td class="py-3 px-4 whitespace-nowrap overflow-hidden text-ellipsis">100,000 บาท</td>
                    <td class="py-3 px-4 whitespace-nowrap overflow-hidden text-ellipsis">เพิ่มแล้ว</td>
                </tr>
            </tbody>
        </table>

        <!-- Pagination Controls -->
        <div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>


    </div>
    
@endsection

@section('script')
    <script></script>
@endsection
