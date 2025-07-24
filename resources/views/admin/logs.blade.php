{{-- filepath: c:\xampp\htdocs\CMSV1\resources\views\admin\logs.blade.php --}}
@extends('layouts.admin')

@section('content')
<section class="content-section">
    <h2 class="section-title">System Logs</h2>
    <form method="GET" action="{{ route('admin.logs') }}" class="search-filter mb-3">
        <div class="header-search">
            <i class="fas fa-search"></i>
            <input type="text" name="search" placeholder="Search logs..." value="{{ request('search') }}" class="form-control">
        </div>
        <select name="sort" class="form-control ml-2">
            <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Newest First</option>
            <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Oldest First</option>
        </select>
        <button type="submit" class="btn btn-primary ml-2">Filter</button>
    </form>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Admin</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td>{{ $log->admin->name ?? '-' }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->description }}</td>
                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($logs->hasPages())
            <div class="pagination-container">
                <div class="pagination-info">
                    Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} results
                </div>
                <div class="pagination-nav">
                    <button class="pagination-nav-btn" {{ $logs->onFirstPage() ? 'disabled' : '' }}
                        onclick="window.location='{{ $logs->previousPageUrl() }}'">
                        « Previous
                    </button>
                    <ul class="pagination">
                        @foreach ($logs->getUrlRange(1, $logs->lastPage()) as $page => $url)
                            @if ($page == $logs->currentPage())
                                <li class="active"><span>{{ $page }}</span></li>
                            @else
                                <li><a href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    </ul>
                    <button class="pagination-nav-btn" {{ !$logs->hasMorePages() ? 'disabled' : '' }}
                        onclick="window.location='{{ $logs->nextPageUrl() }}'">
                        Next »
                    </button>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
