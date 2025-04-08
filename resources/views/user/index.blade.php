@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')

<script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

<div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto">
    <div class="flex justify-between items-center mb-3">
        <h2 class="text-lg font-bold">จัดการสมาชิก</h2>
        <button class="bg-blue-500 hover:bg-blue-700 text-white border border-gray-400 font-bold py-2 px-4 rounded" style="background-color: #3062B8" onclick="addMember()">
            สร้างสมาชิก
        </button>
    </div>

    <input type="text" id="searchInput" placeholder="ค้นหาชื่อ อีเมล หรือบทบาท" class="w-full p-2 border border-gray-300 rounded mb-3">

    <div class="mb-3">
        <label class="block text-gray-600 mb-1">Sale Supervisor</label>
        <select id="supervisorSelect" class="w-full p-2 border border-gray-300 rounded"></select>
    </div>

    <div class="mb-3">
        <label class="block text-gray-600 mb-1">บทบาท</label>
        <select id="roleSelect" class="w-full p-2 border border-gray-300 rounded">
            <option value="" selected disabled class="hidden">ค้นหาด้วยตำแหน่ง</option>
            <option value="sale">Sale</option>
            <option value="supervisor">Sale Supervisor</option>
            <option value="ceo">CEO</option>
        </select>
    </div>

    <p class="text-gray-700" id="resultCount">ผลลัพธ์ 0 รายการ</p>
</div>

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

<div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>

<script>
let members = [];
let currentPage = 1;
const rowsPerPage = 10;

async function fetchUsers() {
    const search = document.getElementById("searchInput").value;
    const role = document.getElementById("roleSelect").value;
    const supervisor = document.getElementById("supervisorSelect").value;

    let url = `{{ route('api.user.query') }}?limit=${rowsPerPage}&page=${currentPage}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    if (role) url += `&role=${encodeURIComponent(role)}`;
    if (supervisor) url += `&target=${encodeURIComponent(supervisor)}`;

    try {
        const res = await fetch(url);
        const result = await res.json();

        if (!res.ok) throw new Error(result.message);

        members = result.data;
        document.getElementById("resultCount").innerText = `ผลลัพธ์ ${result.total} รายการ`;
        renderTable();
        renderPagination(result.total);
        populateSupervisorDropdown();
    } catch (error) {
        console.error(error);
        Swal.fire("ผิดพลาด", error.message ?? "ไม่สามารถโหลดข้อมูลได้", "error");
    }
}

function renderTable() {
    const tableBody = document.getElementById("tableBody");
    tableBody.innerHTML = "";
    members.forEach(member => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td class="py-3 px-4">${member.user_id}</td>
            <td class="py-3 px-4">
                <div class="font-semibold text-md">${member.name}</div>
                <div class="text-sm text-gray-400 truncate">${member.email}</div>
            </td>
            <td class="py-3 px-4 text-left">${member.role_name}</td>
            <td class="py-3 px-1 text-center">&#8230;</td>
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
    fetchUsers();
}

function populateSupervisorDropdown() {
    const supervisorSelect = document.getElementById("supervisorSelect");
    supervisorSelect.innerHTML = `<option value="">เลือก Sale Supervisor</option>`;
    const supervisors = members.filter(m => m.role_name === "supervisor");
    supervisors.forEach(sup => {
        const option = document.createElement("option");
        option.value = sup.user_id;
        option.textContent = `${sup.name} - ${sup.email}`;
        supervisorSelect.appendChild(option);
    });
}

// Events
window.addEventListener("DOMContentLoaded", () => {
    fetchUsers();
    document.getElementById("searchInput").addEventListener("input", () => {
        currentPage = 1;
        fetchUsers();
    });
    document.getElementById("roleSelect").addEventListener("change", () => {
        currentPage = 1;
        fetchUsers();
    });
    document.getElementById("supervisorSelect").addEventListener("change", () => {
        currentPage = 1;
        fetchUsers();
    });
});
</script>

@endsection