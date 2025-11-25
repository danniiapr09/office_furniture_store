{{-- 
    File: resources/views/admin/furniture/index.blade.php 
    Halaman ini seharusnya di-extend dari master layout Anda, tetapi saya membuatnya 
    stand-alone (self-contained) untuk tujuan demonstrasi dan perbaikan error. 
    Jika Anda memiliki @extends('layouts.admin'), tambahkan di baris atas.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Manajemen Furniture - Admin Panel</title>
    <!-- Asumsikan Anda menggunakan Bootstrap 5.3 untuk styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary-color: #3C7FAA; /* Warna biru baru yang lebih profesional */
            --secondary-bg: #f8f9fa;
        }
        body {
            background-color: var(--secondary-bg);
            font-family: 'Inter', sans-serif;
            padding-top: 20px;
        }
        .container {
            max-width: 1200px;
        }
        .card { border-radius: 10px; border: 1px solid #e9ecef; }
        .table-hover > tbody > tr:hover > td, 
        .table-hover > tbody > tr:hover > th { --bs-table-bg-hover: #eaf3fa; }
        .table-bordered { border-radius: 10px; overflow: hidden; }
        .table thead { background-color: #e9ecef; }
        .btn-primary {
             background-color: var(--primary-color);
             border-color: var(--primary-color);
             border-radius: 6px;
        }
        .btn-primary:hover {
             background-color: #2e5f80;
             border-color: #2e5f80;
        }
        .btn { border-radius: 6px; }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1060;
            flex-direction: column;
        }
    </style>
</head>
<body>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay">
    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Memuat...</span>
    </div>
    <p class="mt-3 text-primary" id="loadingMessage">Memverifikasi koneksi server...</p>
</div>

<div class="container" id="main-content" style="display: none;">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h3><i class="bi bi-box-seam-fill me-2 text-primary"></i> Manajemen Furniture</h3>
        <small class="text-muted">Kelola semua produk furniture Anda.</small>
      </div>
      <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFurnitureModal">
            <i class="bi bi-plus-lg"></i> Tambah Item Baru
        </button>
      </div>
    </div>

    <!-- Area Filter & Search -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <input id="searchInput" class="form-control" placeholder="Cari berdasarkan nama..." oninput="debouncedLoad()">
                </div>
                <div class="col-md-4">
                    <select id="filterCategory" class="form-select" onchange="loadFurniture(1)">
                        <option value="">Semua Kategori</option>
                        <!-- Opsi akan diisi oleh JS -->
                    </select>
                </div>
                <div class="col-md-3 text-end">
                    <button class="btn btn-outline-secondary w-100" onclick="loadFurniture(1)">
                        <i class="bi bi-arrow-clockwise"></i> Muat Ulang Data
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Peringatan Autentikasi -->
    <div id="authAlert" class="alert alert-danger d-none" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> 
        <strong>Akses Ditolak!</strong> Anda mungkin belum login atau ada masalah konfigurasi Session/Sanctum di server Laravel Anda.
    </div>

    <!-- Tabel Data Furniture -->
    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle bg-white">
            <thead class="table-light">
                <tr>
                    <th width="60">#</th>
                    <th>Nama</th>
                    <th width="180">Kategori</th>
                    <th width="120">Harga</th>
                    <th width="180">Aksi</th>
                </tr>
            </thead>
            <tbody id="furniture-table">
                <tr><td colspan="5" class="text-center">Data sedang dimuat...</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <nav>
      <ul id="pagination" class="pagination justify-content-center"></ul>
    </nav>
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
            <i class="bi bi-upload"></i> Perbarui
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
                Pesan error akan muncul di sini.
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
                <button type="button" class="btn btn-danger" id="confirmActionBtn">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    /* ---------------------------
    CONFIG & HELPER
    ----------------------------*/
    // KRITIS: GANTI URL INI sesuai dengan URL Laravel API Anda (gunakan domain yang sama dengan frontend)
    // Jika Anda menggunakan domain Railway, ganti:
    const API_BASE_URL = 'https://officefurniturestore-production.up.railway.app'; 
    // Jika Anda masih di lokal, ganti ke:
    // const API_BASE_URL = 'http://127.0.0.1:8000'; 
    
    let currentPage = 1;
    let lastPage = 1;
    let searchTimeout = null;
    let isAppReady = false; 

    // Helper DOM
    const qs = id => document.getElementById(id);

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

    // Custom Alert replacement (menggunakan modal)
    const showErrorMessage = (message, title = 'Error Koneksi') => {
        qs('messageModalTitle').innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i> ${title}`;
        qs('messageModalBody').innerHTML = escapeHtml(message);
        const modal = new bootstrap.Modal(qs('messageModal'));
        modal.show();
    };
    
    // Custom Confirm replacement (menggunakan modal)
    const showConfirmModal = (message, callback) => {
        qs('confirmModalBody').innerHTML = escapeHtml(message);
        const confirmBtn = qs('confirmActionBtn');
        
        // Atur event handler baru
        confirmBtn.onclick = () => {
            document.querySelector('#confirmModal .btn-close').click();
            callback();
        };

        const modal = new bootstrap.Modal(qs('confirmModal'));
        modal.show();
    };

    /* -------------------------------------
    INISIALISASI APLIKASI (Minta CSRF Token)
    --------------------------------------*/
    async function initApp() {
        qs('loadingMessage').textContent = 'Memverifikasi sesi admin...';
        
        // KRITIS: Langkah 1 - Permintaan cookie CSRF untuk Sanctum
        try {
            const res = await fetch(`${API_BASE_URL}/sanctum/csrf-cookie`, {
                method: 'GET',
                credentials: 'include' // Wajib agar cookie diterima
            });

            if (!res.ok) {
                console.error(`[AUTH INIT ERROR] Gagal mendapatkan CSRF cookie. Status: ${res.status}`);
                showErrorMessage(`Gagal inisialisasi sesi (${res.status}). Cek SANCTUM_STATEFUL_DOMAINS di .env.`, 'Error Inisialisasi');
                
                qs('loadingOverlay').style.display = 'none';
                return;
            }
            
            console.log('[AUTH INIT SUCCESS] CSRF Cookie berhasil diterima. Memuat data utama...');
            isAppReady = true;
            
            // Langkah 2 - Lanjutkan pemuatan data utama setelah sukses
            loadCategoriesFor('addCategorySelect');
            loadFurniture(1);
            
            qs('loadingOverlay').style.display = 'none';
            qs('main-content').style.display = 'block';

        } catch (err) {
            console.error('[FETCH ERROR] General fetch error during init:', err);
             if (err.message.includes('Failed to fetch')) {
                 showErrorMessage(`Gagal terhubung ke API di ${API_BASE_URL}. Pastikan server Laravel berjalan.`, 'Error Koneksi Jaringan');
            } else {
                 showErrorMessage(`Gagal inisialisasi aplikasi. Error: ${err.message}`);
            }
            qs('loadingOverlay').style.display = 'none';
        }
    }


    /* ===========================
    LOAD FURNITURE (LIST, SEARCH, FILTER)
    =========================== */
    async function loadFurniture(page = 1) {
        if (!isAppReady) return; 

        qs('authAlert').classList.add('d-none'); // Sembunyikan alert auth
        currentPage = page;
        const categoryId = qs('filterCategory').value || '';
        const q = encodeURIComponent(qs('searchInput').value || '');
        
        let url = `${API_BASE_URL}/api/furniture?page=${page}&q=${q}`;
        if (categoryId) {
            url += `&category_id=${categoryId}`;
        }
        
        qs('furniture-table').innerHTML = `<tr><td colspan="5" class="text-center"><div class="spinner-border spinner-border-sm me-2"></div> Memuat Data...</td></tr>`;

        try {
            const res = await fetch(url, {
                // KRITIS: Wajib untuk mengirim cookie session (Sanctum)
                credentials: 'include' 
            });

            if (!res.ok) {
                // Jika 401/403: Tampilkan alert otentikasi
                if (res.status === 401 || res.status === 403) {
                    qs('authAlert').classList.remove('d-none'); 
                    console.warn('[AUTH ERROR] Akses Ditolak (401/403). Cek Auth/Sanctum.');
                    qs('furniture-table').innerHTML = `<tr><td colspan="5" class="text-center text-danger">Sesi login habis/tidak valid. Silakan login kembali.</td></tr>`;
                    return;
                } else {
                    // Jika 500: Kemungkinan error di Controller
                    if (res.status === 500) {
                        const errorText = await res.text();
                         showErrorMessage(`Gagal memuat data. Status: 500 (Internal Server Error). Pesan: ${errorText.substring(0, 500)}... Cek log Laravel!`, 'Server Error 500');
                    } else {
                        showErrorMessage(`Gagal memuat data. Status: ${res.status}.`);
                    }
                    throw new Error('Failed to load furniture data.');
                }
            }

            const payload = await res.json();
            
            // Penanganan Struktur Pagination (untuk Laravel Resources/Paginator)
            const items = payload.data ?? payload.items ?? payload;
            if (payload.meta) { // Laravel Resource Collection
                currentPage = payload.meta.current_page;
                lastPage = payload.meta.last_page;
            } else if (payload.last_page) { // Laravel Simple Paginator
                currentPage = payload.current_page;
                lastPage = payload.last_page;
            } else {
                currentPage = page;
                lastPage = 1;
            }

            let html = '';
            
            if (!items || items.length === 0) {
                html = `<tr><td colspan="5" class="text-center">Tidak ada data furniture ditemukan.</td></tr>`;
            } else {
                items.forEach((item, index) => {
                    const rowNumber = (currentPage - 1) * 15 + index + 1; // Asumsi 15 items per page
                    html += `
                        <tr id="row-${item.id}">
                            <td>${rowNumber}</td>
                            <td>${escapeHtml(item.name)}</td>
                            <td>${escapeHtml(item.category?.name ?? 'Tanpa Kategori')}</td>
                            <td>Rp ${numberWithCommas(item.price)}</td>
                            <td>
                                <button class="btn btn-sm btn-warning me-1" onclick="openEditModal(${item.id})"><i class="bi bi-pencil"></i> Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="confirmDelete(${item.id})"><i class="bi bi-trash"></i> Hapus</button>
                            </td>
                        </tr>
                    `;
                });
            }
            qs('furniture-table').innerHTML = html;
            renderPagination();

        } catch (err) {
            console.error('[FETCH ERROR] General fetch error:', err);
             if (err.message.includes('Failed to fetch')) {
                 showErrorMessage(`Gagal terhubung ke API (Jaringan/Koneksi Ditolak). Pastikan API_BASE_URL sudah benar.`, 'Error Koneksi Jaringan');
             }
            qs('furniture-table').innerHTML = `<tr><td colspan="5" class="text-danger text-center">Gagal memuat data (Kesalahan Jaringan)</td></tr>`;
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

        // ... (Logic Pagination sama seperti sebelumnya)
        // ... (Pastikan logic pagination di sini sesuai dengan kebutuhan)

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
        if (!isAppReady) return; 

        const selectElement = qs(selectId);
        selectElement.innerHTML = '<option value="">Memuat...</option>';

        const url = `${API_BASE_URL}/api/categories`;
        
        try {
            const res = await fetch(url, {
                credentials: 'include'
            });
            
            if(!res.ok) {
                 if (res.status === 401 || res.status === 403) {
                    showErrorMessage('Akses Ditolak saat memuat Kategori (401/403). Session mungkin habis.', 'Error Otorisasi');
                 } else {
                     showErrorMessage(`Gagal memuat kategori. Status: ${res.status}.`);
                 }
                selectElement.innerHTML = '<option value="">Error memuat</option>';
                return;
            }

            const data = await res.json();
            const categories = data.data ?? data;
            
            let html = '<option value="">-- Pilih Kategori --</option>';
            
            categories.forEach(cat=>{
                html += `<option value="${cat.id}" ${selected && selected==cat.id ? 'selected':''}>${escapeHtml(cat.name)}</option>`;
            });
            selectElement.innerHTML = html;

            // Salin opsi ke dropdown filter
            if (selectId === 'addCategorySelect') {
                const filterSelect = qs('filterCategory');
                if (filterSelect) {
                    filterSelect.innerHTML = `<option value="">Semua Kategori</option>` + html.substring(html.indexOf('>')+1);
                }
            }

        } catch (err) {
            console.error('[FETCH ERROR] Category load failed:', err);
             if (err.message.includes('Failed to fetch')) {
                 showErrorMessage(`Gagal terhubung ke API Kategori.`);
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
        qs(containerId).innerHTML = errorHtml;
    };


    /* ===========================
    CREATE FURNITURE
    =========================== */
    async function createFurniture(){
        if (!isAppReady) return; 

        const errorContainer = 'addErrors';
        qs(errorContainer).innerHTML = '';
        const form = qs('addFurnitureForm');
        const fd = new FormData(form);

        try {
            const res = await fetch(`${API_BASE_URL}/api/furniture`, {
                method: 'POST',
                body: fd,
                headers: { 'Accept': 'application/json' },
                credentials: 'include' 
            });

            const json = await res.json();
            
            if(!res.ok){
                if (res.status === 401 || res.status === 403) {
                    showErrorMessage('Akses Ditolak (401/403) saat membuat item. Anda perlu login ulang.', 'Error Otorisasi');
                }
                showValidationErrors(errorContainer, json);
                return;
            }
            
            document.querySelector('#addFurnitureModal .btn-close').click();
            form.reset();
            loadFurniture(1); 
            showErrorMessage('Item berhasil ditambahkan!', 'Sukses');

        } catch (err) {
            console.error('[FETCH ERROR] Create Furniture network error:', err);
            qs(errorContainer).innerHTML = 'Gagal membuat item (Kesalahan Jaringan/Server)';
        }
    }

    /* ===========================
    OPEN EDIT MODAL (populate)
    =========================== */
    async function openEditModal(id){
        if (!isAppReady) return; 

        qs('editErrors').innerHTML = '';
        
        try {
            const res = await fetch(`${API_BASE_URL}/api/furniture/${id}`, {
                credentials: 'include'
            });
            
            if(!res.ok) {
                 if (res.status === 401 || res.status === 403) {
                    showErrorMessage('Akses Ditolak (401/403). Session mungkin habis.', 'Error Otorisasi');
                 }
                showErrorMessage('Gagal memuat data item. Mungkin tidak ditemukan.');
                return;
            }
            
            const data = await res.json();
            const item = data.data ?? data; 

            // Populate categories dan pilih yang sesuai
            loadCategoriesFor('editCategorySelect', item.category_id);

            qs('edit_id').value = item.id;
            qs('edit_name').value = item.name ?? '';
            qs('edit_price').value = item.price ?? '';
            qs('edit_description').value = item.description ?? '';
            
            // Preview Gambar
            if(item.image_url){
                qs('currentImagePreview').innerHTML = `
                <label class="form-label">Gambar Saat Ini</label>
                <div><img src="${item.image_url}" onerror="this.onerror=null;this.src='https://placehold.co/180x180/EAEAEA/555555?text=No+Image';" style="max-width:180px;height:auto;border-radius:6px;border:1px solid #ddd;"></div>
                <small class="text-muted">Kosongkan input 'Ganti Gambar' jika tidak ingin mengubah.</small>
                `;
            } else {
                qs('currentImagePreview').innerHTML = '';
            }

            // Reset file input
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
        if (!isAppReady) return; 

        const errorContainer = 'editErrors';
        qs(errorContainer).innerHTML = '';
        const id = qs('edit_id').value;
        const form = qs('editFurnitureForm');
        const fd = new FormData(form);

        // Tambahkan method override untuk PUT/PATCH karena FormData hanya mendukung GET/POST
        fd.append('_method', 'PUT');

        if (qs('edit_image').files.length === 0) {
            fd.delete('image'); // Jangan kirim field 'image' jika kosong
        }

        try {
            // Kita tetap menggunakan method POST di fetch, tetapi Laravel akan menangkap _method=PUT
            const res = await fetch(`${API_BASE_URL}/api/furniture/${id}`, {
                method: 'POST', 
                body: fd,
                headers: { 'Accept': 'application/json' },
                credentials: 'include' 
            });

            const json = await res.json();
            if(!res.ok){
                if (res.status === 401 || res.status === 403) {
                    showErrorMessage('Akses Ditolak (401/403) saat update item. Anda perlu login ulang.', 'Error Otorisasi');
                }
                showValidationErrors(errorContainer, json);
                return;
            }

            document.querySelector('#editFurnitureModal .btn-close').click();
            loadFurniture(currentPage); 
            showErrorMessage('Item berhasil diperbarui!', 'Sukses');

        } catch (err) {
            console.error('[FETCH ERROR] Update Furniture network error:', err);
            qs(errorContainer).innerHTML = 'Gagal mengupdate item (Kesalahan Jaringan/Server)';
        }
    }
    
    /* ===========================
    DELETE FURNITURE
    =========================== */
    function confirmDelete(id) {
        if (!isAppReady) return; 
        showConfirmModal(`Apakah Anda yakin ingin menghapus item #${id}? Aksi ini tidak bisa dibatalkan.`, () => deleteFurnitureAction(id));
    }

    async function deleteFurnitureAction(id){
        try {
            const res = await fetch(`${API_BASE_URL}/api/furniture/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json' },
                credentials: 'include'
            });

            if (!res.ok) {
                 if (res.status === 401 || res.status === 403) {
                    showErrorMessage('Akses Ditolak (401/403) saat menghapus item. Anda perlu login ulang.', 'Error Otorisasi');
                } else {
                    showErrorMessage(`Gagal menghapus item #${id}. Status: ${res.status}`);
                }
                return;
            }

            // Hapus baris dari tabel dan muat ulang
            const row = qs(`row-${id}`);
            if (row) row.remove();
            loadFurniture(currentPage); 
            showErrorMessage('Item berhasil dihapus!', 'Sukses');


        } catch (err) {
            console.error('[FETCH ERROR] Delete Furniture network error:', err);
            showErrorMessage('Gagal menghapus item (Kesalahan Jaringan/Server)');
        }
    }

    /* ===========================
    INITIAL LOAD
    =========================== */
    window.onload = function(){
        // Mulai proses inisialisasi CSRF dan pemuatan data
        initApp();
    }
</script>

</body>
</html>