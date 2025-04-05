@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')
    <!-- <form method="POST" action="{{ route('logout') }}">
                            @csrf -->
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-2xl font-bold text-gray-700">POI จัดการสถานที่ที่สนใจ</h2>

            <a href="{{ route('poi.create') }}">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap">
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
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap">
                ไปหน้า POI type
            </button>
        </a>
    </div>


    <!-- **************************************************************************** -->

    <!-- Pagination Controls -->
    <div class="overflow-visible">
        <table class="w-full mt-5 border-collapse rounded-lg overflow-hidden table-fixed">
            <thead class="bg-blue-500 text-white">
                <tr>
                <th class="py-3 px-4 w-13 text-left">ID</th>
                <th class="py-3 px-4 text-left whitespace-nowrap">ชื่อสถานที่</th>
                <th class="py-3 px-4 text-left whitespace-nowrap">ประเภท</th>
                <th class="py-3 px-4 text-left whitespace-nowrap">จังหวัด</th>
                <th class="py-3 px-1 w-7 text-center"></th>
                </tr>
            </thead>
    </table>
</div>

    <!-- Pagination Controls -->
    <div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>
@endsection
@section('script')
    <script>
        let branches = [
            { id: 1, name: "ศูนย์กลางอำเภอ", type: "amphoe-center", province: "ชลบุรี" },
            { id: 2, name: "ธนาคาร", type: "bank", province: "กรุงเทพมหานคร" },
            { id: 3, name: "ชายหาด", type: "beach", province: "ภูเก็ต" },
            { id: 4, name: "อาคารสำคัญ", type: "building-landmark", province: "เชียงใหม่" },
            { id: 5, name: "ถ้ำ", type: "cave", province: "แม่ฮ่องสอน" },
            { id: 6, name: "ศูนย์กลางจังหวัด", type: "changwat-center", province: "นครราชสีมา" },
            { id: 7, name: "เจดีย์", type: "chedi", province: "อยุธยา" },
            { id: 8, name: "โบสถ์", type: "church", province: "กรุงเทพมหานคร" },
            { id: 9, name: "วิทยาลัยและมหาวิทยาลัย", type: "college-and-university", province: "ขอนแก่น" },
            { id: 10, name: "ศาล", type: "court-center", province: "นครปฐม" }
        ]; // Your existing data
        let currentPage = 1;
        const rowsPerPage = 25;
        let currentSort = { column: null, ascending: true };

        function renderTable() {
            const tableBody = document.getElementById("tableBody");
            tableBody.innerHTML = "";

            const start = (currentPage - 1) * rowsPerPage;
            const paginatedData = branches.slice(start, start + rowsPerPage);


            paginatedData.forEach(($pois) => {
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
                    onclick="window.location.href='{{ route('poi.edit') }}'">แก้ไข</button>
                    <button class="block w-full px-4 py-2 text-white bg-red-600 rounded-lg shadow-md hover:bg-red-700 cursor-pointer" onclick="deleteBranch(${branch.id})">ลบ</button>
                </div>
            </td>
        `;
                        <td class="py-3 px-4 w-16">${branch.id}</td>
                        <td class="py-3 px-4 truncate">${branch.name}</td>
                        <td class="py-3 px-4 w-32 truncate">${branch.type}</td>
                        <td class="py-3 px-4 w-32 truncate">${branch.province}</td>
                        <td class="py-3 px-1 w-10 text-center relative">
                            <button class="cursor-pointer" onclick="toggleMenu(event, ${branch.id})">&#8230;</button>
                            <div id="menu-${branch.id}" class="hidden absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-32 z-50 p-2 space-y-2">
                                <button class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 whitespace-nowrap cursor-pointer" onclick="viewDetail(${branch.id})">ดูรายละเอียด</button>
                                <button class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 cursor-pointer"
                                onclick="window.location.href='{{ route('poi.edit') }}'">แก้ไข</button>
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
