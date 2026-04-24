@extends('layouts.app')

@section('title', 'Escalation Matrix')

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-0 fw-bold" style="color:#065321;">Escalation Matrix</h4>
            <p class="text-muted mb-0 small">Track all feedback escalations to HODs and incharge officers</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('escalations.index') }}"
               class="btn btn-sm {{ !request('status') ? 'text-white' : 'btn-outline-secondary' }}"
               style="{{ !request('status') ? 'background:#065321;' : '' }}">
                All <span class="badge ms-1 bg-secondary">{{ $counts['pending'] + $counts['responded'] }}</span>
            </a>
            <a href="{{ route('escalations.index', ['status' => 'pending']) }}"
               class="btn btn-sm {{ request('status') === 'pending' ? 'text-white' : 'btn-outline-warning' }}"
               style="{{ request('status') === 'pending' ? 'background:#b45309;' : '' }}">
                Pending <span class="badge ms-1 bg-warning text-dark">{{ $counts['pending'] }}</span>
            </a>
            <a href="{{ route('escalations.index', ['status' => 'responded']) }}"
               class="btn btn-sm {{ request('status') === 'responded' ? 'text-white' : 'btn-outline-success' }}"
               style="{{ request('status') === 'responded' ? 'background:#065321;' : '' }}">
                Responded <span class="badge ms-1" style="background:#94c83d; color:#163223;">{{ $counts['responded'] }}</span>
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f6fbf4; color:#065321;">
                    <tr>
                        <th class="px-4 py-3">Escalation Ref</th>
                        <th>Feedback Ref</th>
                        <th>Escalated To</th>
                        <th>Department</th>
                        <th>Escalated By</th>
                        <th>Escalated At</th>
                        <th>Elapsed</th>
                        <th>Status</th>
                        <th class="px-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($escalations as $esc)
                    <tr>
                        <td class="px-4 fw-semibold" style="font-family:monospace; color:#065321;">
                            {{ $esc->reference }}
                        </td>
                        <td>
                            <a href="{{ route('feedback.admin.show', $esc->feedback_id) }}"
                               class="text-decoration-none fw-semibold" style="color:#0b6b2c;">
                                {{ $esc->feedback?->reference_no ?? '—' }}
                            </a>
                        </td>
                        <td>{{ $esc->hod?->name ?? '—' }}</td>
                        <td class="text-muted small">{{ $esc->hod?->department ?? '—' }}</td>
                        <td class="text-muted small">{{ $esc->escalatedBy?->getFullName() ?? '—' }}</td>
                        <td class="text-muted small">{{ $esc->escalated_at->format('d M Y, H:i') }}</td>
                        <td class="small">
                            @php $hrs = $esc->elapsedHours(); @endphp
                            @if($hrs < 24)
                                <span class="text-success fw-semibold">{{ $hrs }}h</span>
                            @elseif($hrs < 72)
                                <span class="text-warning fw-semibold">{{ round($hrs/24, 1) }}d</span>
                            @else
                                <span class="text-danger fw-semibold">{{ round($hrs/24, 1) }}d</span>
                            @endif
                            @if($esc->isResponded())
                                <span class="text-muted">(closed)</span>
                            @endif
                        </td>
                        <td>
                            @if($esc->isPending())
                                <span class="badge" style="background:#fff3cd; color:#856404; border:1px solid #ffc107;">Pending</span>
                            @else
                                <span class="badge" style="background:#eef7e8; color:#065321; border:1px solid #94c83d;">Responded</span>
                            @endif
                        </td>
                        <td class="px-4">
                            @if($esc->isResponded())
                            <button class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#resp-{{ $esc->id }}">
                                <i class="bi bi-eye"></i> View
                            </button>

                            {{-- Response Modal --}}
                            <div class="modal fade" id="resp-{{ $esc->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background:#f6fbf4; border-bottom:1px solid #ddeedd;">
                                            <h6 class="modal-title fw-bold" style="color:#065321;">
                                                Response — {{ $esc->reference }}
                                            </h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-2 small text-muted">
                                                Responded by <strong>{{ $esc->hod_name }}</strong>
                                                on {{ $esc->responded_at?->format('d M Y, H:i') }}
                                                &mdash; {{ $esc->elapsedHours() }}h after escalation
                                            </div>
                                            <div class="p-3 rounded" style="background:#f6fbf4; border-left:4px solid #0b6b2c; white-space:pre-wrap; font-size:14px;">{{ $esc->hod_response }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                                <span class="text-muted small">Awaiting</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="bi bi-arrow-up-right-circle fs-2 d-block mb-2"></i>
                            No escalations yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $escalations->links() }}</div>

</div>
@endsection
