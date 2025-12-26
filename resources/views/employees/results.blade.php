@extends('layouts.app', ['title' => 'Search Results'])

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h5 class="fw-semibold mb-1">Search Results</h5>
    <div class="muted">Query: <span class="fw-semibold">{{ $q }}</span></div>
  </div>

  <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-secondary">
    ← Back
  </a>
</div>

<div class="card">
  <div class="card-body p-0">
    @if($message)
      <div class="p-4">
        <div class="alert alert-warning mb-0 d-flex align-items-center" role="alert">
          <div class="me-2">⚠️</div>
          <div class="fw-semibold">{{ $message }}</div>
        </div>

        <div class="mt-3 muted small">
          Try a different spelling or search using the first name instead.
        </div>
      </div>
    @else
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th class="ps-4">Payroll Number</th>
              <th>Last Name</th>
              <th>First Name</th>
              <th>Middle Initial</th>
            </tr>
          </thead>
          <tbody>
            @foreach($employees as $e)
              <tr>
                <td class="ps-4 fw-semibold">{{ $e->payroll_number }}</td>
                <td>{{ $e->lastname }}</td>
                <td>{{ $e->firstname }}</td>
                <td class="text-uppercase">{{ $e->middle_initial }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="px-4 py-3 border-top muted small">
        Showing <span class="fw-semibold">{{ $employees->count() }}</span> result(s). (Max 50)
      </div>
    @endif
  </div>
</div>
@endsection
