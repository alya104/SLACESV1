@extends('layouts.student')

@section('content')
<div class="join-class-wrapper">
    <div class="join-class-card">
        <h2 class="join-class-title">Join a Class</h2>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif
        <form action="{{ route('student.join-class.submit') }}" method="POST" class="join-class-form">
            @csrf
            <div class="form-group">
                <label for="invoice_number">Invoice Number</label>
                <input type="text" name="invoice_number" id="invoice_number" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required value="{{ auth()->user()->email }}">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Join Class</button>
        </form>
    </div>
</div>
@endsection