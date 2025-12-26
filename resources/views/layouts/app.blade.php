<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Employee Records' }}</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body { background:#f4f6f9; }
    .navbar-brand { font-weight:600; }
    .card { border:0; border-radius:12px; box-shadow:0 6px 24px rgba(16,24,40,.08); }
    .table thead th { font-weight:600; color:#475467; }
  </style>
</head>
<body>

<nav class="navbar bg-white border-bottom">
  <div class="container-fluid px-4">
    <span class="navbar-brand">
      Tertiary Education Subsidy Grantees of 1st semester, AY2025-2026
    </span>

    <!-- Upload button (TOP RIGHT) -->
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
      Upload CSV
    </button>
  </div>
</nav>

<main class="container-fluid px-4 my-4">
  @yield('content')
</main>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="{{ route('employees.upload') }}" method="POST" enctype="multipart/form-data" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Upload Employee File</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label class="form-label">CSV File</label>
        <input type="file" name="file" class="form-control" required>
        <div class="form-text mt-2">
          Required columns: <b>payroll number, lastname, firstname, middle initial</b>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Upload</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
