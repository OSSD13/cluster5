@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-2xl font-bold text-gray-700">POIT จัดการประเภทสถานที่ที่สนใจ</h2>
            <a href="{{ route('poi.type.create') }}">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap"
                    style="background-color: #3062B8">
                    สร้าง POI
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
                    <th class="py-2 px-3 text-left w-3 whitespace-nowrap">ชื่อ / ประเภท</th>
                    <th class="py-2 px-2 text-center w-1 whitespace-nowrap">Icon</th>
                    <th class="py-2 px-4 text-left w-3 whitespace-nowrap">คำอธิบาย</th>
                    <th class="py-2 px-1 w1 text-left w-1 whitespace-nowrap"></th>
                </tr>
            </thead>
            <tbody id="tableBody" class="bg-white divide-y divide-gray-200 text-sm">
                <!-- เนื้อหาของตารางจะถูกเติมโดย JavaScript -->
            </tbody>
        </table>
    </div>

    <div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>
@endsection

@section('script')
    <script>
        let poits = [
            { name: "บางแสน", type: "ร้านอาหาร", province: "ชลบุรี", description: "ร้านอาหารริมทะเลที่มีอาหารทะเลสดใหม่และบรรยากาศดี", id: 1 },
            { name: "อุดรธานี", type: "ร้านกาแฟ", province: "อุดรธานี", description: "ร้านกาแฟบรรยากาศสบาย ๆ พร้อมกาแฟคุณภาพดี", id: 2  },
            { name: "ศรีราชา", type: "ร้านขนม", province: "ชลบุรี", description: "ร้านขนมหวานที่มีเมนูหลากหลายและรสชาติอร่อย", id: 3 },
            { name: "พัทยา", type: "ผับบาร์", province: "ชลบุรี", description: "ผับบาร์ที่มีดนตรีสดและเครื่องดื่มหลากหลาย", id: 4 },
            { name: "เซนทรัล", type: "ศูนย์การค้า", province: "ชลบุรี", description: "ศูนย์การค้าขนาดใหญ่ที่มีร้านค้าหลากหลายและสิ่งอำนวยความสะดวกครบครัน", id: 5 },
            { name: "เชียงใหม่", type: "ร้านอาหาร", province: "เชียงใหม่", description: "ร้านอาหารที่มีวิวภูเขาและอาหารพื้นเมือง", id: 6 },
            { name: "ขอนแก่น", type: "ร้านกาแฟ", province: "ขอนแก่น", description: "ร้านกาแฟที่มีเมล็ดกาแฟคุณภาพจากทั่วโลก", id: 7 },
            { name: "หาดใหญ่", type: "ร้านขนม", province: "สงขลา", description: "ร้านขนมที่มีเมนูขนมไทยและขนมสากล", id: 8 },
            { name: "ภูเก็ต", type: "ผับบาร์", province: "ภูเก็ต", description: "ผับบาร์ที่มีวิวทะเลและดนตรีสด", id: 9 },
            { name: "กรุงเทพ", type: "ศูนย์การค้า", province: "กรุงเทพ", description: "ศูนย์การค้าขนาดใหญ่ที่มีร้านค้าหรูหราและร้านอาหารหลากหลาย" , id: 10},
        ]

        for (let i = 12; i <= 50; i++) {
            poits.push({
                id: i,
                name: `สถานที่ ${i}`,
                type: ['ร้านกาแฟ', 'ร้านอาหาร', 'ร้านขนม', 'ผับบาร์', 'ศูนย์การค้า'][i % 5],
                province: ['อุดรธานี', 'ชลบุรี', 'กรุงเทพฯ', 'ขอนแก่น', 'เชียงใหม่'][i % 5],
            });
        }

        let currentPage = 1;
        const rowsPerPage = 10;

        function getIconByType(type) {
            switch (type) {
                case "ร้านอาหาร": return "🍴";
                case "ร้านกาแฟ": return "☕";
                case "ร้านขนม": return "🍰";
                case "ผับบาร์": return "🍺";
                case "ศูนย์การค้า": return "🏬";
                case "ตลาด": return "🛒";
                case "ที่เที่ยว": return "🏖️";
                default: return "🏢";
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
                row.innerHTML = `
                <td class="py-3 px-4 text-left">
                    <div class="font-bold text-md">${poit.name}</div>
                    <div class="text-sm text-gray-400">${poit.type}</div>
                </td>
                <td class="py-3 px-4 text-center text-md">${getIconByType(poit.type)}</td>
                <td class="py-3 px-4 text-left truncate">${poit.description}</td>
                <td class="py-3 px-1 w-10 text-center relative">
                    <button class="cursor-pointer" onclick="toggleMenu(event, ${poit.id})">&#8230;</button>
                    <div id="menu-${poit.id}" class="hidden absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-32 z-50 p-2 space-y-2" >
                        <button class="block w-full px-4 py-2 text-white border border-gray-400 rounded-lg hover:bg-blue-700" style="background-color: #3062B8" onclick="viewDetail(${poit.id})">ดูรายละเอียด</button>
                        <button class="block w-full px-4 py-2 text-white border border-gray-400 rounded-lg hover:bg-blue-700" style="background-color: #3062B8" onclick="editPoit(${poit.id})">แก้ไข</button>
                        <button class="block w-full px-4 py-2 text-white border border-gray-400 rounded-lg hover:bg-red-700" style="background-color: #CF3434" onclick="deletePoit(${poit.id})">ลบ</button>
                    </div>
                </td>`;
                tableBody.appendChild(row);
            });

            renderPagination(data);
        }

        function renderPagination(data) {
            const pagination = document.getElementById("pagination");
            pagination.innerHTML = ""; // Clear previous pagination

            const totalPages = Math.ceil(poits.length / rowsPerPage);

            // Previous button
            const prevBtn = document.createElement("button");
            prevBtn.innerHTML = '<span class="icon-[material-symbols--chevron-left-rounded]"></span>';
            prevBtn.className = `px-3 py-1 ${currentPage === 1 ? "text-gray-800 cursor-not-allowed" : "text-blue-600 cursor-pointer"} text-5xl`;
            prevBtn.disabled = currentPage === 1;
            prevBtn.onclick = () => goToPage(currentPage - 1);
            pagination.appendChild(prevBtn);

            // Display first page button if needed
            if (currentPage > 3) {
                const firstBtn = document.createElement("button");
                firstBtn.innerText = "1";
                firstBtn.className = `px-4 py-2 mx-1 rounded-lg text-base font-semibold bg-white border border-gray-300 text-black cursor-pointer`;
                firstBtn.onclick = () => goToPage(1);
                pagination.appendChild(firstBtn);
                pagination.appendChild(document.createTextNode("..."));
            }

            // Display middle page numbers
            for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
                const btn = document.createElement("button");
                btn.innerText = i;
                btn.className = `px-4 py-2 mx-1 rounded-lg text-base font-semibold 
                                ${i === currentPage ? "bg-blue-600 text-white " : "bg-white border border-gray-300 text-black cursor-pointer"}`;
                btn.onclick = () => goToPage(i);
                pagination.appendChild(btn);
            }

            // Display last page button if needed
            if (currentPage < totalPages - 2) {
                pagination.appendChild(document.createTextNode("..."));
                const lastBtn = document.createElement("button");
                lastBtn.innerText = totalPages;
                lastBtn.className = `px-4 py-2 mx-1 rounded-lg text-base font-semibold bg-white border border-gray-300 text-black cursor-pointer`;
                lastBtn.onclick = () => goToPage(totalPages);
                pagination.appendChild(lastBtn);
            }

            // Next button
            const nextBtn = document.createElement("button");
            nextBtn.innerHTML = '<span class="icon-[material-symbols--chevron-right-rounded]"></span>';
            nextBtn.className = `px-3 py-1 ${currentPage === totalPages ? "text-gray-800 cursor-not-allowed" : "text-blue-600 cursor-pointer"} text-5xl`;
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

        function viewDetail(id) {
            const poit = poits.find(p => p.id === id);
            Swal.fire({
                title: "<b class='text-gray-800'>รายละเอียดข้อมูล POI</b>",
                html: `
                        <div class="flex flex-col items-center space-y-4 text-left w-full max-w-md mx-auto">
                            <div class="w-full">
                                <label class="block text-gray-800 text-sm mb-1">ชื่อ</label>
                                <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${poit.name}" readonly>
                            </div>
                            <div class="w-full">
                            <label class="block text-gray-800 text-sm mb-1">ประเภท</label>
                            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${poit.type}" readonly>
                            </div>
                            <div class="w-full">
                            <label class="block text-gray-800 text-sm mb-1">ชื่อสมาชิก</label>
                            <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${poit.provice}" readonly>
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

        function editPoit(id) {
    const poit = poits.find(p => p.id === id);

    Swal.fire({
        title: `
            <div class="flex flex-col items-center mb-1">
                <span class="iconify" data-icon="material-symbols-light:edit-square-rounded" data-width="160" data-height="160"></span>
            </div>
            <b class="text-gray-800">แก้ไขข้อมูล POI</b>
        `,
        html: `
            <div class="flex flex-col space-y-1 text-left">
                <label class="font-semibold text-gray-800">ชื่อสถานที่</label>
                <input type="text" id="poiName" class="w-full p-2 border border-gray-300 rounded mb-3" value="${poit.name}">

                <label class="font-semibold text-gray-800">ประเภท</label>
                <input type="text" id="poiType" class="w-full p-2 border border-gray-300 rounded mb-3" value="${poit.type}">

                <label class="font-semibold text-gray-800">คำอธิบาย</label>
                <textarea id="poiDescription" class="w-full p-2 border border-gray-300 rounded mb-3">${poit.description}</textarea>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: "ยืนยัน",
        cancelButtonText: "ยกเลิก",
        confirmButtonColor: "#2D8C42",
        focusCancel: true,
        preConfirm: () => {
            const name = document.getElementById("poiName").value;
            const type = document.getElementById("poiType").value;
            const description = document.getElementById("poiDescription").value;

            if (!name || !type || !description) {
                Swal.showValidationMessage("กรุณากรอกข้อมูลให้ครบทุกช่อง");
                return false;
            }

            // อัปเดตข้อมูล POI
            poit.name = name;
            poit.type = type;
            poit.description = description;

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


function editPoit(id) {
    const poit = poits.find(p => p.id === id);

    Swal.fire({
        title: `<b class="text-gray-800">แก้ไขข้อมูล POI</b>`,
        html: `
            <div class="flex flex-col space-y-1 text-left">
                <label class="font-semibold text-gray-800">ชื่อสถานที่</label>
                <input type="text" id="poiName" class="w-full p-2 border border-gray-300 rounded mb-3" value="${poit.name}">

                <label class="font-semibold text-gray-800">ประเภท</label>
                <select id="poiType" class="w-full p-2 border border-gray-300 rounded mb-3" onchange="updateIconPreview()">
                    <option value="ร้านอาหาร" ${poit.type === "ร้านอาหาร" ? "selected" : ""}>ร้านอาหาร</option>
                    <option value="ร้านกาแฟ" ${poit.type === "ร้านกาแฟ" ? "selected" : ""}>ร้านกาแฟ</option>
                    <option value="ร้านขนม" ${poit.type === "ร้านขนม" ? "selected" : ""}>ร้านขนม</option>
                    <option value="ผับบาร์" ${poit.type === "ผับบาร์" ? "selected" : ""}>ผับบาร์</option>
                    <option value="ศูนย์การค้า" ${poit.type === "ศูนย์การค้า" ? "selected" : ""}>ศูนย์การค้า</option>
                </select>

                <label class="font-semibold text-gray-800">Icon</label>
                <div id="iconPreview" class="text-2xl mb-3">${getIconByType(poit.type)}</div>

                <label class="font-semibold text-gray-800">คำอธิบาย</label>
                <textarea id="poiDescription" class="w-full p-2 border border-gray-300 rounded mb-3">${poit.description}</textarea>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: "ยืนยัน",
        cancelButtonText: "ยกเลิก",
        confirmButtonColor: "#2D8C42",
        focusCancel: true,
        preConfirm: () => {
            const name = document.getElementById("poiName").value;
            const type = document.getElementById("poiType").value;
            const description = document.getElementById("poiDescription").value;

            if (!name || !type || !description) {
                Swal.showValidationMessage("กรุณากรอกข้อมูลให้ครบทุกช่อง");
                return false;
            }

            // อัปเดตข้อมูล POI
            poit.name = name;
            poit.type = type;
            poit.description = description;

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

// ฟังก์ชันสำหรับอัปเดต Icon ตามประเภทที่เลือก
function updateIconPreview() {
    const type = document.getElementById("poiType").value;
    const iconPreview = document.getElementById("iconPreview");
    iconPreview.innerHTML = getIconByType(type);
}
        document.addEventListener("DOMContentLoaded", () => {
            renderTable();

            const filterAll = () => {
                const searchVal = document.getElementById("searchInput").value.toLowerCase();
                const typeVal = document.getElementById("typeSelect").value;
                const provVal = document.getElementById("provinceSelect").value;

                const filtered = poits.filter(p =>
                    (!searchVal || p.name.toLowerCase().includes(searchVal) || p.type.toLowerCase().includes(searchVal) || p.province.toLowerCase().includes(searchVal)) &&
                    (!typeVal || p.type === typeVal) &&
                    (!provVal || p.province === provVal)
                );

                currentPage = 1;
                renderTable(filtered);
            };

            document.getElementById("searchInput").addEventListener("input", filterAll);
            document.getElementById("typeSelect").addEventListener("change", filterAll);
            document.getElementById("provinceSelect").addEventListener("change", filterAll);
        });

        document.addEventListener("click", () => {
            document.querySelectorAll("[id^=menu-]").forEach(menu => menu.classList.add("hidden"));
        });
        function deletePoit(id) {
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
            }).then((result) => {
                if (result.isConfirmed) {
                    // ลบรายการออกจากอาร์เรย์
                    poits = poits.filter(poits => poits.id !== id);

                    // อัปเดตตาราง
                    renderTable();

                    // แจ้งเตือนว่าลบสำเร็จ
                    Swal.fire({
                        title: "ลบแล้ว!",
                        text: "สถานที่ที่สนใจถูกลบเรียบร้อย",
                        icon: "success"
                    });
                }
            });
        }
    </script>

@endsection