@extends('layouts.main')

@section('title', 'Branch')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-2xl font-bold text-gray-800">สาขาทั้งหมด</h2>

            <a href="{{ route('branch.create') }}">
                <button class="hover:bg-blue-700 text-white shadow-lg font-bold py-2 px-4 rounded-md whitespace-nowrap border border-gray-300 " style="background-color: #3062B8">
                    สร้างสาขา
                </button>
            </a>
        </div>

        <!-- Search Input -->
        <input type="text" id="searchInput" placeholder="ค้นหาชื่อ อีเมล หรือบทบาท" class="w-full p-2 border border-gray-300 rounded mb-3">

        <!-- Dropdowns -->
        <div class="mb-3">
            <label class="block text-gray-800 mb-1">บทบาท</label>
            <select class="w-full p-2 border border-gray-300 rounded-md shadow-lg">
                <option>Sale</option>
                <option>CEO</option>
                <option>Sale Supervisor</option>
            </select>
        </div>

        <!-- Result Count -->
        <p class="text-gray-800">ผลลัพธ์ 0 รายการ</p>
    </div>

    <!-- Pagination Controls -->
    <div class="overflow-visible">
        <table class="w-full mt-5 border-collapse rounded-md overflow-hidden table-fixed">
            <thead class=" text-gray-800" style="background-color: #B5CFF5">
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
            { id: 7, name: "กรุงเทพฯ", type: "ร้านอาหาร", province: "กรุงเทพ" },
            { id: 8, name: "ปราจีนบุรี", type: "ร้านกาแฟ", province: "ปราจีนบุรี" },
            { id: 9, name: "ฉะเชิงเทรา", type: "ตลาด", province: "ฉะเชิงเทรา" },
            { id: 10, name: "สระบุรี", type: "ร้านขนม", province: "สระบุรี" },
            { id: 11, name: "แหลมแท่น", type: "ที่เที่ยว", province: "ชลบุรี" }
        ]; 
        for (let i = 12; i <= 50; i++) {
            branches.push({
                id: i,
                name: `${i}`,
                type: `${i % 5 === 0 ? 'ร้านกาแฟ' : i % 5 === 1 ? 'ร้านอาหาร' : i % 5 === 2 ? 'ร้านขนม' : i % 5 === 3 ? 'ผับบาร์' : 'ศูนย์การค้า'}`,
                province: `${i % 5 === 0 ? 'อุดรธานี' : i % 5 === 1 ? 'ชลบุรี' : i % 5 === 2 ? 'กรุงเทพฯ' : i % 5 === 3 ? 'ขอนแก่น' : 'เชียงใหม่'}`,
            });
        }
        // Your existing data
        let currentPage = 1;
        const rowsPerPage = 10;
        let currentSort = { column: null, ascending: true };

        function renderTable(filteredData = null) {
            const tableBody = document.getElementById("tableBody");
            tableBody.innerHTML = "";

            const dataToRender = filteredData || branches;

            const start = (currentPage - 1) * rowsPerPage;
            const paginatedData = branches.slice(start, start + rowsPerPage);

            // แสดงจำนวนผลลัพธ์
            const resultCount = document.querySelector("#resultCount");
            resultCount.textContent = `ผลลัพธ์ ${dataToRender.length} รายการ`;

            paginatedData.forEach((branch) => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td class="py-3 px-4 w-16">${branch.id}</td>
                    <td class="py-3 px-4 truncate">${branch.name}</td>
                    <td class="py-3 px-4 w-32 truncate">${branch.type}</td>
                    <td class="py-3 px-4 w-32 truncate">${branch.province}</td>
                    <td class="py-3 px-1 w-10 text-center relative">
                        <button class="cursor-pointer" onclick="toggleMenu(event, ${branch.id})">&#8230;</button>
                        <div id="menu-${branch.id}" class="hidden absolute right-0 mt-2 bg-white shadow-lg rounded-xl w-32 z-50 p-2 space-y-2">
                            <button class="block w-full px-4 py-2 text-white border border-gray-400 rounded-md shadow-lg hover:bg-blue-700 cursor-pointer" style="background-color: #3062B8"
                            onclick="window.location.href='{{ route('branch.manage.index') }}'">จัดการ</button>
                            <button class="block w-full px-4 py-2 text-white rounded-md border border-gray-400 shadow-lg hover:bg-blue-700 cursor-pointer" style="background-color: #3062B8"
                            onclick="window.location.href='{{ route('branch.edit') }}'">แก้ไข</button>
                            <button class="block w-full px-4 py-2 text-white border rounded-md border-gray-400 shadow-lg hover:bg-red-700 cursor-pointer" onclick="deleteBranch(${branch.id})" style="background-color: #CF3434">ลบ</button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            renderPagination(dataToRender);
        }

        function renderPagination(dataToRender) {
            const pagination = document.getElementById("pagination");
            pagination.innerHTML = ""; // Clear previous pagination

            const totalPages = Math.ceil(branches.length / rowsPerPage);

            // Previous button
            const prevBtn = document.createElement("button");
            prevBtn.innerHTML = '<span class="icon-[material-symbols--chevron-left-rounded]"></span>';
            prevBtn.className = `px-3 py-1 ${currentPage === 1 ? "text-gray-800 cursor-not-allowed" : "text-blue-600 cursor-pointer"} text-5xl`;
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
            nextBtn.className = `px-3 py-1 ${currentPage === totalPages ? "text-gray-800 cursor-not-allowed" : "text-blue-600 cursor-pointer"} text-5xl`;
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

        // ฟังก์ชันสำหรับจัดการการคลิกที่ปุ่ม "..."
        function editBranch(id) { alert(`แก้ไขข้อมูลของ ID ${id}`); }
        function deleteBranch(id) {
            Swal.fire({
                title: "ลบสถานที่ที่สนใจ",
                text: "คุณต้องการลบสถานที่ที่สนใจ ใช่หรือไม่",
                icon: "warning",
                iconColor: "#d33",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3062B8",
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
@endsection
