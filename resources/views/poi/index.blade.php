@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')
<div id="globalDropdownMenu" class="hidden absolute bg-white shadow-lg rounded-lg w-[150px] z-50 p-2 space-y-2 transition duration-200"></div>
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-2xl font-bold text-gray-800">POI จัดการสถานที่ที่สนใจ</h2>

            <a href="{{ route('poi.create') }}">
                <button
                    class="bg-blue-500 hover:bg-blue-700 border border-gray-400 text-white font-bold py-2 px-4 rounded whitespace-nowrap"
                    style="background-color: #3062B8">
                    สร้าง POI
                </button>
            </a>
        </div>

        <input id="searchInput" type="text" placeholder="ค้นหาสถานที่ที่สนใจ"
            class="w-full p-2 border border-gray-300 rounded mb-3">

        <p class="text-gray-700">ผลลัพธ์ <span id="resultCount">0</span> รายการ</p>
        <a href="{{ route('poi.type.index') }}">
            <button
                class="hover:bg-blue-700 text-white border border-gray-400 font-bold py-2 px-4 rounded whitespace-nowrap"
                style="background-color: #3062B8">
                ไปหน้า POI type
            </button>
        </a>
    </div>

<div class="overflow-visible">
    <table class="w-full mt-5 border-collapse rounded-lg overflow-hidden table-fixed">
        <thead class="bg-blue-500 text-black text-sm" style="background-color: #B5CFF5">
            <tr>
                <th class="py-2 px-2 text-left w-1/12">ID</th>
                <th class="py-2 px-4 text-center w-3/12">ชื่อสถานที่</th>
                <th class="py-2 px-2 text-center w-2/12">ประเภท</th>
                <th class="py-2 px-2 text-center w-2/12">จังหวัด</th>
                <th class="py-2 px-2 text-center w-1/12">ตัวเลือก</th>
            </tr>
        </thead>
        <tbody id="tableBody" class="bg-white divide-y divide-gray-200 text-sm"></tbody>
    </table>
</div>

    <div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let pois = [];
        let currentPage = 1;
        const rowsPerPage = 10;

        document.addEventListener("DOMContentLoaded", () => {
            fetchPois();

            document.getElementById("searchInput").addEventListener("input", function () {
                currentPage = 1;
                fetchPois(this.value);
            });
        });

    async function fetchPois(search = '') {
        const res = await fetch(`{{ route('api.poi.query') }}?limit=${rowsPerPage}&page=${currentPage}&search=${encodeURIComponent(search)}`);
        const result = await res.json();
        pois = result.data;
        document.getElementById("resultCount").innerText = result.total;
        renderTable();
        renderPagination(result.total);
    }

        function renderTable() {
            const tableBody = document.getElementById("tableBody");
            tableBody.innerHTML = "";

            pois.forEach((poi) => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td class="py-3 px-4 w-16">${poi.poi_id}</td>
                    <td class="py-3 px-4 truncate">${safeText(poi.poi_name)}</td>
                    <td class="py-3 px-4 truncate">${safeText(poi.poit_name)}</td>
                    <td class="py-3 px-4 truncate">${safeText(poi.province)}</td>
                    <td class="py-3 px-1 w-10 text-center relative">
                        <button class="cursor-pointer" onclick="toggleMenu(event, ${poi.poi_id})">&#8230;</button>
                        <div id="menu-${poi.poi_id}" class="hidden absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-32 z-50 p-2 space-y-2">
                            <button class="block w-full px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-lg" onclick="viewDetail(${poi.poi_id})">ดูรายละเอียด</button>
                            <button class="block w-full px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-lg" onclick="window.location.href='{{ route('poi.edit') }}?id=${poi.poi_id}'">แก้ไข</button>
                            <button class="block w-full px-4 py-2 text-white bg-red-600 hover:bg-red-700 rounded-lg" onclick="deletePoi(${poi.poi_id})">ลบ</button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        function renderPagination(totalItems) {
            const pagination = document.getElementById("pagination");
            pagination.innerHTML = "";
            const totalPages = Math.ceil(totalItems / rowsPerPage);

            const prevBtn = document.createElement("button");
            prevBtn.innerHTML = '&larr;';
            prevBtn.className = `px-3 py-1 ${currentPage === 1 ? "text-gray-400 cursor-not-allowed" : "text-blue-600"} text-xl`;
            prevBtn.disabled = currentPage === 1;
            prevBtn.onclick = () => goToPage(currentPage - 1);
            pagination.appendChild(prevBtn);

            for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
                const btn = document.createElement("button");
                btn.innerText = i;
                btn.className = `px-4 py-2 mx-1 rounded-lg text-base font-semibold 
                                     ${i === currentPage ? "bg-blue-600 text-white" : "bg-white border border-gray-300 text-black"}`;
                btn.onclick = () => goToPage(i);
                pagination.appendChild(btn);
            }

            const nextBtn = document.createElement("button");
            nextBtn.innerHTML = '&rarr;';
            nextBtn.className = `px-3 py-1 ${currentPage === totalPages ? "text-gray-400 cursor-not-allowed" : "text-blue-600"} text-xl`;
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.onclick = () => goToPage(currentPage + 1);
            pagination.appendChild(nextBtn);
        }

        function goToPage(pageNumber) {
            currentPage = pageNumber;
            fetchPois(document.getElementById("searchInput").value);
        }

        function toggleMenu(event, id) {
            event.stopPropagation();
            document.querySelectorAll('[id^="menu-"]').forEach(menu => menu.classList.add("hidden"));
            document.getElementById(`menu-${id}`).classList.toggle("hidden");
        }

    let activeMenuId = null;

function toggleMenu(event, id) {
    event.stopPropagation();

    const button = event.currentTarget;
    const menu = document.getElementById("globalDropdownMenu");

    // ปิดถ้าเมนูเดิมเปิดอยู่
    if (activeMenuId === id && !menu.classList.contains("hidden")) {
        menu.classList.add("hidden");
        activeMenuId = null;
        return;
    }

    activeMenuId = id;

    menu.innerHTML = `
        <button class="block w-full px-4 py-2 text-white border border-gray-400 bg-blue-600 rounded-lg hover:bg-blue-700 whitespace-nowrap" style="background-color: #3062B8" 
            onclick="document.getElementById('globalDropdownMenu').classList.add('hidden'); activeMenuId = null; viewDetail(${id})">
            ดูรายละเอียด
        </button>
        <button class="block w-full px-4 py-2 text-white border border-gray-400 bg-blue-600 rounded-lg hover:bg-blue-700" style="background-color: #3062B8" 
            onclick="document.getElementById('globalDropdownMenu').classList.add('hidden'); activeMenuId = null; window.location.href='{{ route('poi.edit') }}?id=${id}'">
            แก้ไข
        </button>
        <button class="block w-full px-4 py-2 text-white border border-gray-400 bg-red-600 rounded-lg hover:bg-red-700" style="background-color: #CF3434" 
            onclick="document.getElementById('globalDropdownMenu').classList.add('hidden'); activeMenuId = null; deletePoi(${id})">
            ลบ
        </button>
    `;

            // เอาตำแหน่งของปุ่มมา
            const rect = button.getBoundingClientRect();
            const menuWidth = menu.offsetWidth || 160; // default กว้างประมาณนี้
            const scrollTop = window.scrollY || document.documentElement.scrollTop;

            // วางเมนูชิดซ้ายของปุ่ม
            menu.style.left = `${rect.left - menuWidth + rect.width}px`;
            menu.style.top = `${rect.top + scrollTop}px`;
            menu.classList.remove("hidden");
        }
        document.addEventListener("click", function () {
        const menu = document.getElementById("globalDropdownMenu");
        if (!menu.classList.contains("hidden")) {
            menu.classList.add("hidden");
            activeMenuId = null;
        }
         });


        function deletePoi(id) {
            Swal.fire({
                title: "ลบสถานที่",
                text: "คุณต้องการลบ POI นี้ใช่หรือไม่?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#aaa",
                confirmButtonText: "ยืนยัน",
                cancelButtonText: "ยกเลิก"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/poi/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(res => {
                        if (res.ok) {
                            Swal.fire("สำเร็จ", "ลบเรียบร้อย", "success");
                            fetchPois();
                        } else {
                            Swal.fire("เกิดข้อผิดพลาด", "ไม่สามารถลบได้", "error");
                        }
                    });
                }
            });
        }

        function viewDetail(id) {
            const poi = pois.find(p => p.poi_id === id);
            if (!poi) return;

            Swal.fire({
                title: "รายละเอียดสถานที่",
                html: `
                        <div class="text-left space-y-2 text-sm text-gray-700">
                            <div><b>ชื่อสถานที่:</b> ${poi.poi_name || '-'}</div>
                            <div><b>ประเภท:</b> ${poi.poit_name || '-'}</div>
                            <div><b>จังหวัด:</b> ${poi.province || '-'}</div>
                            <div><b>ที่อยู่:</b> ${poi.poi_address || '-'}</div>
                            <div><b>เพิ่มเมื่อ:</b> ${formatThaiDate(poi.created_at)}</div>
                        </div>
                    `,
                confirmButtonText: "ปิด",
                confirmButtonColor: "#3085d6"
            });
        }

        function formatThaiDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString("th-TH", {
                year: "numeric",
                month: "short",
                day: "numeric"
            });
        }

        function safeText(text) {
            return text ?? '-';
        }

    </script>
@endsection