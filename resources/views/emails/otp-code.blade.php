<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Launch Pad verification code</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        /* Basic reset */
        html, body {
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #f3f4f6; /* soft gray */
            color: #111827; /* neutral dark */
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI",
                Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        a {
            color: #2563eb;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .wrapper {
            width: 100%;
            margin: 0;
            padding: 24px 12px;
            background-color: #f3f4f6;
        }

        .container {
            max-width: 560px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }

        .header {
            padding: 16px 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-mark {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: #111827; /* subtle dark "tile" like Microsoft account icon */
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #f9fafb;
            font-weight: 600;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
        }

        .brand-name {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
        }

        .brand-sub {
            font-size: 11px;
            color: #6b7280;
        }

        .header-right {
            font-size: 11px;
            color: #6b7280;
            text-align: right;
        }

        .body {
            padding: 24px;
        }

        .title {
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 4px;
        }

        .subtitle {
            font-size: 13px;
            color: #4b5563;
            margin: 0 0 20px;
        }

        .code-block {
            margin: 20px 0 16px;
            padding: 16px 18px;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }

        .code-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .code-value {
            font-size: 24px;
            letter-spacing: 0.24em;
            font-weight: 700;
            color: #111827;
        }

        .meta {
            font-size: 12px;
            color: #4b5563;
            margin: 0 0 4px;
        }

        .meta-strong {
            font-weight: 500;
            color: #111827;
        }

        .btn-row {
            margin: 18px 0 10px;
        }

        .btn-primary {
            display: inline-block;
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 500;
            border-radius: 4px;
            border: 1px solid #2563eb;
            background-color: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
        }

        .btn-primary:hover {
            text-decoration: none;
        }

        .hint {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }

        .divider {
            margin: 20px 0 16px;
            border: none;
            border-top: 1px solid #e5e7eb;
        }

        .small-heading {
            font-size: 12px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 6px;
        }

        .list {
            font-size: 11px;
            color: #6b7280;
            margin: 0;
            padding-left: 16px;
        }

        .footer {
            padding: 16px 24px 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 11px;
            color: #9ca3af;
        }

        .footer-top {
            margin-bottom: 4px;
        }

        .footer-app {
            font-weight: 500;
            color: #6b7280;
        }

        .footer-meta {
            margin-top: 4px;
        }

        @media (max-width: 600px) {
            .container {
                border-radius: 0;
            }

            .header,
            .body,
            .footer {
                padding-left: 16px;
                padding-right: 16px;
            }

            .code-value {
                font-size: 22px;
                letter-spacing: 0.18em;
            }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="container">

        <!-- Header -->
        <div class="header">
            <div class="brand-left">
                <div class="brand-mark">
                    LP
                </div>
                <div class="brand-text">
                    <div class="brand-name">Launch Pad</div>
                    <div class="brand-sub">Assessment & analytics platform</div>
                </div>
            </div>
            <div class="header-right">
                <div>Security notification</div>
                <div>{{ now()->format('M j, Y') }}</div>
            </div>
        </div>

        <!-- Body -->
        <div class="body">
            <h1 class="title">Your verification code</h1>
            <p class="subtitle">
                Use the code below to verify your identity for
                <span class="meta-strong">{{ ucfirst($purpose) }}</span> on Launch Pad.
            </p>

            <div class="code-block">
                <div class="code-label">One-time code</div>
                <div class="code-value">
                    {{ trim(chunk_split($code, 3, ' ')) }}
                </div>
            </div>

            <p class="meta">
                This code will expire in
                <span class="meta-strong">{{ $ttlMinutes }} minutes</span>.
            </p>
            <p class="meta">
                For your security, do not share this code with anyone.
            </p>

            <div class="btn-row">
                {{-- Optional primary action – you can wire this to a deep link/URL if you have one --}}
                <a href="{{ config('app.frontend_url') }}" class="btn-primary">
                    Continue to Launch Pad
                </a>
                <div class="hint">
                    If the button doesn’t work, you can safely return to Launch Pad and enter your code manually.
                </div>
            </div>

            <hr class="divider">

            <div class="small-heading">What’s happening?</div>
            <ul class="list">
                <li>Someone (possibly you) requested a verification code for your Launch Pad account.</li>
                <li>If this was you, enter the code on the verification screen to continue.</li>
                <li>If you didn’t request this, you can ignore this email.</li>
            </ul>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-top">
                You’re receiving this email because security is important on
                <span class="footer-app">Launch Pad</span>.
            </div>
            <div class="footer-meta">
                {{ config('app.name') }} &middot; {{ config('app.url') }}
            </div>
        </div>

    </div>
</div>
</body>
</html>
