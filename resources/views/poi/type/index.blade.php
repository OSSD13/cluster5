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
        <table class="w-full mt-5 border-collapse rounded-lg overflow-hidden table-fixed " >
            <thead class="text-gray-800 text-md" style="background-color: #B5CFF5">
                <tr>
                    <th class="py-3 px-4 text-left">ชื่อ / ประเภท</th>
                    <th class="py-3 px-4 text-center">Icon</th>
                    <th class="py-3 px-4 text-left">คำอธิบาย</th>
                    <th class="py-3 px-4 text-center">การจัดการ</th>
                </tr>
            </thead>
            <tbody id="tableBody" class="text-sm text-gray-700" style="background-color:rgb(255, 255, 255)">
                <!-- Filled by JS -->
            </tbody>
        </table>
    </div>

    <div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>
@endsection

@section('script')
    <script>
        let poits = [];
        let currentPage = 1;
        const rowsPerPage = 10;

        async function fetchPoits() {
            try {
                const response = await fetch('{{ route('api.poit.query.all') }}');
                const result = await response.json();
                poits = result.data || [];
                renderTable();
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function renderTable(data = poits) {
            const tableBody = document.getElementById("tableBody");
            const start = (currentPage - 1) * rowsPerPage;
            const paginated = data.slice(start, start + rowsPerPage);
            tableBody.innerHTML = "";
            document.getElementById("resultCount").innerText = data.length;

            paginated.forEach(poit => {
                const row = document.createElement("tr");
                row.classList.add("border-b", "border-gray-200", "hover:bg-blue-50"); // เพิ่ม border และ hover effect
                row.innerHTML = `
                    <td class="py-3 px-4 text-left font-semibold">${poit.poit_name}</td>
                    <td class="py-3 px-4 text-center text-xl">${poit.poit_icon || '🏢'}</td>
                    <td class="py-3 px-4 text-left">${poit.poit_description || '-'}</td>
                    <td class="py-3 px-4 text-center relative">
                        <button class="cursor-pointer text-blue-600 hover:text-blue-800" onclick="toggleMenu(event, '${poit.poit_type}')">&#8230;</button>
                        <div id="menu-${poit.poit_type}" class="hidden absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-32 z-50 p-2 space-y-2">
                            <button class="view-btn block w-full px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700"
                                data-type="${poit.poit_type}">ดูรายละเอียด</button>
                            <button class="edit-btn block w-full px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700"
                                data-type="${poit.poit_type}">แก้ไข</button>
                            <button class="delete-btn block w-full px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700"
                            data-type="${poit.poit_type}">ลบ</button>
                        </div>
                    </td>`;
                tableBody.appendChild(row);
            });

            renderPagination(data);
        }

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

        function toggleMenu(event, id) {
            event.stopPropagation();
            document.querySelectorAll("[id^=menu-]").forEach(el => el.classList.add("hidden"));
            document.getElementById(`menu-${id}`).classList.toggle("hidden");
        }

            // ✅ ฟังก์ชันปุ่ม
        document.addEventListener("click", function (e) {
        let poitType = e.target.dataset.type;
                
        if (e.target.classList.contains("view-btn")) {
            
            let poit = poits.find(p => p.poit_type === poitType);
            if (!poit) return;

                Swal.fire({
                    title: "<b class='text-gray-800'>รายละเอียดข้อมูล POIT</b>",
                    html: `
                            <div class="flex flex-col items-center space-y-4 text-left w-full max-w-md mx-auto">
                                <div class="w-full">
                                    <label class="block text-gray-800 text-sm mb-1">ชื่อสถานที่</label>
                                    <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${poit.poit_name}" readonly>
                                </div>
                                <div class="w-full">
                                <label class="block text-gray-800 text-sm mb-1">ประเภท</label>
                                <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${poit.poit_type}" readonly>
                                </div>
                                <div class="w-full">
                                <label class="block text-gray-800 text-sm mb-1">รายละเอียด</label>
                                <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${poit.poit_description}" readonly>
                                </div>
                                <div class="w-full">
                                <label class="block text-gray-800 text-sm mb-1">วันที่เพิ่ม</label>
                                <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="17 ก.ย. 2568" readonly>
                                </div>
                                <div class="w-full">
                                <label class="block text-gray-800 text-sm mb-1">เพิ่มโดย</label>
                                <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="jeng@gmail.com" readonly>
                                </div>
                            </div>
                        `,
                    confirmButtonText: "ยืนยัน",
                    confirmButtonColor: "#2D8C42",
                });
            }
            if (e.target.classList.contains("edit-btn")) {
                let poit = poits.find(p => p.poit_type === poitType);
                 if (!poit) return;
                Swal.fire({
                    title: `<b class="text-gray-800">แก้ไขข้อมูล POI</b>`,
                    html: `
                        <div class="flex flex-col items-center space-y-4 text-left w-full max-w-md mx-auto">
                            <div class="w-full">
                                <label class="block text-gray-800 text-sm mb-1">ประเภท</label>
                                <input type="text" id="poitType" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${poit.poit_type}" readonly>
                            </div>
                            <div class="w-full">
                                <label class="block text-gray-800 text-sm mb-1">ชื่อสถานที่</label>
                                <input type="text" id="poitName" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${poit.poit_name}">
                            </div>
                            <div class="w-full">
                            
                        
                        <!-- Icon -->
                        <div class="w-full">
                            <label class="block text-gray-800 text-sm mb-1">Icon</label>
                            <div class="relative mb-3">
                             <input type="text"  id="iconInput" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm @error('icon') error-input-style
                             @enderror" placeholder="เลือกอีโมจิ" name="icon" value="{{ old('icon') }}">
                             <button type="button" id="emojiButton"
                                 class="absolute inset-y-0 right-0 px-4 py-2 cursor-pointer bg-primary-dark hover:bg-primary-light text-white rounded-r-lg">😀</button>
                            </div>
                        </div>
                        @error('icon')
                            <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
                        @enderror
                        <div id="emojiPickerContainer" class="hidden">
                            <emoji-picker class="w-full light"></emoji-picker>
                        </div>

                        <!-- สี -->
                        <div class="w-full">
                        <label class="block text-gray-800 text-sm mb-1">สี</label>
                        <div class="relative mb-3 flex items-center">
                            <!-- input สี (hex) -->
                            <input type="text" id="colorInput"
                                class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm @error('color') error-input-style @enderror"
                                placeholder="สี" name="color" value="{{ old('color') }}">

                            <!-- ปุ่ม color picker -->
                            <button type="button" id="colorButton" class="h-full px-4 py-2 cursor-pointer text-white rounded-r-lg"
                                style="background-color: {{ old('color', '#888') }};">🎨</button>
                        </div>
                        </div>
                        </div>

                        <!-- ซ่อนตัวเลือกสีไว้ใต้ form -->
                        <input type="color" id="colorPicker" class="hidden" value="{{ old('color', '#ffffff') }}">

                        @error('color')
                            <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
                        @enderror
                            <div class="w-full">
                                <label class="block text-gray-800 text-sm mb-1">คำอธิบาย</label>
                                <textarea id="poitDescription" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm">${poit.poit_description}</textarea>
                            </div>
                                `,
                    showCancelButton: true,
                    confirmButtonText: "ยืนยัน",
                    cancelButtonText: "ยกเลิก",
                    confirmButtonColor: "#2D8C42",
                    focusCancel: true,
                    preConfirm: () => {
                        const name = document.getElementById("poitName").value;
                        const type = document.getElementById("poitType").value;
                        const description = document.getElementById("poitDescription").value;
                        const icon = document.getElementById("iconInput").value;
                        const color = document.getElementById("colorInput").value

                        if (!name || !type || !description) {
                            Swal.showValidationMessage("กรุณากรอกข้อมูลให้ครบทุกช่อง");
                            return false;
                        }

                        // อัปเดตข้อมูล POI
                        poit.poit_name = name;
                        poit.poit_description = description;
                        poit.poit_icon = icon;
                        poit.poit_color = color;

                        renderTable();

                        Swal.fire({
                            title: "สำเร็จ!",
                            text: "แก้ไขข้อมูล POI เรียบร้อยแล้ว",
                            icon: "success",
                            confirmButtonColor: "#2D8C42",
                            confirmButtonText: "ตกลง"
                        });
                    }
                });
            }
            if (e.target.classList.contains("delete-btn")) {
                Swal.fire({
                    title: "ลบสถานที่ที่สนใจ",
                    text: "คุณต้องการลบสถานที่ที่สนใจ ใช่หรือไม่",
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
                            const response = await fetch("{{ route('api.poit.delete') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    poit_type: poitType
                                })
                            });
                                console.log();
                                
                                

                            const resultData = await response.json();

                            if (resultData.status === "success") {
                                poits = poits.filter(p => p.poit_type !== poitType);
                                renderTable();

                                Swal.fire({
                                    title: "ลบแล้ว!",
                                    text: "สถานที่ที่สนใจถูกลบเรียบร้อย",
                                    icon: "success"
                                });
                            } else {
                                Swal.fire("ผิดพลาด", resultData.message || "ลบไม่สำเร็จ", "error");
                            }
                        } catch (error) {
                            console.error(error);
                            Swal.fire("ผิดพลาด", "เกิดข้อผิดพลาดในการเชื่อมต่อ API", "error");
                        }
                    }
                });
            }
        });
    
        function getPoitTypeById(id) {
            const item = poits.find(p => p.id === id);
            return item ? item.poit_type : '';
        }

        // ค้นหา
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

        document.addEventListener("DOMContentLoaded", fetchPoits);
        document.addEventListener("click", () => {
            document.querySelectorAll("[id^=menu-]").forEach(el => el.classList.add("hidden"));
        });

        </script>
        
@endsection
