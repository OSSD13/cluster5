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
            <tbody id="tableBody" class="text-sm text-gray-700" style="background-color:rgb(255, 255, 255)">
                <!-- JS จะเติมข้อมูลให้ -->
            </tbody>
        </table>
    </div>
 <!-- ปุ่มเปลี่ยนหน้า -->
 <div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>
<style>
    table {
        border-radius: 12px;
        /* ทำให้ขอบตารางโค้ง */
        overflow: hidden;
        /* ป้องกันไม่ให้เนื้อหาเกินขอบ */
    }

    thead {
        border-radius: 12px 12px 0 0;
        /* ทำให้เฉพาะหัวตารางโค้ง */
    }

    button {
        border-radius: 8px;
        /* ทำให้ปุ่มโค้งมน */
    }

    .view-btn,
    .edit-btn,
    .delete-btn {
        border-radius: 8px;
        /* ปรับปุ่มในเมนูให้โค้งมน */
    }

    #pagination button {
        border-radius: 50%;
        /* ทำให้ปุ่มเปลี่ยนหน้าเป็นวงกลม */
    }
        th:nth-child(1), td:nth-child(1) {
        max-width: 100px; /* ลดความกว้างของคอลัมน์ประเภท */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    th:nth-child(3), td:nth-child(3) {
        max-width: 150px; /* ลดความกว้างของคอลัมน์คำอธิบาย */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
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
        totalItems = result.total || 0; // ตรวจสอบว่าค่า total ถูกต้อง
        renderTable();
        renderPagination(); // เรียก renderPagination หลังจากโหลดข้อมูล
    } catch (error) {
        console.error('Error fetching POITs:', error);
    }
}

    // แสดงตารางพร้อม pagination
    function renderTable(data = poits) {
            const tableBody = document.getElementById("tableBody");
            const start = (currentPage - 1) * rowsPerPage;
            const paginated = data.slice(start, start + rowsPerPage);
            tableBody.innerHTML = "";
            document.getElementById("resultCount").innerText = data.length;

            paginated.forEach(poit => {
                const row = document.createElement("tr");
                row.classList.add("border-b", "border-gray-200", "hover:bg-blue-50");

                // แสดงแต่ละแถว
                row.innerHTML = `
                            <td class="py-3 px-4 text-left font-semibold">${poit.poit_name}</td>
                            <td class="py-3 px-4 text-center text-xl">${poit.poit_icon || '🏢'}</td>
                            <td class="py-3 px-4 text-left">${poit.poit_description || '-'}</td>
                            <td class="py-3 px-4 text-center relative">
                                <button class="cursor-pointer text-blue-600 hover:text-blue-800" onclick="toggleMenu(event, '${poit.poit_type}')">&#8230;</button>
                                <div id="menu-${poit.poit_type}" class="hidden absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-32 z-50 p-2 space-y-2">
                                    <button class="view-btn block w-full px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700" data-type="${poit.poit_type}">ดูรายละเอียด</button>
                                    <button class="edit-btn block w-full px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700" data-type="${poit.poit_type}">แก้ไข</button>
                                    <button class="delete-btn block w-full px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700" data-type="${poit.poit_type}">ลบ</button>
                                </div>
                            </td>`;
                tableBody.appendChild(row);
            });
        }

    // การเปลี่ยนหน้า
function renderPagination() {
    const pagination = document.getElementById("pagination");
    pagination.innerHTML = "";

    const totalPages = Math.ceil(totalItems / rowsPerPage);
    if (totalPages <= 1) return; // หากมีเพียง 1 หน้า ไม่ต้องแสดงปุ่มเลื่อนหน้า

    const prevBtn = document.createElement("button");
    prevBtn.innerText = "<";
    prevBtn.className = `px-3 py-1 ${currentPage === 1 ? "text-gray-400 cursor-not-allowed" : "text-blue-600"} text-xl`;
    prevBtn.disabled = currentPage === 1;
    prevBtn.onclick = () => goToPage(currentPage - 1);
    pagination.appendChild(prevBtn);

    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement("button");
        btn.innerText = i;
        btn.className = `px-4 py-2 mx-1 rounded-lg text-base font-semibold 
                        ${i === currentPage ? "bg-blue-600 text-white " : "bg-white border border-gray-300 text-black cursor-pointer"}`;
        btn.onclick = () => goToPage(i);
        pagination.appendChild(btn);
    }

    const nextBtn = document.createElement("button");
    nextBtn.innerText = ">";
    nextBtn.className = `px-3 py-1 ${currentPage === totalPages ? "text-gray-400 cursor-not-allowed" : "text-blue-600"} text-xl`;
    nextBtn.disabled = currentPage === totalPages;
    nextBtn.onclick = () => goToPage(currentPage + 1);
    pagination.appendChild(nextBtn);
}

        function goToPage(pageNumber) {
            currentPage = pageNumber;
            renderTable();
        }
        // แปลงวันที่เป็นวันภาษาไทย
            function formatThaiDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString("th-TH", {
                year: "numeric",
                month: "short",
                day: "numeric"
            });
        }
        // ซ่อนเมนูอื่นก่อนโชว์เมนูที่คลิก
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
                                <label class="font-medium text-gray-800 text-sm">ประเภท</label>
                                <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${poit.poit_name}" readonly>
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
                const poitType = poit.poit_type; // เก็บไว้ตรงนี้เพื่อใช้ใน preConfirm

                Swal.fire({
                    title: "แก้ไข POIT",
                    html: `<div id="editPoitContainer"></div>`,
                    showCancelButton: true,
                    confirmButtonText: "ยืนยัน",
                    cancelButtonText: "ยกเลิก",
                    didOpen: () => {
                        document.getElementById("editPoitContainer").innerHTML = `
                            <div class="space-y-4 text-left">
                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">ประเภท</label>
                                    <input id="poitName" class="w-full p-2 border border-gray-300 rounded-md text-sm text-gray-800" value="${poit.poit_name}">
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">Icon</label>
                                    <div class="relative flex items-center">
                                        <input id="iconInput" class="w-full p-2 border border-gray-300 rounded-md text-sm text-gray-800 pr-10" value="${poit.poit_icon || ''}"readonly>
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
                                        <button id="colorButton" class="absolute right-0 top-0 bottom-0 px-3 bg-blue-600 text-white rounded-r" style="background-color: ${poit.poit_color || '#888'};">🎨</button>
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
                                poit_type: poitType, // ใช้ค่าจาก const ด้านบน
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