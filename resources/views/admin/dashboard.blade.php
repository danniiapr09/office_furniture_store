@extends('admin.layout')

@section('content')
  <h1 class="mb-4">Dashboard</h1>
  <div class="row">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h4>Total Categories</h4>
          <h2>{{ $categoryCount }}</h2>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h4>Total Furniture</h4>
          <h2>{{ $furnitureCount }}</h2>
        </div>
      </div>
    </div>
  </div>
@endsection
