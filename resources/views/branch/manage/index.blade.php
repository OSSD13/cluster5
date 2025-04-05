@extends('layouts.main')

@section('title', 'Branch')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto mb-5">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-700">จัดการสาขา - sdasd</h2>
        </div>
    </div>
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto mb-5">
        <div class="flex flex-col space-y-2 text-left">
            <label class="font-medium text-gray-700 text-sm">ชื่อสถานที่</label>
            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm"
                value="${branch.name}" readonly>

            <label class="font-medium text-gray-700 text-sm">ประเภท</label>
            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm"
                value="${branch.type}" readonly>

            <label class="font-medium text-gray-700 text-sm">จังหวัด</label>
            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm"
                value="${branch.province}" readonly>

            <label class="font-medium text-gray-700 text-sm">วันที่เพิ่ม</label>
            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm"
                value="17 ก.ย. 2568" readonly>

            <label class="font-medium text-gray-700 text-sm">เพิ่มโดย</label>
            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm"
                value="jeng@gmail.com" readonly>
        </div>
    </div>
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto mb-5">
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
                                <button class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 whitespace-nowrap cursor-pointer" onclick="viewDetail(${branch.id})">ดูรายละเอียด</button>
                                <button class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 cursor-pointer" 
                                onclick="edit(${branch.id})">แก้ไข</button>
                                <button class="block w-full px-4 py-2 text-white bg-red-600 rounded-lg shadow-md hover:bg-red-700 cursor-pointer" onclick="deleteBranch(${branch.id})">ลบ</button>
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

            // Page number buttons
            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement("button");
                btn.innerText = i;
                btn.className = `px-4 py-2 mx-1 rounded-lg text-base font-semibold 
                                     ${i === currentPage ? "bg-blue-600 text-white " : "bg-white border border-gray-300 text-black cursor-pointer"}`;
                btn.onclick = () => goToPage(i);
                pagination.appendChild(btn);
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
                title: "<b class=text-gray-800>รายละเอียดข้อมูล POI</b>",
                html: `
                        <div class="flex flex-col space-y-2 text-left">
                            <label class="font-medium text-gray-700 text-sm">ชื่อสถานที่</label>
                            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${branch.name}" readonly>

                            <label class="font-medium text-gray-700 text-sm">ประเภท</label>
                            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${branch.type}" readonly>

                            <label class="font-medium text-gray-700 text-sm">จังหวัด</label>
                            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${branch.province}" readonly>

                            <label class="font-medium text-gray-700 text-sm">วันที่เพิ่ม</label>
                            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="17 ก.ย. 2568" readonly>

                            <label class="font-medium text-gray-700 text-sm">เพิ่มโดย</label>
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
                       <div class="flex flex-col items-center space-y-4 p-6 max-w-xs mx-auto bg-white shadow-lg rounded-lg">
    <!-- ไอคอนแก้ไข -->
    <div class="w-16 h-16 bg-black flex items-center justify-center rounded-lg">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="currentColor">
            <path d="M16.713 2.299a2.5 2.5 0 1 1 3.535 3.535L6.5 19.583l-3.914.438a.5.5 0 0 1-.548-.548l.438-3.914L16.713 2.299zM15 5l4 4m-1 12H4a1 1 0 0 1-1-1v-1h14v1a1 1 0 0 1-1 1z"/>
        </svg>
    </div>

    <!-- หัวข้อ -->
    <h2 class="text-xl font-bold text-gray-900">แก้ไขยอด</h2>

    <!-- ฟอร์ม -->
    <div class="w-full space-y-3">
        <label class="block text-sm font-medium text-gray-700">เดือน</label>
        <select class="w-full h-12 text-sm px-4 text-gray-800 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500">
            <option>กุมภาพันธ์ - 2568</option>
        </select>

        <label class="block text-sm font-medium text-gray-700">จำนวน</label>
        <input type="number" class="w-full h-12 text-sm px-4 text-gray-800 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" value="482">

        <label class="block text-sm font-medium text-gray-700">ยอดเงิน</label>
        <input type="text" class="w-full h-12 text-sm px-4 text-gray-800 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" value="234,454">
    </div>

    <!-- ปุ่ม -->
    <div class="flex space-x-4 w-full mt-4">
        <button class="w-1/2 h-12 bg-gray-500 text-white font-medium rounded-md shadow-md hover:bg-gray-600 transition">
            ยกเลิก
        </button>
        <button class="w-1/2 h-12 bg-green-600 text-white font-medium rounded-md shadow-md hover:bg-green-700 transition">
            ยืนยัน
        </button>
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
@endsection