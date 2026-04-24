<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Response to Your Feedback – CCBRT Hospital</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #eef4eb;
            padding: 32px 16px;
            color: #163223;
        }
        .wrapper {
            max-width: 620px;
            margin: 0 auto;
        }

        /* ── TOP BAR ── */
        .topbar {
            background: #94c83d;
            height: 4px;
            border-radius: 4px 4px 0 0;
        }

        /* ── HEADER ── */
        .header {
            background: linear-gradient(160deg, #065321 0%, #0b6b2c 100%);
            color: #ffffff;
            padding: 20px 28px;
            text-align: left;
            display: block;
        }
        .header-inner {
            display: table;
            width: 100%;
        }
        .header-logo-cell {
            display: table-cell;
            vertical-align: middle;
            width: 52px;
        }
        .header-logo {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255,255,255,0.12);
            display: block;
            line-height: 44px;
            text-align: center;
            font-size: 20px;
            font-weight: 900;
            color: #add95a;
            font-family: Arial, sans-serif;
        }
        .header-text-cell {
            display: table-cell;
            vertical-align: middle;
            padding-left: 12px;
        }
        .header-title {
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.2px;
            line-height: 1.2;
        }
        .header-sub {
            font-size: 11px;
            color: rgba(255,255,255,0.65);
            letter-spacing: 0.8px;
            margin-top: 2px;
        }

        /* ── BODY ── */
        .body {
            background: #ffffff;
            padding: 32px;
            line-height: 1.75;
            font-size: 15px;
            color: #163223;
        }

        /* ── REFERENCE BOX ── */
        .ref-box {
            background: linear-gradient(135deg, #065321 0%, #0b6b2c 100%);
            color: #ffffff;
            border-radius: 12px;
            padding: 20px 24px;
            margin: 24px 0;
            text-align: center;
        }
        .ref-label {
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            opacity: 0.75;
            margin-bottom: 6px;
        }
        .ref-number {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: 3px;
            color: #add95a;
        }

        /* ── STATUS CHIP ── */
        .status-row {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #eef7e8;
            border-left: 4px solid #94c83d;
            border-radius: 8px;
            padding: 14px 18px;
            margin: 20px 0;
            font-size: 14px;
        }
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #15803d;
            flex-shrink: 0;
        }

        /* ── RESPONSE BOX ── */
        .response-heading {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #0b6b2c;
            margin-bottom: 10px;
        }
        .response-box {
            background: #f6fbf4;
            border: 1px solid #c8e0c2;
            border-left: 4px solid #0b6b2c;
            border-radius: 10px;
            padding: 20px 22px;
            white-space: pre-wrap;
            font-size: 15px;
            line-height: 1.8;
            color: #163223;
        }

        /* ── CTA BUTTON ── */
        .btn-wrap {
            text-align: center;
            margin: 28px 0 8px;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #15803d, #065321);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        /* ── DIVIDER ── */
        .divider {
            border: none;
            border-top: 1px solid #ddeedd;
            margin: 28px 0;
        }

        /* ── SIGNATURE ── */
        .signature {
            font-size: 14px;
            color: #163223;
        }
        .signature strong {
            display: block;
            font-size: 15px;
            color: #0b6b2c;
            margin-bottom: 2px;
        }
        .signature .dept {
            font-weight: 700;
            color: #065321;
        }

        /* ── FOOTER ── */
        .footer {
            background: #065321;
            color: rgba(255,255,255,0.80);
            padding: 24px 32px;
            font-size: 12px;
            text-align: center;
            line-height: 1.7;
        }
        .footer a { color: #add95a; text-decoration: none; }
        .footer .accent-line {
            border-top: 1px solid rgba(148,200,61,0.3);
            margin: 14px 0;
        }
        .bottombar {
            background: #94c83d;
            height: 5px;
            border-radius: 0 0 6px 6px;
        }

        @media only screen and (max-width: 600px) {
            .body, .header, .footer { padding: 22px 18px; }
            .ref-number { font-size: 20px; }
        }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="topbar"></div>

    <!-- HEADER -->
    <div class="header">
        <div class="header-inner">
            <div class="header-logo-cell">
                <span class="header-logo">C</span>
            </div>
            <div class="header-text-cell">
                <div class="header-title">CCBRT Hospital</div>
                <div class="header-sub">Quality Assurance Department &nbsp;·&nbsp; Feedback Response</div>
            </div>
        </div>
    </div>

    <!-- BODY -->
    <div class="body">
        <p>Dear <strong>{{ $patientName }}</strong>,</p>

        <p style="margin-top:14px; color:#4a6155;">
            We have reviewed your feedback and prepared a response for you below.
        </p>

        <!-- Reference -->
        <div class="ref-box">
            <div class="ref-label">Your Feedback Reference</div>
            <div class="ref-number">{{ $referenceNo }}</div>
        </div>

        <!-- Status -->
        <div class="status-row">
            <div class="status-dot"></div>
            <span><strong>Current Status:</strong>&nbsp; {{ $statusLabel }}</span>
        </div>

        <!-- Response -->
        <div class="response-heading">&#10003;&nbsp; Our Official Response</div>
        <div class="response-box">{{ $responseContent }}</div>

        <!-- CTA -->
        <div class="btn-wrap">
            <a href="{{ $trackUrl }}" class="btn">View on Feedback Portal &rarr;</a>
        </div>

        <hr class="divider">

        <!-- Signature -->
        <div class="signature">
            <p>We value your feedback as it helps us continuously improve the quality of care
            and services we deliver to our patients and community. If you have any further
            concerns, please do not hesitate to reach out to us.</p>

            <p style="margin-top:20px;">
                Warm regards,<br><br>
                <strong>Quality Assurance Department</strong>
                <span class="dept">CCBRT Hospital</span>
                Comprehensive Community Based Rehabilitation in Tanzania<br>
                <a href="mailto:feedback@ccbrt.org" style="color:#0b6b2c;">feedback@ccbrt.org</a>
                &nbsp;|&nbsp; +255 22 277 5000
            </p>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div><strong style="color:#fff;">CCBRT Hospital</strong> &mdash; Comprehensive Community Based Rehabilitation in Tanzania</div>
        <div class="accent-line"></div>
        <div>
            Dar es Salaam, Tanzania &nbsp;|&nbsp;
            <a href="mailto:feedback@ccbrt.org">feedback@ccbrt.org</a> &nbsp;|&nbsp;
            +255 22 277 5000
        </div>
        <div style="margin-top:10px; font-size:11px; opacity:0.65;">
            This email was sent in response to feedback submitted through the CCBRT Customer Feedback Portal.
            Please do not reply directly to this email.
        </div>
    </div>

    <div class="bottombar"></div>

</div>
</body>
</html>
