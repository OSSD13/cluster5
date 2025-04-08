
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
    let currentSort = { column: 'id', ascending: true };

    // ฟังก์ชันสำหรับดึงข้อมูลสมาชิกจาก API
    async function fetchMembers() {
        try {
            const response = await fetch('{{ route('api.user.query.all') }}'); // ดึงข้อมูลจาก API
            const result = await response.json();
            members = result.data || []; // เก็บข้อมูลสมาชิกที่ดึงมา
            console.log(members);
            renderTable(); // เรียกฟังก์ชัน renderTable เพื่อแสดงข้อมูล
            populateSupervisorDropdown(); // ป populate ตัวเลือก Supervisor
        } catch (error) {
            console.error('Error fetching members:', error);
        }
    }

    // ฟังก์ชันสำหรับการแสดงข้อมูลในตาราง
    function renderTable(filteredData = null) {
        const tableBody = document.getElementById("tableBody");
        tableBody.innerHTML = "";

        const dataToRender = filteredData || members;

        const start = (currentPage - 1) * rowsPerPage;
        const paginatedData = dataToRender.slice(start, start + rowsPerPage);

        const resultCount = document.querySelector("#resultCount");
        resultCount.textContent = `ผลลัพธ์ ${dataToRender.length} รายการ`;

        paginatedData.forEach((member) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td class="py-3 px-4 w-16">${member.user_id}</td>
                <td class="py-3 px-4">
                    <div class="font-semibold text-md" title="${member.name}">${member.name}</div>
                    <div class="text-sm text-gray-400 truncate" title="${member.email}">${member.email}</div>
                </td>
                <td class="py-3 px-4 text-center" title="${member.role_name}">${member.role_name}</td>
                <td class="py-3 px-1 w-10 text-center relative">
                    <button onclick="toggleMenu(event, ${member.id})">&#8230;</button>
                </td>
            `;
            tableBody.appendChild(row);
        });

        renderPagination(dataToRender);
    }

    // ฟังก์ชันที่ใช้ในการ render pagination
    function renderPagination(dataToRender) {
        const paginationContainer = document.getElementById('pagination');
        paginationContainer.innerHTML = "";

        const totalPages = Math.ceil(dataToRender.length / rowsPerPage);

        const prevBtn = document.createElement("button");
        prevBtn.innerHTML = '<span class="icon-[material-symbols--chevron-left-rounded]"></span>';
        prevBtn.className = `px-3 py-1 ${currentPage === 1 ? "text-gray-400 cursor-not-allowed" : "text-blue-600 cursor-pointer"} text-5xl`;
        prevBtn.disabled = currentPage === 1;
        prevBtn.onclick = () => goToPage(currentPage - 1);
        paginationContainer.appendChild(prevBtn);

        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement("button");
            pageBtn.innerText = i;
            pageBtn.className = `px-4 py-2 mx-1 rounded-lg text-base font-semibold 
                                ${i === currentPage ? "bg-blue-600 text-white" : "bg-white border border-gray-300 text-black cursor-pointer"}`;
            pageBtn.onclick = () => goToPage(i);
            paginationContainer.appendChild(pageBtn);
        }

        const nextBtn = document.createElement("button");
        nextBtn.innerHTML = '<span class="icon-[material-symbols--chevron-right-rounded]"></span>';
        nextBtn.className = `px-3 py-1 ${currentPage === totalPages ? "text-gray-400 cursor-not-allowed" : "text-blue-600 cursor-pointer"} text-5xl`;
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.onclick = () => goToPage(currentPage + 1);
        paginationContainer.appendChild(nextBtn);
    }

    // ฟังก์ชันสำหรับเปลี่ยนหน้า
    function goToPage(pageNumber) {
        currentPage = pageNumber;
        renderTable();
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

    // ฟังก์ชันสำหรับกรองข้อมูลตาม Supervisor
    function populateSupervisorDropdown() {
        const supervisorSelect = document.getElementById("supervisorSelect");
        supervisorSelect.innerHTML = `<option value="" selected disabled class="hidden">แสดงสมาชิก</option>`;

        const supervisors = members.filter(m => m.role === "Sale Sup.");
        supervisors.forEach(sup => {
            const option = document.createElement("option");
            option.value = sup.id;
            option.textContent = `${sup.name} - ${sup.email}`;
            supervisorSelect.appendChild(option);
        });
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
        const member = members.find(item => item.id === id);

        // เช็คถ้าสมาชิกเป็น "Sale" และมี Sales Supervisor
        let supervisorInfo = "";
        if (member.role === "Sale" && member.supervisorId) {
            const supervisor = members.find(item => item.id === member.supervisorId);
            if (supervisor) {
                supervisorInfo = `
                <div class="w-full">
                    <label class="font-semibold text-gray-800 text-sm">Sales Supervisor</label>
                    <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm"" value="${supervisor.name} - ${supervisor.email}" readonly>
                </div>
                `;
            } else {
                supervisorInfo = `
                <div class="w-full">
                    <label class="font-semibold text-gray-800 text-sm">Sales Supervisor</label>
                    <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm"" value="ไม่พบ Supervisor" readonly>
                </div>
                `;
            }
        }

        Swal.fire({
            html: `
                <div class="flex flex-col text-3xl mb-6 mt-4">
                     <b class=text-gray-800 >รายละเอียดข้อมูลสมาชิก</b>
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
                        <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${member.role}" readonly>
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
                        <option value="Sale">Sale</option>
                        <option value="CEO">CEO</option>
                        <option value="Sale Sup.">Sale Supervisor</option>
                    </select>
                </div>
                <div class="w-full">
                    <!-- ตรงนี้จะแสดงเมื่อเลือก Sale -->
                    <div id="supervisorSection" style="display: none;" class="mt-4">
                        <label class="font-medium text-gray-800 text-sm">Sales supervisor</label>
                        <select id="supervisorDropdown" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm">
                            <option value="" selected disabled>เลือก Sales Supervisor</option>
                            ${members.filter(member => member.role === 'Sale Sup.').map(supervisor => 
                                `<option value="${supervisor.id}">${supervisor.name} - ${supervisor.email}</option>`
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


    // ฟังก์ชันนี้สำหรับแสดงหรือซ่อน Sales Supervisor dropdown
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

    // ฟังก์ชันสำหรับแก้ไขสมาชิก
    function editMember(id) {
        const member = members.find(item => item.id === id);

        Swal.fire({
            html: `
             <div class="flex flex-col items-center mb-1">
                    <span class="iconify" data-icon="material-symbols-light:edit-square-rounded" data-width="70" data-height="70"></span>
                </div>
                <div class="flex flex-col text-3xl mb-6 mt-4">
                     <b class=text-gray-800 >แก้ไขสมาชิก</b>
                 </div>
                <div class="flex flex-col space-y-2 text-left">
                    <div class="w-full">
                    <label class="font-semibold text-gray-800 text-sm">Email</label>
                    <input type="email" id="memberEmail" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${member.email}" >
                    </div>

                    <div class="w-full">
                    <label class="font-semibold text-gray-800 text-sm">Password</label>
                    <input type="password" id="memberPassword" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" >
                    </div>

                    <div class="w-full">
                    <label class="font-medium text-gray-800 text-sm">ชื่อผู้ใช้</label>
                    <input type="text" id="memberName" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${member.name}">
                    </div>

                    <div class="w-full">
                    <label class="font-medium text-gray-800 text-sm">บทบาท</label>
                    <select id="memberRole" onchange="toggleSupervisor()" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm">
                        <option value="Sale" ${member.role === 'Sale' ? 'selected' : ''}>Sale</option>
                        <option value="CEO" ${member.role === 'CEO' ? 'selected' : ''}>CEO</option>
                        <option value="Sale Sup." ${member.role === 'Sale Sup.' ? 'selected' : ''}>Sale Supervisor</option>
                    </select>
                    </div>
                     
                    <div class="w-full">
                    <div id="supervisorSection" style="display: ${member.role === 'Sale' ? 'block' : 'none'};" class="mt-4">
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

