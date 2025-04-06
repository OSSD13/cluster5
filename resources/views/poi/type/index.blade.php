@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')
    <!-- <form method="POST" action="{{ route('logout') }}">
                @csrf -->
                <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-2xl font-bold text-gray-700">POIT จัดการประเภทสถานที่ที่สนใจ</h2>
            <a href="{{ route('poi.type.create') }}">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap" style="background-color: #3062B8">
                    สร้าง POI
                </button>
            </a>
        </div>

        <!-- Search Input -->
        <input type="text" placeholder="ค้นหาสถานที่ที่สนใจ" class="w-full p-2 border border-gray-300 rounded mb-3" id="searchInput">

        <!-- Dropdowns -->
        <div class="mb-3">
            <label class="block text-gray-600 mb-1">ประเภท</label>
            <select class="w-full p-2 border border-gray-300 rounded" id="typeSelect">
                <option value="">ประเภทสถานที่</option>
                <option value="ร้านอาหาร">ร้านอาหาร</option>
                <option value="ร้านกาแฟ">ร้านกาแฟ</option>
                <option value="ร้านขนม">ร้านขนม</option>
                <option value="ผับบาร์">ผับบาร์</option>
                <option value="ศูนย์การค้า">ศูนย์การค้า</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="block text-gray-600 mb-1">จังหวัด</label>
            <select class="w-full p-2 border border-gray-300 rounded" id="provinceSelect">
                <option value="">จังหวัด</option>
                <option value="ชลบุรี">ชลบุรี</option>
                <option value="กรุงเทพมหานคร">กรุงเทพมหานคร</option>
                <option value="ขอนแก่น">ขอนแก่น</option>
                <option value="เชียงใหม่">เชียงใหม่</option>
                <option value="ปราจีนบุรี">ปราจีนบุรี</option>
            </select>
        </div>

        <!-- Result Count -->
        <p class="text-gray-700">ผลลัพธ์ 302 รายการ</p>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full mt-5 border-collapse rounded-lg overflow-hidden table-fixed">
            <thead class="bg-blue-500 text-black text-sm" style="background-color: #B5CFF5">
                <tr>
                    <th class="py-2 px-4 text-center w-3/12">ประเภท</th>
                    <th class="py-2 px-4 text-center w-3/12">ชื่อสถานที่</th>
                    <th class="py-2 px-4 text-center w-2/12">Icon</th>
                    <th class="py-2 px-4 text-center w-3/12">คำอธิบาย</th>
                    <th class="py-2 px-4 text-center w-1/12"></th>
                </tr>
            </thead>
            <tbody id="tableBody" class="bg-white divide-y divide-gray-200 text-sm">
                <!-- ข้อมูลจะถูกเติมโดย JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Pagination Controls -->
    <div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>
@endsection

@section('script')
<script>
    let poits = [
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
        { id: 11, name: "แหลมแท่น", type: "ที่เที่ยว", province: "ชลบุรี" }
    ];
    for (let i = 12; i <= 50; i++) {
            poits.push({
                id: i,
                name: `${i}`,
                type: `${i % 5 === 0 ? 'ร้านกาแฟ' : i % 5 === 1 ? 'ร้านอาหาร' : i % 5 === 2 ? 'ร้านขนม' : i % 5 === 3 ? 'ผับบาร์' : 'ศูนย์การค้า'}`,
                province: `${i % 5 === 0 ? 'อุดรธานี' : i % 5 === 1 ? 'ชลบุรี' : i % 5 === 2 ? 'กรุงเทพฯ' : i % 5 === 3 ? 'ขอนแก่น' : 'เชียงใหม่'}`,
            });
        } // Your existing data
    let currentPage = 1;
    const rowsPerPage = 10;
    let currentSort = { column: null, ascending: true };


    function renderTable(filteredPoit = poits) {
        const tableBody = document.getElementById("tableBody");
        tableBody.innerHTML = "";

        const start = (currentPage - 1) * rowsPerPage;
            const paginatedData = filteredPoit.slice(start, start + rowsPerPage);

            paginatedData.forEach(poit => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td class="py-3 px-4 text-left">${poit.type}</td>
                <td class="py-3 px-4 text-left">${poit.name}</td>
                <td class="py-3 px-4 text-center">${poit.icon || "🏢"}</td>
                <td class="py-3 px-4 text-left">${poit.province}</td>
                <td class="py-3 px-1 w-10 text-center relative">
                    <button class="cursor-pointer" onclick="toggleMenu(event, ${poit.id})">&#8230;</button>
                    <div id="menu-${poit.id}" class="hidden absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-32 z-50 p-2 space-y-2">
                        <button class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700" style="background-color: #3062B8" onclick="viewDetail(${poit.id})">ดูรายละเอียด</button>
                        <button class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700" style="background-color: #3062B8" onclick="editPoit(${poit.id})">แก้ไข</button>
                        <button class="block w-full px-4 py-2 text-white bg-red-600 rounded-lg shadow-md hover:bg-red-700" style="background-color: #CF3434" onclick="deletePoit(${poit.id})">ลบ</button>
                    </div>
                </td>
            `;
            tableBody.appendChild(row);
        });

        renderPagination(filteredPoit); // เรียกใช้ฟังก์ชัน pagination
    }

    function renderPagination() {
            const pagination = document.getElementById("pagination");
            pagination.innerHTML = ""; // Clear previous pagination

            const totalPages = Math.ceil(poits.length / rowsPerPage);

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

    // ฟังก์ชันกรองข้อมูลตามคำค้นหา
    document.getElementById("searchInput").addEventListener("input", function() {
        const searchValue = this.value.toLowerCase();
        const filteredPoit = poits.filter(poits => 
        poits.name.toLowerCase().includes(searchValue) ||
        poits.type.toLowerCase().includes(searchValue) ||
        poits.province.toLowerCase().includes(searchValue)
        );
        renderTable(filteredPoit);
    });

    // ฟังก์ชันกรองตามประเภท
    document.getElementById("typeSelect").addEventListener("change", function() {
        const selectedType = this.value;
        const filteredPoit = poits.filter(poits => poits.type.includes(selectedType));
        renderTable(filteredPoit);
    });

    // ฟังก์ชันกรองตามจังหวัด
    document.getElementById("provinceSelect").addEventListener("change", function() {
        const selectedProvince = this.value;
        const filteredPoit = poits.filter(poits => poits.province.includes(selectedProvince));
        renderTable(filteredPoit);
    });

    // ฟังก์ชันเพื่อแสดงข้อมูลเมื่อโหลดหน้าเว็บ
    document.addEventListener("DOMContentLoaded", function() {
        renderTable(); // แสดงข้อมูลทั้งหมดเมื่อเริ่มต้น
    });

    function toggleMenu(event, id) {
        event.stopPropagation();
        document.querySelectorAll("[id^=menu-]").forEach(menu => menu.classList.add("hidden"));
        document.getElementById(`menu-${id}`).classList.toggle("hidden");
    }
    document.addEventListener("click", () => {
        document.querySelectorAll("[id^=menu-]").forEach(menu => menu.classList.add("hidden"));
    });
    function viewDetail(id) {
            const poit = poits.find(item => item.id === id);

            Swal.fire({
                title: "<b class=text-gray-800>รายละเอียดข้อมูล POI</b>",
                html: `
                    
                <div class="flex flex-col items-center space-y-4 text-left w-full max-w-md mx-auto">
                    <div class="w-full">
                        <label class="block text-gray-800 text-sm mb-1">ชื่อสมาชิก</label>
                        <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${poit.name}" readonly>
                    </div>

                    <div class="w-full">
                        <label class="block text-gray-800 text-sm mb-1">อีเมล</label>
                        <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${poit.type}" readonly>
                    </div>

                    <div class="w-full">
                        <label class="block text-gray-800 text-sm mb-1">วันที่เพิ่ม</label>
                        <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${poit.province}" readonly>
                    </div>

                    <div class="w-full">
                        <label class="block text-gray-800 text-sm mb-1">บทบาท</label>
                        <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="17 ก.ย. 2568" readonly>
                    </div>

                    <div class="w-full">
                        <label class="block text-gray-800 text-sm mb-1">เพิ่มโดย</label>
                        <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="jeng@gmail.com" readonly>
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
    function editPoit(id) { alert(`แก้ไขข้อมูลของ ID ${id}`); }
    function deletePoit(id) {
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
                    poits = poits.filter(poit => poit.id !== id);

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

    renderTable(); // เรียกใช้ฟังก์ชัน renderTable เพื่อแสดงข้อมูลที่หน้าแรก

</script>




        <!-- **************************************************************************** -->

        <!-- </form> -->
@endsection