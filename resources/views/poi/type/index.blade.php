@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-2xl font-bold text-gray-700">POIT จัดการประเภทสถานที่ที่สนใจ</h2>
            <a href="{{ route('poi.type.create') }}">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap" style="background-color: #3062B8">
                    สร้าง POI
                </button>
            </a>
        </div>

        <input type="text" placeholder="ค้นหาสถานที่ที่สนใจ" class="w-full p-2 border border-gray-300 rounded mb-3" id="searchInput">

        <div class="mb-3">
            <label class="block text-gray-600 mb-1">ประเภท</label>
            <select class="w-full p-2 border border-gray-300 rounded" id="typeSelect">
                <option value="">ประเภทสถานที่</option>
                <option value="ร้านอาหาร">ร้านอาหาร</option>
                <option value="ร้านกาแฟ">ร้านกาแฟ</option>
                <option value="ร้านขนม">ร้านขนม</option>
                <option value="ผับบาร์">ผับบาร์</option>
                <option value="ศูนย์การค้า">ศูนย์การค้า</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="block text-gray-600 mb-1">จังหวัด</label>
            <select class="w-full p-2 border border-gray-300 rounded" id="provinceSelect">
                <option value="">จังหวัด</option>
                <option value="ชลบุรี">ชลบุรี</option>
                <option value="กรุงเทพมหานคร">กรุงเทพมหานคร</option>
                <option value="ขอนแก่น">ขอนแก่น</option>
                <option value="เชียงใหม่">เชียงใหม่</option>
                <option value="ปราจีนบุรี">ปราจีนบุรี</option>
            </select>
        </div>

        <p class="text-gray-700">ผลลัพธ์ <span id="resultCount">0</span> รายการ</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full mt-5 border-collapse rounded-lg overflow-hidden table-fixed">
            <thead class="bg-blue-500 text-black text-sm" style="background-color: #B5CFF5">
                <tr>
                    <th class="py-2 px-4 text-center w-3/12">ประเภท</th>
                    <th class="py-2 px-4 text-center w-3/12">ชื่อสถานที่</th>
                    <th class="py-2 px-4 text-center w-2/12">Icon</th>
                    <th class="py-2 px-4 text-center w-3/12">จังหวัด</th>
                    <th class="py-2 px-4 text-center w-1/12"></th>
                </tr>
            </thead>
            <tbody id="tableBody" class="bg-white divide-y divide-gray-200 text-sm"></tbody>
        </table>
    </div>

    <div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>
@endsection

@section('script')
<script>
    let poits = [
        { id: 1, name: "บางแสน", type: "ร้านอาหาร", province: "ชลบุรี" },
        { id: 2, name: "อุดรธานี", type: "ร้านกาแฟ", province: "อุดรธานี" },
        { id: 3, name: "ศรีราชา", type: "ร้านขนม", province: "ชลบุรี" },
        { id: 4, name: "พัทยา", type: "ผับบาร์", province: "ชลบุรี" },
        { id: 5, name: "เซนทรัล", type: "ศูนย์การค้า", province: "ชลบุรี" },
        { id: 6, name: "ท่าพระ", type: "ตลาด", province: "ขอนแก่น" },
        { id: 7, name: "กรุงเทพฯ", type: "ร้านอาหาร", province: "กรุงเทพมหานคร" },
        { id: 8, name: "ปราจีนบุรี", type: "ร้านกาแฟ", province: "ปราจีนบุรี" },
        { id: 9, name: "ฉะเชิงเทรา", type: "ตลาด", province: "ฉะเชิงเทรา" },
        { id: 10, name: "สระบุรี", type: "ร้านขนม", province: "สระบุรี" },
        { id: 11, name: "แหลมแท่น", type: "ที่เที่ยว", province: "ชลบุรี" }
    ];

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
                <td class="py-3 px-4 text-left">${poit.type}</td>
                <td class="py-3 px-4 text-left">${poit.name}</td>
                <td class="py-3 px-4 text-center">${getIconByType(poit.type)}</td>
                <td class="py-3 px-4 text-left">${poit.province}</td>
                <td class="py-3 px-1 w-10 text-center relative">
                    <button class="cursor-pointer" onclick="toggleMenu(event, ${poit.id})">&#8230;</button>
                    <div id="menu-${poit.id}" class="hidden absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-32 z-50 p-2 space-y-2">
                        <button class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700" onclick="viewDetail(${poit.id})">ดูรายละเอียด</button>
                        <button class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700" onclick="editPoit(${poit.id})">แก้ไข</button>
                        <button class="block w-full px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700" onclick="deletePoit(${poit.id})">ลบ</button>
                    </div>
                </td>`;
            tableBody.appendChild(row);
        });

        renderPagination(data);
    }

    function renderPagination(data) {
        const totalPages = Math.ceil(data.length / rowsPerPage);
        const pagination = document.getElementById("pagination");
        pagination.innerHTML = "";

        const createBtn = (text, page) => {
            const btn = document.createElement("button");
            btn.innerText = text;
            btn.className = `px-4 py-2 mx-1 rounded-lg ${page === currentPage ? 'bg-blue-600 text-white' : 'bg-white border text-black'}`;
            btn.onclick = () => { currentPage = page; renderTable(data); };
            return btn;
        };

        if (currentPage > 1) {
            pagination.appendChild(createBtn("«", currentPage - 1));
        }

        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                pagination.appendChild(createBtn(i, i));
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                const dots = document.createElement("span");
                dots.innerText = "...";
                pagination.appendChild(dots);
            }
        }

        if (currentPage < totalPages) {
            pagination.appendChild(createBtn("»", currentPage + 1));
        }
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
                <div class="flex flex-col items-start space-y-4 text-left">
                    <div><b>ชื่อสถานที่:</b> ${poit.name}</div>
                    <div><b>ประเภท:</b> ${poit.type}</div>
                    <div><b>จังหวัด:</b> ${poit.province}</div>
                    <div><b>วันที่เพิ่ม:</b> 17 ก.ย. 2568</div>
                    <div><b>เพิ่มโดย:</b> jeng@gmail.com</div>
                </div>
            `,
            confirmButtonText: "ยืนยัน",
            confirmButtonColor: "#2D8C42",
        });
    }

    function editPoit(id) {
        alert(`แก้ไขข้อมูล ID: ${id}`);
    }

    function deletePoit(id) {
        Swal.fire({
            title: "ลบสถานที่ที่สนใจ",
            text: "คุณแน่ใจหรือไม่ว่าต้องการลบ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "ยืนยัน",
            cancelButtonText: "ยกเลิก"
        }).then(result => {
            if (result.isConfirmed) {
                poits = poits.filter(p => p.id !== id);
                renderTable();
                Swal.fire("ลบแล้ว!", "ข้อมูลถูกลบเรียบร้อย", "success");
            }
        });
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
</script>

@endsection
