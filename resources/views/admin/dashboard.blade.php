@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4 fw-bold">Admin Dashboard</h3>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5>Total Users</h5>
                    <p class="fs-4 fw-bold">—</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5>Total Errands</h5>
                    <p class="fs-4 fw-bold">—</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5>Active Runners</h5>
                    <p class="fs-4 fw-bold">—</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
