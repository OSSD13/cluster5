@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-2xl font-bold text-gray-800">POI จัดการสถานที่สนใจ</h2>

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
        <table class="w-full mt-5 border-collapse rounded-lg">
            <thead class="text-gray-800 text-md" style="background-color: #B5CFF5">
                <tr>
                    <th scope="col" class="py-2 px-4 text-left rounded-tl-lg">ID</th>
                    <th class="py-3 px-4 text-left min-w-[150px]">ชื่อสถานที่ / ประเภท</th>
                    <th class="py-3 px-4 text-center max-w-[120px]">จังหวัด</th>
                    <th class="py-3 px-1 w-7 text-center rounded-tr-lg">&#8230;</th>
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
        let searchTimeout;

        document.addEventListener("DOMContentLoaded", () => {
            fetchPois();

            document.getElementById("searchInput").addEventListener("input", function() {
                clearTimeout(searchTimeout);
                const keyword = this.value;
                searchTimeout = setTimeout(() => {
                    currentPage = 1;
                    fetchPois(keyword);
                }, 300); //  debounce 300ms
            });
        });

        async function fetchPois(search = '') {
            const res = await fetch(
                `{{ route('api.poi.query') }}?limit=${rowsPerPage}&page=${currentPage}&search=${encodeURIComponent(search)}`
                );
            const result = await res.json();
            pois = result.data;
            total = result.total;
            document.getElementById("searchInput").value = search;
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
                        <td class="py-3 px-4 ">
                            <div class="font-semibold text-md" title="${poi.poi_name}">${poi.poi_name}</div>
                            <div class="text-sm text-gray-400 " title="${poi.poit_name}">${poi.poit_name}</div>
                        </td>
                        <td class="py-3 px-4 text-center ">${poi.province || '-'}</td>

                        <td class="py-3 px-1 w-10 text-center relative">
                            <button class="cursor-pointer" onclick="toggleMenu(event, ${poi.poi_id})">&#8230;</button>
                            <div id="menu-${poi.poi_id}" class="hidden absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-32 z-50 p-2 space-y-2 -translate-y-1/2">
                                <button class="block w-full px-4 py-2 text-white bg-blue-600 border border-gray-400 rounded-lg shadow-md hover:bg-blue-700" style="background-color: #3062B8"  onclick="viewDetail(${poi.poi_id})">ดูรายละเอียด</button>
                                <button class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg border border-gray-400 shadow-md hover:bg-blue-700" style="background-color: #3062B8"  onclick="window.location.href='{{ route('poi.edit') }}?poi_id=${poi.poi_id}'">แก้ไข</button>
                                <button class="block w-full px-4 py-2 text-white bg-red-600 rounded-lg border border-gray-400 shadow-md hover:bg-red-700" style="background-color: #CF3434"   onclick="deletePoi(${poi.poi_id})">ลบ</button>
                            </div>
                        </td>
                    `;
                tableBody.appendChild(row);
            });
        }

        function renderPagination() {
            const pagination = document.getElementById("pagination");
            pagination.innerHTML = "";

            const totalPages = Math.ceil(total / rowsPerPage);
            const maxVisible = 1;
            let startPage = Math.max(1, currentPage - maxVisible);
            let endPage = Math.min(totalPages, currentPage + maxVisible);

            if (totalPages <= 1) return;

            const createPageButton = (page, isActive = false) => {
                const btn = document.createElement("button");
                btn.innerText = page;
                btn.className = `min-w-[40px] h-10 px-3 mx-1 rounded-lg text-sm font-medium flex items-center justify-center whitespace-nowrap ${isActive ? "bg-blue-600 text-white" : "bg-white border border-gray-300 text-black hover:bg-gray-100"}`;
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
            prevBtn.className = `min-w-[40px] h-10 px-3 mx-1 rounded-lg text-xl font-bold flex items-center justify-center ${currentPage === 1 ? "text-gray-300 bg-white border border-gray-200 cursor-not-allowed" : "text-blue-600 bg-white border border-gray-300 hover:bg-blue-50"}`;
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
            fetchPois(searchValue); // แก้ไขจาก fetchPoits เป็น fetchPois
        }


        function toggleMenu(event, id) {
            event.stopPropagation();
            document.querySelectorAll('[id^="menu-"]').forEach(menu => menu.classList.add("hidden"));
            document.getElementById(`menu-${id}`).classList.toggle("hidden");
        }

        document.addEventListener("click", () => {
            document.querySelectorAll('[id^="menu-"]').forEach(menu => menu.classList.add("hidden"));
        });

        //deletePoi
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
            }).then(async (result) => {
                if (result.isConfirmed) {
                    console.log(id); //ไม่เกี่ยวกับการลบ เป็นการตรวจสอบค่า id ว่าที่ส่งมามีค่าไหม

                    const res = await fetch("{{ route('api.poi.delete') }}", {
                        method: 'DELETE',
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name=\"csrf-token\"]').content
                        },
                        body: JSON.stringify({
                            poi_id: id
                        })
                    }).then(res => {
                        if (res.ok) {
                            Swal.fire("สำเร็จ", "ลบเรียบร้อย", "success");
                            fetchPois();
                        } else {
                            Swal.fire("เกิดข้อผิดพลาด",
                                "ไม่สามารถลบได้ poiนี้เป็นสาขา กรุณาลบข้อมูลที่หน้าสาขา", "error");
                        }
                    });
                }
            });
        }

        function viewDetail(id) {
            const poi = pois.find(p => p.poi_id === id);
            if (!poi) return;

            Swal.fire({
                html: `
                    <div class="flex flex-col text-2xl mb-6 mt-4">
                    <b class=text-gray-800>รายละเอียดข้อมูลสถานที่สนใจ</b>
                    </div>
                    <div class="flex flex-col space-y-2 text-left">
                    <div class="w-full">
                        <label class="font-medium text-gray-800 text-sm">ชื่อสถานที่</label>
                        <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value=" ${poi.poi_name || '-'}" readonly>
                    </div>
                    <div class="w-full">
                        <label class="font-medium text-gray-800 text-sm">ประเภท</label>
                        <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${poi.poit_name || '-'}" readonly>
                    </div>
                    <div class="w-full">
                        <label class="font-medium text-gray-800 text-sm">ประเภท</label>
                        <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${poi.province || '-'}" readonly>
                    </div>
                    <div class="w-full">
                        <label class="font-medium text-gray-800 text-sm">ที่อยู่</label>
                        <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${poi.poi_address || '-'}" readonly>
                    </div>
                    <div class="w-full">
                        <label class="font-medium text-gray-800 text-sm">เพิ่มเมื่อ</label>
                        <input type="text" class="w-full h-10 text-sm px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${formatThaiDate(poi.created_at)}" readonly>
                    </div>
                     </div>`,

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
