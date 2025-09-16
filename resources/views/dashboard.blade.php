@extends('layouts.main')

@section('content')
   <div class="content-bg">
            <h1 class="heading">Welcome to your dashboard</h1>

        <div class="card">
            <h3 class="heading">Account</h3>
            <p class="muted">Email: {{ $user->email_address ?? '—' }}</p>
            <p class="muted">Status: {{ $user->status ?? '—' }}</p>
            <p class="muted">Role: {{ $user->role ?? '—' }}</p>
        </div>

        <div class="card">
            <h3 class="heading">Supplier Profile</h3>
            <p class="muted">Company: {{ $supplier->company_name ?? '—' }}</p>
            <p class="muted">Verified at: {{ $supplier && $supplier->email_verified_at ? $supplier->email_verified_at : 'Not verified' }}</p>
        </div>

        <div class="card">
            <h3 class="heading">Documents</h3>
            <p class="muted">Uploaded documents: {{ $documentCount }}</p>
        </div>
   </div>
@endsection
