@extends('layouts.app')
@section('title', 'Escalation Matrix')

@section('content')

{{-- ── Page Title ──────────────────────────────────────────────────────── --}}
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h4 class="mb-0">Escalation Matrix</h4>
                <p class="text-muted mb-0 small mt-1">Track all feedback escalations sent to HODs and incharge officers</p>
            </div>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Escalations</li>
            </ol>
        </div>
    </div>
</div>

{{-- ── Stats Cards ─────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-3">
    @php
        $total = $counts['pending'] + $counts['responded'];
        $responseRate = $total > 0 ? round(($counts['responded'] / $total) * 100) : 0;
    @endphp
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left:3px solid var(--bs-primary) !important;">
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle flex-shrink-0"
                         style="width:42px;height:42px;font-size:18px;">
                        <i class="bi bi-arrow-up-right-circle text-primary"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1 text-dark">{{ $total }}</div>
                        <div class="text-muted small">Total Escalations</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left:3px solid var(--bs-warning) !important;">
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning-subtle flex-shrink-0"
                         style="width:42px;height:42px;font-size:18px;">
                        <i class="bi bi-hourglass-split text-warning"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1 {{ $counts['pending'] > 0 ? 'text-warning' : 'text-dark' }}">
                            {{ $counts['pending'] }}
                        </div>
                        <div class="text-muted small">Pending</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left:3px solid var(--bs-success) !important;">
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success-subtle flex-shrink-0"
                         style="width:42px;height:42px;font-size:18px;">
                        <i class="bi bi-check2-circle text-success"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1 text-dark">{{ $counts['responded'] }}</div>
                        <div class="text-muted small">Responded</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left:3px solid var(--bs-info) !important;">
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-info-subtle flex-shrink-0"
                         style="width:42px;height:42px;font-size:18px;">
                        <i class="bi bi-graph-up text-info"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold fs-4 lh-1 text-dark">{{ $responseRate }}%</div>
                        <div class="text-muted small">Response Rate</div>
                        <div class="progress mt-1" style="height:3px;">
                            <div class="progress-bar bg-info" style="width:{{ $responseRate }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Status Filter Tabs ──────────────────────────────────────────────── --}}
<div class="card mb-0">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2 py-2">
        <div class="d-flex align-items-center gap-2">
            <h5 class="card-title mb-0 small fw-bold text-uppercase" style="letter-spacing:.05em;">Escalations</h5>
            <span class="badge bg-secondary rounded-pill">{{ $escalations->total() }}</span>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('escalations.index') }}"
               class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">
                All <span class="badge ms-1 {{ !request('status') ? 'bg-white text-primary' : 'bg-secondary' }}">{{ $total }}</span>
            </a>
            <a href="{{ route('escalations.index', ['status'=>'pending']) }}"
               class="btn btn-sm {{ request('status')==='pending' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">
                Pending <span class="badge ms-1 bg-warning text-dark">{{ $counts['pending'] }}</span>
            </a>
            <a href="{{ route('escalations.index', ['status'=>'responded']) }}"
               class="btn btn-sm {{ request('status')==='responded' ? 'btn-success' : 'btn-outline-success' }}">
                Responded <span class="badge ms-1 bg-success">{{ $counts['responded'] }}</span>
            </a>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th class="ps-3 py-3 fw-semibold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Ref</th>
                        <th class="py-3 fw-semibold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Feedback</th>
                        <th class="py-3 fw-semibold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Escalated To</th>
                        <th class="py-3 fw-semibold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">By</th>
                        <th class="py-3 fw-semibold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Escalated At</th>
                        <th class="py-3 fw-semibold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Elapsed</th>
                        <th class="py-3 fw-semibold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Status</th>
                        <th class="py-3 text-end pe-3" style="width:90px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($escalations as $esc)
                    @php $hrs = $esc->elapsedHours(); @endphp
                    <tr class="{{ $esc->isPending() && $hrs >= 72 ? 'table-danger bg-opacity-10' : '' }}">

                        {{-- Reference --}}
                        <td class="ps-3 py-3">
                            <span class="fw-semibold font-monospace" style="color:#065321;font-size:12px;">
                                {{ $esc->reference }}
                            </span>
                        </td>

                        {{-- Feedback --}}
                        <td class="py-3">
                            @if($esc->feedback)
                            <a href="{{ route('feedback.admin.show', $esc->feedback_id) }}"
                               class="fw-semibold text-decoration-none font-monospace"
                               style="color:#0b6b2c;font-size:12px;">
                                {{ $esc->feedback->reference_number ?? '—' }}
                            </a>
                            @else
                            <span class="text-muted small">—</span>
                            @endif
                        </td>

                        {{-- Escalated To --}}
                        <td class="py-3">
                            @if($esc->hod)
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                     style="width:30px;height:30px;font-size:11px;background:#065321;">
                                    {{ strtoupper(substr($esc->hod->name,0,1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold small text-dark">{{ $esc->hod->name }}</div>
                                    <div class="text-muted" style="font-size:11px;">{{ $esc->hod->department }}</div>
                                </div>
                            </div>
                            @else
                            <span class="text-muted small">—</span>
                            @endif
                        </td>

                        {{-- Escalated By --}}
                        <td class="py-3">
                            <div class="small text-dark">{{ $esc->escalatedBy?->getFullName() ?? '—' }}</div>
                        </td>

                        {{-- Date --}}
                        <td class="py-3">
                            <div class="small text-dark">{{ $esc->escalated_at->format('d M Y') }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ $esc->escalated_at->format('H:i') }}</div>
                        </td>

                        {{-- Elapsed time with visual indicator --}}
                        <td class="py-3">
                            @if($esc->isResponded())
                                <span class="text-muted small">
                                    {{ $esc->escalated_at->diffForHumans($esc->responded_at, true) }}
                                </span>
                            @elseif($hrs < 24)
                                <span class="badge bg-success-subtle text-success">{{ $hrs }}h</span>
                            @elseif($hrs < 48)
                                <span class="badge bg-warning-subtle text-warning">{{ round($hrs/24,1) }}d</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-exclamation-triangle-fill" style="font-size:9px;"></i>
                                    {{ round($hrs/24,1) }}d
                                </span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="py-3">
                            @if($esc->isPending())
                            <span class="badge bg-warning-subtle text-warning d-inline-flex align-items-center gap-1">
                                <i class="bi bi-circle-fill" style="font-size:7px;"></i>Pending
                            </span>
                            @else
                            <span class="badge bg-success-subtle text-success d-inline-flex align-items-center gap-1">
                                <i class="bi bi-circle-fill" style="font-size:7px;"></i>Responded
                            </span>
                            @endif
                        </td>

                        {{-- Action --}}
                        <td class="text-end pe-3 py-3">
                            @if($esc->isResponded())
                            <button class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal" data-bs-target="#resp-{{ $esc->id }}">
                                <i class="bi bi-eye me-1"></i>View
                            </button>
                            @else
                            <span class="text-muted small fst-italic">Awaiting</span>
                            @endif
                        </td>
                    </tr>

                    {{-- Response Modal --}}
                    @if($esc->isResponded())
                    <div class="modal fade" id="resp-{{ $esc->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <div>
                                        <h6 class="modal-title fw-bold mb-0" style="color:#065321;">
                                            <i class="bi bi-chat-left-text me-2"></i>HOD Response — {{ $esc->reference }}
                                        </h6>
                                        <div class="text-muted small mt-1">
                                            Responded by <strong>{{ $esc->hod_name }}</strong>
                                            on {{ $esc->responded_at?->format('d M Y, H:i') }}
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="d-flex align-items-center gap-2 mb-3 text-muted small">
                                        <i class="bi bi-clock"></i>
                                        Response time: {{ $esc->escalated_at->diffForHumans($esc->responded_at, true) }} after escalation
                                    </div>
                                    <div class="p-3 rounded" style="background:#f6fbf4;border-left:4px solid #0b6b2c;white-space:pre-wrap;font-size:14px;line-height:1.6;">{{ $esc->hod_response }}</div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    @if($esc->feedback)
                                    <a href="{{ route('feedback.admin.show', $esc->feedback_id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-arrow-right me-1"></i>View Feedback
                                    </a>
                                    @endif
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-arrow-up-right-circle d-block fs-1 mb-3 opacity-25"></i>
                            <p class="fw-semibold mb-1">No escalations found</p>
                            <p class="small mb-0">Escalations are created from the feedback detail page.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($escalations->hasPages())
    <div class="card-footer d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
        <span class="text-muted small">
            Page {{ $escalations->currentPage() }} of {{ $escalations->lastPage() }}
            &nbsp;·&nbsp; {{ $escalations->total() }} total
        </span>
        {{ $escalations->links() }}
    </div>
    @endif
</div>

@endsection
