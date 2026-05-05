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
    <div class="col-xl-8 col-lg-7">

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
                        <p class="text-muted small text-uppercase mb-1">Location</p>
                        <p class="fw-semibold mb-0">{{ \App\Models\Feedback::LOCATIONS[$feedback->location] ?? '—' }}</p>
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

        {{-- Quality Assurance Assessment + Internal Notes (consolidated) --}}
        @php $sentimentBadge = ['positive'=>'bg-success','negative'=>'bg-danger','neutral'=>'bg-secondary']; @endphp
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0"><i class="bi bi-clipboard2-pulse me-2"></i>Quality Assurance Assessment</h6>
                @if($feedback->sentiment)
                    <span class="badge {{ $sentimentBadge[$feedback->sentiment] ?? 'bg-secondary' }}">{{ $feedback->getSentimentLabel() }}</span>
                @endif
            </div>

            {{-- Current classification summary --}}
            <div class="card-body border-bottom pb-3">
                <div class="row g-3 mb-3">
                    <div class="col-sm-4">
                        <p class="text-muted small text-uppercase mb-1">Theme</p>
                        <p class="fw-semibold mb-0">{{ $feedback->getThemeLabel() }}</p>
                    </div>
                    <div class="col-sm-4">
                        <p class="text-muted small text-uppercase mb-1">Sentiment</p>
                        <p class="fw-semibold mb-0">{{ $feedback->getSentimentLabel() }}</p>
                    </div>
                    <div class="col-sm-4">
                        <p class="text-muted small text-uppercase mb-1">Wing</p>
                        <p class="fw-semibold mb-0">{{ $feedback->getWingLabel() }}</p>
                    </div>
                    <div class="col-sm-4">
                        <p class="text-muted small text-uppercase mb-1">Department Type</p>
                        <p class="fw-semibold mb-0">{{ $feedback->getDepartmentTypeLabel() }}</p>
                    </div>
                    <div class="col-sm-4">
                        <p class="text-muted small text-uppercase mb-1">Service Category</p>
                        <p class="fw-semibold mb-0">{{ $feedback->getServiceCategoryLabel() }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('feedback.admin.classify', $feedback) }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Theme</label>
                            <select name="theme" class="form-select form-select-sm">
                                <option value="">— Select theme —</option>
                                @foreach(\App\Models\Feedback::THEMES as $key => $label)
                                    <option value="{{ $key }}" {{ $feedback->theme === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Sentiment</label>
                            <select name="sentiment" class="form-select form-select-sm">
                                <option value="">— Select sentiment —</option>
                                @foreach(\App\Models\Feedback::SENTIMENTS as $key => $label)
                                    <option value="{{ $key }}" {{ $feedback->sentiment === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Wing</label>
                            <select name="wing" class="form-select form-select-sm">
                                <option value="">— Select wing —</option>
                                @foreach(\App\Models\Feedback::WINGS as $key => $label)
                                    <option value="{{ $key }}" {{ $feedback->wing === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Service Category</label>
                            <select name="service_category" class="form-select form-select-sm">
                                <option value="">— Select category —</option>
                                @foreach(\App\Models\Feedback::SERVICE_CATEGORIES as $key => $label)
                                    <option value="{{ $key }}" {{ $feedback->service_category === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold">Department / Service Unit</label>
                            <select name="department_id" class="form-select form-select-sm">
                                <option value="">— Select department —</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}"
                                        {{ ($feedback->department_id ?? null) == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                        @if(!empty($dept->categories))
                                            ({{ implode(', ', array_map(fn($c) => \App\Models\Department::CATEGORIES[$c] ?? $c, $dept->categories)) }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @if($departments->isEmpty())
                                <p class="text-muted small mt-1">
                                    No departments configured.
                                    <a href="{{ route('departments.create') }}">Add departments</a> to enable this field.
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label small fw-semibold">Internal Memo</label>
                        <textarea name="note_content" rows="3" class="form-control form-control-sm" placeholder="Add an internal note for the team (optional)..."></textarea>
                        <p class="text-muted" style="font-size:11px;" class="mt-1">This memo will be recorded alongside the assessment changes.</p>
                    </div>

                    <div class="mt-2 d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-sm btn-success px-4">
                            <i class="bi bi-check2 me-1"></i>Save Assessment
                        </button>
                        @if($feedback->reviewedBy && $feedback->reviewed_at)
                            <span class="text-muted" style="font-size:11px;">
                                Last updated by <strong>{{ $feedback->reviewedBy->getFullName() }}</strong>
                                on {{ $feedback->reviewed_at->format('d M Y, H:i') }}
                            </span>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Internal Notes (merged into same card) --}}
            <div class="card-body border-bottom pb-0 pt-3 px-3">
                <p class="small fw-semibold text-muted text-uppercase mb-2">
                    <i class="bi bi-sticky me-1"></i>Internal Notes
                    <span class="badge bg-secondary ms-1">{{ $feedback->internalNotes->count() }}</span>
                </p>
                @forelse($feedback->internalNotes as $note)
                <div class="py-2 border-top">
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
                <p class="text-muted small py-2">No internal notes yet.</p>
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
    <div class="col-xl-4 col-lg-5">
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
                            <option value="responded" {{ $feedback->status=='responded' ?'selected':'' }}>Responded</option>
                            <option value="closed"    {{ $feedback->status=='closed'    ?'selected':'' }}>Closed</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    </form>
                    @error('status')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="response_content" class="form-label small fw-semibold">Client Response</label>
                    <form method="POST" action="{{ route('feedback.admin.response', $feedback) }}">
                        @csrf

                        {{-- Canned response templates --}}
                        @php
                        $templates = [
                            'compliment' => [
                                ['label' => 'Generic – English',   'text' => "Thank you for your kind feedback regarding our services at CCBRT. We are truly grateful for your encouraging words and will share them with the team involved. It motivates us to continue providing excellent care. We look forward to serving you again and wish you good health. Welcome to CCBRT."],
                                ['label' => 'Generic – Swahili',   'text' => "Asante kwa mrejesho uliotuandikia kuhusu huduma zetu. Tunashukuru sana maneno yako ya moyo na tutayashirikisha na timu inayohusika. Yanaturahisishea kuendelea kutoa huduma bora. Tunakutakia afya njema. Karibu CCBRT."],
                                ['label' => 'Team Recognition – English', 'text' => "Thank you so much for taking the time to share your positive experience with us. Your kind words have been passed on to the team and serve as a great encouragement. We remain committed to providing you and all our patients with the highest standard of care. We wish you good health and look forward to welcoming you again at CCBRT."],
                                ['label' => 'Team Recognition – Swahili', 'text' => "Asante sana kwa muda uliochukua kushiriki uzoefu wako mzuri. Maneno yako ya moyo yamepitishwa kwa timu na ni motisha kubwa kwao. Tunaendelea kujitolea kutoa huduma bora zaidi. Tunakutakia afya njema na tunatarajia kukuona tena CCBRT."],
                            ],
                            'complaint' => [
                                ['label' => 'Acknowledgement & Investigation – English', 'text' => "Thank you for bringing this matter to our attention. We sincerely apologise for the experience you had and assure you that your concern has been taken seriously. We are currently investigating the issue and will take the necessary corrective action. Your feedback helps us improve the quality of our services. We value you as our patient and hope to restore your confidence in CCBRT."],
                                ['label' => 'Acknowledgement & Investigation – Swahili', 'text' => "Asante kwa kuleta suala hili kwa uangalifu wetu. Tunaomba msamaha kwa uzoefu uliokupata na tunakuhakikishia kwamba wasiwasi wako umechukuliwa kwa uzito. Tunachunguza tatizo hili sasa hivi na tutachukua hatua zinazohitajika. Maoni yako yanatusaidia kuboresha ubora wa huduma zetu. Tunakuthamini kama mgonjwa wetu na tunatumai kurudisha imani yako kwa CCBRT."],
                                ['label' => 'Resolved – English', 'text' => "Thank you for your patience while we looked into your concern. We have reviewed the matter and appropriate action has been taken to address the issue and prevent recurrence. We deeply regret any inconvenience caused and remain committed to providing you with the highest quality of care. Please do not hesitate to reach out if you require any further assistance. We wish you good health."],
                                ['label' => 'Resolved – Swahili', 'text' => "Asante kwa uvumilivu wako wakati tulipochunguza wasiwasi wako. Tumepitia suala hili na hatua zinazofaa zimechukuliwa kushughulikia tatizo na kuzuia kujirudia. Tunaomba msamaha kwa usumbufu wowote uliosababishwa. Tuko tayari kukusaidia ukihitaji msaada zaidi. Tunakutakia afya njema."],
                            ],
                            'suggestion' => [
                                ['label' => 'Valued Suggestion – English', 'text' => "Thank you for your thoughtful suggestion. We greatly value the input of our patients and community in helping us improve our services. Your suggestion has been forwarded to the relevant department for consideration. We appreciate you taking the time to help us grow and provide better care for everyone. Welcome to CCBRT."],
                                ['label' => 'Valued Suggestion – Swahili', 'text' => "Asante kwa pendekezo lako la kina. Tunathamini sana maoni ya wagonjwa wetu na jamii katika kutusaidia kuboresha huduma zetu. Pendekezo lako limepelekwa kwa idara husika kwa kuzingatiwa. Tunashukuru kwa muda uliochukua kutusaidia kukua na kutoa huduma bora zaidi kwa kila mtu. Karibu CCBRT."],
                                ['label' => 'Implemented – English', 'text' => "Thank you for your valuable suggestion. We are pleased to inform you that your feedback has been reviewed and the relevant team is actively working on implementing improvements based on your recommendation. Your contribution makes a real difference in the quality of care we provide. We wish you good health and thank you for being part of the CCBRT family."],
                                ['label' => 'Implemented – Swahili', 'text' => "Asante kwa pendekezo lako la thamani. Tunafurahi kukuarifu kwamba maoni yako yamekaguliwa na timu husika inafanya kazi kikamilifu kutekeleza maboresho kulingana na mapendekezo yako. Mchango wako unafanya tofauti halisi katika ubora wa huduma tunazotoa. Tunakutakia afya njema na asante kwa kuwa sehemu ya familia ya CCBRT."],
                            ],
                            'enquiry' => [
                                ['label' => 'General Enquiry Response – English', 'text' => "Thank you for contacting CCBRT. We have received your enquiry and are pleased to assist you. Please find the relevant information addressed below. Should you require any further clarification, do not hesitate to reach out to us directly. We are here to help and wish you good health. Welcome to CCBRT."],
                                ['label' => 'General Enquiry Response – Swahili', 'text' => "Asante kwa kuwasiliana na CCBRT. Tumepokea swali lako na tunafurahi kukusaidia. Tafadhali pata taarifa husika zilizoshughulikiwa hapa chini. Iwapo unahitaji ufafanuzi zaidi, usisite kuwasiliana nasi moja kwa moja. Tuko hapa kukusaidia na tunakutakia afya njema. Karibu CCBRT."],
                                ['label' => 'Referral / Appointment – English', 'text' => "Thank you for your enquiry. To assist you further, we recommend visiting our facility or contacting our front desk team directly. Our staff will be happy to guide you through the process and ensure you receive the appropriate care. We look forward to serving you at CCBRT and wish you good health."],
                                ['label' => 'Referral / Appointment – Swahili', 'text' => "Asante kwa swali lako. Ili kukusaidia zaidi, tunapendekeza utembelee kituo chetu au uwasiliane na timu yetu ya mapokezi moja kwa moja. Wafanyakazi wetu watafurahi kukuongoza kupitia mchakato huu na kuhakikisha unapata huduma inayofaa. Tunatarajia kukuhudumia CCBRT na tunakutakia afya njema."],
                            ],
                        ];
                        $feedbackType = $feedback->feedback_type ?? 'compliment';
                        $typeTemplates = $templates[$feedbackType] ?? $templates['compliment'];
                        @endphp

                        <div class="mb-2">
                            <label for="response_template" class="form-label small text-muted">
                                <i class="bi bi-lightning me-1"></i>Quick Template
                                <span class="badge bg-secondary ms-1" style="font-size:10px;">{{ ucfirst($feedbackType) }}</span>
                            </label>
                            <select id="response_template" class="form-select form-select-sm"
                                    onchange="if(this.value){document.getElementById('response_content').value=this.value;this.value='';}">
                                <option value="">— Select a template to pre-fill —</option>
                                @foreach($typeTemplates as $tpl)
                                    <option value="{{ $tpl['text'] }}">{{ $tpl['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

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

                {{-- ESCALATION SECTION --}}
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label small fw-semibold mb-0" style="color:#065321;">
                            <i class="bi bi-arrow-up-right-circle me-1"></i> Escalate to HOD / Incharge
                        </label>
                        @php $escalations = $feedback->escalations()->with('hod')->latest()->get(); @endphp
                        @if($escalations->count())
                            <span class="badge" style="background:#fff3cd; color:#856404; border:1px solid #ffc107;">
                                {{ $escalations->count() }} escalation(s)
                            </span>
                        @endif
                    </div>

                    @if($escalations->count())
                    <div class="mb-3">
                        @foreach($escalations as $esc)
                        <div class="d-flex align-items-center justify-content-between p-2 rounded mb-1"
                             style="background:#f6fbf4; border:1px solid #ddeedd; font-size:12px;">
                            <div>
                                <span class="fw-semibold" style="color:#065321; font-family:monospace;">{{ $esc->reference }}</span>
                                &nbsp;&rarr;&nbsp; {{ $esc->hod?->name }} ({{ $esc->hod?->department }})
                                &nbsp;&bull;&nbsp; {{ $esc->escalated_at->diffForHumans() }}
                            </div>
                            @if($esc->isPending())
                                <span class="badge" style="background:#fff3cd; color:#856404;">Pending</span>
                            @else
                                <span class="badge" style="background:#eef7e8; color:#065321;">Responded</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @php $activeHods = \App\Models\Hod::active()->orderBy('department')->orderBy('name')->get(); @endphp
                    @if($activeHods->isEmpty())
                        <div class="alert alert-warning py-2 small mb-0">
                            No HOD officers configured yet.
                            <a href="{{ route('hods.create') }}" style="color:#065321;">Add officers</a> to enable escalation.
                        </div>
                    @else
                    <form method="POST" action="{{ route('escalations.store', $feedback) }}">
                        @csrf
                        <select name="hod_id" class="form-select form-select-sm mb-2 @error('hod_id') is-invalid @enderror" required>
                            <option value="">— Select officer to escalate to —</option>
                            @foreach($activeHods as $hod)
                                <option value="{{ $hod->id }}">{{ $hod->name }} — {{ $hod->department }}</option>
                            @endforeach
                        </select>
                        @error('hod_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <textarea name="message" class="form-control form-control-sm mb-2" rows="2"
                                  placeholder="Optional message to the HOD (e.g. specific area to address)..."></textarea>
                        <button type="submit" class="btn btn-sm w-100 fw-semibold text-white" style="background:#b45309;">
                            <i class="bi bi-arrow-up-right-circle me-1"></i> Escalate This Feedback
                        </button>
                    </form>
                    @endif
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
