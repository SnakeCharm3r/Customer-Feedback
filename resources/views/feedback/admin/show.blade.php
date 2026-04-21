@extends('layouts.app')
@section('title', 'Feedback: ' . $feedback->reference_number)

@section('content')
<style>
    .feedback-detail-sticky-card {
        top: 90px;
        z-index: 10;
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Feedback Detail</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('feedback.admin.index') }}">Feedback</a></li>
                    <li class="breadcrumb-item active">{{ $feedback->reference_number }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="row g-4">

    {{-- LEFT: Submission Details --}}
    <div class="col-xl-8">

        @if(session('status'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <span class="font-monospace text-primary">{{ $feedback->reference_number }}</span>
                </h5>
                <div class="d-flex align-items-center gap-2">
                    @if($feedback->is_priority)
                        <span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>High Priority</span>
                    @endif
                    {!! $feedback->getStatusBadge() !!}
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-md-3">
                        <p class="text-muted small text-uppercase mb-1">Patient Name</p>
                        <p class="fw-semibold mb-0">{{ $feedback->patient_name }}</p>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <p class="text-muted small text-uppercase mb-1">Email</p>
                        <p class="fw-semibold mb-0">{{ $feedback->patient_email ?: '—' }}</p>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <p class="text-muted small text-uppercase mb-1">Phone</p>
                        <p class="fw-semibold mb-0">{{ $feedback->patient_phone ?: '—' }}</p>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <p class="text-muted small text-uppercase mb-1">Visit Date</p>
                        <p class="fw-semibold mb-0">{{ $feedback->visit_date ? \Carbon\Carbon::parse($feedback->visit_date)->format('d M Y') : '—' }}</p>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <p class="text-muted small text-uppercase mb-1">Category</p>
                        <p class="fw-semibold mb-0">{{ $feedback->getServiceCategoryLabel() }}</p>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <p class="text-muted small text-uppercase mb-1">Type</p>
                        @php $tc = ['complaint'=>'bg-danger','compliment'=>'bg-success','suggestion'=>'bg-info','enquiry'=>'bg-secondary'][$feedback->feedback_type] ?? 'bg-secondary'; @endphp
                        <span class="badge {{ $tc }}">{{ ucfirst($feedback->feedback_type) }}</span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <p class="text-muted small text-uppercase mb-1">Service Rating</p>
                        <p class="fw-semibold mb-0">{{ $feedback->service_rating ? $feedback->getServiceRatingLabel() : '—' }}</p>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <p class="text-muted small text-uppercase mb-1">Confidentiality Kept</p>
                        <p class="fw-semibold mb-0">{{ $feedback->getConfidentialityLabel() ?: '—' }}</p>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <p class="text-muted small text-uppercase mb-1">Submitted</p>
                        <p class="fw-semibold mb-0">{{ $feedback->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <p class="text-muted small text-uppercase mb-1">Assigned To</p>
                        <p class="fw-semibold mb-0">{{ $feedback->assignedTo?->getFullName() ?? 'Unassigned' }}</p>
                    </div>
                </div>

                @if($feedback->service_units_summary)
                    <div class="mb-4">
                        <p class="text-muted small text-uppercase mb-2">Service Offered Today</p>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($feedback->service_units_labels as $serviceUnitLabel)
                                <span class="badge bg-primary-subtle text-primary">{{ $serviceUnitLabel }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mb-4">
                    <p class="text-muted small text-uppercase mb-2">Overall Experience</p>
                    <div class="p-3 rounded" style="background:#f8f9fa;border-left:4px solid #065321;">
                        <p class="mb-0" style="white-space:pre-wrap;">{{ $feedback->overall_experience ?: $feedback->message ?: '—' }}</p>
                    </div>
                </div>

                @if($feedback->improvement_suggestion)
                    <div class="mb-4">
                        <p class="text-muted small text-uppercase mb-2">Suggested Improvement</p>
                        <div class="p-3 rounded" style="background:#f8f9fa;border-left:4px solid #198754;">
                            <p class="mb-0" style="white-space:pre-wrap;">{{ $feedback->improvement_suggestion }}</p>
                        </div>
                    </div>
                @endif

                @if($feedback->confidentiality_comment)
                    <div class="mb-4">
                        <p class="text-muted small text-uppercase mb-2">Confidentiality Explanation</p>
                        <div class="p-3 rounded" style="background:#fff8e1;border-left:4px solid #ffc107;">
                            <p class="mb-0" style="white-space:pre-wrap;">{{ $feedback->confidentiality_comment }}</p>
                        </div>
                    </div>
                @endif

                <div class="mb-0">
                    <p class="text-muted small text-uppercase mb-2">Additional Comments</p>
                    <div class="p-3 rounded" style="background:#f8f9fa;border-left:4px solid #065321;">
                        <p class="mb-0" style="white-space:pre-wrap;">{{ $feedback->message ?: 'No additional comments provided.' }}</p>
                    </div>
                </div>

                @if($feedback->attachment)
                    <div class="mt-3">
                        <p class="text-muted small text-uppercase mb-1">Attachment</p>
                        <a href="{{ asset('storage/' . $feedback->attachment) }}" target="_blank"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-paperclip me-1"></i>View Attachment
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Internal Notes --}}
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="bi bi-sticky me-2"></i>Internal Notes
                    <span class="badge bg-secondary ms-1">{{ $feedback->internalNotes->count() }}</span>
                </h6>
            </div>
            <div class="card-body p-0">
                @forelse($feedback->internalNotes as $note)
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <span class="fw-semibold small text-dark">{{ $note->author?->getFullName() ?? 'System' }}</span>
                        <span class="text-muted" style="font-size:11px;">{{ $note->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <p class="mb-0 small text-muted">{{ $note->content }}</p>
                    @if($note->is_coo_comment)
                        <span class="badge bg-danger-subtle text-danger mt-1"><i class="bi bi-eye me-1"></i>COO Comment</span>
                    @endif
                </div>
                @empty
                <div class="text-center text-muted py-4 small">
                    <i class="bi bi-sticky d-block fs-4 mb-1 opacity-25"></i>No internal notes yet.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Patient Responses --}}
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="bi bi-chat-dots me-2"></i>Patient Responses
                    <span class="badge bg-secondary ms-1">{{ $feedback->patientResponses->count() }}</span>
                </h6>
            </div>
            <div class="card-body p-0">
                @forelse($feedback->patientResponses as $response)
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <span class="fw-semibold small">{{ $response->sender?->getFullName() ?? 'Staff' }}</span>
                        <div class="d-flex align-items-center gap-2">
                            @if($response->is_public)
                                <span class="badge bg-success-subtle text-success" style="font-size:10px;">Public</span>
                            @endif
                            <span class="text-muted" style="font-size:11px;">{{ $response->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                    <p class="mb-0 small">{{ $response->content }}</p>
                </div>
                @empty
                <div class="text-center text-muted py-4 small">
                    <i class="bi bi-chat-dots d-block fs-4 mb-1 opacity-25"></i>No responses sent yet.
                </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- RIGHT: Actions Panel --}}
    <div class="col-xl-4">
        <div class="card sticky-top feedback-detail-sticky-card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="bi bi-gear me-2"></i>Actions</h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">Manage status, assignment, and responses for this submission.</p>

                {{-- Review Metadata --}}
                @if($feedback->reviewedBy || $feedback->reviewed_at)
                <div class="mb-3 p-2 rounded" style="background:#eef7e8;border-left:3px solid #0b6b2c;">
                    <p class="text-muted small text-uppercase mb-1">Last Reviewed</p>
                    <p class="fw-semibold small mb-0">{{ $feedback->reviewedBy?->getFullName() ?? 'Not reviewed' }}</p>
                    <p class="text-muted small mb-0">{{ $feedback->reviewed_at?->format('d M Y, H:i') ?? '—' }}</p>
                </div>
                @endif

                {{-- Assignment --}}
                <div class="mb-3">
                    <label for="assigned_to" class="form-label small fw-semibold">Assign To</label>
                    <form method="POST" action="{{ route('feedback.admin.assignment', $feedback) }}" class="d-flex gap-2">
                        @csrf
                        <select id="assigned_to" name="assigned_to" class="form-select form-select-sm">
                            <option value="">-- Unassigned --</option>
                            @foreach($assignableUsers as $user)
                                <option value="{{ $user->id }}" {{ $feedback->assigned_to === $user->id ? 'selected' : '' }}>
                                    {{ $user->getFullName() }} ({{ $user->getRoleLabel() }})
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">Assign</button>
                    </form>
                    @error('assigned_to')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Status Update --}}
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Update Status</label>
                    <form method="POST" action="{{ route('feedback.admin.status', $feedback) }}" class="d-flex gap-2">
                        @csrf
                        <select name="status" class="form-select form-select-sm">
                            <option value="new"          {{ $feedback->status=='new'          ?'selected':'' }}>New</option>
                            <option value="under_review" {{ $feedback->status=='under_review' ?'selected':'' }}>Under Review</option>
                            <option value="responded"    {{ $feedback->status=='responded'    ?'selected':'' }}>Responded</option>
                            <option value="closed"       {{ $feedback->status=='closed'       ?'selected':'' }}>Closed</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    </form>
                    @error('status')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="note_content" class="form-label small fw-semibold">Internal Comment</label>
                    <form method="POST" action="{{ route('feedback.admin.note', $feedback) }}">
                        @csrf
                        <textarea id="note_content" name="note_content" rows="4" class="form-control form-control-sm @error('note_content') is-invalid @enderror" placeholder="Add an internal review note for the team...">{{ old('note_content') }}</textarea>
                        @error('note_content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn btn-outline-secondary btn-sm mt-2 w-100">
                            <i class="bi bi-sticky me-1"></i>Save Internal Comment
                        </button>
                    </form>
                </div>

                <div class="mb-3">
                    <label for="response_content" class="form-label small fw-semibold">Client Response</label>
                    <form method="POST" action="{{ route('feedback.admin.response', $feedback) }}">
                        @csrf
                        <textarea id="response_content" name="response_content" rows="5" class="form-control form-control-sm @error('response_content') is-invalid @enderror" placeholder="Write the response that should appear on the tracking portal and be emailed to the client...">{{ old('response_content') }}</textarea>
                        @error('response_content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text small">This response will appear on the feedback tracking portal. If the client provided an email address, it will also be emailed to them.</div>
                        <button type="submit" class="btn btn-success btn-sm mt-2 w-100">
                            <i class="bi bi-send me-1"></i>Send Response to Client
                        </button>
                    </form>
                </div>

                <hr>
                <div class="text-center">
                    <a href="{{ route('feedback.admin.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Back to All
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
