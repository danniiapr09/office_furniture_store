<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>User Management - Admin</title>
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

        /* Sidebar Styling */
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
            overflow: hidden; 
        }
        .table thead {
            background-color: #e9ecef;
        }
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        .btn-primary {
             background-color: var(--primary-color);
             border-color: var(--primary-color);
        }
        .btn-primary:hover {
             background-color: #E38D2F;
             border-color: #E38D2F;
        }
        .modal-header.bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        /* Modal Form Styling */
        .modal-body .form-control {
            margin-bottom: 15px;
            border-radius: 8px;
            padding: 10px;
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
                <a class="nav-link" href="/admin/furniture">
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
                <a class="nav-link active" href="/admin/users">
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
                Management: <span class="text-primary">Users</span>
            </div>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/admin/dashboard" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Users</li>
              </ol>
            </nav>
        </header>

        <div id="main-content">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
              <div>
                <h3><i class="bi bi-people-fill me-2 text-primary"></i> User Management</h3>
                <small class="text-muted">Kelola semua pengguna aplikasi.</small>
              </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-9">
                            <input id="searchInput" class="form-control" placeholder="Search by name, email, or phone..." oninput="debouncedLoad()">
                        </div>
                        <div class="col-md-3 text-end">
                            <button class="btn btn-outline-secondary w-100" onclick="loadUsers(1)">
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
                            <th width="80">ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th width="150">Phone</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="user-table">
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

<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="editUserForm">
            <input type="hidden" id="edit_id" name="id">
            <div id="editErrors" class="text-danger small mb-2"></div>

            <label class="form-label">Name</label>
            <input type="text" id="edit_name" name="name" class="form-control" required>
            
            <label class="form-label">Email</label>
            <input type="email" id="edit_email" name="email" class="form-control" required>
            
            <label class="form-label">Phone</label>
            <input type="text" id="edit_phone" name="phone" class="form-control">
            
            <label class="form-label">New Password (optional)</label>
            <input type="password" id="edit_password" name="password" class="form-control" placeholder="Biarkan kosong jika tidak ingin mengubah">
            
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary" onclick="updateUser()">
             <i class="bi bi-save"></i> Save Changes
          </button>
        </div>
      </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let currentPage = 1, lastPage = 1, searchTimeout = null;
    
    // Helper untuk mengambil element
    function qs(id){ return document.getElementById(id); }
    function showError(containerId, message){ qs(containerId).innerHTML = message; }
    
    /* ===========================
       LOAD USERS (with search & pagination)
    =========================== */
    function loadUsers(page = 1) {
        currentPage = page;
        const q = encodeURIComponent(document.getElementById('searchInput').value || '');
        
        // Menampilkan Loading
        document.getElementById('user-table').innerHTML = '<tr><td colspan="5" class="text-center">Loading...</td></tr>';
        
        fetch(`/admin/users/list?q=${q}&page=${page}`)
            .then(async res => {
                if(!res.ok) throw new Error('Failed to load users');
                const data = await res.json();
                
                const items = data.data || data;
                lastPage = data.meta?.last_page || data.last_page || 1;
                
                let html = '';
                if (!items || items.length === 0) {
                    html = '<tr><td colspan="5" class="text-center">No users found.</td></tr>';
                } else {
                    items.forEach(u => {
                        html += `<tr id="row-${u.id}">
                            <td>${u.id}</td>
                            <td>${escapeHtml(u.name)}</td>
                            <td>${escapeHtml(u.email)}</td>
                            <td>${escapeHtml(u.phone ?? '-')}</td>
                            <td>
                                <button class="btn btn-sm btn-warning me-1" onclick="openEditUser(${u.id})"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="deleteUser(${u.id})"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>`;
                    });
                }
                
                document.getElementById('user-table').innerHTML = html;
                renderPagination();
            })
            .catch(err => {
                document.getElementById('user-table').innerHTML = `<tr><td colspan="5" class="text-danger text-center">Error loading data: ${err.message}</td></tr>`;
            });
    }

    /* debounce loader for search input */
    function debouncedLoad() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => loadUsers(1), 400);
    }
    
    /* ===========================
    Pagination render (Disamakan dengan Furniture)
    =========================== */
    function renderPagination(){
        const ul = qs('pagination');
        ul.innerHTML = '';
        if(lastPage <= 1) return;

        const makeItem = (p, text, active=false) => {
            return `<li class="page-item ${active ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="loadUsers(${p});return false;">${text}</a>
                    </li>`;
        };

        // prev
        if(currentPage > 1) ul.innerHTML += makeItem(currentPage-1, '« Prev');

        // show 1..n (simple)
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
    OPEN EDIT MODAL (populate)
    =========================== */
    function openEditUser(id){
        showError('editErrors', ''); // Clear previous errors
        
        fetch(`/admin/users/${id}`)
            .then(async res => {
                if(!res.ok) throw new Error('User not found');
                const u = await res.json();
                
                document.getElementById('edit_id').value = u.id;
                document.getElementById('edit_name').value = u.name;
                document.getElementById('edit_email').value = u.email;
                document.getElementById('edit_phone').value = u.phone ?? '';
                document.getElementById('edit_password').value = ''; // Selalu kosongkan password
                
                // Open modal
                const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
                modal.show();
            })
            .catch(err => alert('Failed to load user data: ' + err.message));
    }

    /* ===========================
    UPDATE USER
    =========================== */
    function updateUser(){
        showError('editErrors', '');
        const id = document.getElementById('edit_id').value;
        const form = document.getElementById('editUserForm');
        const fd = new FormData(form);
        fd.append('_method', 'PUT');

        fetch(`/admin/users/${id}`, {
            method: 'POST', // Use POST with _method=PUT
            body: fd,
            headers: { 'Accept': 'application/json' }
        })
        .then(async res => {
             const json = await res.json();
             if(!res.ok){
                 showValidationErrors('editErrors', json);
                 return;
             }
            
            // Close modal
            document.querySelector('#editUserModal .btn-close').click();
            loadUsers(currentPage);
        })
        .catch(() => showError('editErrors','Failed to update user'));
    }

    /* ===========================
    DELETE USER
    =========================== */
    function deleteUser(id){
        if(!confirm('Are you sure you want to delete this user?')) return;
        
        fetch(`/admin/users/${id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json' }
        })
        .then(res => {
            if(res.status === 204 || res.ok) {
                // Remove row instantly or reload
                const row = qs(`row-${id}`);
                if(row) row.remove();
                else loadUsers(currentPage); 
                return;
            }
            alert('Failed to delete user.');
        })
        .catch(() => alert('Failed to delete user.'));
    }
    
    // Helper untuk escape HTML (untuk keamanan)
    function escapeHtml(unsafe){
        if(unsafe === null || unsafe === undefined) return '';
        return unsafe.toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    
    // Helper untuk menampilkan error (sama seperti furniture)
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

    // Call loadUsers on page load
    loadUsers();
</script>

</body>
</html>