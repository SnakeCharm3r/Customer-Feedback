@extends('layouts.app')
@section('title', 'Feedback Submissions')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Feedback Submissions</h4>
            <div class="page-title-right d-flex gap-2">
                <a href="{{ route('feedback.manual.create') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>Add Manual Feedback
                </a>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Feedback</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="row g-3 mb-4">
    @php
        $statusCards = [
            ['label' => 'New',          'key' => 'new',          'icon' => 'bi-inbox',          'color' => 'danger'],
            ['label' => 'Under Review', 'key' => 'under_review', 'icon' => 'bi-hourglass-split', 'color' => 'warning'],
            ['label' => 'Responded',    'key' => 'responded',    'icon' => 'bi-check2-circle',   'color' => 'success'],
            ['label' => 'Closed',       'key' => 'closed',       'icon' => 'bi-archive',         'color' => 'secondary'],
        ];
    @endphp
    @foreach($statusCards as $card)
    <div class="col-6 col-md-3">
        <a href="{{ route('feedback.admin.index') }}?status={{ $card['key'] }}"
           class="card text-decoration-none {{ request('status') == $card['key'] ? 'border-primary' : '' }}">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-{{ $card['color'] }} bg-{{ $card['color'] }}-subtle"
                     style="width:44px;height:44px;font-size:20px;flex-shrink:0;">
                    <i class="bi {{ $card['icon'] }}"></i>
                </div>
                <div>
                    <div class="fw-bold fs-5 text-dark lh-1">{{ $counts[$card['key']] }}</div>
                    <div class="text-muted small">{{ $card['label'] }}</div>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">
            <i class="bi bi-chat-left-text me-2"></i>
            @if(request('status'))
                {{ ucfirst(str_replace('_', ' ', request('status'))) }} Submissions
            @else
                All Submissions
            @endif
            <span class="badge bg-secondary ms-2">{{ $feedbacks->total() }}</span>
        </h5>
        <div class="d-flex gap-2">
            @if(request('status'))
                <a href="{{ route('feedback.admin.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x me-1"></i>Clear Filter
                </a>
            @endif
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Ref #</th>
                        <th>Patient</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Submitted</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feedbacks as $feedback)
                    <tr>
                        <td class="ps-3">
                            <span class="fw-semibold text-primary font-monospace small">{{ $feedback->reference_number }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold small">{{ $feedback->patient_name }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ $feedback->patient_email ?: '—' }}</div>
                        </td>
                        <td>
                            <div class="text-muted small">{{ $feedback->service_units_summary ?: $feedback->getServiceCategoryLabel() }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ $feedback->getServiceCategoryLabel() }}</div>
                        </td>
                        <td>
                            @php
                                $typeClass = ['complaint'=>'bg-danger','compliment'=>'bg-success','suggestion'=>'bg-info','enquiry'=>'bg-secondary'][$feedback->feedback_type] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $typeClass }}">{{ ucfirst($feedback->feedback_type) }}</span>
                            @if($feedback->service_rating)
                                <div class="text-muted mt-1" style="font-size:11px;">Rating: {{ $feedback->getServiceRatingLabel() }}</div>
                            @endif
                        </td>
                        <td>
                            @if($feedback->is_priority)
                                <span class="badge bg-danger-subtle text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>High</span>
                            @else
                                <span class="text-muted small">Normal</span>
                            @endif
                        </td>
                        <td>
                            {!! $feedback->getStatusBadge() !!}
                        </td>
                        <td class="text-muted small">{{ $feedback->assignedTo?->getFullName() ?? '—' }}</td>
                        <td class="text-muted small">{{ $feedback->created_at->format('d M Y') }}</td>
                        <td class="text-end pe-3">
                            <a href="{{ route('feedback.admin.show', $feedback) }}"
                               class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                            No feedback submissions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($feedbacks->hasPages())
    <div class="card-footer">
        {{ $feedbacks->links() }}
    </div>
    @endif
</div>
@endsection
