@extends('layouts.main')

@section('title', 'Point of Interest')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md mx-auto">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-2xl font-bold text-gray-800" >POI จัดการสถานที่ที่สนใจ</h2>

            <a href="{{ route('poi.create') }}">
                <button class="bg-blue-500 hover:bg-blue-700 border border-gray-400 text-white font-bold py-2 px-4 rounded whitespace-nowrap" style="background-color: #3062B8">
                    สร้าง POI
                </button>
            </a>
        </div>

        <input id="searchInput" type="text" placeholder="ค้นหาสถานที่ที่สนใจ" class="w-full p-2 border border-gray-300 rounded mb-3">

        <p class="text-gray-700">ผลลัพธ์ <span id="resultCount">0</span> รายการ</p>
        <a href="{{ route('poi.type.index') }}">
            <button class="hover:bg-blue-700 text-white border border-gray-400 font-bold py-2 px-4 rounded whitespace-nowrap" style="background-color: #3062B8">
                ไปหน้า POI type
            </button>
        </a>
    </div>

    <div class="overflow-visible">
        <table class="w-full mt-5 border-collapse rounded-lg overflow-hidden ">
            <thead class="text-gray-800 text-md" style="background-color: #B5CFF5">
                <tr>
                    <th scope="col" class="py-2 px-4 text-left">ID</th>
                    <th class="py-3 px-4 text-left min-w-[200px]">ชื่อสถานที่ / ประเภท</th>
                    <th class="py-3 px-4 text-center max-w-[120px]">จังหวัด</th>
                    <th class="py-3 px-1 w-7 text-center">&#8230;</th>
                  </tr>
            </thead>
    
            <tbody id="tableBody" class="bg-white divide-y divide-gray-200 text-sm"></tbody>
        </table>
    </div>

    <div class="flex justify-center items-center mt-4 space-x-2" id="pagination"></div>
@endsection

@section('script')
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
                <td class="py-3 px-4 ">
                    <div class="font-semibold text-md" title="${poi.poi_name}">${poi.poi_name}</div>
                    <div class="text-sm text-gray-400 " title="${poi.poit_name}">${poi.poit_name}</div>
                </td>
                <td class="py-3 px-4 text-center ">${poi.province}</td>

                <td class="py-3 px-1 w-10 text-center relative">
                    <button class="cursor-pointer" onclick="toggleMenu(event, ${poi.poi_id})">&#8230;</button>
                    <div id="menu-${poi.poi_id}" class="hidden absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-32 z-50 p-2 space-y-2">
                        <button class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700" onclick="viewDetail(${poi.poi_id})">ดูรายละเอียด</button>
                        <button class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700" onclick="window.location.href='{{ route('poi.edit') }}?id=${poi.poi_id}'">แก้ไข</button>
                        <button class="block w-full px-4 py-2 text-white bg-red-600 rounded-lg shadow-md hover:bg-red-700" onclick="deletePoi(${poi.poi_id})">ลบ</button>
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

        // Previous button
        const prevBtn = document.createElement("button");
        prevBtn.innerHTML = '<span class="icon-[material-symbols--chevron-left-rounded]"></span>';
        prevBtn.className = `px-3 py-1 ${currentPage === 1 ? "text-gray-400 cursor-not-allowed" : "text-blue-600 cursor-pointer"} text-5xl`;
        prevBtn.disabled = currentPage === 1;
        prevBtn.onclick = () => goToPage(currentPage - 1);
        pagination.appendChild(prevBtn);
        for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
            const btn = document.createElement("button");
            btn.innerText = i;
            btn.className = `px-4 py-2 mx-1 rounded-lg text-base font-semibold 
                             ${i === currentPage ? "bg-blue-600 text-white" : "bg-white border border-gray-300 text-black cursor-pointer"}`;
            btn.onclick = () => goToPage(i);
            pagination.appendChild(btn);
        }

                // Next button
                const nextBtn = document.createElement("button");
        nextBtn.innerHTML = '<span class="icon-[material-symbols--chevron-right-rounded]"></span>';
        nextBtn.className = `px-3 py-1 ${currentPage === totalPages ? "text-gray-400 cursor-not-allowed" : "text-blue-600 cursor-pointer"} text-5xl`;
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

    document.addEventListener("click", () => {
        document.querySelectorAll('[id^="menu-"]').forEach(menu => menu.classList.add("hidden"));
    });

    function deletePoi(id) {
        if (confirm("ยืนยันการลบ POI?")) {
            fetch(`/poi/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(res => {
                if (res.ok) {
                    alert("ลบเรียบร้อย");
                    fetchPois();
                }
            });
        }
    }

    function viewDetail(id) {
        alert("ดูรายละเอียด POI ID: " + id);
    }
</script>
@endsection
