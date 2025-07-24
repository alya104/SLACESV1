@extends('layouts.guest')

@section('content')
    @include('guest.partials.header')
    <section class="content-section">
        <h2>Join a Class</h2>
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('guest.join-class.submit') }}" style="max-width:400px;">
            @csrf
            <div class="form-group" style="margin-bottom:18px;">
                <label>Email</label>
                <input type="email" value="{{ $user->email }}" class="form-control" disabled>
            </div>
            <div class="form-group" style="margin-bottom:18px;">
                <label>Invoice Number</label>
                <input type="text" name="invoice_number" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Join Class</button>
        </form>
    </section>
@endsection