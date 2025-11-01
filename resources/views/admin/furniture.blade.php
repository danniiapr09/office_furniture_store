@extends('admin.layout')

@section('content')
  <h1 class="mb-4">Furniture</h1>

  <table class="table table-striped">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Category</th>
        <th>Price</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($furnitures as $f)
        <tr>
          <td>{{ $f->id }}</td>
          <td>{{ $f->name }}</td>
          <td>{{ $f->category->name ?? '-' }}</td>
          <td>Rp{{ number_format($f->price, 0, ',', '.') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endsection
