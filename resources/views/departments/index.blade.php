@extends('layouts.app')

@section('title', 'Departments')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Departments</h4>
            <p class="text-muted small mb-0">Manage department names used across the Quality Assurance Assessment form.</p>
        </div>
        <form method="POST" action="{{ route('departments.store') }}" class="d-flex gap-2 align-items-center">
            @csrf
            <input type="text" name="name" value="{{ old('name') }}"
                   class="form-control form-control-sm @error('name') is-invalid @enderror"
                   placeholder="New department name" required>
            <input type="hidden" name="is_active" value="1">
            <button type="submit" class="btn btn-success btn-sm text-nowrap">
                <i class="bi bi-plus-lg me-1"></i>Add
            </button>
            <a href="{{ route('departments.create') }}" class="btn btn-outline-secondary btn-sm text-nowrap">Advanced</a>
        </form>
    </div>

    @error('name')
        <div class="text-danger small mb-3">{{ $message }}</div>
    @enderror

    @if(session('toast'))
        <div class="alert alert-{{ session('toast_type') === 'success' ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
            {{ session('toast') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Department Name</th>
                        <th>Applies To</th>
                        <th>HOD / Incharge</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $dept)
                    <tr>
                        <td class="ps-3" style="min-width:280px;">
                            <form method="POST" action="{{ route('departments.update', $dept) }}" class="d-flex align-items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $dept->name }}" class="form-control form-control-sm" required>

                                @foreach($dept->categories ?? [] as $cat)
                                    <input type="hidden" name="categories[]" value="{{ $cat }}">
                                @endforeach
                                <input type="hidden" name="hod_id" value="{{ $dept->hod_id }}">
                                <input type="hidden" name="description" value="{{ $dept->description }}">
                                <input type="hidden" name="is_active" value="{{ $dept->is_active ? 1 : 0 }}">

                                <button type="submit" class="btn btn-sm btn-outline-success" title="Save Name">
                                    <i class="bi bi-check2"></i>
                                </button>
                            </form>
                        </td>
                        <td>
                            @forelse($dept->categories ?? [] as $cat)
                                <span class="badge bg-primary-subtle text-primary me-1">
                                    {{ \App\Models\Department::CATEGORIES[$cat] ?? ucfirst($cat) }}
                                </span>
                            @empty
                                <span class="text-muted small">Organisation-wide</span>
                            @endforelse
                        </td>
                        <td class="small">{{ $dept->hod?->name ?? '—' }}</td>
                        <td class="text-muted small">{{ $dept->description ?: '—' }}</td>
                        <td>
                            @if($dept->is_active)
                                <span class="badge bg-success-subtle text-success">Active</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('departments.edit', $dept) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('departments.destroy', $dept) }}" class="d-inline"
                                  onsubmit="return confirm('Delete this department? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-buildings d-block fs-3 mb-2 opacity-25"></i>
                            No departments yet. <a href="{{ route('departments.create') }}">Add the first one.</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($departments->hasPages())
        <div class="card-footer">
            {{ $departments->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
