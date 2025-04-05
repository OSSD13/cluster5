@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')

    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

    <!-- <form method="POST" action="{{ route('logout') }}">
            @csrf -->
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-lg font-bold">จัดการสมาชิก</h2>
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="addMember()" >
                สร้างสมาชิก
            </button>
        </div>

        <!-- Search Input -->
        <input type="text" placeholder="ค้นหาสมาชิก" class="w-full p-2 border border-gray-300 rounded mb-3">

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
                <th class="py-3 px-4 text-left whitespace-nowrap">ชื่อ / อีเมล</th>
                <th class="py-3 px-4 text-left whitespace-nowrap">บทบาท</th>
                <th class="py-3 px-1 w-7 text-center">&#8230;</th>
             </tr>
        </thead>


        <tbody id="tableBody" class="bg-white divide-y divide-gray-200"></tbody>
    </table>
</div>

<!-- Pagination Controls -->
<div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>

<!-- contextMenu Controls-->
<div id="contextMenu" class="hidden absolute bg-white shadow-lg rounded-lg w-32 z-50 p-2 space-y-2"></div>

<script>
    let branches = [
        { id: 1, name: "พีระพัท", type: "per@gmail.com", province: "Sale" },
        { id: 2, name: "กานต์", type: "knn@gmail.com", province: "CEO" },
        { id: 3, name: "อิทธิ์", type: "itt@gmail.com", province: "Sale" },
        { id: 4, name: "เจษฎา", type: "jess@gmail.com", province: "Sale" },
        { id: 5, name: "บุญมี", type: "bun@gmail.com", province: "Sale Supervisor" },
        { id: 6, name: "เอกรินทร์", type: "egn@gmail.com", province: "CEO" },
        { id: 7, name: "อิศรา", type: "isra@gmail.com", province: "Sale Supervisor" },
        { id: 8, name: "มีนา", type: "me@gmail.com", province: "Sale" },
        { id: 9, name: "น้ำทิพย์", type: "nam@gmail.com", province: "Sale" },
        { id: 10, name: "โอภาส", type: "oop@gmail.com", province: "CEO" },
        { id: 10, name: "ดลภพ", type: "dol@gmail.com", province: "CEO" }
    ]; // Your existing data
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
        <td class="py-3 px-4 truncate">
            <div class="font-semibold">${branch.name}</div>
            <div class="text-sm text-gray-500">${branch.type}</div>
        </td>
        <td class="py-3 px-4 w-32 truncate">${branch.province}</td>
        <td class="py-3 px-1 w-10 text-center relative">
            <button onclick="toggleMenu(event, ${branch.id})">&#8230;</button>
           
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


    let activeMenuId = null;

    function toggleMenu(event, id) {
        event.stopPropagation();

        const menu = document.getElementById("contextMenu");
        const button = event.currentTarget;
        const parentCell = button.closest('td');

        if (activeMenuId === id && !menu.classList.contains("hidden")) {
            menu.classList.add("hidden");
            activeMenuId = null;
            return;
        }

        activeMenuId = id;

        menu.innerHTML = `
            <button class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 whitespace-nowrap"
                onclick="document.getElementById('contextMenu').classList.add('hidden'); activeMenuId = null; viewDetail(${id})">
                ดูรายละเอียด
            </button>
            <button class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700"
                onclick="document.getElementById('contextMenu').classList.add('hidden'); activeMenuId = null; editBranch(${id})">
                แก้ไข
            </button>
            <button class="block w-full px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700"
                onclick="document.getElementById('contextMenu').classList.add('hidden'); activeMenuId = null; deleteBranch(${id})">
                ลบ
            </button>
        `;

        menu.classList.remove("hidden");

        // **แสดงเมนูก่อนเพื่อให้ offsetWidth ทำงาน**
        menu.classList.remove("hidden");

        // ตั้งตำแหน่งเมนูใหม่
        const top = parentCell.offsetTop + parentCell.offsetHeight -20; // ลดลงมานิด (4px)
        const left = parentCell.offsetLeft + parentCell.offsetWidth - menu.offsetWidth;


        menu.style.position = "absolute";
        menu.style.top = `${top}px`;
        menu.style.left = `${left}px`;
    }






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
        
        html: `
            <div class="flex flex-col space-y-2 text-left">
                <label class='font-bold text-gray-800 text-3xl mt-3 mb-3'>รายละเอียดข้อมูลสมาชิก</b>
                <label class="font-medium text-gray-700 text-sm">ชื่อสถานที่</label>
                <input type="text" class="font-medium w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-lg" value="${branch.name}" readonly>
                
                <label class="font-medium text-gray-700 text-sm br">ประเภท</label>
                <input type="text" class="font-medium w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-lg" value="${branch.type}" readonly>
              
                <label class="font-medium text-gray-700 text-sm">จังหวัด</label>
                <input type="text" class="font-medium w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-lg" value="${branch.province}" readonly>
                
                <label class="font-medium text-gray-700 text-sm">วันที่เพิ่ม</label>
                <input type="text" class="font-medium w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-lg" value="17 ก.ย. 2568" readonly>
                
                <label class="font-medium text-gray-700 text-sm">เพิ่มโดย</label>
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

function addMember() {
    Swal.fire({
        title: `
            <div class="flex flex-col items-center mb-1 ">
                <span class="iconify" data-icon="material-symbols-light:edit-square-rounded" data-width="160" data-height="160"></span>
            </div>
            <b class=text-gray-800>สร้างสมาชิก </b>
        `,
        html: `
            <div class="flex flex-col space-y-1 text-left">
                <label class="font-semibold text-gray-800">Email</label>
                <input type="email" id="memberEmail" class="w-full p-2 border border-gray-300 rounded mb-3" >

                <label class="font-semibold text-gray-800">Password</label>
                <input type="password" id="memberPassword" class="w-full p-2 border border-gray-300 rounded mb-3" >

                <label class="font-semibold text-gray-800">ชื่อผู้ใช้</label>
                <input type="text" id="memberName" class="w-full p-2 border border-gray-300 rounded mb-3">

                <label class="font-semibold text-gray-800">บทบาท</label>
                <select id="memberRole" class="swal2-input w-full h-10 text-lg px-3 text-gray-800 border border-gray-300 rounded">
                    <option value="Sale">Sale</option>
                    <option value="CEO">CEO</option>
                    <option value="Sale Sup.">Sale Supervisor</option>
                </select>
            </div>
        `,
        
        showCancelButton: true,
        confirmButtonText: "ยืนยัน",
        cancelButtonText: "ยกเลิก",
        confirmButtonColor: "#2D8C42",
        focusCancel: true,
        customClass: {
            actions: "flex justify-between w-full px-4",
            cancelButton: "ml-0",
            confirmButton: "mr-0",
        },
        preConfirm: () => {
            const email = document.getElementById("memberEmail").value;
            const password = document.getElementById("memberPassword").value;
            const name = document.getElementById("memberName").value;
            const role = document.getElementById("memberRole").value;

            if (!email || !password || !name || !role) {
                Swal.showValidationMessage("กรุณากรอกข้อมูลให้ครบทุกช่อง");
                return false;
            }
            
            // เพิ่มสมาชิกใหม่เข้าไปในอาร์เรย์
            const newMember = {
                id: branches.length + 1,
                name: name,
                type: email,
                province: role
            };
            branches.push(newMember);
            renderTable();

            // แจ้งเตือนว่าบันทึกสำเร็จ
            Swal.fire({
                title: "สำเร็จ!",
                text: "เพิ่มสมาชิกเรียบร้อยแล้ว",
                icon: "success",
                confirmButtonColor: "#2D8C42",
                confirmButtonText: "ตกลง"
            });
        }
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