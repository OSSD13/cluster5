@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')
    <!-- <form method="POST" action="{{ route('logout') }}">
            @csrf -->
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-lg font-bold">จัดการสมาชิก</h2>
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                สร้างสมาชิก
            </button>
        </div>

        <!-- Search Input -->
        <input type="text" placeholder="ค้นหาสถานที่ที่สนใจ" class="w-full p-2 border border-gray-300 rounded mb-3">

        <!-- Dropdowns -->
        <div class="mb-3">
            <label class="block text-gray-600 mb-1">Sale Supervisor</label>
            <select class="w-full p-2 border border-gray-300 rounded">
                <option>แสดงสมาชิก</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="block text-gray-600 mb-1">บทบาท</label>
            <select class="w-full p-2 border border-gray-300 rounded">
                <option>ค้นหาด้วยตำแหน่ง</option>
            </select>
        </div>

        <!-- Result Count -->
        <p class="text-gray-700">ผลลัพธ์ 302 รายการ</p>
    </div>


    <!-- **************************************************************************** -->

<!-- Pagination Controls -->
<div class="overflow-x-auto">
    <table class="w-full mt-5 border-collapse rounded-lg overflow-hidden table-fixed">
        <thead class="bg-blue-500 text-white">
            <tr>
                <th class="py-3 px-4 w-13 text-left">ID</th>
                <th class="py-3 px-4 text-left whitespace-nowrap">ชื่อ</th>
                <th class="py-3 px-4 text-left whitespace-nowrap cursor-pointer" onclick="sortTable('type')">อีเมล</th>
                <th class="py-3 px-4 text-left whitespace-nowrap cursor-pointer" onclick="sortTable('province')">บทบาท</th>
                <th class="py-3 px-1 w-7 text-center">&#8230;</th>
            </tr>
        </thead>
        <tbody id="tableBody" class="bg-white divide-y divide-gray-200"></tbody>
    </table>
</div>

<!-- Pagination Controls -->
<div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>

<script>
    let branches = [
        { id: 1, name: "พีระพัท", type: "per@gmail.com", province: "Sale" },
        { id: 2, name: "กานต์", type: "knn@gmail.com", province: "CEO" },
        { id: 3, name: "อิทธิ์", type: "itt@gmail.com", province: "Sale" },
        { id: 4, name: "เจษฎา", type: "jess@gmail.com", province: "Sale" },
        { id: 5, name: "บุญมี", type: "bun@gmail.com", province: "Sale Sup." },
        { id: 6, name: "เอกรินทร์", type: "egn@gmail.com", province: "CEO" },
        { id: 7, name: "อิศรา", type: "isra@gmail.com", province: "Sale Sup." },
        { id: 8, name: "มีนา", type: "me@gmail.com", province: "Sale" },
        { id: 9, name: "น้ำทิพย์", type: "nam@gmail.com", province: "Sale" },
        { id: 10, name: "โอภาส", type: "oop@gmail.com", province: "CEO" },
        { id: 10, name: "ดลภพ", type: "dol@gmail.com", province: "CEO" }
    ]; // Your existing data
    let currentPage = 1;
    const rowsPerPage = 5;
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
            <button onclick="toggleMenu(event, ${branch.id})">&#8230;</button>
            <div id="menu-${branch.id}" class="hidden absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-32 z-50 p-2 space-y-2">
                <button class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 whitespace-nowrap" onclick="viewDetail(${branch.id})">ดูรายละเอียด</button>
                <button class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700" onclick="editBranch(${branch.id})">แก้ไข</button>
                <button class="block w-full px-4 py-2 text-white bg-red-600 rounded-lg shadow-md hover:bg-red-700" onclick="deleteBranch(${branch.id})">ลบ</button>
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
    prevBtn.innerText = "<";
    prevBtn.className = `px-3 py-1 ${currentPage === 1 ? "bg-gray-300 cursor-not-allowed" : "bg-gray-500 text-white"} rounded hover:bg-gray-400`;
    prevBtn.disabled = currentPage === 1;
    prevBtn.onclick = () => goToPage(currentPage - 1);
    pagination.appendChild(prevBtn);

    // Page number buttons
    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement("button");
        btn.innerText = i;
        btn.className = `px-3 py-1 ${i === currentPage ? "bg-blue-500 text-white" : "bg-gray-200"} rounded hover:bg-gray-300`;
        btn.onclick = () => goToPage(i);
        pagination.appendChild(btn);
    }

    // Next button
    const nextBtn = document.createElement("button");
    nextBtn.innerText = ">";
    nextBtn.className = `px-3 py-1 ${currentPage === totalPages ? "bg-gray-300 cursor-not-allowed" : "bg-gray-500 text-white"} rounded hover:bg-gray-400`;
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

    function sortTable(column) {
        if (currentSort.column === column) {
            currentSort.ascending = !currentSort.ascending;
        } else {
            currentSort.column = column;
            currentSort.ascending = true;
        }
        branches.sort((a, b) => (a[column] < b[column] ? (currentSort.ascending ? -1 : 1) : (a[column] > b[column] ? (currentSort.ascending ? 1 : -1) : 0)));
        renderTable();
    }

    function viewDetail(id) {
    const branch = branches.find(item => item.id === id);

    Swal.fire({
        title: "<b class=text-gray-800>รายละเอียดข้อมูลสมาชิก </b>",
        html: `
            <div class="flex flex-col space-y-2 text-left">
                <label class="font-semibold text-gray-800">ชื่อสมาชิก</label>
                <input type="text" class="swal2-input w-full h-10 text-lg px-3 text-gray-800" value="${branch.name}" readonly>

                <label class="font-semibold text-gray-800">อีเมล</label>
                <input type="text" class="swal2-input w-full h-10 text-lg px-3 text-gray-800" value="${branch.type}" readonly>

                <label class="font-semibold text-gray-800">วันที่เพิ่ม</label>
                <input type="text" class="swal2-input w-full h-10 text-lg px-3 text-gray-800" value="${branch.province}" readonly>

                <label class="font-semibold text-gray-800">บทบาท</label>
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

    function editBranch(id) { alert(`แก้ไขข้อมูลของ ID ${id}`); }
    function deleteBranch(id) {
    Swal.fire({
        title: "ลบสมาชิก",
        text: "คุณต้องการลบสมาชิก ใช่หรือไม่",
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
                text: "สมาชิกถูกลบเรียบร้อย",
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
