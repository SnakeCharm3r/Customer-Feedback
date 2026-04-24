<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Action Required – Feedback Escalation {{ $escalation->reference }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; background: #eef4eb; padding: 28px 16px; color: #163223; }
        .wrapper { max-width: 620px; margin: 0 auto; }
        .topbar { background: #94c83d; height: 4px; border-radius: 4px 4px 0 0; }
        .header { background: linear-gradient(160deg, #065321 0%, #0b6b2c 100%); color: #fff; padding: 20px 28px; }
        .header-inner { display: table; width: 100%; }
        .header-logo-cell { display: table-cell; vertical-align: middle; width: 52px; }
        .header-logo { width: 44px; height: 44px; border-radius: 50%; background: rgba(255,255,255,0.12); display: block; line-height: 44px; text-align: center; font-size: 20px; font-weight: 900; color: #add95a; }
        .header-text-cell { display: table-cell; vertical-align: middle; padding-left: 12px; }
        .header-title { font-size: 15px; font-weight: 700; color: #fff; }
        .header-sub { font-size: 11px; color: rgba(255,255,255,0.65); letter-spacing: 0.8px; margin-top: 2px; }
        .body { background: #fff; padding: 32px; font-size: 15px; line-height: 1.75; }
        .alert-box { background: #fff8e6; border-left: 4px solid #f59e0b; border-radius: 8px; padding: 14px 18px; margin: 20px 0; font-size: 14px; }
        .ref-box { background: linear-gradient(135deg, #065321, #0b6b2c); color: #fff; border-radius: 12px; padding: 18px 22px; margin: 22px 0; text-align: center; }
        .ref-label { font-size: 11px; letter-spacing: 2px; text-transform: uppercase; opacity: 0.7; margin-bottom: 4px; }
        .ref-number { font-size: 22px; font-weight: 800; letter-spacing: 3px; color: #add95a; }
        .detail-table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 14px; }
        .detail-table td { padding: 10px 14px; border-bottom: 1px solid #eef4eb; }
        .detail-table td:first-child { font-weight: 700; color: #065321; width: 38%; }
        .message-box { background: #f6fbf4; border: 1px solid #c8e0c2; border-left: 4px solid #0b6b2c; border-radius: 10px; padding: 18px 20px; white-space: pre-wrap; font-size: 14px; margin: 16px 0; }
        .btn-wrap { text-align: center; margin: 28px 0 8px; }
        .btn { display: inline-block; background: linear-gradient(135deg, #15803d, #065321); color: #fff !important; text-decoration: none; padding: 14px 32px; border-radius: 999px; font-weight: 700; font-size: 14px; }
        .divider { border: none; border-top: 1px solid #ddeedd; margin: 24px 0; }
        .footer { background: #065321; color: rgba(255,255,255,0.75); padding: 20px 28px; font-size: 12px; text-align: center; line-height: 1.7; }
        .footer a { color: #add95a; text-decoration: none; }
        .bottombar { background: #94c83d; height: 4px; border-radius: 0 0 4px 4px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="topbar"></div>

    <div class="header">
        <div class="header-inner">
            <div class="header-logo-cell"><span class="header-logo">C</span></div>
            <div class="header-text-cell">
                <div class="header-title">CCBRT Hospital</div>
                <div class="header-sub">Quality Assurance Department &nbsp;·&nbsp; Escalation Notice</div>
            </div>
        </div>
    </div>

    <div class="body">
        <p>Dear <strong>{{ $hod->name }}</strong>,</p>

        <p style="margin-top:14px; color:#4a6155;">
            A customer feedback item has been escalated to your department for review and action.
            Please respond using the secure link below.
        </p>

        <div class="alert-box">
            <strong>&#9888; Action Required</strong> — Please respond within the expected timeframe for your department.
        </div>

        <div class="ref-box">
            <div class="ref-label">Escalation Reference</div>
            <div class="ref-number">{{ $escalation->reference }}</div>
        </div>

        <table class="detail-table">
            <tr><td>Feedback Reference</td><td>{{ $feedback->reference_no }}</td></tr>
            <tr><td>Feedback Type</td><td>{{ $feedback->getFeedbackTypeLabel() }}</td></tr>
            <tr><td>Service Category</td><td>{{ $feedback->getServiceCategoryLabel() }}</td></tr>
            @if($feedback->location)
            <tr><td>Location</td><td>{{ $feedback->location }}</td></tr>
            @endif
            <tr><td>Submitted</td><td>{{ $feedback->created_at->format('d M Y, H:i') }}</td></tr>
            <tr><td>Escalated To</td><td>{{ $hod->name }} &mdash; {{ $hod->department }}</td></tr>
            <tr><td>Escalated On</td><td>{{ $escalation->escalated_at->format('d M Y, H:i') }}</td></tr>
        </table>

        <p><strong style="color:#065321;">Feedback Content:</strong></p>
        <div class="message-box">{{ $feedback->overall_experience ?: $feedback->message }}</div>

        @if($escalation->message)
        <p style="margin-top:16px;"><strong style="color:#065321;">Message from Quality Assurance Team:</strong></p>
        <div class="message-box">{{ $escalation->message }}</div>
        @endif

        <div class="btn-wrap">
            <a href="{{ $respondUrl }}" class="btn">Submit Your Response &rarr;</a>
        </div>
        <p style="text-align:center; font-size:12px; color:#888; margin-top:8px;">
            This link is unique to you. Do not share it with others.
        </p>

        <hr class="divider">

        <p style="font-size:13px; color:#4a6155;">
            Warm regards,<br><br>
            <strong style="color:#065321;">Quality Assurance Department</strong><br>
            CCBRT Hospital &mdash; Comprehensive Community Based Rehabilitation in Tanzania<br>
            <a href="mailto:edoc@ccbrt.ccbrt.org" style="color:#0b6b2c;">edoc@ccbrt.ccbrt.org</a>
        </p>
    </div>

    <div class="footer">
        <div><strong style="color:#fff;">CCBRT Hospital</strong> &mdash; Escalation Matrix System</div>
        <div style="margin-top:8px; font-size:11px; opacity:0.6;">
            This is a confidential internal escalation. Do not forward this email.
        </div>
    </div>
    <div class="bottombar"></div>
</div>
</body>
</html>
