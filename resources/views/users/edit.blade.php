@extends('layouts.app')
@section('title', 'Edit User: ' . $user->getFullName())

@section('content')
@php
    $departmentName = collect($departments)->firstWhere('id', $user->department_id)?->name;
    $roleLabels = \App\Models\User::getRoleLabels();
@endphp

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h4 class="mb-0">Edit user</h4>
                <p class="text-muted small mb-0 mt-1">Update profile details, organizational access, and account status.</p>
            </div>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.show', $user) }}">{{ $user->getFullName() }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </div>
    </div>
</div>

@if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger" role="alert">
        <div class="fw-semibold mb-1">Please review the highlighted fields.</div>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="user-profile-grid">
    <x-admin.user-identity :user="$user" :department-name="$departmentName">
        <p class="text-muted small mb-0">
            <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
            Changes take effect after the profile is saved.
        </p>
    </x-admin.user-identity>

    <form method="POST" action="{{ route('users.update', $user) }}" class="card user-settings-card h-100">
        @method('PUT')
        @csrf

        <header class="user-settings-card__header">
            <div>
                <h2 class="user-settings-card__title"><i class="bi bi-person-gear me-2" aria-hidden="true"></i>Profile settings</h2>
                <p class="user-settings-card__description">Fields marked with an asterisk are required.</p>
            </div>
            <span class="user-profile-badge {{ $user->is_active ? 'user-profile-badge--active' : 'user-profile-badge--pending' }}">
                {{ $user->is_active ? 'Active account' : 'Pending account' }}
            </span>
        </header>

        <div class="user-form-section">
            <div class="user-form-section__intro">
                <h3>Personal information</h3>
                <p>The user’s name and basic personal details.</p>
            </div>
            <div class="user-form-fields">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-4">
                        <label for="fname" class="form-label">First name <span class="text-danger">*</span></label>
                        <input type="text" name="fname" id="fname" value="{{ old('fname', $user->fname) }}"
                               class="form-control form-control-sm @error('fname') is-invalid @enderror" required autocomplete="given-name">
                        @error('fname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <label for="mname" class="form-label">Middle name</label>
                        <input type="text" name="mname" id="mname" value="{{ old('mname', $user->mname) }}"
                               class="form-control form-control-sm @error('mname') is-invalid @enderror" autocomplete="additional-name">
                        @error('mname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <label for="lname" class="form-label">Last name <span class="text-danger">*</span></label>
                        <input type="text" name="lname" id="lname" value="{{ old('lname', $user->lname) }}"
                               class="form-control form-control-sm @error('lname') is-invalid @enderror" required autocomplete="family-name">
                        @error('lname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="dob" class="form-label">Date of birth</label>
                        <input type="date" name="dob" id="dob" value="{{ old('dob', $user->dob?->format('Y-m-d')) }}"
                               class="form-control form-control-sm @error('dob') is-invalid @enderror">
                        @error('dob')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="user-form-section">
            <div class="user-form-section__intro">
                <h3>Account and access</h3>
                <p>Contact details and the access level assigned to this user.</p>
            </div>
            <div class="user-form-fields">
                <div class="row g-3">
                    <div class="col-12">
                        <label for="email" class="form-label">Email address <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                               class="form-control form-control-sm @error('email') is-invalid @enderror" required autocomplete="email">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-select form-select-sm @error('role') is-invalid @enderror" required>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ old('role', $user->role) === $role ? 'selected' : '' }}>
                                    {{ $roleLabels[$role] ?? ucfirst(str_replace(['-', '_'], ' ', $role)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="department_id" class="form-label">Department</label>
                        <select name="department_id" id="department_id" class="form-select form-select-sm @error('department_id') is-invalid @enderror">
                            <option value="">No department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="user-form-section">
            <div class="user-form-section__intro">
                <h3>Account status</h3>
                <p>Inactive users cannot sign in or access the administration area.</p>
            </div>
            <div class="user-form-fields">
                <div class="user-account-toggle">
                    <div>
                        <strong>Active user</strong>
                        <span>Allow this user to sign in and use their assigned permissions.</span>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                               class="form-check-input @error('is_active') is-invalid @enderror"
                               role="switch" aria-label="Active user">
                    </div>
                </div>
                @error('is_active')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>

        <footer class="user-settings-card__footer">
            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-secondary">
                Cancel
            </a>
            <button type="submit" class="btn btn-sm btn-primary px-3">
                <i class="bi bi-check-lg me-1" aria-hidden="true"></i>Save changes
            </button>
        </footer>
    </form>
</div>
@endsection
