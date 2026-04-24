@extends('layouts.app')

@section('title', 'Edit HOD Officer')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('hods.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h4 class="mb-0 fw-bold" style="color:#065321;">Edit Officer — {{ $hod->name }}</h4>
                    <p class="text-muted mb-0 small">{{ $hod->department }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('hods.update', $hod) }}">
                        @csrf @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $hod->name) }}">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Department / Role <span class="text-danger">*</span></label>
                            <input type="text" name="department" class="form-control @error('department') is-invalid @enderror"
                                   value="{{ old('department', $hod->department) }}">
                            @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $hod->email) }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone', $hod->phone) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $hod->notes) }}</textarea>
                        </div>

                        <div class="mb-4 form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                   {{ old('is_active', $hod->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">Active (available for escalation)</label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn text-white fw-semibold px-4" style="background:#065321;">
                                <i class="bi bi-check2 me-1"></i> Update Officer
                            </button>
                            <a href="{{ route('hods.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
