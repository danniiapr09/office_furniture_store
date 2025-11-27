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
            --secondary-bg: #212529; /* Warna gelap untuk sidebar */
            --light-bg: #f8f9fa; /* Latar belakang konten */
        }
        body {
            background-color: var(--light-bg);
        }
        
        /* Layout Grid Utama */
        #main-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styling (Sama seperti Dashboard) */
        #sidebar {
            width: 250px;
            background-color: var(--secondary-bg);
            color: white;
            padding: 0;
            flex-shrink: 0;
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
        }
        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: rgba(250, 163, 60, 0.1); 
            border-left-color: var(--primary-color);
        }
        .nav-link i {
            margin-right: 10px;
        }

        /* Content Area Styling */
        #content-area {
            flex-grow: 1;
            padding: 0;
        }

        /* Top Header Styling */
        #top-header {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 15px 30px;
            border-bottom: 1px solid #e9ecef;
        }
        .welcome-text {
            font-size: 1.25rem;
            font-weight: 600;
            color: #343a40;
        }

        /* Main Content Padding */
        #main-content {
            padding: 30px;
        }
        
        /* Custom Table Styling */
        .table-hover > tbody > tr:hover > td, 
        .table-hover > tbody > tr:hover > th {
            --bs-table-bg-hover: #fff3e0; /* Hover color oranye pucat */
        }
        .table-bordered {
            border-radius: 10px;
            overflow: hidden; /* agar border radius bekerja pada thead */
        }
        .table thead {
            background-color: #e9ecef;
        }
        
        .btn-primary {
             background-color: var(--primary-color);
             border-color: var(--primary-color);
        }
        .btn-primary:hover {
             background-color: #E38D2F;
             border-color: #E38D2F;
        }
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
                <a class="nav-link" href="/admin/dashboard">
                    <i class="bi bi-grid-fill"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="/admin/furniture">
                    <i class="bi bi-box-seam-fill"></i> Manage Furniture
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/admin/categories">
                    <i class="bi bi-tags-fill"></i> Manage Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/admin/orders">
                    <i class="bi bi-receipt"></i> Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/admin/users">
                    <i class="bi bi-people-fill"></i> Users
                </a>
            </li>
            
            <li class="nav-item mt-5">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="btn btn-outline-danger w-75 ms-3">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
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
                <li class="breadcrumb-item"><a href="/admin/dashboard" class="text-decoration-none">Home</a></li>
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

<div class="modal fade" id="addFurnitureModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-plus-circle-fill me-2"></i> Add New Furniture</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="addFurnitureForm">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div id="addErrors" class="text-danger small mb-2"></div>
 
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="nama" class="form-control" required> 
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select" id="addCategorySelect" required>
                  <option value="">Loading...</option>
                </select>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">Stock</label>
                <input type="number" name="stok" class="form-control" required>
              </div>
            </div>
 
            <div class="mb-3">
              <label class="form-label">Price</label>
              <input type="number" name="harga" class="form-control" required>
            </div>
 
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea name="deskripsi" class="form-control" rows="3"></textarea>
            </div>
 
            <div class="mb-3">
              <label class="form-label">Image</label>
              <input type="file" name="image" class="form-control">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary" onclick="createFurniture()">
            <i class="bi bi-save"></i> Save Item
          </button>
        </div>
      </div>
    </div>
</div>

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
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div id="editErrors" class="text-danger small mb-2"></div>
 
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="nama" id="edit_nama" class="form-control" required>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select" id="editCategorySelect" required>
                  <option value="">Loading...</option>
                </select>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">Stock</label>
                 <input type="number" name="stok" id="edit_stok" class="form-control" required>
              </div>
            </div>
 
            <div class="mb-3">
              <label class="form-label">Price</label>
              <input type="number" name="harga" id="edit_harga" class="form-control" required>
            </div>
 
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="3"></textarea>
            </div>
 
            <div class="mb-3">
              <label class="form-label">Replace Image (optional)</label>
              <input type="file" name="image" id="edit_image" class="form-control">
            </div>
 
            <div id="currentImagePreview" class="mb-3"></div>
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary" onclick="updateFurniture()">
            <i class="bi bi-upload"></i> Update
          </button>
        </div>
      </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    /* ---------------------------
    Config & helper
    ----------------------------*/
    let currentPage = 1;
    let lastPage = 1;
    let searchTimeout = null;
    
    // Helper function
    function qs(id){ return document.getElementById(id); }
    function showError(containerId, message){ qs(containerId).innerHTML = message; }

    /* ===========================
    LOAD FURNITURE (with search & pagination)
    GET /api/furniture?page=...&q=...&category_id=...
    =========================== */
    function loadFurniture(page = 1) {
        currentPage = page;
        // Ambil nilai dari filter kategori
        const categoryId = document.getElementById('filterCategory').value || '';
        const q = encodeURIComponent(document.getElementById('searchInput').value || '');
        
        // **PERBAIKAN:** Menggunakan category_id di URL
        let url = `/api/furniture?page=${page}&q=${q}`;
        if (categoryId) {
            url += `&category_id=${categoryId}`; 
        }

        fetch(url)
            .then(async res => {
                if(!res.ok) throw new Error('Failed to load');
                const payload = await res.json();
                
                // Mengambil data dari response paginasi Laravel
                let items = payload.data ?? [];
                
                currentPage = payload.current_page;
                lastPage = payload.last_page;

                let html = '';
                if(!items || items.length === 0){
                    html = `<tr><td colspan="5" class="text-center">No data</td></tr>`;
                } else {
                    items.forEach(item => {
                        html += `
                            <tr id="row-${item.id}">
                                <td>${item.id}</td>
                                <td>${escapeHtml(item.nama)}</td>
                                <td>${escapeHtml(item.category?.name ?? '-')}</td>
                                <td>Rp ${numberWithCommas(item.harga)}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning me-1" onclick="openEditModal(${item.id})"><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteFurniture(${item.id})"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        `;
                    });
                }
                qs('furniture-table').innerHTML = html;
                renderPagination();
            })
            .catch(err=>{
                qs('furniture-table').innerHTML = `<tr><td colspan="5" class="text-danger text-center">Error loading data: ${err.message}</td></tr>`;
            });
    }

    /* debounce loader for search input */
    function debouncedLoad(){
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(()=> loadFurniture(1), 400);
    }

    /* ===========================
    Pagination render
    =========================== */
    function renderPagination(){
        const ul = qs('pagination');
        ul.innerHTML = '';
        if(lastPage <= 1) return;

        const makeItem = (p, text, active=false) => {
            return `<li class="page-item ${active ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="loadFurniture(${p});return false;">${text}</a>
                    </li>`;
        };

        // prev
        if(currentPage > 1) ul.innerHTML += makeItem(currentPage-1, '« Prev');

        // show 1..n (simple logic)
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(lastPage, currentPage + 2);

        if (currentPage <= 3) endPage = Math.min(lastPage, 5);
        if (currentPage >= lastPage - 2) startPage = Math.max(1, lastPage - 4);
        
        if (startPage > 1) ul.innerHTML += makeItem(1, '1...');

        for(let p=startPage; p<= endPage; p++){
            ul.innerHTML += makeItem(p, p, p===currentPage);
        }

        if (endPage < lastPage) ul.innerHTML += makeItem(lastPage, '...'+lastPage);


        // next
        if(currentPage < lastPage) ul.innerHTML += makeItem(currentPage+1, 'Next »');
    }

    /* ===========================
    Load categories (for filter, add & edit)
    =========================== */
    function loadCategoriesFor(selectId, selected = null){
        // **PERBAIKAN:** Pastikan endpoint API kategori ada!
        fetch('/api/categories') 
            .then(res=>{
                if (!res.ok) throw new Error("API not found or failed");
                return res.json();
            })
            .then(data=>{
                let html = '<option value="">-- Select Category --</option>';
                data.forEach(cat=>{
                    html += `<option value="${cat.id}" ${selected && selected==cat.id ? 'selected':''}>${escapeHtml(cat.name)}</option>`;
                });
                qs(selectId).innerHTML = html;

                // Load filter category too (only once, after loading add form)
                if (selectId === 'addCategorySelect') {
                    const filterSelect = qs('filterCategory');
                    if (filterSelect) {
                        // Salin opsi kategori ke filter, pertahankan 'All Categories'
                        filterSelect.innerHTML = `<option value="">All Categories</option>` + html.substring(html.indexOf('>')+1);
                    }
                }
            })
            .catch((e)=> {
                console.error("Error loading categories:", e);
                qs(selectId).innerHTML = '<option value="">Error loading</option>';
            });
    }
    // initial load for add form AND filter dropdown
    loadCategoriesFor('addCategorySelect');


    /* ===========================
    CREATE FURNITURE
    =========================== */
    function createFurniture(){
        showError('addErrors','');
        const form = qs('addFurnitureForm');
        const fd = new FormData(form);

        fetch('/api/furniture', {
            method: 'POST',
            body: fd,
            headers: { 'Accept': 'application/json' }
        })
        .then(async res=>{
            const json = await res.json();
            if(!res.ok){
                showValidationErrors('addErrors', json);
                return;
            }
            // close modal
            document.querySelector('#addFurnitureModal .btn-close').click();
            form.reset();
            // Load data ke halaman 1 setelah berhasil menambah
            loadFurniture(1); 
        })
        .catch(()=> showError('addErrors','Failed to create item'));
    }

    /* ===========================
    OPEN EDIT MODAL (populate)
    GET /api/furniture/{id}
    =========================== */
    function openEditModal(id){
        showError('editErrors','');
        
        fetch(`/api/furniture/${id}`)
            .then(async res=>{
                if(!res.ok) throw new Error('not found');
                const json = await res.json();
                
                // **PERBAIKAN:** Data yang benar ada di json.data
                const item = json.data;
                
                if (!item) throw new Error('Item data not found in response');

                // populate categories first, passing the selected category_id
                loadCategoriesFor('editCategorySelect', item.category_id);

                qs('edit_id').value = item.id;
                // **PERBAIKAN:** Menggunakan nama kolom yang benar
                qs('edit_nama').value = item.nama ?? ''; 
                qs('edit_harga').value = item.harga ?? '';
                qs('edit_stok').value = item.stok ?? ''; // Tambah stok
                qs('edit_deskripsi').value = item.deskripsi ?? '';
                
                // show current image preview if exists
                if(item.image_url){
                    qs('currentImagePreview').innerHTML = `
                    <label class="form-label">Current Image</label>
                    <div><img src="${item.image_url}" style="max-width:180px;height:auto;border-radius:6px;border:1px solid #ddd;"></div>`;
                } else {
                    qs('currentImagePreview').innerHTML = '';
                }

                // open modal
                const modal = new bootstrap.Modal(document.getElementById('editFurnitureModal'));
                modal.show();
            })
            .catch((e)=> {
                console.error(e);
                alert('Failed to load item data');
            });
    }

    /* ===========================
    UPDATE FURNITURE (PUT via _method override)
    =========================== */
    function updateFurniture(){
        showError('editErrors','');
        const form = qs('editFurnitureForm');
        const id = qs('edit_id').value;
        const fd = new FormData(form);
        fd.append('_method', 'PUT'); // method override for Laravel

        fetch(`/api/furniture/${id}`, {
            method: 'POST', // use POST with _method=PUT to support multipart
            body: fd,
            headers: { 'Accept': 'application/json' }
        })
        .then(async res=>{
            const json = await res.json();
            if(!res.ok){
                showValidationErrors('editErrors', json);
                return;
            }

            // close modal
            document.querySelector('#editFurnitureModal .btn-close').click();
            loadFurniture(currentPage);
        })
        .catch(()=> showError('editErrors','Failed to update item'));
    }

    /* ===========================
    DELETE FURNITURE
    =========================== */
    function deleteFurniture(id){
        if(!confirm('Are you sure you want to delete this item?')) return;
        fetch(`/api/furniture/${id}`, {
            method: 'DELETE',
            // Tambahkan CSRF Token
            headers: { 
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        })
        .then(async res=>{
            if(res.status === 204 || res.ok){
                // remove row if present
                const row = qs(`row-${id}`);
                if(row) row.remove();
                else loadFurniture(currentPage);
                return;
            }
            const json = await res.json();
            alert(json.message || 'Failed to delete');
        })
        .catch(()=> alert('Failed to delete'));
    }

    /* ===========================
    Helpers
    =========================== */
    function showValidationErrors(containerId, json){
        if(!json) return showError(containerId, 'Validation failed');
        if(json.errors){
            const msgs = Object.values(json.errors).flat().map(x => escapeHtml(x));
            showError(containerId, msgs.join('<br>'));
        } else if(json.message){
            showError(containerId, escapeHtml(json.message));
        } else {
            showError(containerId, 'Validation error');
        }
    }

    function numberWithCommas(x){
        if(x===null || x===undefined) return '-';
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function escapeHtml(unsafe){
        if(unsafe === null || unsafe === undefined) return '';
        return unsafe.toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Call loadFurniture on page load
    loadFurniture();
</script>

</body>
</html>