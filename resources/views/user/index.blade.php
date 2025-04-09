
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
            <button class="bg-blue-500 hover:bg-blue-700 text-white border border-gray-400 font-bold py-2 px-4 rounded" style="background-color: #3062B8" onclick="addMember()" >
                สร้างสมาชิก
            </button>
        </div>

        <!-- Search Input -->
        <input type="text" id="searchInput" placeholder="ค้นหาชื่อ อีเมล หรือบทบาท" class="w-full p-2 border border-gray-300 rounded mb-3">

        <!-- Dropdown: Sale Supervisor -->
        <div class="mb-3">
            <label class="block text-gray-600 mb-1">Sale Supervisor</label>
            <select id="supervisorSelect" class="w-full p-2 border border-gray-300 rounded">
                <option value="">ทั้งหมด</option>
            </select>
        </div>


        <!-- Dropdown: Role -->
        <div class="mb-3">
            <label class="block text-gray-600 mb-1">บทบาท</label>
            <select id="roleSelect" class="w-full p-2 border border-gray-300 rounded">
                <option value="" selected disabled class="hidden">ค้นหาด้วยตำแหน่ง</option>
                <option value="Sale">Sale</option>
                <option value="supervisor">Sale Supervisor</option>
                <option value="CEO">CEO</option>
            </select>
        </div>


        <!-- Result Count -->
        <p class="text-gray-700" id="resultCount">ผลลัพธ์ 0 รายการ</p>
    </div>


    <!-- **************************************************************************** -->

<!-- Pagination Controls -->
<div class="overflow-x-auto">
    <table class="w-full mt-5 border-collapse rounded-lg overflow-hidden ">
        <thead class="text-gray-800 text-md" style="background-color: #B5CFF5">
            <tr>
                <th scope="col" class="py-2 px-4 text-left">ID</th>
                <th class="py-3 px-4 text-left min-w-[200px]">ชื่อ / อีเมล</th>
                <th class="py-3 px-4 text-center max-w-[150px]">บทบาท</th>
                <th class="py-3 px-1 w-7 text-center">&#8230;</th>
              </tr>
        </thead>

        <tbody id="tableBody" class="bg-white divide-y divide-gray-200 text-sm"></tbody>
    </table>
</div>

<!-- Pagination Controls -->
<div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>

<!-- contextMenu Controls-->
<div id="contextMenu" class="hidden absolute bg-white shadow-lg rounded-lg w-32 z-50 p-2 space-y-2"></div>

<script>
    let members = [];
    let currentPage = 1;
    const rowsPerPage = 10;
    let totalMembers = 0;
    let currentSort = { column: 'id', ascending: true };

    document.addEventListener("DOMContentLoaded", () => {
        fetchMembers();
        document.getElementById("searchInput").addEventListener("input", () => {
            currentPage = 1;
            fetchMembers();
        });
        document.getElementById("supervisorSelect").addEventListener("change", () => {
            currentPage = 1;
            fetchMembers();
        });
        document.getElementById("roleSelect").addEventListener("change", () => {
            currentPage = 1;
            fetchMembers();
        });
    });

    async function fetchMembers() {
        const search = document.getElementById("searchInput").value || '';
        const supervisorId = document.getElementById("supervisorSelect").value || '';
        const role = document.getElementById("roleSelect").value || '';

        let query = `?page=${currentPage}&limit=${rowsPerPage}&search=${encodeURIComponent(search)}&supervisor_id=${supervisorId}&role=${encodeURIComponent(role)}`;

        try {
            const response = await fetch(`{{ route('api.user.query') }}${query}`);
            const result = await response.json();
            members = result.data || [];
            totalMembers = result.total || 0;

            document.getElementById("resultCount").textContent = `ผลลัพธ์ ${totalMembers} รายการ`;

            populateSupervisorDropdown(); // เติม dropdown ทุกครั้งเผื่อรายการเปลี่ยน
            renderTable();
            renderPagination(totalMembers);
        } catch (error) {
            console.error("Error fetching members:", error);
        }
    }


    function renderTable() {
        const tableBody = document.getElementById("tableBody");
        tableBody.innerHTML = "";

        members.forEach((member) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td class="py-3 px-4 w-16 text-md">${member.user_id}</td>
                <td class="py-3 px-4 max-w-[200px]">
                    <div class="font-semibold text-md" title="${member.name}">${member.name}</div>
                    <div class="text-sm text-gray-400 truncate" title="${member.email}">${member.email}</div>
                </td>
                <td class="py-3 px-4 w-32 truncate text-center text-md" title="${member.role_name}">${member.role_name}</td>
                <td class="py-3 px-1 w-10 text-center relative">
                    <button onclick="toggleMenu(event, ${member.user_id})">&#8230;</button>
                </td>
            `;
            tableBody.appendChild(row);
        });
    }

    function renderPagination(totalItems) {
        const pagination = document.getElementById("pagination");
        pagination.innerHTML = "";

        const totalPages = Math.ceil(totalItems / rowsPerPage);
        const maxVisible = 1;
        let startPage = Math.max(1, currentPage - maxVisible);
        let endPage = Math.min(totalPages, currentPage + maxVisible);

        if (totalPages <= 1) return;

        const createPageButton = (page, isActive = false) => {
            const btn = document.createElement("button");
            btn.innerText = page;
            btn.className = `min-w-[36px] h-10 px-3 mx-1 rounded-lg text-sm font-medium ${isActive ? "bg-blue-600 text-white" : "bg-white border border-gray-300 text-black hover:bg-gray-100"}`;
            btn.onclick = () => goToPage(page);
            return btn;
        };

        const createEllipsis = () => {
            const btn = document.createElement("button");
            btn.innerText = "...";
            btn.className = "px-3 text-gray-500 hover:text-black rounded hover:bg-gray-100";
            btn.onclick = () => {
                Swal.fire({
                    title: "ไปยังหน้าที่...",
                    input: "number",
                    inputLabel: `กรอกหมายเลขหน้า (1 - ${totalPages})`,
                    inputAttributes: { min: 1, max: totalPages, step: 1 },
                    showCancelButton: true,
                    confirmButtonText: "ไปเลย!",
                    confirmButtonColor: "#3062B8",
                    inputValidator: (value) => {
                        if (!value || isNaN(value)) return "กรุณากรอกตัวเลข";
                        if (value < 1 || value > totalPages) return `หน้าต้องอยู่ระหว่าง 1 ถึง ${totalPages}`;
                        return null;
                    }
                }).then(result => {
                    if (result.isConfirmed) goToPage(parseInt(result.value));
                });
            };
            return btn;
        };

        const prevBtn = document.createElement("button");
        prevBtn.innerHTML = "&lt;";
        prevBtn.className = `min-w-[40px] h-10 px-3 mx-1 rounded-lg text-xl font-bold ${currentPage === 1 ? "text-gray-300 bg-white border border-gray-200 cursor-not-allowed" : "text-blue-600 bg-white border border-gray-300 hover:bg-blue-50"}`;
        prevBtn.disabled = currentPage === 1;
        prevBtn.onclick = () => goToPage(currentPage - 1);
        pagination.appendChild(prevBtn);

        if (startPage > 1) {
            pagination.appendChild(createPageButton(1));
            if (startPage > 2) pagination.appendChild(createEllipsis());
        }

        for (let i = startPage; i <= endPage; i++) {
            pagination.appendChild(createPageButton(i, i === currentPage));
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) pagination.appendChild(createEllipsis());
            pagination.appendChild(createPageButton(totalPages));
        }

        const nextBtn = document.createElement("button");
        nextBtn.innerHTML = "&gt;";
        nextBtn.className = `min-w-[40px] h-10 px-3 mx-1 rounded-lg text-xl font-bold ${currentPage === totalPages ? "text-gray-300 bg-white border border-gray-200 cursor-not-allowed" : "text-blue-600 bg-white border border-gray-300 hover:bg-blue-50"}`;
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.onclick = () => goToPage(currentPage + 1);
        pagination.appendChild(nextBtn);
    }

    function goToPage(pageNumber) {
        currentPage = pageNumber;
        fetchMembers();
    }

    // ฟังก์ชันสำหรับเรียงข้อมูลตามคอลัมน์ที่เลือก
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

    // ฟังก์ชันสำหรับกรองข้อมูลทั้งหมด
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

        currentPage = 1; // รีเซ็ตหน้าเป็นหน้าแรกเมื่อมีการกรองข้อมูล
        renderTable(filtered); // เรียก renderTable โดยส่งข้อมูลที่กรองแล้ว
    }

    let supervisors = [];
    // ฟังก์ชันสำหรับกรองข้อมูลตาม Supervisor
    async function populateSupervisorDropdown() {
    const supervisorSelect = document.getElementById("supervisorSelect");
        supervisorSelect.innerHTML = `<option value="">ทั้งหมด</option>`;

        try {
            const response = await fetch("{{ route('api.user.query.all') }}?role=supervisor");
            const result = await response.json();
            supervisors = result.data || []; // เก็บข้อมูล supervisor ไว้ใช้

            supervisors.forEach(sup => {
                const option = document.createElement("option");
                option.value = sup.user_id;
                option.textContent = `${sup.name} - ${sup.email}`;
                supervisorSelect.appendChild(option);
            });
        } catch (error) {
            console.error("โหลด supervisor ไม่ได้:", error);
            supervisorSelect.innerHTML += `<option value="">(โหลดรายชื่อ supervisor ไม่สำเร็จ)</option>`;
        }
    }




    // เมื่อโหลดหน้าเว็บเสร็จ ให้ดึงข้อมูลสมาชิกจาก API
    document.addEventListener("DOMContentLoaded", () => {
        fetchMembers(); // เรียกดึงข้อมูลจาก API
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
            <button class="block w-full px-4 py-2 text-white border border-gray-400 bg-blue-600 rounded-lg hover:bg-blue-700 whitespace-nowrap" style="background-color: #3062B8" 
                onclick="document.getElementById('contextMenu').classList.add('hidden'); activeMenuId = null; viewDetail(${id})">
                ดูรายละเอียด
            </button>
            <button class="block w-full px-4 py-2 text-white border border-gray-400 bg-blue-600 rounded-lg hover:bg-blue-700" style="background-color: #3062B8" 
                onclick="document.getElementById('contextMenu').classList.add('hidden'); activeMenuId = null; editMember(${id})">
                แก้ไข
            </button>
            <button class="block w-full px-4 py-2 text-white border border-gray-400 bg-red-600 rounded-lg hover:bg-red-700" style="background-color: #CF3434" 
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

    // ฟังก์ชันสำหรับดูรายละเอียดสมาชิก
    function viewDetail(id) {
        const member = members.find(item => item.user_id === id);

        let supervisorInfo = "";
        if (member.role_name.toLowerCase() === "sale" && member.manager) {
            const supervisor = supervisors.find(sup => sup.user_id === member.manager);
            supervisorInfo = supervisor ? `
                <div class="w-full">
                    <label class="font-semibold text-gray-800 text-sm">Sales Supervisor</label>
                    <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" 
                        value="${supervisor.name} - ${supervisor.email}" readonly>
                </div>` : `
                <div class="w-full">
                    <label class="font-semibold text-gray-800 text-sm">Sales Supervisor</label>
                    <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" 
                        value="ไม่พบ Supervisor" readonly>
                </div>`;
        }

        Swal.fire({
            html: `
                <div class="flex flex-col text-3xl mb-6 mt-4">
                    <b class=text-gray-800>รายละเอียดข้อมูลสมาชิก</b>
                </div>
                <div class="flex flex-col space-y-2 text-left">
                    <div class="w-full">
                        <label class="font-medium text-gray-800 text-sm">ชื่อสมาชิก</label>
                        <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${member.name}" readonly>
                    </div>
                    <div class="w-full">
                        <label class="font-medium text-gray-800 text-sm">อีเมล</label>
                        <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${member.email}" readonly>
                    </div>
                    <div class="w-full">
                        <label class="font-medium text-gray-800 text-sm">วันที่เพิ่ม</label>
                        <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${formatThaiDate(member.created_at)}" readonly>
                    </div>
                    <div class="w-full">
                        <label class="font-medium text-gray-800 text-sm">บทบาท</label>
                        <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${member.role_name}" readonly>
                    </div>
                    ${supervisorInfo}
                </div>`,
            customClass: { popup: 'custom-popup' },
            confirmButtonText: "ยืนยัน",
            confirmButtonColor: "#2D8C42",
        });
    }

    // แปลงวันที่เป็นภาษาไทย
    function formatThaiDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString("th-TH", {
                year: "numeric",
                month: "short",
                day: "numeric"
            });
        }

    // ฟังก์ชันสำหรับเพิ่มสมาชิกใหม่
    function addMember() {
        Swal.fire({

            html: 
                `
                <div class="flex flex-col items-center mb-1">
                    <span class="iconify" data-icon="material-symbols-light:edit-square-rounded" data-width="70" data-height="70"></span>
                </div>
                <div class="flex flex-col text-3xl mb-6 mt-4">
                     <b class=text-gray-800 >สร้างสมาชิก</b>
                 </div>
                <div class="flex flex-col space-y-2 text-left">
                    <div class="w-full">
                        <label class="font-semibold text-gray-800 text-sm">Email</label>
                        <input type="email" id="memberEmail" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" >
                    </div>
                <div class="w-full">
                    <label class="font-semibold text-gray-800 text-sm">Password</label>
                    <input type="password" id="memberPassword" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" >
                </div>
                <div class="w-full">
                    <label class="font-medium text-gray-800 text-sm">ชื่อผู้ใช้</label>
                    <input type="text" id="memberName" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="w-full">
                    <label class="font-medium text-gray-800 text-sm">บทบาท</label>
                    <select id="memberRole" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" onchange="toggleSupervisor()">
                        <option value="" selected disabled class="hidden">-- เลือก บทบาท --</option>
                        <option value="sale">Sale</option>
                        <option value="ceo">CEO</option>
                        <option value="supervisor">Sale Supervisor</option>
                    </select>
                </div>
                <div class="w-full">
                    <!-- ตรงนี้จะแสดงเมื่อเลือก Sale -->
                    <div id="supervisorSection" style="display: none;" class="mt-4">
                        <label class="font-medium text-gray-800 text-sm">Sales supervisor</label>
                        <select id="supervisorDropdown" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm">
                            <option value="" selected disabled>เลือก Sales Supervisor</option>
                            ${members.filter(member => member.role === 'supervisor').map(supervisor => 
                                `<option value="${supervisor.user_id}">${supervisor.name} - ${supervisor.email}</option>`
                            ).join('')}
                        </select>
                    </div>
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
            preConfirm: async () => {
            const email = document.getElementById("memberEmail").value;
            const password = document.getElementById("memberPassword").value;
            const name = document.getElementById("memberName").value;
            const role = document.getElementById("memberRole").value;

            if (!email || !password || !name || !role) {
                Swal.showValidationMessage("กรุณากรอกข้อมูลให้ครบทุกช่อง");
                return false;
            }

            let manager = null;
            if (role === "sale") {
                manager = document.getElementById("supervisorDropdown").value;
                if (!manager) {
                    Swal.showValidationMessage("กรุณาเลือก Sales Supervisor");
                    return false;
                }
            }

            try {
                const response = await fetch("{{ route('api.user.create') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name=\"csrf-token\"]').content
                    },
                    body: JSON.stringify({
                        email: email,
                        password: password,
                        name: name,
                        role_name: role, 
                        user_status: "normal", 
                        manager: manager ? parseInt(manager) : null
                        
                    })
                });

                const result = await response.json();

                if (!response.ok) {
                    Swal.showValidationMessage(result?.message || "เกิดข้อผิดพลาดในการเพิ่มสมาชิก");
                    return false;
                }

                Swal.fire({
                    title: "สำเร็จ!",
                    text: "เพิ่มสมาชิกเรียบร้อยแล้ว",
                    icon: "success",
                    confirmButtonColor: "#2D8C42",
                    confirmButtonText: "ตกลง"
                });

                fetchMembers(); // รีโหลดข้อมูลใหม่

            } catch (error) {
                console.error("Add user error:", error);
                Swal.showValidationMessage("ไม่สามารถเชื่อมต่อ API ได้");
            }
        }

        });
    }

    // ฟังก์ชันนี้สำหรับแสดงหรือซ่อน Sales Supervisor dropdown
    // เรียก API เพื่อโหลด supervisor ทั้งหมด
    async function toggleSupervisor() {
        const role = document.getElementById("memberRole").value;
        const section = document.getElementById("supervisorSection");
        const dropdown = document.getElementById("supervisorDropdown");

        if (role === "sale") {
            section.style.display = "block";
            dropdown.innerHTML = `<option value="" disabled selected hidden>-- กำลังโหลด Supervisor... --</option>`;

            try {
                const response = await fetch("{{ route('api.user.query.all') }}?role=supervisor");
                const result = await response.json();
                const supervisors = result.data || [];

                dropdown.innerHTML = "";

                if (supervisors.length === 0) {
                    dropdown.innerHTML = `<option value="">(ไม่มี Supervisor)</option>`;
                } else {
                    dropdown.innerHTML += `<option value="" disabled selected hidden>-- เลือก Supervisor --</option>`;
                    supervisors.forEach(sup => {
                        dropdown.innerHTML += `<option value="${sup.user_id}">${sup.name} - ${sup.email}</option>`;
                    });
                }

            } catch (error) {
                console.error("ไม่สามารถโหลด supervisor:", error);
                dropdown.innerHTML = `<option value="">โหลด supervisor ไม่สำเร็จ</option>`;
            }
        } else {
            section.style.display = "none";
            dropdown.innerHTML = "";
        }
    }



    // ฟังก์ชันสำหรับแก้ไขสมาชิก
    async function editMember(id) {
    const member = members.find(item => item.user_id === id);

    const result = await Swal.fire({
        html: `
            <div class="flex flex-col items-center mb-1">
                <span class="iconify" data-icon="material-symbols-light:edit-square-rounded" data-width="70" data-height="70"></span>
            </div>
            <div class="flex flex-col text-3xl mb-6 mt-4">
                <b class="text-gray-800">แก้ไขสมาชิก</b>
            </div>
            <div class="flex flex-col space-y-2 text-left">
                <div class="w-full">
                    <label class="font-semibold text-gray-800 text-sm">Email</label>
                    <input type="email" id="memberEmail" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${member.email}">
                </div>

                <div class="w-full">
                    <label class="font-semibold text-gray-800 text-sm">Password</label>
                    <input type="password" id="memberPassword" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${member.password}">
                </div>

                <div class="w-full">
                    <label class="font-medium text-gray-800 text-sm">ชื่อผู้ใช้</label>
                    <input type="text" id="memberName" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${member.name}">
                </div>

                <div class="w-full">
                    <label class="font-medium text-gray-800 text-sm">บทบาท</label>
                    <select id="memberRole" onchange="toggleSupervisor()" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm">
                        <option value="sale" ${member.role_name === 'sale' ? 'selected' : ''}>Sale</option>
                        <option value="ceo" ${member.role_name === 'ceo' ? 'selected' : ''}>CEO</option>
                        <option value="supervisor" ${member.role_name === 'supervisor' ? 'selected' : ''}>Sale Supervisor</option>
                    </select>
                </div>
                 
                <div class="w-full">
                    <div id="supervisorSection" style="display: ${member.role_name === 'sale' ? 'block' : 'none'};" class="mt-4">
                        <label class="font-semibold text-gray-800 text-sm">Sales Supervisor</label>
                        <select id="supervisorDropdown" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm">
                            <!-- options จะเติมโดย toggleSupervisor() -->
                        </select>
                    </div>
                </div>
            </div>
        `,
        didOpen: () => {
            toggleSupervisor();
            if (member.role_name === "sale" && member.supervisorId) {
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
        preConfirm: async () => {
            const email = document.getElementById("memberEmail").value;
            const name = document.getElementById("memberName").value;
            const password = document.getElementById("memberPassword").value;
            const role = document.getElementById("memberRole").value;

            if (!email || !name || !role) {
                Swal.showValidationMessage("กรุณากรอกข้อมูลให้ครบทุกช่อง");
                return false;
            }

            let manager = null;
            if (role === "sale") {
                manager = document.getElementById("supervisorDropdown").value;
                if (!manager) {
                    Swal.showValidationMessage("กรุณาเลือก Sales Supervisor");
                    return false;
                }
            }
            
            if (role === "ceo" || role === "supervisor") {
                manager = null;
            }

            try {
                const response = await fetch("{{ route('api.user.edit') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                    },
                    body: JSON.stringify({
                        user_id: id,
                        email: email,
                        name: name,
                        password: password || undefined,
                        role_name: role,
                        manager: manager ? parseInt(manager) : null,
                        user_status: "normal"

                    })
                });

                const result = await response.json();

                if (!response.ok) {
                    const errorMsg = result?.message || "เกิดข้อผิดพลาด";
                    Swal.showValidationMessage(errorMsg);
                    return false;
                }

                Swal.fire({
                    title: "สำเร็จ!",
                    text: "แก้ไขข้อมูลสมาชิกเรียบร้อยแล้ว",
                    icon: "success",
                    confirmButtonColor: "#2D8C42",
                    confirmButtonText: "ตกลง"
                });

                // รีเฟรชข้อมูลจาก API ใหม่
                fetchMembers();

            } catch (error) {
                Swal.showValidationMessage("เกิดข้อผิดพลาดในการเชื่อมต่อ API");
                console.error("Edit API error:", error);
                return false;
            }
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
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch("{{ route('api.user.delete') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name=\"csrf-token\"]').content 
                    },
                    body: JSON.stringify({
                        user_id: id
                    })
                });

                const result = await response.json();

                if (!response.ok) {
                    Swal.fire({
                        title: "ผิดพลาด",
                        text: result.message || "ไม่สามารถลบข้อมูลได้",
                        icon: "error"
                    });
                    return;
                }

                Swal.fire({
                    title: "ลบแล้ว!",
                    text: "สมาชิกถูกลบเรียบร้อย",
                    icon: "success",
                    confirmButtonColor: "#2D8C42"
                });

                fetchMembers(); // โหลดข้อมูลใหม่

            } catch (error) {
                console.error("ลบสมาชิก error:", error);
                Swal.fire({
                    title: "เกิดข้อผิดพลาด",
                    text: "ไม่สามารถเชื่อมต่อ API ได้",
                    icon: "error"
                });
            }
        }
    });
}


    renderTable();
   
</script>




    <!-- **************************************************************************** -->

    <!-- </form> -->
@endsection