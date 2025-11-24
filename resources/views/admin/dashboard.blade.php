<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand">Admin Panel</span>

        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button class="btn btn-outline-light btn-sm">Logout</button>
        </form>
    </div>
</nav>

<div class="container mt-4">
    <h3>Welcome, {{ auth('admin')->user()->name }} 👋</h3>
    <p class="text-muted">Kelola data furniture & kategori melalui menu berikut:</p>

    <div class="row mt-4">
        <div class="col-md-4">
            <a href="/admin/furniture" class="text-decoration-none">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5>Manage Furniture</h5>
                        <p class="text-muted mb-0">CRUD furniture dengan realtime.</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="/admin/categories" class="text-decoration-none">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5>Manage Categories</h5>
                        <p class="text-muted mb-0">Kelola kategori furniture.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

</body>
</html>