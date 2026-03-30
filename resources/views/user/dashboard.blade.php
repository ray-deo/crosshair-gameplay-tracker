@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4 fw-bold">User Dashboard</h3>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <p class="mb-3">
                Welcome, <strong>{{ Auth::user()->name }}</strong>
            </p>

            <a href="#" class="btn btn-primary">
                Create New Errand
            </a>
        </div>
    </div>
</div>
@endsection
