@extends('layouts.master')

@section('content')
<div class="container-fluid" style="padding-top: 100px;">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark">Register New Logger</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('activations.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Logger ID *</label>
                                <input type="text" name="logger_id" class="form-control" placeholder="e.g. LOG-JKT-01" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Logger Name *</label>
                                <input type="text" name="logger_name" class="form-control" placeholder="e.g. Main Stack Logger" required>
                            </div>

                            <div class="col-12">
                                <hr class="my-2">
                                <div class="alert alert-light-info text-info border-info d-flex align-items-center">
                                    <i class="ti ti-info-circle me-2"></i>
                                    <small>Token & Activation Code will be <b>auto-generated</b> if left blank.</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Token (Optional)</label>
                                <input type="text" name="token" class="form-control bg-light" placeholder="Auto Generate">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Activation Code (Optional)</label>
                                <input type="text" name="activation_code" class="form-control bg-light" placeholder="Auto Generate">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-muted">Status</label>
                                <select name="status" class="form-select">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('activations.index') }}" class="btn btn-light rounded-pill px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: #009688; border:none;">Save Logger</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
