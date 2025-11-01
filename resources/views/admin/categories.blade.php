@extends('admin.layout')

@section('content')
  <h1 class="mb-4">Categories</h1>

  <table class="table table-striped">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Name</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($categories as $cat)
        <tr>
          <td>{{ $cat->id }}</td>
          <td>{{ $cat->name }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endsection
