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
        <table class="min-w-full mt-5 table-auto border-collapse rounded-lg bg-gray-100">
            <thead class="bg-blue-500 text-white text-sm">
                <tr>
                    <th class="py-3 px-4 text-left">ชื่อ / ประเภท</th>
                    <th class="py-3 px-4 text-center">Icon</th>
                    <th class="py-3 px-4 text-left">คำอธิบาย</th>
                    <th class="py-3 px-4 text-center">การจัดการ</th>
                </tr>
            </thead>
            <tbody id="tableBody" class="text-sm text-gray-700">
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
                        <button class="cursor-pointer text-blue-600 hover:text-blue-800" onclick="toggleMenu(event, ${poit.id})">&#8230;</button>
                        <div id="menu-${poit.id}" class="hidden absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-32 z-50 p-2 space-y-2">
                            <button class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700"
                                onclick="viewDetail(${poit.id})">ดูรายละเอียด</button>
                            <button class="block w-full px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700"
                                onclick="editPoit(${poit.id})">แก้ไข</button>
                            <button class="block w-full px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700"
                                onclick="deletePoit(${poit.id})">ลบ</button>
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
        function viewDetail(id) {
            alert("ดูรายละเอียด ID: " + id);
            // หรือเปลี่ยนหน้า location.href = `/poi/type/${id}`
        }

        function editPoit(id) {
            window.location.href = `/poi/type/${id}/edit`;
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
            if (confirm("คุณแน่ใจหรือไม่ว่าต้องการลบ?")) {
                fetch(`/api/poit/delete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ poit_type: getPoitTypeById(id) }) // คุณต้องดึง poit_type จาก id นี้
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    fetchPoits();
                })
                .catch(error => console.error("Delete error:", error));
            }
        }

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
