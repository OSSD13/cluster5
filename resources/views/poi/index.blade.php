@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')
    <!-- <form method="POST" action="{{ route('logout') }}">
                            @csrf -->
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-2xl font-bold text-gray-700" >POI จัดการสถานที่ที่สนใจ</h2>

            <a href="{{ route('poi.create') }}">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap" style="background-color: #3062B8">
                    สร้าง POI
                </button>
            </a>
        </div>

        <!-- Search Input -->
        <input type="text" placeholder="ค้นหาสถานที่ที่สนใจ" class="w-full p-2 border border-gray-300 rounded mb-3">

        <!-- Dropdowns -->
        <div class="mb-3">
            <label class="block text-gray-600 mb-1">ประเภท</label>
            <select class="w-full p-2 border border-gray-300 rounded">
                <option>ประเภทสถานที่</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="block text-gray-600 mb-1">จังหวัด</label>
            <select class="w-full p-2 border border-gray-300 rounded">
                <option>จังหวัด</option>
            </select>
        </div>

        <p class="text-gray-700">ผลลัพธ์ 302 รายการ</p>
        <a href="{{ route('poi.type.index') }}">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap" style="background-color: #3062B8">
                ไปหน้า POI type
            </button>
        </a>
    </div>


    <!-- **************************************************************************** -->

    <!-- Pagination Controls -->
    <div class="overflow-visible">
    <table class="w-full mt-5 border-collapse rounded-lg overflow-hidden table-fixed">
    <thead class="bg-blue-500 text-black text-sm" style="background-color: #B5CFF5">
    <tr>
        <th class="py-2 px-2 text-left w-1/12 whitespace-nowrap">ID</th>
        <th class="py-2 px-4 text-center w-3/12 whitespace-nowrap">ชื่อสถานที่</th> <!-- เพิ่ม px-4 และ w-3/12 -->
        <th class="py-2 px-2 text-center w-2/12 whitespace-nowrap">ประเภท</th>
        <th class="py-2 px-2 text-center w-2/12 whitespace-nowrap">จังหวัด</th>
        <th class="py-2 px-2 text-center w-1/12 whitespace-nowrap"></th>
    </tr>
    </thead>
    <tbody id="tableBody" class="bg-white divide-y divide-gray-200 text-sm">
        <!-- เนื้อหาของตารางจะถูกเติมโดย JavaScript -->
    </tbody>
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
        ];
        for (let i = 12; i <= 50; i++) {
            branches.push({
                id: i,
                name: `${i}`,
                type: `${i % 5 === 0 ? 'ร้านกาแฟ' : i % 5 === 1 ? 'ร้านอาหาร' : i % 5 === 2 ? 'ร้านขนม' : i % 5 === 3 ? 'ผับบาร์' : 'ศูนย์การค้า'}`,
                province: `${i % 5 === 0 ? 'อุดรธานี' : i % 5 === 1 ? 'ชลบุรี' : i % 5 === 2 ? 'กรุงเทพฯ' : i % 5 === 3 ? 'ขอนแก่น' : 'เชียงใหม่'}`,
            });
        } // Your existing data
        let currentPage = 1;
        const rowsPerPage = 10;
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
                        <button class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 whitespace-nowrap cursor-pointer" style="background-color: #3062B8" onclick="viewDetail(${branch.id})">ดูรายละเอียด</button>
                        <button class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 cursor-pointer " style="background-color: #3062B8"
                        onclick="window.location.href='{{ route('poi.type.edit') }}'">แก้ไข</button>
                        <button class="block w-full px-4 py-2 text-white bg-red-600 rounded-lg shadow-md hover:bg-red-700 cursor-pointer" style="background-color: #CF3434" onclick="deleteBranch(${branch.id})">ลบ</button>
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
                title: "<b class=text-gray-800>รายละเอียดข้อมูล POI</b>",
                html: `
                <div class="flex flex-col space-y-2 text-left">
                    <label class="font-semibold text-gray-800">ชื่อสถานที่</label>
                    <input type="text" class="font-medium w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-lg" value="${branch.name}" readonly>

                    <label class="font-semibold text-gray-800">ประเภท</label>
                    <input type="text" class="font-medium w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-lg" value="${branch.type}" readonly>

                    <label class="font-semibold text-gray-800">จังหวัด</label>
                    <input type="text" class="font-medium w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-lg" value="${branch.province}" readonly>

                    <label class="font-semibold text-gray-800">วันที่เพิ่ม</label>
                    <input type="text" class="font-medium w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-lg" value="17 ก.ย. 2568" readonly>

                    <label class="font-semibold text-gray-800">เพิ่มโดย</label>
                    <input type="text" class="font-medium w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-lg" value="jeng@gmail.com" readonly>
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




    <!-- **************************************************************************** -->

    <!-- </form> -->
@endsection
