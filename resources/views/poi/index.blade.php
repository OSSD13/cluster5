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

    function displayValue(value) {
    return value === null || value === undefined || value === "" ? "-" : value;
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
                <td class="py-3 px-4 text-center ">${displayValue(poi.province)}</td>

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
    const maxVisiblePages = 5; // 4+1 current

    const addButton = (text, page, isActive = false, isDisabled = false) => {
        const btn = document.createElement("button");
        btn.innerText = text;
        btn.className = `px-4 py-2 mx-1 rounded-lg text-base font-semibold 
            ${isActive ? "bg-blue-600 text-white" : "bg-white border border-gray-300 text-black cursor-pointer"} 
            ${isDisabled ? "text-gray-400 cursor-not-allowed" : ""}`;
        if (!isDisabled) {
            btn.onclick = () => goToPage(page);
        }
        pagination.appendChild(btn);
    };

    const addEllipsis = () => {
        const dots = document.createElement("span");
        dots.innerText = "...";
        dots.className = "mx-2 text-gray-500";
        pagination.appendChild(dots);
    };

    // Previous button
    const prevBtn = document.createElement("button");
    prevBtn.innerHTML = '<span class="icon-[material-symbols--chevron-left-rounded]"></span>';
    prevBtn.className = `px-3 py-1 ${currentPage === 1 ? "text-gray-400 cursor-not-allowed" : "text-blue-600 cursor-pointer"} text-5xl`;
    prevBtn.disabled = currentPage === 1;
    prevBtn.onclick = () => goToPage(currentPage - 1);
    pagination.appendChild(prevBtn);

    // Always show page 1
    addButton("1", 1, currentPage === 1);

    // Left dots
    if (currentPage > 4) {
        addEllipsis();
    }

    // Middle pages
    const startPage = Math.max(2, currentPage - 2);
    const endPage = Math.min(totalPages - 1, currentPage + 2);

    for (let i = startPage; i <= endPage; i++) {
        addButton(i, i, currentPage === i);
    }

    // Right dots
    if (currentPage < totalPages - 3) {
        addEllipsis();
    }

    // Always show last page (if > 1)
    if (totalPages > 1) {
        addButton(totalPages, totalPages, currentPage === totalPages);
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

    function formatThaiDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}


    function viewDetail(id) {
    const poi = pois.find(item => item.poi_id === id);

    if (!poi) {
        Swal.fire("ไม่พบข้อมูล POI", "", "error");
        return;
    }

    Swal.fire({
        html: `
            <div class="flex flex-col text-3xl mb-6 mt-4">
                <b class="text-gray-800">รายละเอียดสถานที่</b>
            </div>
            <div class="flex flex-col space-y-2 text-left text-sm">
                <div class="w-full">
                    <label class="font-medium text-gray-800">ชื่อสถานที่</label>
                    <input type="text" class="w-full h-10 px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${displayValue(poi.poi_name)}" readonly>
                </div>

                <div class="w-full">
                    <label class="font-medium text-gray-800">ประเภท</label>
                    <input type="text" class="w-full h-10 px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${displayValue(poi.poit_name)}" readonly>
                </div>

                <div class="w-full">
                    <label class="font-medium text-gray-800">จังหวัด</label>
                    <input type="text" class="w-full h-10 px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${displayValue(poi.province)}" readonly>
                </div>

                <div class="w-full">
                    <label class="font-medium text-gray-800">วันที่เพิ่ม</label>
                    <input type="text" class="w-full h-10 px-3 text-gray-800 border border-gray-300 rounded-md shadow-sm" value="${displayValue(formatThaiDate(poi.created_at))}" readonly>
                </div>
            </div>
        `,
        customClass: {
            popup: 'custom-popup'
        },
        confirmButtonText: "ยืนยัน",
        confirmButtonColor: "#2D8C42",
    });
}

</script>
@endsection
