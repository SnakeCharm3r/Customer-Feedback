<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You're Invited to Join CCBRT</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 32px 16px;
            background-color: #eef4eb;
            color: #163223;
            font-family: Arial, Helvetica, sans-serif;
            -webkit-text-size-adjust: 100%;
        }
        .wrapper { width: 100%; max-width: 620px; margin: 0 auto; }
        .topbar { height: 5px; background-color: #94c83d; border-radius: 6px 6px 0 0; }
        .header { padding: 22px 28px; background-color: #065321; color: #ffffff; }
        .header-table { width: 100%; border-collapse: collapse; }
        .logo-cell { width: 58px; vertical-align: middle; }
        .logo-mark {
            display: block;
            width: 46px;
            height: 46px;
            border: 1px solid rgba(255,255,255,0.22);
            border-radius: 50%;
            background-color: #0b6b2c;
            color: #add95a;
            font-size: 21px;
            font-weight: 800;
            line-height: 46px;
            text-align: center;
        }
        .brand-cell { vertical-align: middle; }
        .brand-name { margin: 0; color: #ffffff; font-size: 18px; font-weight: 700; line-height: 1.25; }
        .brand-subtitle { margin: 4px 0 0; color: #d9ebca; font-size: 11px; letter-spacing: 0.7px; line-height: 1.4; }
        .content { padding: 34px 32px; background-color: #ffffff; font-size: 15px; line-height: 1.75; }
        .eyebrow { margin: 0 0 8px; color: #15803d; font-size: 11px; font-weight: 700; letter-spacing: 1.4px; text-transform: uppercase; }
        .title { margin: 0 0 18px; color: #065321; font-size: 24px; font-weight: 700; line-height: 1.3; }
        .message { margin: 0 0 16px; color: #4a6155; }
        .access-panel {
            margin: 24px 0;
            padding: 18px 20px;
            border: 1px solid #c8e0c2;
            border-left: 4px solid #94c83d;
            border-radius: 9px;
            background-color: #f6fbf4;
        }
        .access-panel-title { margin: 0 0 8px; color: #065321; font-size: 14px; font-weight: 700; }
        .access-panel p { margin: 0; color: #4a6155; font-size: 13px; line-height: 1.65; }
        .button-table { margin: 28px auto 24px; border-collapse: separate; }
        .button-cell { border-radius: 999px; background-color: #15803d; text-align: center; }
        .button-link {
            display: inline-block;
            padding: 14px 34px;
            border: 1px solid #15803d;
            border-radius: 999px;
            background-color: #15803d;
            color: #ffffff !important;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.2;
            text-align: center;
            text-decoration: none !important;
        }
        .button-text { color: #ffffff !important; text-decoration: none !important; }
        .security-note {
            margin: 0 0 24px;
            padding: 14px 16px;
            border-radius: 8px;
            background-color: #eef7e8;
            color: #365642;
            font-size: 13px;
            line-height: 1.6;
        }
        .security-note strong { color: #065321; }
        .fallback { margin: 22px 0 0; padding-top: 20px; border-top: 1px solid #ddeedd; color: #64746b; font-size: 12px; line-height: 1.6; }
        .fallback a { color: #0b6b2c; word-break: break-all; }
        .signature { margin: 24px 0 0; color: #4a6155; }
        .signature strong { color: #065321; }
        .footer { padding: 24px 28px; background-color: #065321; color: #cfe4c4; font-size: 12px; line-height: 1.7; text-align: center; }
        .footer strong { color: #ffffff; }
        .footer-divider { margin: 14px 0; border-top: 1px solid rgba(148,200,61,0.3); }
        .bottombar { height: 5px; background-color: #94c83d; border-radius: 0 0 6px 6px; }

        @media only screen and (max-width: 600px) {
            body { padding: 16px 8px; }
            .header, .footer { padding: 20px; }
            .content { padding: 28px 20px; }
            .title { font-size: 21px; }
            .button-link { padding: 14px 26px; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="topbar"></div>

    <div class="header">
        <table role="presentation" class="header-table">
            <tr>
                <td class="logo-cell"><span class="logo-mark">C</span></td>
                <td class="brand-cell">
                    <p class="brand-name">CCBRT Hospital</p>
                    <p class="brand-subtitle">QUALITY ASSURANCE &nbsp;&middot;&nbsp; CUSTOMER FEEDBACK PORTAL</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="content">
        <p class="eyebrow">Staff Account Invitation</p>
        <h1 class="title">Welcome to the CCBRT Feedback Workspace</h1>

        <p class="message">Hello <strong style="color:#065321;">{{ $userName }}</strong>,</p>
        <p class="message">
            You have been invited to join the CCBRT Customer Feedback Management System.
            This secure workspace helps authorized staff track, manage, and respond to patient feedback.
        </p>

        <div class="access-panel">
            <p class="access-panel-title">Complete your account setup</p>
            <p>Use the secure button below to create your password and activate access to your staff workspace.</p>
        </div>

        <table role="presentation" class="button-table" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td class="button-cell" bgcolor="#15803d">
                    <a href="{{ $resetUrl }}"
                       class="button-link"
                       style="display:inline-block;background-color:#15803d;border:1px solid #15803d;border-radius:999px;color:#ffffff !important;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:700;line-height:1.2;padding:14px 34px;text-align:center;text-decoration:none !important;">
                        <span class="button-text" style="color:#ffffff !important;text-decoration:none !important;">Set Your Password &rarr;</span>
                    </a>
                </td>
            </tr>
        </table>

        <div class="security-note">
            <strong>Secure invitation:</strong> This password setup link expires in 60 minutes and should not be shared with anyone.
        </div>

        <p class="message">If you did not expect this invitation, you can safely ignore this email.</p>

        <p class="signature">
            Warm regards,<br>
            <strong>Quality Assurance Department</strong><br>
            CCBRT Hospital
        </p>

    </div>

    <div class="footer">
        <strong>CCBRT Hospital</strong> &mdash; Comprehensive Community Based Rehabilitation in Tanzania
        <div class="footer-divider"></div>
        <div>This is an automated account invitation from the CCBRT Customer Feedback Portal.</div>
    </div>

    <div class="bottombar"></div>
</div>
</body>
</html>
