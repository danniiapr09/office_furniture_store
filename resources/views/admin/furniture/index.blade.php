<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Furniture Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

    <h2>Furniture Management</h2>
    <p class="text-muted">Realtime Admin Panel</p>

    <button class="btn btn-primary mb-3" onclick="loadFurniture()">Reload Data</button>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody id="furniture-table">
            <tr><td colspan="4" class="text-center">Loading...</td></tr>
        </tbody>
    </table>

</div>

<script>
function loadFurniture() {

    fetch('/api/furniture')
        .then(res => res.json())
        .then(data => {
            let html = '';
            data.forEach(item => {
                html += `
                    <tr>
                        <td>${item.id}</td>
                        <td>${item.name}</td>
                        <td>${item.category?.name ?? '-'}</td>
                        <td>${item.price}</td>
                    </tr>
                `;
            });
            document.getElementById('furniture-table').innerHTML = html;
        })
        .catch(err => {
            document.getElementById('furniture-table').innerHTML = 
                `<tr><td colspan="4" class="text-danger">Error loading data</td></tr>`;
        });
}

loadFurniture();
</script>

</body>
</html>
