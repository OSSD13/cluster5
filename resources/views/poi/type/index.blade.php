@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')
    <!-- กล่องหัวข้อและปุ่มสร้างใหม่ -->
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

        <!-- ช่องค้นหา -->
        <input type="text" placeholder="ค้นหาสถานที่ที่สนใจ" class="w-full p-2 border border-gray-300 rounded mb-3"
            id="searchInput">

        <!-- แสดงจำนวนผลลัพธ์ -->
        <p class="text-gray-700">ผลลัพธ์ <span id="resultCount">0</span> รายการ</p>
    </div>

    <!-- ตารางรายการ POIT -->
    <div class="overflow-x-auto">
        <table class="min-w-full mt-5 table-auto border-collapse rounded-lg bg-gray-100">
            <thead class="text-gray-800 text-md" style="background-color: #B5CFF5">
                <tr>
                    <th class="py-3 px-4 text-left">ชื่อ / ประเภท</th>
                    <th class="py-3 px-4 text-center">Icon</th>
                    <th class="py-3 px-4 text-left">คำอธิบาย</th>
                    <th class="py-3 px-4 text-center">การจัดการ</th>
                </tr>
            </thead>
            <tbody id="tableBody" class="text-sm text-gray-700" style="background-color:rgb(255, 255, 255)">
                <!-- JS จะเติมข้อมูลให้ -->
            </tbody>
        </table>
    </div>

    <!-- ปุ่มเปลี่ยนหน้า -->
    <div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>
@endsection

@section('script')
    <script>
        let poits = [];
        let currentPage = 1;
        const rowsPerPage = 10;

        // ดึงข้อมูล POIT จาก API
        async function fetchPoits() {
            try {
                const response = await fetch('{{ route('api.poit.query') }}');
                const result = await response.json();
                poits = result.data || [];
                renderTable();
            } catch (error) {
                console.error('Error:', error);
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

            

            renderPagination(data);
        }

        

        // การเปลี่ยนหน้า
        function renderPagination(data) {
            const pagination = document.getElementById("pagination");
            pagination.innerHTML = "";
            const totalPages = Math.ceil(data.length / rowsPerPage);

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

        // ซ่อนเมนูอื่นก่อนโชว์เมนูที่คลิก
        function toggleMenu(event, id) {
            event.stopPropagation();
            document.querySelectorAll("[id^=menu-]").forEach(el => el.classList.add("hidden"));
            document.getElementById(`menu-${id}`).classList.toggle("hidden");
        }

        // ✅ ฟังก์ชันหลักสำหรับปุ่ม "ดูรายละเอียด", "แก้ไข", "ลบ"
        document.addEventListener("click", async function (e) {
            const poitType = e.target.dataset.type;

            // 🎯 ดูรายละเอียด
            if (e.target.classList.contains("view-btn")) {
                const poit = poits.find(p => p.poit_type === poitType);
                if (!poit) return;

                Swal.fire({
                    title: "<b class='text-gray-800'>รายละเอียดข้อมูล POIT</b>",
                    html: `
                        <div class="space-y-3 text-left">
                            <p><b>ชื่อสถานที่:</b> ${poit.poit_name}</p>
                            <p><b>ประเภท:</b> ${poit.poit_type}</p>
                            <p><b>Icon:</b> ${poit.poit_icon || '🏢'}</p>
                            <p><b>รายละเอียด:</b> ${poit.poit_description || '-'}</p>
                        </div>
                    `,
                    confirmButtonText: "ปิด",
                    confirmButtonColor: "#2D8C42",
                });
            }

            // ✏️ แก้ไขข้อมูล POIT (ใช้ SweetAlert + emoji picker + color picker)
            if (e.target.classList.contains("edit-btn")) {
                const poit = poits.find(p => p.poit_type === poitType);
                if (!poit) return;

                Swal.fire({
                    title: "แก้ไข POIT",
                    html: `<div id="editPoitContainer"></div>`, // จะเติมใน didOpen
                    showCancelButton: true,
                    confirmButtonText: "ยืนยัน",
                    cancelButtonText: "ยกเลิก",
                    confirmButtonColor: "#2D8C42",
                    focusCancel: true,
                    didOpen: () => {
                        const container = document.getElementById("editPoitContainer");
                        container.innerHTML = `
                            <div class="space-y-3">
                                <label>ชื่อสถานที่</label>
                                <input id="poitName" class="w-full border rounded p-2" value="${poit.poit_name}">

                                <label>ประเภท</label>
                                <input id="poitType" class="w-full border rounded p-2" value="${poit.poit_type}" readonly>

                                <label>Icon</label>
                                <div class="relative mb-2">
                                    <input id="iconInput" class="w-full border rounded p-2" value="${poit.poit_icon || ''}">
                                    <button id="emojiButton" class="absolute right-0 top-0 bottom-0 px-3 bg-blue-600 text-white rounded-r">😀</button>
                                </div>
                                <div id="emojiPickerContainer" class="hidden">
                                    <emoji-picker class="w-full light"></emoji-picker>
                                </div>

                                <label>สี</label>
                                <div class="relative mb-2 flex items-center">
                                    <input id="colorInput" class="w-full border rounded p-2" value="${poit.poit_color || '#888'}">
                                    <button id="colorButton" class="px-3 py-2 text-white ml-2 rounded" style="background-color: ${poit.poit_color || '#888'};">🎨</button>
                                </div>
                                <input type="color" id="colorPicker" class="hidden" value="${poit.poit_color || '#888'}">

                                <label>คำอธิบาย</label>
                                <textarea id="poitDescription" class="w-full border rounded p-2">${poit.poit_description || ''}</textarea>
                            </div>
                        `;

                        // Emoji Picker
                        const emojiButton = document.getElementById("emojiButton");
                        const emojiPickerContainer = document.getElementById("emojiPickerContainer");
                        const iconInput = document.getElementById("iconInput");

                        emojiButton.addEventListener("click", () => {
                            emojiPickerContainer.classList.toggle("hidden");
                        });

                        emojiPickerContainer.querySelector("emoji-picker").addEventListener("emoji-click", event => {
                            iconInput.value = event.detail.unicode;
                            emojiPickerContainer.classList.add("hidden");
                        });

                        // Color Picker
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
                    const type = document.getElementById("poitType").value;
                    const icon = document.getElementById("iconInput").value;
                    const color = document.getElementById("colorInput").value;
                    const desc = document.getElementById("poitDescription").value;

                    if (!name || !desc) {
                        Swal.showValidationMessage("กรุณากรอกชื่อและคำอธิบาย");
                        return false;
                    }

                    try {
                        const response = await fetch(`{{ route('api.poit.edit') }}`, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                poit_type: type,
                                poit_name: name,
                                poit_icon: icon,
                                poit_color: color,
                                poit_description: desc
                            })
                        });

                        const data = await response.json();

                        if (data.status === 'success') {
                            await fetchPoits(); // รีเฟรชข้อมูลหลังแก้ไข
                            Swal.fire("สำเร็จ", "อัปเดตข้อมูลเรียบร้อยแล้ว", "success");
                        } else {
                            Swal.showValidationMessage(data.message || "ไม่สามารถอัปเดตข้อมูลได้");
                        }
                    } catch (err) {
                        Swal.showValidationMessage("เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์");
                    }
                }

                });
            }

            // 🗑️ ลบ POIT
            if (e.target.classList.contains("delete-btn")) {
                Swal.fire({
                    title: "ลบสถานที่ที่สนใจ?",
                    text: "คุณแน่ใจหรือไม่ว่าต้องการลบรายการนี้",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonText: "ยกเลิก",
                    confirmButtonText: "ยืนยัน",
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        const response = await fetch("{{ route('api.poit.delete') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ poit_type: poitType })
                        });
                        const data = await response.json();

                        if (data.status === "success") {
                            poits = poits.filter(p => p.poit_type !== poitType);
                            renderTable();

                            Swal.fire("ลบแล้ว!", "ข้อมูลถูกลบเรียบร้อย", "success");
                        } else {
                            Swal.fire("ผิดพลาด", data.message || "ไม่สามารถลบได้", "error");
                        }
                    }
                });
            }
        });

        // 🔍 ค้นหา POIT
        document.getElementById("searchInput").addEventListener("input", function () {
            const keyword = this.value.toLowerCase();
            const filtered = poits.filter(p =>
                p.poit_name.toLowerCase().includes(keyword) ||
                p.poit_type.toLowerCase().includes(keyword) ||
                (p.poit_description && p.poit_description.toLowerCase().includes(keyword))
            );
            currentPage = 1;
            renderTable(filtered);
        });

        // โหลดข้อมูลเมื่อเริ่มต้น
        document.addEventListener("DOMContentLoaded", fetchPoits);

        // ซ่อนเมนูเมื่อคลิกนอกจอ
        document.addEventListener("click", () => {
            document.querySelectorAll("[id^=menu-]").forEach(el => el.classList.add("hidden"));
        });
    </script>
@endsection
