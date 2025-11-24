<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Furniture Management - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f8f9fa; }
    .container { max-width:1100px; }
    .modal-lg { max-width: 760px; }
  </style>
</head>
<body>
<div class="container mt-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3>Furniture Management</h3>
      <small class="text-muted">Realtime CRUD — Admin Panel</small>
    </div>
    <div>
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addFurnitureModal">+ Add Furniture</button>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body p-2">
      <div class="row g-2">
        <div class="col-md-6">
          <input id="searchInput" oninput="debouncedLoad()" class="form-control" placeholder="Search by name..." />
        </div>
        <div class="col-md-6 text-end">
          <button class="btn btn-outline-secondary" onclick="loadFurniture()">Reload</button>
        </div>
      </div>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
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

  <!-- basic pagination placeholder -->
  <nav>
    <ul id="pagination" class="pagination justify-content-center"></ul>
  </nav>

</div>

<!-- ===========================
   ADD FURNITURE MODAL
=========================== -->
<div class="modal fade" id="addFurnitureModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Furniture</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="addFurnitureForm">
          <div id="addErrors" class="text-danger small mb-2"></div>

          <div class="row">
            <div class="col-md-8 mb-3">
              <label>Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
              <label>Category</label>
              <select name="category_id" class="form-select" id="addCategorySelect" required>
                <option>Loading...</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label>Price</label>
            <input type="number" name="price" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
          </div>

          <div class="mb-3">
            <label>Image</label>
            <input type="file" name="image" class="form-control">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" onclick="createFurniture()">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- ===========================
   EDIT FURNITURE MODAL
=========================== -->
<div class="modal fade" id="editFurnitureModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Furniture</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editFurnitureForm">
          <input type="hidden" name="id" id="edit_id">

          <div id="editErrors" class="text-danger small mb-2"></div>

          <div class="row">
            <div class="col-md-8 mb-3">
              <label>Name</label>
              <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
              <label>Category</label>
              <select name="category_id" class="form-select" id="editCategorySelect" required>
                <option>Loading...</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label>Price</label>
            <input type="number" name="price" id="edit_price" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Description</label>
            <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
          </div>

          <div class="mb-3">
            <label>Replace Image (optional)</label>
            <input type="file" name="image" id="edit_image" class="form-control">
          </div>

          <div id="currentImagePreview" class="mb-3"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" onclick="updateFurniture()">Update</button>
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
let perPage = 10;
let searchTimeout = null;

function qs(id){ return document.getElementById(id); }
function showError(containerId, message){ qs(containerId).innerHTML = message; }

/* ===========================
   LOAD FURNITURE (with search & pagination)
   GET /api/furniture?page=...&q=...
=========================== */
function loadFurniture(page = 1) {
  currentPage = page;
  const q = encodeURIComponent(document.getElementById('searchInput').value || '');
  fetch(`/api/furniture?page=${page}&q=${q}`)
    .then(async res => {
      if(!res.ok) throw new Error('Failed to load');
      const payload = await res.json();

      // Accept either paginated structure or simple array
      let items = payload.data ?? payload;
      // when paginated
      if(payload.meta){
        currentPage = payload.meta.current_page;
        lastPage = payload.meta.last_page;
      } else if(payload.last_page){
        currentPage = payload.current_page;
        lastPage = payload.last_page;
      } else {
        // fallback
        currentPage = page;
        lastPage = 1;
      }

      let html = '';
      if(!items || items.length === 0){
        html = `<tr><td colspan="5" class="text-center">No data</td></tr>`;
      } else {
        items.forEach(item => {
          html += `
            <tr id="row-${item.id}">
              <td>${item.id}</td>
              <td>${escapeHtml(item.name)}</td>
              <td>${escapeHtml(item.category?.name ?? '-')}</td>
              <td>Rp ${numberWithCommas(item.price)}</td>
              <td>
                <button class="btn btn-sm btn-warning me-1" onclick="openEditModal(${item.id})">Edit</button>
                <button class="btn btn-sm btn-danger" onclick="deleteFurniture(${item.id})">Delete</button>
              </td>
            </tr>
          `;
        });
      }
      qs('furniture-table').innerHTML = html;
      renderPagination();
    })
    .catch(err=>{
      qs('furniture-table').innerHTML = `<tr><td colspan="5" class="text-danger text-center">Error loading data</td></tr>`;
    });
}
loadFurniture();

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

  // show 1..n (simple)
  for(let p=1; p<= lastPage; p++){
    if(p>10) break; // limit
    ul.innerHTML += makeItem(p, p, p===currentPage);
  }

  // next
  if(currentPage < lastPage) ul.innerHTML += makeItem(currentPage+1, 'Next »');
}

/* ===========================
   Load categories (for both add & edit)
=========================== */
function loadCategoriesFor(selectId, selected = null){
  fetch('/api/categories')
    .then(res=>res.json())
    .then(data=>{
      let html = '<option value="">-- Select Category --</option>';
      data.forEach(cat=>{
        html += `<option value="${cat.id}" ${selected && selected==cat.id ? 'selected':''}>${escapeHtml(cat.name)}</option>`;
      });
      qs(selectId).innerHTML = html;
    })
    .catch(()=> qs(selectId).innerHTML = '<option value="">Error loading</option>');
}
// initial load for add form
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
    loadFurniture(currentPage);
  })
  .catch(()=> showError('addErrors','Failed to create item'));
}

/* ===========================
   OPEN EDIT MODAL (populate)
   GET /api/furniture/{id}
=========================== */
function openEditModal(id){
  showError('editErrors','');
  // populate categories first
  loadCategoriesFor('editCategorySelect');

  fetch(`/api/furniture/${id}`)
    .then(async res=>{
      if(!res.ok) throw new Error('not found');
      const item = await res.json();

      qs('edit_id').value = item.id;
      qs('edit_name').value = item.name ?? '';
      qs('edit_price').value = item.price ?? '';
      qs('edit_description').value = item.description ?? '';
      // category select will be loaded async; set selected slightly later
      setTimeout(()=> {
        qs('editCategorySelect').value = item.category_id ?? '';
      }, 250);

      // show current image preview if exists
      if(item.image_url){
        qs('currentImagePreview').innerHTML = `<div>Current image:</div><img src="${item.image_url}" style="max-width:180px;border-radius:6px;">`;
      } else {
        qs('currentImagePreview').innerHTML = '';
      }

      // open modal
      const modal = new bootstrap.Modal(document.getElementById('editFurnitureModal'));
      modal.show();
    })
    .catch(()=> alert('Failed to load item data'));
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
  if(!confirm('Delete this item?')) return;
  fetch(`/api/furniture/${id}`, {
    method: 'DELETE',
    headers: { 'Accept': 'application/json' }
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
</script>

</body>
</html>