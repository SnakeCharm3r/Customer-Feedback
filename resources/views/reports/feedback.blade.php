@extends('layouts.app')
@section('title', 'Feedback Reports')

@section('content')
@php
    $summaryCards = [
        ['label' => 'Total Feedback', 'value' => $summary['total'], 'icon' => 'bi-bar-chart-line', 'class' => 'primary'],
        ['label' => 'Portal', 'value' => $summary['portal'], 'icon' => 'bi-globe2', 'class' => 'success'],
        ['label' => 'Manual / Paper', 'value' => $summary['manual'], 'icon' => 'bi-file-earmark-text', 'class' => 'warning'],
        ['label' => 'Other Sources', 'value' => $summary['other'], 'icon' => 'bi-diagram-3', 'class' => 'info'],
        ['label' => 'Reviewed', 'value' => $summary['reviewed'], 'icon' => 'bi-person-check', 'class' => 'secondary'],
        ['label' => 'Pending Review', 'value' => $summary['pending_review'], 'icon' => 'bi-hourglass-split', 'class' => 'danger'],
    ];
@endphp

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-sm-0">Feedback Reports</h4>
                <p class="text-muted mb-0 small mt-1">Review all submitted feedback, response ownership, reviewer activity, and source breakdowns.</p>
            </div>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Feedback Reports</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach($summaryCards as $card)
        <div class="col-6 col-xl-2 col-md-4">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-{{ $card['class'] }} bg-{{ $card['class'] }}-subtle"
                         style="width:48px;height:48px;font-size:20px;flex-shrink:0;">
                        <i class="bi {{ $card['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">{{ $card['value'] }}</div>
                        <div class="text-muted small">{{ $card['label'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h5 class="card-title mb-0"><i class="bi bi-funnel me-2"></i>Report Filters</h5>
        <a href="{{ route('reports.feedback.export', request()->query()) }}" class="btn btn-success btn-sm">
            <i class="bi bi-download me-1"></i>Export CSV
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.feedback.index') }}" class="row g-3">
            <div class="col-md-4 col-lg-3">
                <label for="search" class="form-label small fw-semibold">Search</label>
                <input type="text" id="search" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control"
                       placeholder="Reference, patient, or report text">
            </div>
            <div class="col-md-4 col-lg-2">
                <label for="source" class="form-label small fw-semibold">Source</label>
                <select id="source" name="source" class="form-select">
                    <option value="">All Sources</option>
                    @foreach(\App\Models\Feedback::SOURCES as $value => $label)
                        <option value="{{ $value }}" {{ ($filters['source'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 col-lg-2">
                <label for="status" class="form-label small fw-semibold">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(\App\Models\Feedback::STATUSES as $value => $label)
                        <option value="{{ $value }}" {{ ($filters['status'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 col-lg-2">
                <label for="reviewed_by" class="form-label small fw-semibold">Reviewer</label>
                <select id="reviewed_by" name="reviewed_by" class="form-select">
                    <option value="">All Reviewers</option>
                    @foreach($reviewers as $reviewer)
                        <option value="{{ $reviewer->id }}" {{ (string) ($filters['reviewed_by'] ?? '') === (string) $reviewer->id ? 'selected' : '' }}>{{ $reviewer->getFullName() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 col-lg-2">
                <label for="assigned_to" class="form-label small fw-semibold">Assigned User</label>
                <select id="assigned_to" name="assigned_to" class="form-select">
                    <option value="">All Assignees</option>
                    @foreach($assignableUsers as $assignableUser)
                        <option value="{{ $assignableUser->id }}" {{ (string) ($filters['assigned_to'] ?? '') === (string) $assignableUser->id ? 'selected' : '' }}>{{ $assignableUser->getFullName() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-1 d-flex align-items-end">
                <div class="d-flex gap-2 w-100">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
                    <a href="{{ route('reports.feedback.index') }}" class="btn btn-outline-secondary w-100"><i class="bi bi-x-lg"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="card-title mb-0"><i class="bi bi-table me-2"></i>Feedback Report Table</h5>
        <span class="badge bg-secondary">{{ $reports->total() }} records</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Ref #</th>
                        <th>Source</th>
                        <th>Submitter Role</th>
                        <th>Report</th>
                        <th>Reviewer</th>
                        <th>Date Reviewed</th>
                        <th>Reviewer Response</th>
                        <th>Assigned User</th>
                        <th>Created</th>
                        <th>Reviewed</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        @php
                            $latestResponse = $report->latest_response;
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <div class="fw-semibold text-primary font-monospace small">{{ $report->reference_no }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $report->getStatusLabel() }} • {{ $report->getFeedbackTypeLabel() }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $report->getSourceLabel() }}</span>
                                <div class="text-muted mt-1" style="font-size:11px;">{{ $report->getServiceCategoryLabel() }}</div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $report->getSubmitterRoleLabel() }}</span>
                            </td>
                            <td style="min-width:280px;">
                                <div class="fw-semibold small">{{ $report->patient_name ?: 'Anonymous / Not Provided' }}</div>
                                <div class="text-muted small">{{ \Illuminate\Support\Str::limit($report->report_excerpt, 140) ?: 'No report text available.' }}</div>
                            </td>
                            <td>
                                <div class="small fw-semibold">{{ $report->reviewedBy?->getFullName() ?? 'Not yet reviewed' }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $report->reviewedBy?->getRoleLabel() ?? '—' }}</div>
                            </td>
                            <td class="text-muted small">{{ $report->reviewed_at?->format('d M Y, H:i') ?? '—' }}</td>
                            <td style="min-width:240px;">
                                <div class="small text-muted">{{ \Illuminate\Support\Str::limit($latestResponse?->content ?? 'No reviewer response recorded.', 120) }}</div>
                            </td>
                            <td>
                                <div class="small fw-semibold">{{ $report->assignedTo?->getFullName() ?? 'Unassigned' }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $report->assignedTo?->getRoleLabel() ?? '—' }}</div>
                            </td>
                            <td class="text-muted small">{{ $report->created_at?->format('d M Y, H:i') ?? '—' }}</td>
                            <td class="text-muted small">{{ $report->reviewed_at?->format('d M Y, H:i') ?? '—' }}</td>
                            <td class="text-end pe-3">
                                <a href="{{ route('feedback.admin.show', $report) }}" class="btn btn-sm btn-outline-primary">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                                <p class="fw-medium mb-1">No feedback report records found</p>
                                <p class="small mb-0">Try adjusting your filters or export the current empty result set.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reports->hasPages())
        <div class="card-footer">
            {{ $reports->links() }}
        </div>
    @endif
</div>
@endsection
