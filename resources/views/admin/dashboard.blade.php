<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            flex-shrink: 0; /* Pastikan sidebar tidak mengecil */
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
            background-color: rgba(250, 163, 60, 0.1); /* Background transparan oranye */
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
        
        /* Statistik Card Styling */
        .stat-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s;
            cursor: pointer;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card .card-body {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .stat-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            padding: 10px;
            background-color: rgba(250, 163, 60, 0.1); /* Background oranye lembut */
            border-radius: 8px;
        }
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
        }
        .stat-title {
            color: #6c757d;
            font-weight: 500;
            margin-bottom: 5px;
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
                <a class="nav-link active" href="/admin/dashboard">
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
                Welcome back, 
                <span class="text-primary">{{ auth('admin')->user()->name }}</span> 👋
            </div>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/admin/dashboard" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
              </ol>
            </nav>
        </header>

        <div id="main-content">
            <h1 class="mb-4">Dashboard Overview</h1>
            <p class="text-muted fs-5">Ringkasan cepat metrik utama furniture Anda.</p>

            <div class="row mt-4">
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div>
                                <div class="stat-title">Total Furniture</div>
                                <div class="stat-value">125</div>
                            </div>
                            <i class="bi bi-boxes stat-icon"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div>
                                <div class="stat-title">Total Categories</div>
                                <div class="stat-value">12</div>
                            </div>
                            <i class="bi bi-tags-fill stat-icon"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div>
                                <div class="stat-title">Total Users</div>
                                <div class="stat-value">8,500</div>
                            </div>
                            <i class="bi bi-people-fill stat-icon"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div>
                                <div class="stat-title">Orders Pending</div>
                                <div class="stat-value text-danger">45</div>
                            </div>
                            <i class="bi bi-exclamation-triangle-fill stat-icon" style="color: #dc3545; background-color: rgba(220, 53, 69, 0.1);"></i>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <h2 class="mt-4 mb-3">Quick Navigation</h2>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <a href="/admin/furniture" class="text-decoration-none">
                        <div class="card h-100 shadow-sm border-start border-5" style="border-color: var(--primary-color) !important;">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-box-seam-fill me-3 fs-3" style="color: var(--primary-color);"></i>
                                    <div>
                                        <h5 class="card-title mb-1">Manage Furniture</h5>
                                        <p class="text-muted mb-0">Tambah, Edit, Hapus data furniture dan gambar.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                
                <div class="col-md-6 mb-4">
                    <a href="/admin/categories" class="text-decoration-none">
                        <div class="card h-100 shadow-sm border-start border-5" style="border-color: var(--primary-color) !important;">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-tags-fill me-3 fs-3" style="color: var(--primary-color);"></i>
                                    <div>
                                        <h5 class="card-title mb-1">Manage Categories</h5>
                                        <p class="text-muted mb-0">Atur dan kelompokkan jenis-jenis furniture.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            
        </div>
        
    </div>
</div>

</body>
</html>