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
<<<<<<< HEAD
        const paginatedData = members.slice(start, start + rowsPerPage);

        paginatedData.forEach((member) => {
    const row = document.createElement("tr");
    row.innerHTML = `
        <td class="py-3 px-4 w-16">${member.id}</td>
        <td class="py-3 px-4 truncate">
            <div class="font-md text-md">${member.name}</div>
            <div class="text-sm text-gray-400">${member.email}</div>
        </td>
        <td class="py-3 px-4 w-32 truncate text-md">${member.role}</td>
        <td class="py-3 px-1 w-10 text-center relative">
            <button onclick="toggleMenu(event, ${member.id})">&#8230;</button>
           
        </td>
    `;
    

    tableBody.appendChild(row);
});



=======
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

>>>>>>> origin/jeng-branch
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

<<<<<<< HEAD

    let activeMenuId = null;

=======
>>>>>>> origin/jeng-branch
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
<<<<<<< HEAD

        html: 
            `<div class="flex flex-col items-center">
                <span class="iconify" data-icon="material-symbols-light:edit-square-rounded" data-width="64" data-height="64"></span>
=======
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
>>>>>>> origin/jeng-branch
            </div>
        `,
        customClass: {
            popup: 'custom-popup'
        },
        confirmButtonText: "ยืนยัน",
        confirmButtonColor: "#2D8C42",
    });
}

<<<<<<< HEAD
            if (!email || !password || !name || !role) {
                Swal.showValidationMessage("กรุณากรอกข้อมูลให้ครบทุกช่อง");
                return false;
            }

            // ถ้าบทบาทเป็น Sale, ต้องมี Sales Supervisor
            let supervisorId = null;
            if (role === "Sale") {
                supervisorId = document.getElementById("supervisorDropdown").value;
                if (!supervisorId) {
                    Swal.showValidationMessage("กรุณาเลือก Sales Supervisor");
                    return false;
                }
            }
            let newMember = {
                id: members.length + 1,
                name: name,
                email: email,
                role: role
            };

            if (role === "Sale") {
                supervisorId = parseInt(document.getElementById("supervisorDropdown").value);
                newMember.supervisorId = supervisorId;
            }

            members.push(newMember);
=======
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
>>>>>>> origin/jeng-branch
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


<<<<<<< HEAD
// ฟังก์ชันนี้จะทำงานเมื่อเลือกบทบาทเป็น Sale
function toggleSupervisor() {
    const role = document.getElementById("memberRole").value;
    const section = document.getElementById("supervisorSection");
    const dropdown = document.getElementById("supervisorDropdown");

    if (role === "Sale") {
        section.style.display = "block";
        dropdown.innerHTML = "";

        const supervisors = members.filter(member => member.role === "Sale Sup.");

        if (supervisors.length === 0) {
            dropdown.innerHTML = `<option value="">(ไม่มี Supervisor)</option>`;
        } else {
            dropdown.innerHTML = `<option value="" disabled selected hidden>-- เลือก Supervisor --</option>`;
            supervisors.forEach(sup => {
                dropdown.innerHTML += `<option value="${sup.id}">${sup.name} - ${sup.email}</option>`;
            });
        }
    } else {
        section.style.display = "none";
        dropdown.innerHTML = "";
    }
}


function editMember(id) {
    const member = members.find(item => item.id === id);

    Swal.fire({
        html: 
        `<div class="flex flex-col items-center">
                <span class="iconify" data-icon="material-symbols-light:edit-square-rounded" data-width="64" data-height="64"></span>
            </div>
            <b class=text-gray-800 text-xl mb-1>แก้ไขสมาชิก </b>

            <div class="flex flex-col items-center space-y-4 text-left w-full max-w-md mx-auto">
                <div class="w-full">
                <label class="block text-gray-800 text-sm mb-1">Email</label>
                <input type="email" id="memberEmail" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${member.email}" >
                </div>

                <div class="w-full">
                <label class="block text-gray-800 text-sm mb-1">Password</label>
                <input type="password" id="memberPassword" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" >
                </div>

                <div class="w-full">
                <label class="block text-gray-800 text-sm mb-1">ชื่อผู้ใช้</label>
                <input type="text" id="memberName" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${member.name}">
                </div>

                <div class="w-full">
                <label class="block text-gray-800 text-sm mb-1">บทบาท</label>
                <select id="memberRole" onchange="toggleSupervisor()" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm">
                    <option value="Sale" ${member.role === 'Sale' ? 'selected' : ''}>Sale</option>
                    <option value="CEO" ${member.role === 'CEO' ? 'selected' : ''}>CEO</option>
                    <option value="Sale Sup." ${member.role === 'Sale Sup.' ? 'selected' : ''}>Sale Supervisor</option>
                </select>
                </div>

                <div class="w-full">
                <div id="supervisorSection" style="display: ${member.role === 'Sale' ? 'block' : 'none'};" class="mt-4">
                    <label class="block text-gray-800 text-sm mb-1">Sales Supervisor</label>
                    <select id="supervisorDropdown" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm">
                        <!-- options จะเติมโดย toggleSupervisor() -->
                    </select>
                </div>
                </div>
                </div>
            </div>
        `,
        didOpen: () => {
            toggleSupervisor();
            if (member.role === "Sale" && member.supervisorId) {
                const dropdown = document.getElementById("supervisorDropdown");
                setTimeout(() => {
                    dropdown.value = member.supervisorId;
                }, 0); // รอให้ toggleSupervisor เติม option ก่อน
            }
        },
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
            const name = document.getElementById("memberName").value;
            const role = document.getElementById("memberRole").value;

            if (!email || !name || !role) {
                Swal.showValidationMessage("กรุณากรอกข้อมูลให้ครบทุกช่อง");
                return false;
            }

            let supervisorId = null;
            if (role === "Sale") {
                supervisorId = document.getElementById("supervisorDropdown").value;
                if (!supervisorId) {
                    Swal.showValidationMessage("กรุณาเลือก Sales Supervisor");
                    return false;
                }
            }

            // อัปเดตข้อมูล
            member.email = email;
            member.name = name;
            member.role = role;
            if (role === "Sale") {
                member.supervisorId = parseInt(supervisorId);
            } else {
                delete member.supervisorId;
            }

            renderTable();

            Swal.fire({
                title: "สำเร็จ!",
                text: "แก้ไขข้อมูลสมาชิกเรียบร้อยแล้ว",
                icon: "success",
                confirmButtonColor: "#2D8C42",
                confirmButtonText: "ตกลง"
            });
        }
    });
}


=======
>>>>>>> origin/jeng-branch
    renderTable();
    
</script>




    <!-- **************************************************************************** -->

    <!-- </form> -->
@endsection
