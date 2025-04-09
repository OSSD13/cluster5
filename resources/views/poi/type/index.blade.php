@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')
<div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto">
    <div class="flex justify-between items-center mb-3">
        <h2 class="text-2xl font-bold text-gray-700">POIT จัดการประเภทสถานที่ที่สนใจ</h2>
        <a href="{{ route('poi.type.create') }}">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap"
                style="background-color: #3062B8">
                สร้าง POIT
            </button>
        </a>
    </div>

    <input type="text" placeholder="ค้นหาสถานที่ที่สนใจ" class="w-full p-2 border border-gray-300 rounded mb-3"
        id="searchInput">

    <p class="text-gray-700">ผลลัพธ์ <span id="resultCount">0</span> รายการ</p>
</div>

<div class="overflow-x-auto">
    <table class="min-w-full mt-5 table-auto border-collapse rounded-lg bg-gray-100">
        <thead class="text-gray-800 text-md" style="background-color: #B5CFF5">
            <tr>
                <th class="py-3 px-4 text-left">ประเภท</th>
                <th class="py-3 px-4 text-center">Icon</th>
                <th class="py-3 px-4 text-left">คำอธิบาย</th>
                <th class="py-3 px-4 text-center"></th>
            </tr>
        </thead>
        <tbody id="tableBody" class="text-sm text-gray-700 bg-white"></tbody>
    </table>
</div>

<div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>

<script>
    let poits = [];
    let currentPage = 1;
    const rowsPerPage = 10;
    let totalItems = 0;

    async function fetchPoits(search = '') {
    try {
        const response = await fetch(`{{ route('api.poit.query') }}?limit=${rowsPerPage}&page=${currentPage}&search=${encodeURIComponent(search)}`);
        const result = await response.json();
        poits = result.data || [];
        totalItems = result.total || 0;

        document.getElementById("resultCount").textContent = `ผลลัพธ์ ${totalItems} รายการ`;

        renderTable();
        renderPagination(totalItems); // ส่งค่า totalItems เข้าไปเหมือนกับ fetchMembers()
    } catch (error) {
        console.error('Error fetching POITs:', error);
    }
}


    function renderTable() {
        const tableBody = document.getElementById("tableBody");
        tableBody.innerHTML = "";
        document.getElementById("resultCount").innerText = totalItems;

        if (poits.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-gray-400">ไม่พบข้อมูล</td></tr>`;
            return;
        }

        poits.forEach(poit => {
            const row = document.createElement("tr");
            row.classList.add("border-b", "border-gray-200", "hover:bg-blue-50");
            row.innerHTML = `
            <td class="py-3 px-4 max-w-[200px]">
                <div class="font-semibold text-md">${poit.poit_name}</div>
                 <div class="text-sm text-gray-400 truncate">${poit.poit_type}</div>
            </td>
            <td class="py-3 px-4 text-center text-xl">${poit.poit_icon || '🏢'}</td>
            <td class="py-3 px-4">${poit.poit_description || '-'}</td>
            <td class="py-3 px-4 text-center relative">
                <button class="cursor-pointer text-blue-600 hover:text-blue-800 flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200" onclick="toggleMenu(event, '${poit.poit_type}')">
                    <span class="text-lg font-bold">⋯</span>
                </button>
                <div id="menu-${poit.poit_type}" class="hidden absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-32 z-50 p-2 space-y-2">
                    <button class="view-btn block w-full px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700" style="background-color: #3062B8" data-type="${poit.poit_type}">ดูรายละเอียด</button>
                    <button class="edit-btn block w-full px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700" style="background-color: #3062B8" data-type="${poit.poit_type}">แก้ไข</button>
                    <button class="delete-btn block w-full px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700" style="background-color: #CF3434"  data-type="${poit.poit_type}">ลบ</button>
                </div>
            </td>`;
            tableBody.appendChild(row);
        });
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
            const searchValue = document.getElementById("searchInput").value || '';
            fetchPoits(searchValue);
        }

        function toggleMenu(event, id) {
            event.stopPropagation();
            document.querySelectorAll("[id^=menu-]").forEach(el => el.classList.add("hidden"));
            document.getElementById(`menu-${id}`).classList.toggle("hidden");
        }

        document.addEventListener("click", () => {
            document.querySelectorAll("[id^=menu-]").forEach(el => el.classList.add("hidden"));
        });

        document.getElementById("searchInput").addEventListener("input", function () {
            currentPage = 1;
            fetchPoits(this.value);
        });

        function formatThaiDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString("th-TH", {
                year: "numeric",
                month: "short",
                day: "numeric"
            });
        }


        document.addEventListener("click", async function (e) {
            const poitType = e.target.dataset.type;
            const poit = poits.find(p => p.poit_type === poitType);
            if (!poit) return;

            if (e.target.classList.contains("view-btn")) {
                Swal.fire({
                    html: `
                        <div class="flex flex-col text-3xl mb-6 mt-4">
                            <b class="text-gray-800">รายละเอียดข้อมูล POIT</b>
                        </div>
                        <div class="flex flex-col space-y-2 text-left">
                            <div class="w-full">
                                <label class="font-medium text-gray-800 text-sm">ชื่อ</label>
                                <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${poit.poit_name}" readonly>
                            </div>
                            <div class="w-full">
                                <label class="font-medium text-gray-800 text-sm">ประเภท</label>
                                <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${poit.poit_type}" readonly>
                            </div>
                            <div class="w-full">
                                <label class="font-medium text-gray-800 text-sm">Icon</label>
                                <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${poit.poit_icon}" readonly>
                            </div>
                            <div class="w-full">
                                <label class="font-medium text-gray-800 text-sm">สี</label>
                                <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${poit.poit_color}" readonly>
                            </div>
                            <div class="w-full">
                                <label class="font-medium text-gray-800 text-sm">รายละเอียด</label>
                                <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${poit.poit_description}" readonly>
                            </div>
                            <div class="w-full">
                                <label class="font-medium text-gray-800 text-sm">วันที่เพิ่ม</label>
                                <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${formatThaiDate(poit.created_at)}" readonly>
                            </div>
                        </div>
                    `,
                    confirmButtonText: "ยืนยัน",
                    confirmButtonColor: "#2D8C42",
                    customClass: {
                        popup: 'custom-popup rounded-lg shadow-lg',
                        confirmButton: 'px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700'
                    }
                });
            }


            if (e.target.classList.contains("edit-btn")) {
                const poitType = poit.poit_type;

                Swal.fire({
                    title: "แก้ไข POIT",
                    html: `<div id="editPoitContainer"></div>`,
                    showCancelButton: true,
                    confirmButtonText: "ยืนยัน",
                    confirmButtonColor: "#2D8C42",
                    cancelButtonText: "ยกเลิก",
                    didOpen: () => {
                        document.getElementById("editPoitContainer").innerHTML = `
                            <div class="space-y-4 text-left">
                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">ชื่อประเภท</label>
                                    <input id="poitName" class="w-full p-2 border border-gray-300 rounded-md text-sm text-gray-800" value="${poit.poit_name}">
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">ประเภท</label>
                                    <input id="poitType" class="w-full p-2 border border-gray-300 rounded-md text-sm text-gray-800 bg-gray-100" value="${poitType}" readonly>
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">Icon</label>
                                    <div class="relative flex items-center">
                                        <input id="iconInput" class="w-full p-2 border border-gray-300 rounded-md text-sm text-gray-800 pr-10" value="${poit.poit_icon || ''}">
                                        <button id="emojiButton" class="absolute right-0 top-0 bottom-0 px-3 bg-blue-600 text-white rounded-r">😀</button>
                                    </div>
                                    <div id="emojiPickerContainer" class="hidden">
                                        <emoji-picker class="w-full light"></emoji-picker>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">สี</label>
                                    <div class="relative flex items-center">
                                        <input id="colorInput" class="w-full p-2 border border-gray-300 rounded-md text-sm text-gray-800 pr-10" value="${poit.poit_color || '#888'}">
                                        <button id="colorButton" class="absolute right-0 top-0 bottom-0 px-3 text-white rounded-r" style="background-color: ${poit.poit_color || '#888'};">🎨</button>
                                    </div>
                                    <input type="color" id="colorPicker" class="hidden" value="${poit.poit_color || '#888'}">
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">คำอธิบาย</label>
                                    <textarea id="poitDescription" class="w-full p-2 border border-gray-300 rounded-md text-sm text-gray-800">${poit.poit_description || ''}</textarea>
                                </div>
                            </div>`;

                        // Emoji picker logic
                        document.getElementById("emojiButton").addEventListener("click", () => {
                            document.getElementById("emojiPickerContainer").classList.toggle("hidden");
                        });
                        document.querySelector("emoji-picker").addEventListener("emoji-click", event => {
                            document.getElementById("iconInput").value = event.detail.unicode;
                            document.getElementById("emojiPickerContainer").classList.add("hidden");
                        });

                        // Color picker logic
                        const colorInput = document.getElementById("colorInput");
                        const colorButton = document.getElementById("colorButton");
                        const colorPicker = document.getElementById("colorPicker");

                        colorButton.addEventListener("click", () => colorPicker.click());
                        colorInput.addEventListener("input", () => {
                            colorButton.style.backgroundColor = colorInput.value;
                        });
                        colorPicker.addEventListener("input", () => {
                            colorInput.value = colorPicker.value;
                            colorButton.style.backgroundColor = colorPicker.value;
                        });
                    },
                    preConfirm: async () => {
                        const name = document.getElementById("poitName").value;
                        const icon = document.getElementById("iconInput").value;
                        const color = document.getElementById("colorInput").value;
                        const desc = document.getElementById("poitDescription").value;

                        if (!name || !desc) {
                            Swal.showValidationMessage("กรุณากรอกชื่อและคำอธิบาย");
                            return false;
                        }

                        const res = await fetch("{{ route('api.poit.edit') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
                            },
                            body: JSON.stringify({
                                poit_type: poitType,
                                poit_name: name,
                                poit_icon: icon,
                                poit_color: color,
                                poit_description: desc
                            })
                        });

                        const data = await res.json();
                        if (data.status === "success") {
                            fetchPoits(document.getElementById("searchInput").value);
                            return true;
                        } else {
                            Swal.showValidationMessage(data.message || "ไม่สามารถอัปเดตข้อมูลได้");
                            return false;
                        }
                    }
                });
            }



        if (e.target.classList.contains("delete-btn")) {
            Swal.fire({
                title: "ลบ POIT?",
                text: "ยืนยันการลบรายการนี้",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "ยืนยัน",
                cancelButtonText: "ยกเลิก",
                confirmButtonColor: "#d33"
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const res = await fetch("{{ route('api.poit.delete') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify({ poit_type: poitType })
                    });
                    const data = await res.json();
                    if (data.status === "success") {
                        fetchPoits(document.getElementById("searchInput").value);
                        Swal.fire("ลบแล้ว!", "ข้อมูลถูกลบเรียบร้อย", "success");
                    } else {
                        Swal.fire("ผิดพลาด", data.message || "ไม่สามารถลบได้", "error");
                    }
                }
            });
        }
    });

    document.addEventListener("DOMContentLoaded", () => fetchPoits());
</script>
@endsection
