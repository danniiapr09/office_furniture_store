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
            --primary-color: #FAA33C; 
            --primary-hover: #E38D2F; 
            --start-gradient: #FFF3E0; /* Krem sangat muda/Oranye sangat pucat */
            --end-gradient: #FFFFFF;
            --shadow-color: rgba(0, 0, 0, 0.15);
        }
        
        body {
            /* Latar Belakang Gradien */
            background: linear-gradient(135deg, var(--start-gradient) 0%, var(--end-gradient) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: background 0.5s ease;
        }
        
        /* Kotak Login (Card) */
        .login-box {
            max-width: 400px;
            padding: 40px; /* Padding lebih besar */
            background: var(--card-color);
            border-radius: 18px; /* Lebih bulat lagi */
            box-shadow: 0 15px 40px var(--shadow-color); /* Bayangan yang lebih dalam dan halus */
            /* Border Top diganti dengan elemen Header di dalam Box */
        }

        /* Header / Judul */
        .login-box h3 {
            font-weight: 700;
            color: #212529; /* Teks lebih kontras */
            margin-bottom: 5px !important;
        }
        .login-box .subtitle {
            color: #6c757d;
            margin-bottom: 30px;
            display: block;
            font-size: 1rem;
        }
        .login-box h3 svg {
            margin-right: 8px;
            color: var(--primary-color);
        }

        /* Form Control Styling */
        .form-label {
            font-weight: 600; /* Label lebih tebal */
            color: #495057;
            margin-bottom: 5px;
        }
        .form-control {
            border-radius: 10px; /* Lebih bulat */
            padding: 14px 15px;
            border: 1px solid #ced4da;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.3rem rgba(250, 163, 60, 0.35); /* Shadow fokus lebih kuat */
        }
        
        /* Tombol Login (Orange) */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 700;
            padding: 12px 15px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(250, 163, 60, 0.4); /* Shadow khusus untuk tombol */
            transition: background-color 0.3s, box-shadow 0.3s, transform 0.1s;
        }
        .btn-primary:hover {
            background-color: var(--primary-hover); 
            border-color: var(--primary-hover);
            transform: translateY(-2px); /* Efek lift lebih terasa */
            box-shadow: 0 6px 12px rgba(250, 163, 60, 0.5);
        }
    </style>
</head>
<body>

<div class="login-box">
    {{-- Header dengan Icon --}}
    <div class="text-center">
        <h3>
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
              <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
              <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
            </svg>
            Admin Panel
        </h3>
        <span class="subtitle">Akses Khusus Pengelola Data</span>
    </div>


    {{-- Pesan Error Laravel Blade --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-4 rounded-3" role="alert">
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