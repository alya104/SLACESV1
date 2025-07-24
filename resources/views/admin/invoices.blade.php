@extends('layouts.admin')
@section('alerts')
@if(session('success'))
    <div class="alert alert-success" id="successAlert">
        <div class="alert-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="alert-message">
            {{ session('success') }}
        </div>
      
    </div>
@endif
@endsection
@section('content')
<section class="content-section">
    <div class="table-actions">
        <h2 class="section-title">Invoices</h2>
        <button class="btn btn-primary" onclick="openInvoiceModal()">
            <i class="fas fa-plus"></i> Add Invoice
        </button>
    </div>
    <div class="search-filter">
        <input type="text" id="invoiceSearch" placeholder="Search by student or invoice..." onkeyup="filterInvoices()">
        <select id="classFilter" onchange="filterInvoices()">
            <option value="">All Classes</option>
            @foreach($classes as $class)
                <option value="{{ $class->id }}">{{ $class->title }}</option>
            @endforeach
        </select>
        <select id="statusFilter" onchange="filterInvoices()">
            <option value="">All Status</option>
            <option value="unused">Unused</option>
            <option value="used">Used</option>
        </select>
    </div>
    <div class="table-container">
        <table class="table" id="invoicesTable">
            <thead>
                <tr>
                    <th>Invoice Number</th>
                    <th>Student</th>
                    <th>Class</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Invoice Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr data-status="{{ $invoice->status }}" data-class="{{ $invoice->class_id }}">
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->user->name ?? '-' }}<br><span class="text-muted">{{ $invoice->user->email ?? $invoice->email }}</span></td>
                    <td>{{ $invoice->class->title ?? '-' }}</td>
                    <td>RM{{ number_format($invoice->amount, 2) }}</td>
                    <td>
                        <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') }}</td>
                    <td>
                        <button class="action-btn" title="Edit" onclick="openEditInvoiceModal({{ $invoice->id }})"><i class="fas fa-edit"></i></button>
                        <button class="action-btn" title="Delete" onclick="openDeleteInvoiceModal({{ $invoice->id }}, '{{ $invoice->invoice_number }}')"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No invoices found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- Place this after your </table> and before @endsection --}}
    @if($invoices->hasPages())
    <div class="pagination-container">
        <div class="pagination-info">
            Showing {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} of {{ $invoices->total() }} results
        </div>
        <div class="pagination-nav">
            <button class="pagination-nav-btn" {{ $invoices->onFirstPage() ? 'disabled' : '' }}
                onclick="window.location='{{ $invoices->previousPageUrl() }}'">
                « Previous
            </button>
            <ul class="pagination">
                @foreach ($invoices->getUrlRange(1, $invoices->lastPage()) as $page => $url)
                    @if ($page == $invoices->currentPage())
                        <li class="active"><span>{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            </ul>
            <button class="pagination-nav-btn" {{ !$invoices->hasMorePages() ? 'disabled' : '' }}
                onclick="window.location='{{ $invoices->nextPageUrl() }}'">
                Next »
            </button>
        </div>
    </div>
    @endif
</section>

{{-- Extend modal partial for invoice modal --}}
@include('admin.partials.modals')

<!-- Invoice Modal (inside modals.blade.php or here if needed) -->
<div class="modal" id="invoiceModal">
    <div class="modal-content">
        <form id="invoiceForm" method="POST" action="{{ route('admin.invoices.store') }}">
            @csrf
            <input type="hidden" name="id" id="invoiceId">
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <h3 id="invoiceModalTitle">Add Invoice</h3>
            <div class="form-group">
                <label for="invoiceNumber">Invoice Number</label>
                <input type="text" name="invoice_number" id="invoiceNumber" class="form-control" required>
            </div>
            <div class="form-group">
    <label for="studentEmail">Student Email</label>
    <input type="email" name="student_email" id="studentEmail" class="form-control" required>
</div>
            <div class="form-group">
                <label for="invoiceClass">Class</label>
                <select name="class_id" id="invoiceClass" class="form-control" required>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="invoiceAmount">Amount</label>
                <input type="number" name="amount" id="invoiceAmount" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <label for="invoiceStatus">Status</label>
                <select name="status" class="form-control" required>
                    <option value="unused" {{ isset($invoice) && $invoice->status === 'unused' ? 'selected' : '' }}>Unused</option>
                    <option value="used" {{ isset($invoice) && $invoice->status === 'used' ? 'selected' : '' }}>Used</option>
                </select>
            </div>
            <div class="form-group">
                <label for="invoiceDate">Invoice Date</label>
                <input type="date" name="invoice_date" id="invoiceDate" class="form-control" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="document.getElementById('invoiceModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="btn">Save Invoice</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterInvoices() {
    const searchValue = document.getElementById('invoiceSearch').value.toLowerCase();
    const classFilter = document.getElementById('classFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('#invoicesTable tbody tr');
    rows.forEach(row => {
        const studentCell = row.cells[1].textContent.toLowerCase();
        const invoiceCell = row.cells[0].textContent.toLowerCase();
        const classMatch = classFilter === '' || row.getAttribute('data-class') === classFilter;
        const statusMatch = statusFilter === '' || row.getAttribute('data-status') === statusFilter;
        const textMatch = studentCell.includes(searchValue) || invoiceCell.includes(searchValue);
        row.style.display = (textMatch && classMatch && statusMatch) ? '' : 'none';
    });
}

function openInvoiceModal() {
    document.getElementById('invoiceForm').reset();
    document.getElementById('invoiceId').value = '';
    document.getElementById('invoiceModalTitle').textContent = 'Add Invoice';
    document.getElementById('invoiceForm').action = "{{ route('admin.invoices.store') }}";
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('invoiceModal').classList.add('active');
}

function openEditInvoiceModal(id) {
    // Fetch invoice data via AJAX and populate modal (implement in your controller as needed)
    fetch(`/admin/invoices/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) { alert('Error loading invoice'); return; }
            const invoice = data.invoice;
            document.getElementById('invoiceId').value = invoice.id;
            document.getElementById('invoiceNumber').value = invoice.invoice_number;
            document.getElementById('studentEmail').value = invoice.email;
            document.getElementById('invoiceClass').value = invoice.class_id;
            document.getElementById('invoiceAmount').value = invoice.amount;
            document.getElementById('invoiceStatus').value = invoice.status;
            document.getElementById('invoiceDate').value = invoice.invoice_date;
            document.getElementById('studentEmail').value = invoice.email;
            document.getElementById('invoiceModalTitle').textContent = 'Edit Invoice';
            document.getElementById('invoiceForm').action = "/admin/invoices/" + id;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('invoiceModal').classList.add('active');
        });
}

function openDeleteInvoiceModal(id, invoiceNumber) {
    document.querySelector('#deleteInvoiceModal p').textContent =
        `Are you sure you want to delete invoice "${invoiceNumber}"? This action cannot be undone.`;
    document.getElementById('deleteInvoiceForm').action = "/admin/invoices/" + id;
    document.getElementById('deleteInvoiceModal').classList.add('active');
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.modal .btn-outline').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.modal').classList.remove('active');
        });
    });
});
</script>
@endpush