<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Furniture Management - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary-color: #FAA33C; 
            --secondary-bg: #212529;
            --light-bg: #f8f9fa;
        }
        body {
            background-color: var(--light-bg);
            font-family: 'Inter', sans-serif;
        }
        #main-layout { display: flex; min-height: 100vh; }
        #sidebar {
            width: 250px;
            background-color: var(--secondary-bg);
            color: white;
            padding: 0;
            flex-shrink: 0;
            border-radius: 0 10px 10px 0;
        }
        .sidebar-header {
            padding: 20px 25px;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 12px 25px;
            border-left: 5px solid transparent;
            transition: all 0.2s;
            border-radius: 0;
        }
        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: rgba(250, 163, 60, 0.1); 
            border-left-color: var(--primary-color);
        }
        .nav-link i { margin-right: 10px; }
        #content-area { flex-grow: 1; padding: 0; }
        #top-header {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 15px 30px;
            border-bottom: 1px solid #e9ecef;
        }
        .welcome-text { font-size: 1.25rem; font-weight: 600; color: #343a40; }
        #main-content { padding: 30px; }
        .card { border-radius: 10px; border: none; }
        .table-hover > tbody > tr:hover > td, 
        .table-hover > tbody > tr:hover > th { --bs-table-bg-hover: #fff3e0; }
        .table-bordered { border-radius: 10px; overflow: hidden; }
        .table thead { background-color: #e9ecef; }
        .btn-primary {
             background-color: var(--primary-color);
             border-color: var(--primary-color);
             border-radius: 6px;
        }
        .btn-primary:hover {
             background-color: #E38D2F;
             border-color: #E38D2F;
        }
        .btn { border-radius: 6px; }
    </style>
</head>
<body>

<div id="main-layout">
    
    <div id="sidebar">
        <div class="sidebar-header">
            Office Furniture Admin
        </div>
        
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-grid-fill"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#">
                    <i class="bi bi-box-seam-fill"></i> Manage Furniture
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-tags-fill"></i> Manage Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-receipt"></i> Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-people-fill"></i> Users
                </a>
            </li>
            
            <li class="nav-item mt-5">
                <!-- Simulasikan tombol Logout -->
                <button class="btn btn-outline-light w-75 ms-3" onclick="showErrorMessage('Logout logic goes here!', 'Info Log Out')">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </li>
        </ul>
    </div>
    
    <div id="content-area">
        
        <header id="top-header" class="d-flex justify-content-between align-items-center">
            <div class="welcome-text">
                Management: <span class="text-primary">Furniture</span>
            </div>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Furniture</li>
              </ol>
            </nav>
        </header>

        <div id="main-content">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
              <div>
                <h3><i class="bi bi-box-seam-fill me-2 text-primary"></i> Furniture Data</h3>
                <small class="text-muted">Kelola semua produk furniture.</small>
              </div>
              <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFurnitureModal">
                    <i class="bi bi-plus-lg"></i> Add Furniture
                </button>
              </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-5">
                            <input id="searchInput" class="form-control" placeholder="Search by name..." oninput="debouncedLoad()">
                        </div>
                        <div class="col-md-4">
                            <select id="filterCategory" class="form-select" onchange="loadFurniture(1)">
                                <option value="">All Categories</option>
                                <!-- Opsi akan diisi oleh JS -->
                            </select>
                        </div>
                        <div class="col-md-3 text-end">
                            <button class="btn btn-outline-secondary w-100" onclick="loadFurniture(1)">
                                <i class="bi bi-arrow-clockwise"></i> Reload Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Area Notifikasi Khusus (Jika 401/403 terjadi) -->
            <div id="authAlert" class="alert alert-danger d-none" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> 
                <strong>Akses Ditolak!</strong> Anda mungkin belum login atau ada masalah konfigurasi CORS/Sanctum di server Laravel Anda. Cek kembali setting API_BASE_URL (sekarang **http://127.0.0.1:8000**).
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle bg-white">
                    <thead class="table-light">
                        <tr>
                            <th width="60">#</th>
                            <th>Name</th>
                            <th width="180">Category</th>
                            <th width="120">Price</th>
                            <th width="180">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="furniture-table">
                        <tr><td colspan="5" class="text-center">Loading...</td></tr>
                    </tbody>
                </table>
            </div>

            <nav>
              <ul id="pagination" class="pagination justify-content-center"></ul>
            </nav>
        </div>
        
    </div>
</div>

<!-- Modal ADD Furniture -->
<div class="modal fade" id="addFurnitureModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-plus-circle-fill me-2"></i> Tambah Furniture Baru</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="addFurnitureForm">
            <div id="addErrors" class="text-danger small mb-2"></div>
 
            <div class="row">
              <div class="col-md-8 mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="name" class="form-control" required>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-select" id="addCategorySelect" required>
                  <option value="">Memuat...</option>
                </select>
              </div>
            </div>
 
            <div class="mb-3">
              <label class="form-label">Harga</label>
              <input type="number" name="price" class="form-control" required>
            </div>
 
            <div class="mb-3">
              <label class="form-label">Deskripsi</label>
              <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
 
            <div class="mb-3">
              <label class="form-label">Gambar</label>
              <input type="file" name="image" class="form-control">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button class="btn btn-primary" onclick="createFurniture()">
            <i class="bi bi-save"></i> Simpan Item
          </button>
        </div>
      </div>
    </div>
</div>

<!-- Modal EDIT Furniture -->
<div class="modal fade" id="editFurnitureModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> Edit Furniture</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="editFurnitureForm">
            <input type="hidden" name="id" id="edit_id">
 
            <div id="editErrors" class="text-danger small mb-2"></div>
 
            <div class="row">
              <div class="col-md-8 mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-select" id="editCategorySelect" required>
                  <option value="">Memuat...</option>
                </select>
              </div>
            </div>
 
            <div class="mb-3">
              <label class="form-label">Harga</label>
              <input type="number" name="price" id="edit_price" class="form-control" required>
            </div>
 
            <div class="mb-3">
              <label class="form-label">Deskripsi</label>
              <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
            </div>
 
            <div class="mb-3">
              <label class="form-label">Ganti Gambar (opsional)</label>
              <input type="file" name="image" id="edit_image" class="form-control">
            </div>
 
            <div id="currentImagePreview" class="mb-3"></div>
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button class="btn btn-primary" onclick="updateFurniture()">
            <i class="bi bi-upload"></i> Update
          </button>
        </div>
      </div>
    </div>
</div>

<!-- Custom Message Modal (Alert replacement) -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="messageModalTitle"><i class="bi bi-exclamation-triangle-fill me-2"></i> Error</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="messageModalBody">
                Message content here.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Custom Confirm Modal (Confirm replacement) -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-question-circle-fill me-2"></i> Konfirmasi Aksi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="confirmModalBody">
                Apakah Anda yakin?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmActionBtn">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    /* ---------------------------
    Config & helper
    ----------------------------*/
    // GANTI URL INI dengan URL Laravel API Anda. Jika 127.0.0.1:8000 tidak bekerja, coba http://[IP_Anda_di_Jaringan_Lokal]:8000
    const API_BASE_URL = 'http://127.0.0.1:8000'; 
    // ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

    let currentPage = 1;
    let lastPage = 1;
    let searchTimeout = null;
    let deleteIdToConfirm = null; 

    // Helper DOM
    const qs = id => document.getElementById(id);
    const showError = (containerId, message) => { qs(containerId).innerHTML = message; };
    const hideAuthAlert = () => qs('authAlert').classList.add('d-none');
    const showAuthAlert = () => qs('authAlert').classList.remove('d-none');
    
    // Formatting
    const escapeHtml = (unsafe) => {
        if (!unsafe) return '';
        return unsafe.toString().replace(/[&<>"']/g, function(m) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[m];
        });
    };
    const numberWithCommas = (x) => {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    };

    // Custom Alert replacement
    const showErrorMessage = (message, title = 'Error') => {
        qs('messageModalTitle').innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i> ${title}`;
        qs('messageModalBody').innerHTML = escapeHtml(message);
        const modal = new bootstrap.Modal(qs('messageModal'));
        modal.show();
    };
    
    // Custom Confirm replacement
    const showConfirmModal = (message, callback) => {
        qs('confirmModalBody').innerHTML = escapeHtml(message);
        const confirmBtn = qs('confirmActionBtn');
        
        confirmBtn.onclick = () => {
            document.querySelector('#confirmModal .btn-close').click();
            callback();
        };

        const modal = new bootstrap.Modal(qs('confirmModal'));
        modal.show();
    };

    /* ===========================
    LOAD FURNITURE (with search & pagination)
    GET /api/furniture?page=...&q=...
    =========================== */
    async function loadFurniture(page = 1) {
        hideAuthAlert();
        currentPage = page;
        const categoryId = qs('filterCategory').value || '';
        const q = encodeURIComponent(qs('searchInput').value || '');
        
        let url = `${API_BASE_URL}/api/furniture?page=${page}&q=${q}`;
        if (categoryId) {
            url += `&category_id=${categoryId}`;
        }
        
        console.log(`[API CALL] Loading Furniture: ${url}`); // Logging URL yang dicoba

        qs('furniture-table').innerHTML = `<tr><td colspan="5" class="text-center"><div class="spinner-border spinner-border-sm me-2"></div> Memuat...</td></tr>`;

        try {
            const res = await fetch(url, {
                // KRITIS: Wajib untuk mengirim cookie session
                credentials: 'include'
            });

            if (!res.ok) {
                console.error(`[API ERROR] Status: ${res.status}. URL: ${url}`);
                if (res.status === 401 || res.status === 403) {
                    showAuthAlert(); // Tampilkan notifikasi Auth Alert di UI
                    console.warn('[AUTH ERROR] Akses Ditolak (401/403) saat memuat furniture. Cek Auth/Sanctum.');
                    qs('furniture-table').innerHTML = `<tr><td colspan="5" class="text-center text-danger">Akses Ditolak (401/403). Cek konfigurasi server.</td></tr>`;
                } else {
                    const errorText = await res.text();
                    console.error(`[API ERROR] Response Body: ${errorText.substring(0, 300)}...`);
                    // Coba parsing JSON, jika gagal, gunakan text
                    try {
                        const errorJson = JSON.parse(errorText);
                        showErrorMessage(`Gagal memuat data. Status: ${res.status}. Pesan: ${errorJson.message || 'Error tidak diketahui'}`);
                    } catch {
                         showErrorMessage(`Gagal memuat data. Status: ${res.status}. Pesan: ${errorText || 'Respon non-JSON.'}`);
                    }
                }
                throw new Error('Failed to load furniture data with response status: ' + res.status);
            }

            const payload = await res.json();
            
            // Handle Pagination structure
            const items = payload.data ?? payload;
            if (payload.meta) {
                currentPage = payload.meta.current_page;
                lastPage = payload.meta.last_page;
            } else if (payload.last_page) {
                currentPage = payload.current_page;
                lastPage = payload.last_page;
            } else {
                currentPage = page;
                lastPage = 1;
            }

            let html = '';
            // Anggap per halaman 15 item (default Laravel)
            const itemsPerPage = 15; 
            
            if (!items || items.length === 0) {
                html = `<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>`;
            } else {
                items.forEach((item, index) => {
                    const rowNumber = (currentPage - 1) * itemsPerPage + index + 1; 
                    html += `
                        <tr id="row-${item.id}">
                            <td>${rowNumber}</td>
                            <td>${escapeHtml(item.name)}</td>
                            <td>${escapeHtml(item.category?.name ?? '-')}</td>
                            <td>Rp ${numberWithCommas(item.price)}</td>
                            <td>
                                <button class="btn btn-sm btn-warning me-1" onclick="openEditModal(${item.id})"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="confirmDelete(${item.id})"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    `;
                });
            }
            qs('furniture-table').innerHTML = html;
            renderPagination();

        } catch (err) {
            console.error('[FETCH ERROR] General fetch error:', err);
            // Failed to fetch error (jaringan/koneksi ditolak)
            if (err.message.includes('Failed to fetch')) {
                 showErrorMessage(`Gagal terhubung ke API di ${API_BASE_URL}. Pastikan server Laravel Anda berjalan dan konfigurasi CORS/Sanctum sudah benar.`);
            }
            qs('furniture-table').innerHTML = `<tr><td colspan="5" class="text-danger text-center">Gagal memuat data (Jaringan/Server)</td></tr>`;
        }
    }

    /* debounce loader for search input */
    const debouncedLoad = () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => loadFurniture(1), 400);
    };

    /* ===========================
    Pagination render
    =========================== */
    const renderPagination = () => {
        const ul = qs('pagination');
        ul.innerHTML = '';
        if(lastPage <= 1) return;

        const makeItem = (p, text, active=false) => {
            return `<li class="page-item ${active ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="loadFurniture(${p});return false;">${text}</a>
                    </li>`;
        };

        if(currentPage > 1) ul.innerHTML += makeItem(currentPage-1, '« Prev');

        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(lastPage, currentPage + 2);

        if (currentPage <= 3) endPage = Math.min(lastPage, 5);
        if (currentPage >= lastPage - 2) startPage = Math.max(1, lastPage - 4);
        
        if (startPage > 1) ul.innerHTML += makeItem(1, '1...');

        for(let p=startPage; p<= endPage; p++){
            ul.innerHTML += makeItem(p, p, p===currentPage);
        }

        if (endPage < lastPage) ul.innerHTML += makeItem(lastPage, '...'+lastPage);

        if(currentPage < lastPage) ul.innerHTML += makeItem(currentPage+1, 'Next »');
    };

    /* ===========================
    Load categories (for filter, add & edit)
    =========================== */
    async function loadCategoriesFor(selectId, selected = null){
        const selectElement = qs(selectId);
        // Pastikan tampilkan "Memuat..." sebelum fetch
        if (selectElement.options.length === 0 || selectElement.options[0].value === "") {
             selectElement.innerHTML = '<option value="">Memuat...</option>';
        }

        const url = `${API_BASE_URL}/api/categories`;
        console.log(`[API CALL] Loading Categories: ${url}`); // Logging URL yang dicoba
        try {
            const res = await fetch(url, {
                credentials: 'include'
            });
            
            if(!res.ok) {
                console.error(`[API ERROR] Gagal memuat kategori. Status: ${res.status}`);
                 if (res.status === 401 || res.status === 403) {
                    showErrorMessage('Akses Ditolak saat memuat Kategori (401/403). Anda perlu login.');
                 }
                throw new Error('Failed to load categories');
            }

            const data = await res.json();
            // Cek apakah response array atau memiliki properti data (jika menggunakan resource/collection)
            const categories = data.data ?? data;
            
            let html = '<option value="">-- Pilih Kategori --</option>';
            
            categories.forEach(cat=>{
                html += `<option value="${cat.id}" ${selected && selected==cat.id ? 'selected':''}>${escapeHtml(cat.name)}</option>`;
            });
            selectElement.innerHTML = html;

            // Load filter category too (hanya dilakukan sekali saat memuat untuk modal Add)
            if (selectId === 'addCategorySelect') {
                const filterSelect = qs('filterCategory');
                if (filterSelect) {
                    // Salin opsi kategori ke dropdown filter, tambahkan opsi "Semua Kategori"
                    filterSelect.innerHTML = `<option value="">Semua Kategori</option>` + html.substring(html.indexOf('>')+1);
                }
            }

        } catch (err) {
            console.error('[FETCH ERROR] Category load failed:', err);
             if (err.message.includes('Failed to fetch')) {
                 showErrorMessage(`Gagal terhubung ke API Kategori di ${API_BASE_URL}. Cek koneksi & CORS server.`);
             }
            selectElement.innerHTML = '<option value="">Error memuat</option>';
        }
    }

    /* ===========================
    Show Validation Errors
    =========================== */
    const showValidationErrors = (containerId, json) => {
        let errorHtml = '';
        if (json.errors) {
            for (const key in json.errors) {
                json.errors[key].forEach(msg => {
                    errorHtml += `<div>• ${msg}</div>`;
                });
            }
        } else if (json.message) {
            errorHtml = `<div>• ${json.message}</div>`;
        }
        showError(containerId, errorHtml);
    };


    /* ===========================
    CREATE FURNITURE
    =========================== */
    async function createFurniture(){
        showError('addErrors','');
        const form = qs('addFurnitureForm');
        const fd = new FormData(form);

        try {
            const res = await fetch(`${API_BASE_URL}/api/furniture`, {
                method: 'POST',
                body: fd,
                headers: { 'Accept': 'application/json' },
                credentials: 'include' 
            });

            // Pastikan kita mencoba membaca response body bahkan jika res.ok false, 
            // karena response body sering berisi pesan error validasi/server
            const json = await res.json();
            
            if(!res.ok){
                console.error(`[API ERROR] Create Furniture failed. Status: ${res.status}`);
                if (res.status === 401 || res.status === 403) {
                    showErrorMessage('Akses Ditolak (401/403) saat membuat item. Anda perlu login ulang.');
                }
                showValidationErrors('addErrors', json);
                // Tambahkan pesan "Unauthenticated" jika status 401
                if (res.status === 401) {
                    showError('addErrors', '<div>• Unauthenticated. Anda harus login untuk melakukan aksi ini.</div>');
                }
                return;
            }
            
            document.querySelector('#addFurnitureModal .btn-close').click();
            form.reset();
            loadFurniture(1); 

        } catch (err) {
            console.error('[FETCH ERROR] Create Furniture network error:', err);
            showError('addErrors','Gagal membuat item (Kesalahan Jaringan/Server)');
        }
    }

    /* ===========================
    OPEN EDIT MODAL (populate)
    =========================== */
    async function openEditModal(id){
        showError('editErrors','');
        
        try {
            const res = await fetch(`${API_BASE_URL}/api/furniture/${id}`, {
                credentials: 'include'
            });
            
            if(!res.ok) {
                 if (res.status === 401 || res.status === 403) {
                    showErrorMessage('Akses Ditolak (401/403). Anda perlu login ulang.');
                 }
                showErrorMessage('Gagal memuat data item. Mungkin tidak ditemukan.');
                return;
            }
            
            const data = await res.json();
            const item = data.data ?? data; // Handle jika menggunakan resource

            // Populate categories
            loadCategoriesFor('editCategorySelect', item.category_id);

            qs('edit_id').value = item.id;
            qs('edit_name').value = item.name ?? '';
            qs('edit_price').value = item.price ?? '';
            qs('edit_description').value = item.description ?? '';
            
            // show current image preview
            if(item.image_url){
                qs('currentImagePreview').innerHTML = `
                <label class="form-label">Gambar Saat Ini</label>
                <div><img src="${item.image_url}" onerror="this.onerror=null;this.src='https://placehold.co/180x180/EAEAEA/555555?text=No+Image';" style="max-width:180px;height:auto;border-radius:6px;border:1px solid #ddd;"></div>
                <small class="text-muted">Kosongkan input 'Ganti Gambar' jika tidak ingin mengubah.</small>
                `;
            } else {
                qs('currentImagePreview').innerHTML = '';
            }

            // Reset file input for image
            qs('edit_image').value = null;

            const modal = new bootstrap.Modal(qs('editFurnitureModal'));
            modal.show();

        } catch (err) {
            console.error('[FETCH ERROR] Open Edit Modal failed:', err);
            showErrorMessage('Gagal memuat data untuk edit.');
        }
    }

    /* ===========================
    UPDATE FURNITURE
    =========================== */
    async function updateFurniture(){
        showError('editErrors','');
        const id = qs('edit_id').value;
        const form = qs('editFurnitureForm');
        const fd = new FormData(form);

        // Tambahkan method override untuk PUT/PATCH karena FormData hanya mendukung GET/POST
        fd.append('_method', 'PUT');

        // Jika input file kosong, hapus entri 'image' dari FormData agar Laravel tidak memprosesnya
        // Catatan: Ini penting jika Anda hanya ingin mengupdate data teks tanpa mengubah gambar.
        if (qs('edit_image').files.length === 0) {
            fd.delete('image');
        }

        try {
            const res = await fetch(`${API_BASE_URL}/api/furniture/${id}`, {
                method: 'POST', // Tetap POST, karena kita pakai _method=PUT
                body: fd,
                headers: { 'Accept': 'application/json' },
                credentials: 'include' 
            });

            const json = await res.json();
            if(!res.ok){
                console.error(`[API ERROR] Update Furniture failed. Status: ${res.status}`);
                 if (res.status === 401 || res.status === 403) {
                    showErrorMessage('Akses Ditolak (401/403) saat update item. Anda perlu login ulang.');
                }
                showValidationErrors('editErrors', json);
                return;
            }

            document.querySelector('#editFurnitureModal .btn-close').click();
            loadFurniture(currentPage); 

        } catch (err) {
            console.error('[FETCH ERROR] Update Furniture network error:', err);
            showError('editErrors','Gagal mengupdate item (Kesalahan Jaringan/Server)');
        }
    }
    
    /* ===========================
    DELETE FURNITURE
    =========================== */
    function confirmDelete(id) {
        deleteIdToConfirm = id;
        showConfirmModal(`Apakah Anda yakin ingin menghapus item #${id}? Aksi ini tidak bisa dibatalkan.`, deleteFurnitureAction);
    }

    async function deleteFurnitureAction(){
        const id = deleteIdToConfirm;
        if (!id) return;
        
        try {
            const res = await fetch(`${API_BASE_URL}/api/furniture/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json' },
                credentials: 'include'
            });

            if (!res.ok) {
                 if (res.status === 401 || res.status === 403) {
                    showErrorMessage('Akses Ditolak (401/403) saat menghapus item. Anda perlu login ulang.');
                    console.warn('[AUTH ERROR] Akses Ditolak (401/403) saat delete furniture. Cek Auth/Sanctum.');
                } else {
                    showErrorMessage(`Gagal menghapus item #${id}. Status: ${res.status}`);
                }
                return;
            }

            // Hapus baris dari tabel
            qs(`row-${id}`).remove();
            loadFurniture(currentPage); // Muat ulang halaman untuk periksa pagination

        } catch (err) {
            console.error('[FETCH ERROR] Delete Furniture network error:', err);
            showErrorMessage('Gagal menghapus item (Kesalahan Jaringan/Server)');
        }
    }

    /* ===========================
    INITIAL LOAD
    =========================== */
    window.onload = function(){
        // Inisialisasi Kategori untuk Add Modal, yang juga akan mengisi Filter
        loadCategoriesFor('addCategorySelect');
        // Muat Furniture
        loadFurniture(1);
    }
</script>

</body>
</html>