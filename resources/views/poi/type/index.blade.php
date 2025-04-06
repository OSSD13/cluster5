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
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap">
                    สร้าง POIT
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

        <!-- Result Count -->
        <p class="text-gray-700">ผลลัพธ์ 302 รายการ</p>
    </div>


    <!-- **************************************************************************** -->

    <!-- Pagination Controls -->
    <div class="overflow-x-auto">
        <table class="w-full mt-5 border-collapse rounded-lg overflow-hidden table-fixed">
            <thead class="bg-blue-500 text-white text-sm">
                <tr>
                    <th class="py-2 px-4 text-left w-4/12 whitespace-nowrap">ชื่อ / ประเภท</th>
                    <th class="py-2 px-4 text-center w-1/12 whitespace-nowrap">Icon</th>
                    <th class="py-2 px-4 text-center w-5/12 whitespace-nowrap">คำอธิบาย</th>
                    <th class="py-2 px-4 text-center w-1/12 whitespace-nowrap"></th>
                </tr>
            </thead>
            <tbody id="tableBody" class="bg-white divide-y divide-gray-200 text-sm">
                <!-- เนื้อหาของตารางจะถูกเติมโดย JavaScript -->
            </tbody>
        </table>
        <td class="py-3 px-1 w-10 text-center relative">
            <button class="cursor-pointer" onclick="toggleMenu(event, ${branch.id})">&#8230;</button>
            <div id="menu-${branch.id}"
                class="hidden absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-32 z-50 p-2 space-y-2">
                <button
                    class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 whitespace-nowrap cursor-pointer"
                    onclick="viewDetail(${branch.id})">ดูรายละเอียด</button>
                <button
                    class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 cursor-pointer"
                    onclick="window.location.href='{{ route('poi.type.edit') }}'">แก้ไข</button>
                <button
                    class="block w-full px-4 py-2 text-white bg-red-600 rounded-lg shadow-md hover:bg-red-700 cursor-pointer"
                    onclick="deleteBranch(${branch.id})">ลบ</button>
            </div>
        </td>
    </div>
    <style>
        td.text-center,
        th.text-center {
            text-align: center;
            /* จัดข้อความให้อยู่ตรงกลางแนวนอน */
            vertical-align: middle;
            /* จัดข้อความให้อยู่ตรงกลางแนวตั้ง */
        }

        td.truncate {
            max-width: 300px;
            /* เพิ่มความกว้างสูงสุดของคำอธิบาย */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-align: center;
            /* จัดข้อความให้อยู่ตรงกลางแนวนอน */
            vertical-align: middle;
            /* จัดข้อความให้อยู่ตรงกลางแนวตั้ง */
        }
    </style>
    <style>
    thead th {
        height: 50px; /* กำหนดความสูงของหัวตาราง */
        vertical-align: middle; /* จัดข้อความให้อยู่ตรงกลางแนวตั้ง */
        text-align: center; /* จัดข้อความให้อยู่ตรงกลางแนวนอน */
    }
</style>
    <!-- Pagination Controls -->
    <div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>
@endsection
@section('script')
    <script>
        let branches = [
            { name: "บางแสน", type: "ร้านอาหาร", province: "ชลบุรี", description: "ร้านอาหารริมทะเลที่มีอาหารทะเลสดใหม่และบรรยากาศดี" },
            { name: "อุดรธานี", type: "ร้านกาแฟ", province: "อุดรธานี", description: "ร้านกาแฟบรรยากาศสบาย ๆ พร้อมกาแฟคุณภาพดี" },
            { name: "ศรีราชา", type: "ร้านขนม", province: "ชลบุรี", description: "ร้านขนมหวานที่มีเมนูหลากหลายและรสชาติอร่อย" },
            { name: "พัทยา", type: "ผับบาร์", province: "ชลบุรี", description: "ผับบาร์ที่มีดนตรีสดและเครื่องดื่มหลากหลาย" },
            { name: "เซนทรัล", type: "ศูนย์การค้า", province: "ชลบุรี", description: "ศูนย์การค้าขนาดใหญ่ที่มีร้านค้าหลากหลายและสิ่งอำนวยความสะดวกครบครัน" },
            { name: "เชียงใหม่", type: "ร้านอาหาร", province: "เชียงใหม่", description: "ร้านอาหารที่มีวิวภูเขาและอาหารพื้นเมือง" },
            { name: "ขอนแก่น", type: "ร้านกาแฟ", province: "ขอนแก่น", description: "ร้านกาแฟที่มีเมล็ดกาแฟคุณภาพจากทั่วโลก" },
            { name: "หาดใหญ่", type: "ร้านขนม", province: "สงขลา", description: "ร้านขนมที่มีเมนูขนมไทยและขนมสากล" },
            { name: "ภูเก็ต", type: "ผับบาร์", province: "ภูเก็ต", description: "ผับบาร์ที่มีวิวทะเลและดนตรีสด" },
            { name: "กรุงเทพ", type: "ศูนย์การค้า", province: "กรุงเทพ", description: "ศูนย์การค้าขนาดใหญ่ที่มีร้านค้าหรูหราและร้านอาหารหลากหลาย" },
            // เพิ่มข้อมูลเพิ่มเติมตามต้องการ
        ];// Your existing data
        let currentPage = 1;
        const rowsPerPage = 5;
        let currentSort = { column: null, ascending: true };

        function renderTable() {
            const tableBody = document.getElementById("tableBody");
            tableBody.innerHTML = "";

            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;
            const paginatedBranches = branches.slice(startIndex, endIndex);

            paginatedBranches.forEach((branch, index) => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td class="py-3 px-4 text-left">
                        <div class="font-bold">${branch.name}</div> <!-- ชื่อสถานที่ -->
                        <div class="text-sm text-gray-500">${branch.type}</div> <!-- ประเภท -->
                    </td>
                    <td class="py-3 px-4 text-center icon-column">${getIconByType(branch.type)}</td> <!-- Icon -->
                    <td class="py-3 px-4 truncate">${branch.description}</td> <!-- คำอธิบาย -->
                    <td class="py-3 px-1 w-10 text-center relative">
                        <button class="cursor-pointer" onclick="toggleMenu(event, ${index})">&#8230;</button>
                        <div id="menu-${index}" class="hidden absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-32 z-50 p-2 space-y-2">
                            <button class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 whitespace-nowrap cursor-pointer" onclick="viewDetail(${index})">ดูรายละเอียด</button>
                            <button class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 cursor-pointer" onclick="editBranch(${index})">แก้ไข</button>
                            <button class="block w-full px-4 py-2 text-white bg-red-600 rounded-lg shadow-md hover:bg-red-700 cursor-pointer" onclick="deleteBranch(${index})">ลบ</button>
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
                                                                        <label class="font-semibold text-gray-800">ชื่อสถานที่</label>
                                                                        <input type="text" class="swal2-input w-full h-10 text-lg px-3 text-gray-800" value="${branch.name}" readonly>

                                                                        <label class="font-semibold text-gray-800">ประเภท</label>
                                                                        <input type="text" class="swal2-input w-full h-10 text-lg px-3 text-gray-800" value="${branch.type}" readonly>

                                                                        <label class="font-semibold text-gray-800">จังหวัด</label>
                                                                        <input type="text" class="swal2-input w-full h-10 text-lg px-3 text-gray-800" value="${branch.province}" readonly>

                                                                        <label class="font-semibold text-gray-800">วันที่เพิ่ม</label>
                                                                        <input type="text" class="swal2-input w-full h-10 text-lg px-3 text-gray-800" value="17 ก.ย. 2568" readonly>

                                                                        <label class="font-semibold text-gray-800">เพิ่มโดย</label>
                                                                        <input type="text" class="swal2-input w-full h-10 text-lg px-3 text-gray-800" value="jeng@gmail.com" readonly>
                                                                    </div>
                                                                `,
                customClass: {
                    popup: 'custom-popup'
                },
                confirmButtonText: "ยืนยัน",
                confirmButtonColor: "#2D8C42",
            });
        }
        function getIconByType(type) {
            switch (type) {
                case "ร้านอาหาร":
                    return "🍴"; // ไอคอนสำหรับร้านอาหาร
                case "ร้านกาแฟ":
                    return "☕"; // ไอคอนสำหรับร้านกาแฟ
                case "ร้านขนม":
                    return "🍰"; // ไอคอนสำหรับร้านขนม
                case "ผับบาร์":
                    return "🍺"; // ไอคอนสำหรับผับบาร์
                case "ศูนย์การค้า":
                    return "🏬"; // ไอคอนสำหรับศูนย์การค้า
                default:
                    return "🏢"; // ไอคอนเริ่มต้น
            }
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