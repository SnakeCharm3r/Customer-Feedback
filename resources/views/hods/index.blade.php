@extends('layouts.app')

@section('title', 'HOD / Incharge Officers')

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-0 fw-bold" style="color:#065321;">HOD & Incharge Officers</h4>
            <p class="text-muted mb-0 small">Escalation matrix — line managers and department heads</p>
        </div>
        <a href="{{ route('hods.create') }}" class="btn btn-sm fw-semibold text-white" style="background:#065321;">
            <i class="bi bi-person-plus me-1"></i> Add Officer
        </a>
    </div>

    @if(session('toast'))
        <div class="alert alert-{{ session('toast_type') === 'success' ? 'success' : 'warning' }} alert-dismissible fade show" role="alert">
            {{ session('toast') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f6fbf4; color:#065321;">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th>Department</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Escalations</th>
                        <th class="text-end px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hods as $hod)
                    <tr>
                        <td class="px-4 fw-semibold">{{ $hod->name }}</td>
                        <td>{{ $hod->department }}</td>
                        <td><a href="mailto:{{ $hod->email }}" class="text-decoration-none" style="color:#0b6b2c;">{{ $hod->email }}</a></td>
                        <td>{{ $hod->phone ?? '—' }}</td>
                        <td>
                            @if($hod->is_active)
                                <span class="badge" style="background:#eef7e8; color:#065321; border:1px solid #94c83d;">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $hod->escalations_count ?? $hod->escalations()->count() }}</span>
                        </td>
                        <td class="text-end px-4">
                            <a href="{{ route('hods.edit', $hod) }}" class="btn btn-sm btn-outline-secondary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('hods.destroy', $hod) }}" class="d-inline"
                                  onsubmit="return confirm('Delete {{ addslashes($hod->name) }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-people fs-2 d-block mb-2"></i>
                            No officers added yet. <a href="{{ route('hods.create') }}" style="color:#065321;">Add one now</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $hods->links() }}</div>
</div>
@endsection
