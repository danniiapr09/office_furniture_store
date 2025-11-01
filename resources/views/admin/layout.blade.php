<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Office Furniture Store</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="/admin">Admin Panel</a>
    <div>
      <a href="/admin/categories" class="btn btn-outline-light btn-sm">Categories</a>
      <a href="/admin/furniture" class="btn btn-outline-light btn-sm">Furniture</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
  @yield('content')
</div>

</body>
</html>
