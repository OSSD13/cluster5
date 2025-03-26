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
                            data.forEach(b => {
                            data.forEach(b => {
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
                            data.forEach(b => {
                            data.forEach(b => {
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

                            data.forEach(b => {
                            data.forEach(b => {
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
                    <span class="icon-[streamline--upload-box-1-solid] text-4xl text-trinary"
                        id='thisMonthTotalPackageIcon'></span>
                    <span class="text-2xl text-bold text-trinary" id='thisMonthTotalPackageNumber'>1,354</span>ชิ้น
                </div>
                <div class="text-success text-sm text-end" id='thisMonthTotalPackagePercentParent'>
                    <span class="icon-[line-md--arrow-up]" id='thisMonthTotalPackageArrow'></span>
                    <span id='thisMonthTotalPackagePercent'>23</span>% จากก่อนหน้านี้
                </div>
            </div>
        </div>

        <div class="flex flex-row gap-4">
            <div class="flex-1 bg-white shadow-md rounded-lg flex flex-col p-4 gap-4">
                <div class="">ยอดรายได้ทั้งหมด</div>
                <div class="flex justify-around items-center">
                    <span class="icon-[tabler--coin-filled] text-4xl text-trinary" id='thisMonthTotalMoneyIcon'></span>
                    <span class="text-2xl text-bold text-trinary" id='thisMonthTotalMoneyNumber'>78,474</span>บาท
                </div>
                <div class="text-success text-sm text-end" id='thisMonthTotalMoneyPercentParent'>
                    <span class="icon-[line-md--arrow-up]" id='thisMonthTotalMoneyArrow'></span>
                    <span id='thisMonthTotalMoneyPercent'>23</span>% จากก่อนหน้านี้
                </div>
            </div>
        </div>

        {{-- <div class="flex-1 bg-green shadow-md rounded-lg flex flex-col p-4 gap-4">
            <div class="flex flex-row items-baseline">
                <div class="mr-4">ยอดรวม</div>
                <div class="text-success"><span class="icon-[line-md--arrow-up]"></span> 70% จากก่อนหน้านี้</div>
            </div>
            <div class="flex justify-around items-center text-success text-4xl text-bold">
                <span class="icon-[bi--graph-up]"></span>
                <span class="text-bold">87,456,722</span>
                บาท
            </div>
        </div> --}}


        <div class="bg-purpur shadow-md rounded-lg p-6 flex flex-col">
            <canvas id="branchVSprofit"></canvas>
        </div>
        <div class="flex flex-col gap-4">
            <div class="flex flex-row gap-4">
                <div class="flex-1 bg-red-light shadow-md rounded-lg flex flex-col p-4 gap-2 text-red-dark">
                    <div class="">Min</div>
                    <div class="flex justify-center items-center text-bold gap-2">
                        <span class="text-2xl text-bold">500</span>บาท
                    </div>
                    <div class="text-sm text-end">
                        <span class="icon-[line-md--arrow-down]"></span>
                        23% จากก่อนหน้านี้
                    </div>
                </div>
                <div class="flex-1 shadow-md rounded-lg flex flex-col p-4 gap-2 bg-green text-success">
                    <div class="">Min</div>
                    <div class="flex justify-center items-center text-bold gap-2">
                        <span class="text-2xl text-bold">500</span>บาท
                    </div>
                    <div class="text-sm text-end">
                        <span class="icon-[line-md--arrow-down]"></span>
                        23% จากก่อนหน้านี้
                    </div>
                </div>
            </div>
            <div class="flex flex-row gap-4">
                <div class="flex-1 shadow-md rounded-lg flex flex-col p-4 gap-2 bg-lightblue text-primary-dark">
                    <div class="">Standard Deviation</div>
                    <div class="flex justify-center items-center text-bold gap-2">
                        <span class="text-2xl text-bold">105.51</span>บาท
                    </div>
                    <div class="text-sm text-end">
                        <span class="icon-[line-md--arrow-down]"></span>
                        23% จากก่อนหน้านี้
                    </div>
                </div>
                <div class="flex-1 shadow-md rounded-lg flex flex-col p-4 gap-2 bg-lightblue text-primary-dark">
                    <div class="">Standard Deviation</div>
                    <div class="flex justify-center items-center text-bold gap-2">
                        <span class="text-2xl text-bold">13,000</span>บาท
                    </div>
                    <div class="text-sm text-end">
                        <span class="icon-[line-md--arrow-down]"></span>
                        23% จากก่อนหน้านี้
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 bg-lightblue shadow-md rounded-lg flex flex-col p-4 gap-4">
            <div class="flex justify-around items-center text-primary-dark text-4xl text-bold">
                ภูมิภาค
            </div>
        </div>

<<<<<<< HEAD
        <h3 class="text-left px-2">สาขาทั้งหมด 3500 สาขา</h3>
=======
<<<<<<< HEAD
        <h3 class="text-left px-2" id='regionBranchCount'>สาขาทั้งหมด 3500 สาขา</h3>
=======
        <h3 class="text-left px-2">สาขาทั้งหมด 3500 สาขา</h3>
>>>>>>> 014d5eb (fix(login):แก้ไขสวยๆ)
>>>>>>> origin/jeng-branch
        <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-lightblue">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ภูมิภาค</th>
                    <th scope="col" class="px-6 py-3" id="regionBranchCount"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="regionTableBody">
                @php
                    $regions = [
                        'NORTH' => 'ภาคเหนือ',
                        'NORTHEAST' => 'ภาคตะวันออกเฉียงเหนือ',
                        'WEST' => 'ภาคตะวันตก',
                        'CENTRAL' => 'ภาคกลาง',
                        'EAST' => 'ภาคตะวันออก',
                        'SOUTH' => 'ภาคใต้',
                    ];
                @endphp
                @foreach ($regions as $regionKey => $regionName)
                    <tr onclick="getReport('{{ $regionKey }}')" class="cursor-pointer">
                        <td class="px-6 py-2 whitespace-nowrap">{{ $loop->iteration }}</td>
                        <td class="px-6 py-2 whitespace-nowrap">{{ $regionName }}</td>
                    <th scope="col" class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php
                    $regions = ['ภาคเหนือ', 'ภาคตะวันออกเฉียงเหนือ', 'ภาคตะวันตก', 'ภาคกลาง', 'ภาคตะวันออก', 'ภาคใต้'];
                @endphp
                @foreach ($regions as $index => $region)
                    <tr onclick="alert('hi')" class="cursor-pointer">
                        <td class="px-6 py-2 whitespace-nowrap">{{ $index + 1 }}</td>
                        <td class="px-6 py-2 whitespace-nowrap">{{ $region }}</td>
                        <td class="px-6 py-2 whitespace-nowrap text-right text-indigo-600 hover:text-indigo-900">
                            >
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="w-full border-collapse rounded-lg overflow-hidden">
        <script>
            function getReport(region, province) {
                const userId = document.getElementById('subordinateSelect') ?
                    document.getElementById('subordinateSelect').value :
                    {{ session()->get('user')->user_id }};
                const date = document.getElementById('timePeriod') ?
                    document.getElementById('timePeriod').value :
                    new Date().toISOString().slice(0, 7);

                fetch(`/api/getBranchReport?user_id=${userId}&date=${date}${region ? `&region=${region}` : ''}${province ? `&province=${province}` : ''}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Filtered Branch Report:', data);


                        if (data.distinct_provinces) {
                            const tableBody = document.getElementById('regionTableBody');
                            tableBody.innerHTML = ''; // Clear existing data
                            data.distinct_provinces.forEach((province, index) => {
                                let row = `<tr class="cursor-pointer" onclick="getReport('${region}', '${province}')">
                                    <td class="px-6 py-2 whitespace-nowrap">${index + 1}</td>
                                    <td class="px-6 py-2 whitespace-nowrap">${province}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-right text-indigo-600 hover:text-indigo-900">></td>
                                </tr>`;
                                tableBody.innerHTML += row;
                            });
                        }

                        // update branchTable
                        const branchTable = document.getElementById('branchTable');
                        branchTable.innerHTML = '';
                        data.branches.forEach((branch, index) => {
                            // check if branch.monthly_sale 2025-03 #
                            let thisMonthSale = branch.monthly_sales[date];
                            let salesAdded = thisMonthSale ? 'เพิ่มแล้ว' : 'ยังไม่เพิ่ม';
                            let salesPrice = thisMonthSale ? thisMonthSale.sales_amount : '';
                            let row = `<tr class="hover:bg-gray-100">
                                <td class="py-3 px-4">${branch.bs_id}</td>
                                <td class="py-3 px-4">${branch.bs_name}</td>
                                <td class="py-3 px-4">${branch.province}</td>
                                <td class="py-3 px-4">${salesPrice}</td>
                                <td class="py-3 px-4">
                                    <span class="px-3 py-1 text-white rounded-full ${salesAdded === "เพิ่มแล้ว" ? "bg-green-500" : "bg-red-500"}">
                                        ${salesAdded}
                                    </span>
                                </td>
                            </tr>`;
                            branchTable.innerHTML += row;
                        });

                        // update pagination
                        const pagination = document.getElementById('pagination');
                        function renderPagination() {
                            const pagination = document.getElementById("pagination");
                            pagination.innerHTML = "";
                            const totalPages = Math.ceil(branches.length / rowsPerPage);

                            // Previous button
                            const prevBtn = document.createElement("button");
                            prevBtn.innerText = "Previous";
                            prevBtn.className = `px-3 py-1 ${currentPage === 1 ? "bg-gray-300" : "bg-gray-500 text-white"} rounded hover:bg-gray-400`;
                            prevBtn.disabled = currentPage === 1;
                            prevBtn.onclick = () => goToPage(currentPage - 1);
                            pagination.appendChild(prevBtn);

                            // Page buttons
                            for (let i = 1; i <= totalPages; i++) {
                                const btn = createPageButton(i);
                                pagination.appendChild(btn);
                            }

                            // Next button
                            const nextBtn = document.createElement("button");
                            nextBtn.innerText = "Next";
                            nextBtn.className = `px-3 py-1 ${currentPage === totalPages ? "bg-gray-300" : "bg-gray-500 text-white"} rounded hover:bg-gray-400`;
                            nextBtn.disabled = currentPage === totalPages;
                            nextBtn.onclick = () => goToPage(currentPage + 1);
                            pagination.appendChild(nextBtn);
                        }

                        function createPageButton(pageNumber) {
                            const btn = document.createElement("button");
                            btn.innerText = pageNumber;
                            btn.className = `px-3 py-1 ${pageNumber === currentPage ? "bg-blue-500 text-white" : "bg-gray-300"} rounded hover:bg-gray-400`;
                            btn.onclick = () => goToPage(pageNumber);
                            return btn;
                        }

                        function goToPage(page) {
                            currentPage = page;
                            renderTable();
                        }



                        document.getElementById("regionBranchCount").textContent = `สาขาทั้งหมด ${data.branch_count} สาขา`;

                    })
                    .catch(error => console.error('Error fetching region report:', error));
            }
        </script>

        <table class="w-full border-collapse rounded-lg overflow-hidden" id="branchTable">
        <table class="w-full border-collapse rounded-lg overflow-hidden">
            <thead class="bg-blue-500 text-white">
                <tr>
                    <th class="py-3 px-4 text-left">ID</th>
                    <th class="py-3 px-4 text-left">ชื่อสาขา</th>
                    <th class="py-3 px-4 text-left">จังหวัด</th>
                    <th class="py-3 px-4 text-left cursor-pointer" onclick="sortTable('sales')">
                        ยอดขาย ⬍
                    </th>
                    <th class="py-3 px-4 text-left cursor-pointer" onclick="sortTable('status')">
                        เพิ่มยอด ⬍
                    </th>
                </tr>
            </thead>
            <tbody id="tableBody" class="bg-white divide-y divide-gray-200"></tbody>
        </table>

        <!-- Pagination Controls -->
        <div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>
    </div>
@endsection

@section('script')
    <script></script>
@endsection
