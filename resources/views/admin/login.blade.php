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
            --primary-color: #007bff; /* Biru Primer */
            --background-color: #e9ecef; /* Abu-abu muda yang lembut */
            --card-color: #ffffff; /* Putih untuk kotak login */
            --shadow-color: rgba(0, 0, 0, 0.15);
        }
        body {
            background-color: var(--background-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Kotak Login Baru */
        .login-box {
            max-width: 400px;
            padding: 35px;
            background: var(--card-color);
            border-radius: 12px;
            box-shadow: 0 8px 25px var(--shadow-color); /* Bayangan lebih tebal dan dalam */
        }

        /* Header / Judul */
        .login-box h3 {
            font-weight: 700;
            color: #343a40; /* Teks lebih gelap */
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            margin-bottom: 30px !important;
        }

        /* Form Control Styling */
        .form-control {
            border-radius: 8px;
            padding: 10px 15px;
            border-color: #ced4da;
        }
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }
        
        /* Tombol */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 600;
            padding: 10px;
            border-radius: 8px;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3; /* Warna hover sedikit lebih gelap */
            border-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h3 class="text-center">Admin Panel Login</h3>

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

        <button type="submit" class="btn btn-primary w-100">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-right me-2" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z"/>
              <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
            </svg>
            MASUK KE DASHBOARD
        </button>
    </form>
</div>

</body>
</html>