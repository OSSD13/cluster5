@extends('layouts.main')

@section('title', 'Branch')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-2xl font-bold text-gray-800">สาขาทั้งหมด</h2>
            <a href="{{ route('branch.create') }}">
                <button
                    class="hover:bg-blue-700 text-white shadow-lg font-bold py-2 px-4 rounded-md whitespace-nowrap border border-gray-300"
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
            <label class="block text-gray-800 mb-1">Sale</label>
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
    <table class="w-full mt-5 border-separate rounded-lg" style="border-spacing: 0">
        <thead class="text-gray-800 text-md bg-[#B5CFF5] rounded-lg">
            <tr class="">
                <th scope="col" class="py-2 px-4 text-left rounded-tl-lg">ID</th>
                <th class="py-3 px-4 text-left min-w-[120px]">ชื่อสาขา / ประเภท</th>
                <th class="py-3 px-4 text-center max-w-[120px]">เพิ่มโดย</th>
                <th class="py-3 px-1 w-7 text-center rounded-tr-lg">&#8230;</th>
            </tr>
        </thead>
        
        <tbody id="tableBody" class="bg-white divide-y divide-gray-200 text-sm rounded-b-4xl"></tbody>
    </table>


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
            const userId = document.getElementById("roleFilter").value; // เปลี่ยนจาก role → userId

            const params = new URLSearchParams({
                page,
                limit: rowsPerPage
            });
            if (search) params.append('search', search);
            if (userId) params.append('user_id', userId); // ส่ง user_id ไปยัง API

            try {
                const response = await fetch(`${apiUrl}?${params.toString()}`);
                const json = await response.json();
                branches = json.data || [];
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

            branches.forEach((branch, index) => {
                const row = document.createElement("tr");

                const isFirst = index === 0;
                const isLast = index === branches.length - 1;

                row.innerHTML = `
                    <td class="py-3 px-4 w-16 ${isLast ? 'rounded-bl-lg' : ''}">${branch.bs_id}</td>
                    <td class="py-3 px-4 max-w-[150px]">
                        <div class="font-semibold text-md break-words whitespace-normal leading-tight">${branch.bs_name}</div>
                        <div class="text-sm text-gray-400 break-words whitespace-normal leading-tight">${branch.poit_name}</div>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <div class="font-semibold text-sm truncate w-[120px] mx-auto" title="${branch.bs_manager_name}">
                            ${branch.bs_manager_name}
                        </div>
                        <div class="text-sm text-gray-400 truncate w-[120px] mx-auto" title="${branch.bs_manager_email}">
                            ${branch.bs_manager_email}
                        </div>
                    </td>
                    <td class="py-3 px-1 w-10 text-center relative ${isLast ? 'rounded-br-lg' : ''}">
                        <button class="cursor-pointer" onclick="toggleMenu(event, ${branch.bs_id})">&#8230;</button>
                        <div id="menu-${branch.bs_id}" class="hidden absolute right-0 mt-2 bg-white shadow-lg rounded-xl w-32 z-50 p-2 space-y-2 -translate-y-1/2">
                            <button class="block w-full px-4 py-2 text-white border border-gray-400 rounded-md shadow-lg hover:bg-blue-700 cursor-pointer" style="background-color: #3062B8"
                                onclick="window.location.href='{{ route('branch.manage.index') }}?bs_id=${branch.bs_id}'">จัดการ</button>
                            <button class="block w-full px-4 py-2 text-white rounded-md border border-gray-400 shadow-lg hover:bg-blue-700 cursor-pointer" style="background-color: #3062B8"
                                onclick="window.location.href='{{ route('branch.edit') }}?bs_id=${branch.bs_id}'">แก้ไข</button>
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
            const maxVisible = 1;
            let startPage = Math.max(1, currentPage - maxVisible);
            let endPage = Math.min(totalPages, currentPage + maxVisible);

            if (totalPages <= 1) return;

            const createPageButton = (page, isActive = false) => {
                const btn = document.createElement("button");
                btn.innerText = page;
                btn.className =
                    `min-w-[36px] h-10 px-3 mx-1 rounded-lg text-sm font-medium ${isActive ? "bg-blue-600 text-white" : "bg-white border border-gray-300 text-black hover:bg-gray-100"}`;
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
                        inputAttributes: {
                            min: 1,
                            max: totalPages,
                            step: 1
                        },
                        showCancelButton: true,
                        confirmButtonText: "ไปเลย!",
                        confirmButtonColor: "#3062B8",
                        inputValidator: (value) => {
                            if (!value || isNaN(value)) return "กรุณากรอกตัวเลข";
                            if (value < 1 || value > totalPages)
                            return `หน้าต้องอยู่ระหว่าง 1 ถึง ${totalPages}`;
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
            prevBtn.className =
                `min-w-[40px] h-10 px-3 mx-1 rounded-lg text-xl font-bold ${currentPage === 1 ? "text-gray-300 bg-white border border-gray-200 cursor-not-allowed" : "text-blue-600 bg-white border border-gray-300 hover:bg-blue-50"}`;
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
            nextBtn.className =
                `min-w-[40px] h-10 px-3 mx-1 rounded-lg text-xl font-bold ${currentPage === totalPages ? "text-gray-300 bg-white border border-gray-200 cursor-not-allowed" : "text-blue-600 bg-white border border-gray-300 hover:bg-blue-50"}`;
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.onclick = () => goToPage(currentPage + 1);
            pagination.appendChild(nextBtn);
        }




        function goToPage(pageNumber) {
            currentPage = pageNumber;
            const searchValue = document.getElementById("searchInput").value || '';
            fetchBranches(pageNumber);
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
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const res = await fetch(deleteBranchUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                bs_id: id
                            })
                        });

                        if (!res.ok) {
                            throw new Error(`HTTP error! Status: ${res.status}`);
                        }

                        const json = await res.json();

                        if (json.status === 'success') {
                            Swal.fire("ลบแล้ว!", "สาขาถูกลบเรียบร้อย", "success");
                            fetchBranches(currentPage); // รีโหลดตาราง
                        } else {
                            Swal.fire("ผิดพลาด!", json.message || "ไม่สามารถลบได้", "error");
                        }

                    } catch (err) {
                        console.error("Delete error:", err);
                        Swal.fire("ผิดพลาด!", "ไม่สามารถติดต่อเซิร์ฟเวอร์ได้", "error");
                    }
                }
            });

        }


        // Initial load
        fetchBranches();

        async function fetchFilterOptions() {
            const roleSelect = document.getElementById("roleFilter");
            roleSelect.innerHTML = `<option value="">ทั้งหมด</option>`; // ✅ fixed

            try {
                const res = await fetch(`/getUserOptionsForBranchFilter`);
                const data = await res.json();

                (data.users || []).forEach(user => {
                    const option = document.createElement("option");
                    option.value = user.user_id;
                    option.textContent = `${user.role_name} - ${user.name}`;
                    roleSelect.appendChild(option);
                });
            } catch (err) {
                console.error("Error fetching role filter options:", err);
            }
        }
        document.addEventListener("DOMContentLoaded", () => {
            fetchFilterOptions(); // ✅ load dropdown
            fetchBranches(); // ✅ load table
        });
    </script>

    <script>
        const deleteBranchUrl = `{{ route('api.branch.delete') }}`;
        const csrfToken = `{{ csrf_token() }}`;
    </script>

@endsection
