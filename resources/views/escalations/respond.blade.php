<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respond to Escalation – CCBRT Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #eef4eb; font-family: Arial, Helvetica, sans-serif; }
        .brand-bar { background: linear-gradient(135deg, #065321, #0b6b2c); color: #fff; padding: 14px 24px; display: flex; align-items: center; gap: 14px; }
        .brand-logo { width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 900; color: #add95a; flex-shrink: 0; }
        .brand-text h6 { margin: 0; font-size: 14px; font-weight: 700; }
        .brand-text small { font-size: 11px; opacity: 0.65; }
        .accent-top { height: 4px; background: #94c83d; }
        .card { border: none; box-shadow: 0 4px 20px rgba(6,83,33,0.08); }
        .ref-badge { display: inline-block; background: linear-gradient(135deg, #065321, #0b6b2c); color: #add95a; font-size: 13px; font-weight: 800; letter-spacing: 2px; padding: 6px 18px; border-radius: 999px; }
        .detail-row { display: flex; gap: 8px; font-size: 13px; margin-bottom: 6px; }
        .detail-label { font-weight: 700; color: #065321; min-width: 130px; }
        .feedback-box { background: #f6fbf4; border-left: 4px solid #0b6b2c; border-radius: 8px; padding: 16px 18px; white-space: pre-wrap; font-size: 14px; color: #163223; }
        .btn-submit { background: linear-gradient(135deg, #15803d, #065321); color: #fff; border: none; padding: 12px 32px; border-radius: 999px; font-weight: 700; }
        .btn-submit:hover { background: #065321; color: #fff; }
        textarea { resize: vertical; min-height: 140px; }
    </style>
</head>
<body>
    <div class="accent-top"></div>
    <div class="brand-bar">
        <div class="brand-logo">C</div>
        <div class="brand-text">
            <h6>CCBRT Hospital</h6>
            <small>Quality Assurance Department &nbsp;·&nbsp; Escalation Response Portal</small>
        </div>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="mb-4">
                    <span class="ref-badge">{{ $escalation->reference }}</span>
                    <h4 class="mt-3 fw-bold" style="color:#065321;">Respond to Escalated Feedback</h4>
                    <p class="text-muted">Please review the feedback details below and submit your official response.</p>
                </div>

                {{-- Feedback Summary --}}
                <div class="card mb-4">
                    <div class="card-header fw-semibold py-3" style="background:#f6fbf4; color:#065321; border-bottom:1px solid #ddeedd;">
                        <i class="bi bi-clipboard-data me-2"></i> Feedback Details
                    </div>
                    <div class="card-body">
                        <div class="detail-row"><span class="detail-label">Reference:</span> {{ $escalation->feedback->reference_no }}</div>
                        <div class="detail-row"><span class="detail-label">Type:</span> {{ $escalation->feedback->getFeedbackTypeLabel() }}</div>
                        <div class="detail-row"><span class="detail-label">Category:</span> {{ $escalation->feedback->getServiceCategoryLabel() }}</div>
                        <div class="detail-row"><span class="detail-label">Submitted:</span> {{ $escalation->feedback->created_at->format('d M Y, H:i') }}</div>
                        <div class="detail-row"><span class="detail-label">Escalated:</span> {{ $escalation->escalated_at->format('d M Y, H:i') }}</div>

                        <div class="mt-3 mb-1 fw-semibold" style="color:#065321; font-size:13px;">Feedback Content:</div>
                        <div class="feedback-box">{{ $escalation->feedback->overall_experience ?: $escalation->feedback->message }}</div>

                        @if($escalation->message)
                        <div class="mt-3 mb-1 fw-semibold" style="color:#065321; font-size:13px;">Message from Quality Assurance Team:</div>
                        <div class="feedback-box">{{ $escalation->message }}</div>
                        @endif
                    </div>
                </div>

                {{-- Response Form --}}
                <div class="card">
                    <div class="card-header fw-semibold py-3" style="background:#f6fbf4; color:#065321; border-bottom:1px solid #ddeedd;">
                        <i class="bi bi-reply me-2"></i> Your Response
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('escalations.submit', $escalation->token) }}">
                            @csrf

                            @if($errors->any())
                            <div class="alert alert-danger mb-3">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Your Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="hod_name" class="form-control @error('hod_name') is-invalid @enderror"
                                       value="{{ old('hod_name', $escalation->hod->name) }}"
                                       placeholder="Confirm your name">
                                @error('hod_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Your Response <span class="text-danger">*</span></label>
                                <textarea name="hod_response" class="form-control @error('hod_response') is-invalid @enderror"
                                          placeholder="Provide your detailed response to this feedback...">{{ old('hod_response') }}</textarea>
                                @error('hod_response')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">Minimum 10 characters. Be specific and professional.</div>
                            </div>

                            <div class="d-flex align-items-center gap-3">
                                <button type="submit" class="btn btn-submit">
                                    <i class="bi bi-send me-1"></i> Submit Response
                                </button>
                                <span class="text-muted small">This action cannot be undone.</span>
                            </div>
                        </form>
                    </div>
                </div>

                <p class="text-center text-muted small mt-4">
                    CCBRT Hospital &mdash; Escalation Reference: {{ $escalation->reference }}<br>
                    This page is confidential and accessible only to authorized personnel.
                </p>

            </div>
        </div>
    </div>
</body>
</html>
