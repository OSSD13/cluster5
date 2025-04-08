@extends('layouts.main')

@section('title', 'Branch')

@section('content')
<div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-3">
        <h2 class="text-2xl font-bold text-gray-800">สาขาทั้งหมด</h2>
        <a href="{{ route('branch.create') }}">
            <button class="hover:bg-blue-700 text-white shadow-lg font-bold py-2 px-4 rounded-md whitespace-nowrap border border-gray-300"
                style="background-color: #3062B8">
                สร้างสาขา
            </button>
        </a>
    </div>

    <!-- Search Input -->
    <input type="text" id="searchInput" placeholder="ค้นหาชื่อ อีเมล หรือบทบาท"
        class="w-full p-2 border border-gray-300 rounded mb-3">

    <!-- Role Dropdown -->
    <div class="mb-3">
        <label class="block text-gray-800 mb-1">บทบาท</label>
        <select id="roleFilter" class="w-full p-2 border border-gray-300 rounded-md shadow-lg">
            <option value="">ทั้งหมด</option>
            <option value="sale">Sale</option>
            <option value="ceo">CEO</option>
            <option value="supervisor">Sale Supervisor</option>
        </select>
    </div>

    <!-- Result Count -->
    <p class="text-gray-800" id='resultCount'>ผลลัพธ์ 0 รายการ</p>
</div>

<!-- Results Table -->
<div class="overflow-visible">
    <table class="w-full mt-5 border-collapse rounded-md overflow-hidden table-fixed">
        <thead class="text-gray-800" style="background-color: #B5CFF5">
            <tr>
                <th class="py-3 px-4 w-13 text-left">ID</th>
                <th class="py-3 px-4 text-left whitespace-nowrap">ชื่อสาขา</th>
                <th class="py-3 px-4 text-left whitespace-nowrap">ประเภท</th>
                <th class="py-3 px-4 text-left whitespace-nowrap">เพิ่มโดย</th>
                <th class="py-3 px-1 w-7 text-center"></th>
            </tr>
        </thead>
        <tbody id="tableBody" class="bg-white divide-y divide-gray-200"></tbody>
    </table>
</div>

<!-- Pagination -->
<div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>
@endsection

@section('script')
<script>
    let branches = [];
    let currentPage = 1;
    let rowsPerPage = 10;
    let totalItems = 0;
    const apiUrl = `{{ route('api.branch.query') }}`;

    let searchTimeout;
    document.getElementById("searchInput").addEventListener("input", () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => fetchBranches(1), 300);
    });

    document.getElementById("roleFilter").addEventListener("change", () => fetchBranches(1));

    async function fetchBranches(page = 1) {
        const search = document.getElementById("searchInput").value.trim();
        const role = document.getElementById("roleFilter").value;

        const params = new URLSearchParams({ page, limit: rowsPerPage });
        if (search) params.append('search', search);
        if (role) params.append('role', role);

        try {
            const response = await fetch(`${apiUrl}?${params.toString()}`);
            const json = await response.json();
            branches = json.data || [];
            currentPage = json.page || 1;
            totalItems = json.total || 0;
            rowsPerPage = json.limit || 10;
            renderTable();
        } catch (error) {
            console.error("ไม่สามารถโหลดข้อมูลได้:", error);
            document.getElementById("tableBody").innerHTML = `
                <tr><td colspan="5" class="text-center py-4 text-red-500">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>
            `;
        }
    }

    function renderTable() {
        const tableBody = document.getElementById("tableBody");
        const resultCount = document.getElementById("resultCount");
        tableBody.innerHTML = "";
        resultCount.innerText = `ผลลัพธ์ ${totalItems} รายการ`;

        if (branches.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-gray-500">ไม่พบข้อมูล</td></tr>`;
            return;
        }

        branches.forEach((branch) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td class="py-3 px-4 w-16">${branch.bs_id}</td>
                <td class="py-3 px-4 truncate">${branch.bs_name}</td>
                <td class="py-3 px-4 w-32 truncate">${branch.poit_name}</td>
                <td class="py-3 px-4 w-32 truncate">${branch.bs_manager_name}</td>
                <td class="py-3 px-1 w-10 text-center relative">
                    <button class="cursor-pointer" onclick="toggleMenu(event, ${branch.bs_id})">&#8230;</button>
                    <div id="menu-${branch.bs_id}" class="hidden absolute right-0 mt-2 bg-white shadow-lg rounded-xl w-32 z-50 p-2 space-y-2">
                        <button class="block w-full px-4 py-2 text-white border border-gray-400 rounded-md shadow-lg hover:bg-blue-700 cursor-pointer" style="background-color: #3062B8"
                            onclick="window.location.href='{{ route('branch.manage.index') }}'">จัดการ</button>
                        <button class="block w-full px-4 py-2 text-white rounded-md border border-gray-400 shadow-lg hover:bg-blue-700 cursor-pointer" style="background-color: #3062B8"
                            onclick="window.location.href='{{ route('branch.edit') }}'">แก้ไข</button>
                        <button class="block w-full px-4 py-2 text-white border rounded-md border-gray-400 shadow-lg hover:bg-red-700 cursor-pointer"
                            onclick="deleteBranch(${branch.bs_id})" style="background-color: #CF3434">ลบ</button>
                    </div>
                </td>
            `;
            tableBody.appendChild(row);
        });

        renderPagination();
    }

    function renderPagination() {
        const pagination = document.getElementById("pagination");
        pagination.innerHTML = "";

        const totalPages = Math.ceil(totalItems / rowsPerPage);
        if (totalPages <= 1) return;

        const createBtn = (text, page, disabled = false, active = false) => {
            const btn = document.createElement("button");
            btn.textContent = text;
            btn.className = `px-3 py-2 mx-1 rounded-lg text-sm font-semibold ${
                active
                    ? "bg-blue-600 text-white"
                    : disabled
                    ? "text-gray-400 cursor-not-allowed"
                    : "bg-white border border-gray-300 text-black hover:bg-gray-100"
            }`;
            btn.disabled = disabled;
            if (!disabled && !active) btn.onclick = () => fetchBranches(page);
            return btn;
        };

        // « First
        pagination.appendChild(createBtn("«", 1, currentPage === 1));

        // Left Ellipsis
        if (currentPage > 3) {
            pagination.appendChild(createBtn("1", 1));
            if (currentPage > 4) {
                const ellipsis = document.createElement("span");
                ellipsis.textContent = "...";
                ellipsis.className = "mx-1 text-gray-500";
                pagination.appendChild(ellipsis);
            }
        }

        // Page Range (current -1, current, current +1)
        for (let i = Math.max(1, currentPage - 1); i <= Math.min(totalPages, currentPage + 1); i++) {
            pagination.appendChild(createBtn(i, i, false, i === currentPage));
        }

        // Right Ellipsis
        if (currentPage < totalPages - 2) {
            if (currentPage < totalPages - 3) {
                const ellipsis = document.createElement("span");
                ellipsis.textContent = "...";
                ellipsis.className = "mx-1 text-gray-500";
                pagination.appendChild(ellipsis);
            }
            pagination.appendChild(createBtn(totalPages, totalPages));
        }

        // » Last
        pagination.appendChild(createBtn("»", totalPages, currentPage === totalPages));
    }

    function toggleMenu(event, id) {
        event.stopPropagation();
        document.querySelectorAll("[id^=menu-]").forEach(menu => menu.classList.add("hidden"));
        document.getElementById(`menu-${id}`).classList.toggle("hidden");
    }

    document.addEventListener("click", () => {
        document.querySelectorAll("[id^=menu-]").forEach(menu => menu.classList.add("hidden"));
    });

    function deleteBranch(id) {
        Swal.fire({
            title: "ลบสาขา",
            text: "คุณต้องการลบสาขานี้ ใช่หรือไม่?",
            icon: "warning",
            iconColor: "#d33",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3062B8",
            confirmButtonText: "ยืนยัน",
            cancelButtonText: "ยกเลิก"
        }).then((result) => {
            if (result.isConfirmed) {
                branches = branches.filter(branch => branch.bs_id !== id);
                renderTable();
                Swal.fire({
                    title: "ลบแล้ว!",
                    text: "สาขาถูกลบเรียบร้อย",
                    icon: "success"
                });
            }
        });
    }

    // Initial load
    fetchBranches();
</script>
@endsection