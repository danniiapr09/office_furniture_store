<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Palet Warna & Background */
        :root {
            --primary-color: #FAA33C; /* Warna Orange Konsisten */
            --primary-hover: #E38D2F; /* Sedikit lebih gelap untuk hover */
            --background-color: #f7f7f7; /* Latar belakang sangat lembut */
            --card-color: #ffffff;
            --shadow-color: rgba(0, 0, 0, 0.1);
        }
        body {
            background-color: var(--background-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        
        /* Kotak Login */
        .login-box {
            max-width: 400px;
            padding: 35px;
            background: var(--card-color);
            border-radius: 15px; /* Lebih bulat */
            box-shadow: 0 10px 30px var(--shadow-color); /* Bayangan yang halus */
            border-top: 5px solid var(--primary-color); /* Garis atas oranye */
        }

        /* Header / Judul */
        .login-box h3 {
            font-weight: 800; /* Lebih tebal */
            color: #343a40; 
            margin-bottom: 30px !important;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box h3 svg {
            margin-right: 10px;
            color: var(--primary-color);
        }

        /* Form Control Styling */
        .form-label {
            font-weight: 500;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 15px; /* Padding lebih besar */
            border: 1px solid #dee2e6;
        }
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(250, 163, 60, 0.25); /* Shadow fokus oranye */
        }
        
        /* Tombol Login (Orange) */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 700;
            padding: 10px 15px;
            border-radius: 8px;
            transition: background-color 0.3s, transform 0.1s;
        }
        .btn-primary:hover {
            background-color: var(--primary-hover); 
            border-color: var(--primary-hover);
            transform: translateY(-1px); /* Efek slight lift pada hover */
        }
    </style>
</head>
<body>

<div class="login-box">
    {{-- Header dengan Icon --}}
    <h3 class="text-center">
        {{-- Icon untuk Admin/Dashboard --}}
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
          <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
          <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
        </svg>
        Admin Panel Login
    </h3>

    {{-- Pesan Error Laravel Blade --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-4 rounded-3">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" required autofocus>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        {{-- Tombol Login --}}
        <button type="submit" class="btn btn-primary w-100">
            LOGIN
        </button>
    </form>
</div>

</body>
</html>