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
        <input type="text" id="searchInput" placeholder="ค้นหาชื่อ อีเมล หรือบทบาท" class="w-full p-2 border border-gray-300 rounded mb-3">

        <!-- Dropdown: Sale Supervisor -->
        <div class="mb-3">
            <label class="block text-gray-600 mb-1">Sale Supervisor</label>
            <select id="supervisorSelect" class="w-full p-2 border border-gray-300 rounded">
                
            </select>
        </div>

        <!-- Dropdown: Role -->
        <div class="mb-3">
            <label class="block text-gray-600 mb-1">บทบาท</label>
            <select id="roleSelect" class="w-full p-2 border border-gray-300 rounded">
                <option value="" selected disabled class="hidden">ค้นหาด้วยตำแหน่ง</option>
                <option value="Sale">Sale</option>
                <option value="Sale Sup.">Sale Supervisor</option>
                <option value="CEO">CEO</option>
            </select>
        </div>


        <!-- Result Count -->
        <p class="text-gray-700" id="resultCount">ผลลัพธ์ 0 รายการ</p>
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
    let members = [
        { id: 1, name: "พีระพัท", email: "per@gmail.com", role: "Sale", supervisorId: 5 },
        { id: 2, name: "กานต์", email: "knn@gmail.com", role: "CEO" },
        { id: 3, name: "อิทธิ์", email: "itt@gmail.com", role: "Sale", supervisorId: 7 },
        { id: 4, name: "เจษฎา", email: "jess@gmail.com", role: "Sale", supervisorId: 5 },
        { id: 5, name: "บุญมี", email: "bun@gmail.com", role: "Sale Sup." },
        { id: 6, name: "เอกรินทร์", email: "egn@gmail.com", role: "CEO" },
        { id: 7, name: "อิศรา", email: "isra@gmail.com", role: "Sale Sup." },
        { id: 8, name: "มีนา", email: "me@gmail.com", role: "Sale", supervisorId: 7 },
        { id: 9, name: "น้ำทิพย์", email: "nam@gmail.com", role: "Sale", supervisorId: 5 },
        { id: 10, name: "โอภาส", email: "oop@gmail.com", role: "CEO" },
        { id: 11, name: "ดลภพ", email: "dol@gmail.com", role: "CEO" }
    ];

    let currentPage = 1;
    const rowsPerPage = 10;
    let currentSort = { column: null, ascending: true };

    
    function renderTable(filteredData = null) {
        const tableBody = document.getElementById("tableBody");
        tableBody.innerHTML = "";

        const dataToRender = filteredData || members;

        const start = (currentPage - 1) * rowsPerPage;
        const paginatedData = dataToRender.slice(start, start + rowsPerPage);

        // แสดงจำนวนผลลัพธ์
        const resultCount = document.querySelector("#resultCount");
        resultCount.textContent = `ผลลัพธ์ ${dataToRender.length} รายการ`;

        paginatedData.forEach((member) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td class="py-3 px-4 w-16">${member.id}</td>
                <td class="py-3 px-4 max-w-[200px]">
                    <div class="font-semibold truncate" title="${member.name}">${member.name}</div>
                    <div class="text-sm text-gray-500 truncate" title="${member.email}">${member.email}</div>
                </td>
                <td class="py-3 px-4 w-32 truncate" title="${member.role}">${member.role}</td>
                <td class="py-3 px-1 w-10 text-center relative">
                    <button onclick="toggleMenu(event, ${member.id})">&#8230;</button>
                </td>
            `;
            tableBody.appendChild(row);
        });

        renderPagination(dataToRender);
}

    // ฟังก์ชันสำหรับแสดงปุ่มเปลี่ยนหน้า
    function renderPagination(dataToRender) {
        const pagination = document.getElementById("pagination");
        pagination.innerHTML = ""; //ล้างข้อมูลเก่า

        const totalPages = Math.ceil(dataToRender.length / rowsPerPage);

        // Show pagination even if there's only one page
        if (totalPages === 1) {
            const prevBtn = document.createElement("button");
            prevBtn.innerHTML = '<span class="icon-[material-symbols--chevron-left-rounded]"></span>';
            prevBtn.className = `px-3 py-1 text-gray-400 cursor-not-allowed text-5xl`;
            prevBtn.disabled = true;
            pagination.appendChild(prevBtn);

            const pageBtn = document.createElement("button");
            pageBtn.innerText = `1`;
            pageBtn.className = `px-4 py-2 mx-1 rounded-lg text-base font-semibold bg-blue-600 text-white`;
            pagination.appendChild(pageBtn);

            const nextBtn = document.createElement("button");
            nextBtn.innerHTML = '<span class="icon-[material-symbols--chevron-right-rounded]"></span>';
            nextBtn.className = `px-3 py-1 text-gray-400 cursor-not-allowed text-5xl`;
            nextBtn.disabled = true;
            pagination.appendChild(nextBtn);

            return;
        }

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

    // ฟังก์ชันสำหรับเปลี่ยนหน้า
    function goToPage(pageNumber) {
        currentPage = pageNumber;
        renderTable();
    }
    
    // ฟังก์ชันสำหรับค้นหาข้อมูล
    function filterAll() {
    const searchVal = document.getElementById("searchInput").value.toLowerCase();
    const supervisorId = document.getElementById("supervisorSelect").value;
    const roleVal = document.getElementById("roleSelect").value;

    let filtered = members.filter(m => {
        const matchesSearch =
            m.id.toString().includes(searchVal) ||
            m.name.toLowerCase().includes(searchVal) ||
            m.email.toLowerCase().includes(searchVal) ||
            m.role.toLowerCase().includes(searchVal);

        const matchesSupervisor = !supervisorId || (
            m.role === "Sale" && m.supervisorId?.toString() === supervisorId
        );

        const matchesRole = !roleVal || m.role === roleVal;

        return matchesSearch && matchesSupervisor && matchesRole;
    });

    currentPage = 1;
    renderTable(filtered);
}

    // ฟังก์ชันสำหรับกรองข้อมูลตาม Supervisor
    function populateSupervisorDropdown() {
        const supervisorSelect = document.getElementById("supervisorSelect");
        supervisorSelect.innerHTML = `<option value="" selected disabled class="hidden">แสดงสมาชิก</option>
`; // reset first

        const supervisors = members.filter(m => m.role === "Sale Sup.");
        supervisors.forEach(sup => {
            const option = document.createElement("option");
            option.value = sup.id;
            option.textContent = `${sup.name} - ${sup.email}`;
            supervisorSelect.appendChild(option);
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        populateSupervisorDropdown();
        renderTable();

        document.getElementById("searchInput").addEventListener("input", filterAll);
        document.getElementById("supervisorSelect").addEventListener("change", filterAll);
        document.getElementById("roleSelect").addEventListener("change", filterAll);
    });




    // ฟังก์ชันที่แสดงเมื่อกดคลิกที่ปุ่ม "Meatballbar"
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
            <button class="block w-full px-4 py-2 text-white border border-gray-400 bg-blue-600 rounded-lg hover:bg-blue-700" style="background-color: #3062B8" 
                onclick="document.getElementById('contextMenu').classList.add('hidden'); activeMenuId = null; editMember(${id})">
                แก้ไข
            </button>
            <button class="block w-full px-4 py-2 text-white bg-red-600 border border-gray-400 rounded-lg hover:bg-red-700" style="background-color: #CF3434"
                onclick="document.getElementById('contextMenu').classList.add('hidden'); activeMenuId = null; deleteMember(${id})">
                ลบ
            </button>
        `;

        

        menu.classList.remove("hidden");

        // **แสดงเมนูก่อนเพื่อให้ offsetWidth ทำงาน**
        menu.classList.remove("hidden");

        document.addEventListener("click", function () {
        const menu = document.getElementById("contextMenu");
        if (!menu.classList.contains("hidden")) {
            menu.classList.add("hidden");
            activeMenuId = null;
        }
});


        // ตั้งตำแหน่งเมนูใหม่
        const top = parentCell.offsetTop + parentCell.offsetHeight - 60; // ลดลงมานิด (4px)
        const left = parentCell.offsetLeft + parentCell.offsetWidth - menu.offsetWidth;

        menu.style.position = "absolute";
        menu.style.top = `${top}px`;
        menu.style.left = `${left}px`;

        // เพิ่ม z-index ให้เมนูเป็นค่าเล็กสุด เพื่อให้แถบด้านล่างทับ
        menu.style.zIndex = "5"; // ให้เมนูอยู่ข้างหลังแถบด้านล่าง

    }



    function sortTable(column) {
        if (currentSort.column === column) {
            currentSort.ascending = !currentSort.ascending;
        } else {
            currentSort.column = column;
            currentSort.ascending = true;
        }
        members.sort((a, b) => (a[column] < b[column] ? (currentSort.ascending ? -1 : 1) : (a[column] > b[column] ? (currentSort.ascending ? 1 : -1) : 0)));
        renderTable();
    }
//ดูรายละเอียดสมาชิก
    function viewDetail(id) {
        const member = members.find(item => item.id === id);

        // เช็คถ้าสมาชิกเป็น "Sale" และมี Sales Supervisor
        let supervisorInfo = "";
        if (member.role === "Sale" && member.supervisorId) {
            const supervisor = members.find(item => item.id === member.supervisorId);
            if (supervisor) {
                supervisorInfo = `
                <div class="w-full">
                    <label class="block text-gray-800 text-sm mb-1">Sales Supervisor</label>
                    <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${supervisor.name} - ${supervisor.email}" readonly>
                </div>
                `;
            } else {
                supervisorInfo = `
                <div class="w-full">
                    <label class="block text-gray-800 text-sm mb-1">Sales Supervisor</label>
                    <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="ไม่พบ Supervisor" readonly>
                </div>
                `;
            }
        }

        Swal.fire({
            html: `
    
                <b class=text-gray-800 text-xl >รายละเอียดข้อมูลสมาชิก</b>
                <div class="flex flex-col mt-4 items-center space-y-4 text-left w-full max-w-md mx-auto">
                    <div class="w-full">
                    <label class="block text-gray-800 text-sm mb-1">ชื่อสมาชิก</label>
                        <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${member.name}" readonly>
                    </div>

                    <div class="w-full">
                    <label class="block text-gray-800 text-sm mb-1">อีเมล</label>
                        <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${member.email}" readonly>
                    </div>

                    <div class="w-full">
                    <label class="block text-gray-800 text-sm mb-1">วันที่เพิ่ม</label>
                        <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="17 ก.ย. 2568" readonly>
                    </div>

                    <div class="w-full">
                    <label class="block text-gray-800 text-sm mb-1">บทบาท</label>
                        <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${member.role}" readonly>
                    </div>

                    <div class="w-full">
                    <label class="block text-gray-800 text-sm mb-1">เพิ่มโดย</label>
                        <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="jeng@gmail.com" readonly>
                    </div>

                    ${supervisorInfo} <!-- แสดง Sales Supervisor ถ้ามี -->
                </div>
            `,
            customClass: {
                popup: 'custom-popup'
            },
            confirmButtonText: "ยืนยัน",
            confirmButtonColor: "#2D8C42",
        });

    }

//เพิ่มสมาชิก
    function addMember() {
        Swal.fire({

            html: 
            `<div class="flex flex-col items-center">
                <span class="iconify" data-icon="material-symbols-light:edit-square-rounded" data-width="64" data-height="64"></span>
            </div>
            <b class=text-gray-800 text-xl mb-1>สร้างสมาชิก </b>

            <div class="flex flex-col items-center space-y-4 text-left w-full max-w-md mx-auto">
                <div class="w-full">
                <label class="block text-gray-800 text-sm mb-1">Email</label>
                <input type="email" id="memberEmail" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" >
                </div>

                <div class="w-full">
                <label class="block text-gray-800 text-sm mb-1">Password</label>
                <input type="password" id="memberPassword" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" >
                 </div>

                <div class="w-full">
                <label class="block text-gray-800 text-sm mb-1">ชื่อผู้ใช้</label>
                <input type="text" id="memberName" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm">
                </div>

                <div class="w-full">
                <label class="block text-gray-800 text-sm mb-1">บทบาท</label>
                <select id="memberRole" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" onchange="toggleSupervisor()">
                        <option value="" selected disabled class="hidden">-- เลือก บทบาท --</option>
                        <option value="Sale">Sale</option>
                        <option value="CEO">CEO</option>
                        <option value="Sale Sup.">Sale Supervisor</option>
                    </select>
                </div>

                <div class="w-full">
                    <!-- ตรงนี้จะแสดงเมื่อเลือก Sale -->
                    <div id="supervisorSection" style="display: none;" class="mt-4">
                    <label class="block text-gray-800 text-sm mb-1">Sales supervisor</label>
                    <select id="supervisorDropdown" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm">
                            <option value="" selected disabled>เลือก Sales Supervisor</option>
                            ${members.filter(member => member.role === 'Sale Sup.').map(supervisor => 
                                `<option value="${supervisor.id}">${supervisor.name} - ${supervisor.email}</option>`
                            ).join('')}
                        </select>
                    </div>
                </div>
                </div>`,
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
                <b class=text-gray-800>แก้ไขสมาชิก </b>
            `,
            html: `
            
                <div class="flex flex-col space-y-1 text-left">
                    <label class="font-semibold text-gray-800">Email</label>
                    <input type="email" id="memberEmail" class="w-full p-2 border border-gray-300 rounded mb-3" value="${member.email}" >

                    <label class="font-semibold text-gray-800">Password</label>
                    <input type="password" id="memberPassword" class="w-full p-2 border border-gray-300 rounded mb-3" >

                    <label class="font-semibold text-gray-800">ชื่อผู้ใช้</label>
                    <input type="text" id="memberName" class="w-full p-2 border border-gray-300 rounded mb-3" value="${member.name}">

                    <label class="font-semibold text-gray-800">บทบาท</label>
                    <select id="memberRole" onchange="toggleSupervisor()" class="swal2-input w-full h-10 text-lg px-3 text-gray-800 border border-gray-300 rounded">
                        <option value="Sale" ${member.role === 'Sale' ? 'selected' : ''}>Sale</option>
                        <option value="CEO" ${member.role === 'CEO' ? 'selected' : ''}>CEO</option>
                        <option value="Sale Sup." ${member.role === 'Sale Sup.' ? 'selected' : ''}>Sale Supervisor</option>
                    </select>

                    <div id="supervisorSection" style="display: ${member.role === 'Sale' ? 'block' : 'none'};" class="mt-4">
                        <label class="font-semibold text-gray-800">Sales Supervisor</label>
                        <select id="supervisorDropdown" class="swal2-input w-full h-10 text-lg px-3 text-gray-800 border border-gray-300 rounded">
                            <!-- options จะเติมโดย toggleSupervisor() -->
                        </select>
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

    // ฟังก์ชันสำหรับลบสมาชิก
    function deleteMember(id) {
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
                members = members.filter(member => member.id !== id);
                
                // คำนวณจำนวนหน้าหลังจากลบข้อมูล
                const totalPages = Math.ceil(members.length / rowsPerPage);

                // ถ้าหน้าเกินจำนวนหน้าใหม่ เช่น ถ้าปัจจุบันอยู่ที่หน้า 3 แต่เหลือแค่ 2 หน้า
                if (currentPage > totalPages) {
                    currentPage = totalPages; // ไปที่หน้าสุดท้ายที่ยังมีข้อมูล
                }

                // ถ้าหน้าเกินจำนวนหน้าใหม่ (ตัวอย่างเช่น ลบจนหน้า 3 ว่าง) ให้ไปที่หน้าก่อนหน้า
                if (currentPage > 1 && members.length > 0) {
                    currentPage--; // ย้ายไปหน้าก่อนหน้า
                }

                // รีเฟรชตารางา
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