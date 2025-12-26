@extends('layouts.app', ['title' => 'TES Grantees - Lookup'])

@section('content')

{{-- Success Message --}}
@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

{{-- Error Message --}}
@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

{{-- SEARCH --}}
<div class="card mb-3">
  <div class="card-body">
    <div class="row align-items-center">
      <div class="col-md-6">
        <label class="form-label fw-semibold">Search</label>
        <input id="searchInput" type="text" class="form-control"
               placeholder="Type lastname or firstname (live search)">
      </div>
      <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <span class="text-muted small">Showing up to 50 results</span>
        <span class="badge bg-secondary ms-2" id="resultCount">{{ $employees->count() }}</span>
      </div>
    </div>
  </div>
</div>

{{-- TABLE --}}
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Payroll #</th>
            <th>Last Name</th>
            <th>First Name</th>
            <th>MI</th>
            <th>Grant Type / Batch</th>
            <th>Validation Status</th>
          </tr>
        </thead>

        <tbody id="tableBody">
        @forelse($employees as $e)
          <tr>
            <td class="text-muted">#{{ $e->id }}</td>
            <td class="fw-semibold">{{ $e->payroll_number }}</td>
            <td>{{ $e->lastname }}</td>
            <td>{{ $e->firstname }}</td>
            <td class="text-uppercase">{{ $e->middle_initial }}</td>
            <td>{{ $e->grant_type_batch }}</td>

            <td style="min-width:200px;">
              <form method="POST"
                    action="{{ route('employees.updateValidation', $e->id) }}"
                    class="validation-form">
                @csrf
                <input type="hidden" name="validation_status"
                       value="{{ $e->validation_status ?? 'Not Validated' }}">

                <select class="form-select form-select-sm validation-select"
                        data-current="{{ $e->validation_status ?? 'Not Validated' }}">
                  <option value="Not Validated"
                    {{ ($e->validation_status ?? 'Not Validated') === 'Not Validated' ? 'selected' : '' }}>
                    Not Validated
                  </option>
                  <option value="Validate"
                    {{ ($e->validation_status ?? 'Not Validated') === 'Validate' ? 'selected' : '' }}>
                    Validate
                  </option>
                </select>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-muted py-4">
              No records available
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="border-top px-3 py-2 text-muted small" id="statusText">
      Tip: Start typing to filter the table automatically.
    </div>
  </div>
</div>

{{-- LIVE SEARCH + VALIDATION SCRIPT --}}
<script>
const searchInput = document.getElementById('searchInput');
const tableBody   = document.getElementById('tableBody');
const resultCount = document.getElementById('resultCount');
const statusText  = document.getElementById('statusText');
let timer = null;

function escapeHtml(v) {
  return String(v ?? '')
    .replaceAll('&','&amp;')
    .replaceAll('<','&lt;')
    .replaceAll('>','&gt;')
    .replaceAll('"','&quot;')
    .replaceAll("'","&#039;");
}

// LIVE SEARCH
async function fetchResults(q) {
  statusText.textContent = 'Searching...';

  const url = new URL("{{ route('employees.liveSearch') }}", window.location.origin);
  url.searchParams.set('q', q);

  const res = await fetch(url.toString());
  const json = await res.json();
  const rows = json.data || [];

  resultCount.textContent = json.count ?? rows.length;

  if (rows.length === 0) {
    tableBody.innerHTML = `
      <tr>
        <td colspan="7" class="text-center text-muted py-4">
          No Record Found
        </td>
      </tr>`;
    return;
  }

  tableBody.innerHTML = rows.map(r => `
    <tr>
      <td class="text-muted">#${escapeHtml(r.id)}</td>
      <td class="fw-semibold">${escapeHtml(r.payroll_number)}</td>
      <td>${escapeHtml(r.lastname)}</td>
      <td>${escapeHtml(r.firstname)}</td>
      <td class="text-uppercase">${escapeHtml(r.middle_initial)}</td>
      <td>${escapeHtml(r.grant_type_batch)}</td>

      <td style="min-width:200px;">
        <form method="POST" action="/employees/${escapeHtml(r.id)}/validation">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <input type="hidden" name="validation_status" value="${escapeHtml(r.validation_status ?? 'Not Validated')}">

          <select class="form-select form-select-sm validation-select"
                  data-current="${escapeHtml(r.validation_status ?? 'Not Validated')}">
            <option value="Not Validated"
              ${(r.validation_status ?? 'Not Validated') === 'Not Validated' ? 'selected' : ''}>
              Not Validated
            </option>
            <option value="Validate"
              ${(r.validation_status ?? 'Not Validated') === 'Validate' ? 'selected' : ''}>
              Validate
            </option>
          </select>
        </form>
      </td>
    </tr>
  `).join('');
}

// debounce
searchInput.addEventListener('input', () => {
  clearTimeout(timer);
  timer = setTimeout(() => fetchResults(searchInput.value.trim()), 300);
});

// VALIDATION CONFIRM
document.addEventListener('change', function (e) {
  if (!e.target.classList.contains('validation-select')) return;

  const select = e.target;
  const form = select.closest('form');
  const hidden = form.querySelector('input[name="validation_status"]');

  const oldVal = select.dataset.current;
  const newVal = select.value;

  if (newVal === 'Validate') {
    if (!confirm('Validate this record?')) {
      select.value = oldVal;
      return;
    }
  }

  hidden.value = newVal;
  select.dataset.current = newVal;
  form.submit();
});
</script>

@endsection
