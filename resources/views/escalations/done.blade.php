<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Response Submitted – CCBRT Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #eef4eb; font-family: Arial, Helvetica, sans-serif; min-height: 100vh; display: flex; flex-direction: column; }
        .accent-top { height: 4px; background: #94c83d; }
        .brand-bar { background: linear-gradient(135deg, #065321, #0b6b2c); color: #fff; padding: 14px 24px; display: flex; align-items: center; gap: 14px; }
        .brand-logo { width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 900; color: #add95a; flex-shrink: 0; }
        .brand-text h6 { margin: 0; font-size: 14px; font-weight: 700; }
        .brand-text small { font-size: 11px; opacity: 0.65; }
        .check-circle { width: 72px; height: 72px; border-radius: 50%; background: #eef7e8; border: 3px solid #94c83d; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
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

    <div class="container py-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div class="card border-0 shadow-sm p-5">
                    <div class="check-circle">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#065321" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <h4 class="fw-bold mb-2" style="color:#065321;">Response Submitted</h4>
                    <p class="text-muted mb-4">
                        Thank you. Your response to escalation <strong>{{ $escalation->reference }}</strong>
                        has been recorded and will be reviewed by the Quality Assurance team.
                    </p>
                    <div class="p-3 rounded" style="background:#f6fbf4; border:1px solid #c8e0c2; font-size:13px; color:#163223;">
                        <strong>Feedback Reference:</strong> {{ $escalation->feedback->reference_no }}<br>
                        <strong>Responded At:</strong> {{ $escalation->responded_at?->format('d M Y, H:i') ?? now()->format('d M Y, H:i') }}
                    </div>
                    <p class="text-muted small mt-4">You may now close this window.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
