{{-- app/Modules/Donations/Views/receipt-pdf.blade.php --}}
{{-- Rendered by DomPDF via SendTaxReceiptJob --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Tax Receipt {{ $receipt->receipt_number }}</title>
    <style>
        /* DomPDF works best with inline styles and simple CSS */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            color: #1a1a2e;
            background: #ffffff;
            padding: 0;
            margin: 0;
        }

        /* ── Header band ── */
        .header {
            background: #0b1a75;
            padding: 28px 40px;
            color: #ffffff;
        }
        .header-inner {
            display: table;
            width: 100%;
        }
        .header-left {
            display: table-cell;
            vertical-align: middle;
            width: 60%;
        }
        .header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 40%;
        }
        .org-name {
            font-size: 20pt;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #ffffff;
            margin-bottom: 2px;
        }
        .org-sub {
            font-size: 8pt;
            color: #21b7e7;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .receipt-title {
            font-size: 10pt;
            color: #21b7e7;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .receipt-number {
            font-size: 15pt;
            font-weight: bold;
            color: #ffffff;
        }

        /* ── DGR badge ── */
        .dgr-band {
            background: #21b7e7;
            padding: 7px 40px;
            font-size: 8pt;
            color: #0b1a75;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* ── Body ── */
        .body {
            padding: 32px 40px;
        }

        /* ── Info row: two columns ── */
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 24px;
        }
        .info-col {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }
        .info-col-right {
            text-align: right;
        }

        .info-label {
            font-size: 7pt;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #8492b4;
            margin-bottom: 3px;
        }
        .info-value {
            font-size: 10.5pt;
            color: #0b1a75;
            font-weight: bold;
        }
        .info-value-light {
            font-size: 10pt;
            color: #1a1a2e;
            font-weight: normal;
        }

        /* ── Divider ── */
        .divider {
            border: none;
            border-top: 1.5px solid #dde3f0;
            margin: 20px 0;
        }

        /* ── Amount box ── */
        .amount-box {
            background: #f4f6fb;
            border: 2px solid #dde3f0;
            border-radius: 8px;
            padding: 20px 28px;
            margin: 24px 0;
            display: table;
            width: 100%;
        }
        .amount-left {
            display: table-cell;
            vertical-align: middle;
            width: 60%;
        }
        .amount-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 40%;
        }
        .amount-label {
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #8492b4;
            margin-bottom: 4px;
        }
        .amount-value {
            font-size: 26pt;
            font-weight: bold;
            color: #0b1a75;
        }
        .amount-currency {
            font-size: 12pt;
            color: #8492b4;
            margin-left: 4px;
        }
        .amount-type {
            font-size: 9pt;
            color: #2683d4;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ── In memory block ── */
        .tribute-box {
            border-left: 3px solid #21b7e7;
            padding: 8px 14px;
            margin: 16px 0;
            background: #f0f8ff;
            border-radius: 0 6px 6px 0;
        }
        .tribute-label {
            font-size: 8pt;
            color: #8492b4;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }
        .tribute-name {
            font-size: 11pt;
            font-weight: bold;
            color: #0b1a75;
        }

        /* ── Legal text ── */
        .legal {
            margin-top: 28px;
            padding: 16px;
            background: #f4f6fb;
            border-radius: 6px;
            font-size: 8pt;
            color: #8492b4;
            line-height: 1.6;
        }
        .legal strong { color: #0b1a75; }

        /* ── Footer ── */
        .footer {
            margin-top: 32px;
            padding-top: 14px;
            border-top: 1px solid #dde3f0;
            display: table;
            width: 100%;
        }
        .footer-left {
            display: table-cell;
            vertical-align: bottom;
            width: 60%;
            font-size: 8pt;
            color: #8492b4;
            line-height: 1.6;
        }
        .footer-right {
            display: table-cell;
            vertical-align: bottom;
            text-align: right;
            width: 40%;
        }
        .footer-logo {
            font-size: 14pt;
            font-weight: bold;
            color: #0b1a75;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .footer-tagline {
            font-size: 7pt;
            color: #21b7e7;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

    {{-- ── Header ── --}}
    <div class="header">
        <div class="header-inner">
            <div class="header-left">
                <div class="org-name">{{ $receipt->org_name }}</div>
                @if($receipt->org_abn)
                    <div class="org-sub">ABN {{ $receipt->org_abn }}</div>
                @endif
            </div>
            <div class="header-right">
                <div class="receipt-title">Tax Receipt</div>
                <div class="receipt-number">{{ $receipt->receipt_number }}</div>
            </div>
        </div>
    </div>

    {{-- ── DGR band ── --}}
    @if($receipt->org_is_dgr)
        <div class="dgr-band">
            ✓ &nbsp;Deductible Gift Recipient (DGR) — Donations of $2 or more may be tax deductible
        </div>
    @endif

    {{-- ── Body ── --}}
    <div class="body">

        {{-- Issued to / Issued on --}}
        <div class="info-row">
            <div class="info-col">
                <div class="info-label">Receipt Issued To</div>
                <div class="info-value">{{ $receipt->donor_name ?? 'Valued Donor' }}</div>
                @if($receipt->donor_email)
                    <div class="info-value-light">{{ $receipt->donor_email }}</div>
                @endif
                @if($receipt->donor_address)
                    <div class="info-value-light">{{ $receipt->donor_address }}</div>
                @endif
            </div>
            <div class="info-col info-col-right">
                <div class="info-label">Date Issued</div>
                <div class="info-value">{{ $receipt->created_at->format('d F Y') }}</div>
                <div style="margin-top: 10px;">
                    <div class="info-label">Financial Year</div>
                    <div class="info-value">{{ $receipt->financial_year }}</div>
                </div>
            </div>
        </div>

        <hr class="divider">

        {{-- Amount box --}}
        <div class="amount-box">
            <div class="amount-left">
                <div class="amount-label">Donation Amount</div>
                <div class="amount-type">
                    @if($receipt->donation->type === 'one_off') One-Off Donation
                    @elseif($receipt->donation->type === 'recurring') Recurring Donation — {{ ucfirst($receipt->donation->plan?->frequency ?? '') }}
                    @elseif($receipt->donation->type === 'in_memory') In Memory Donation
                    @endif
                </div>
            </div>
            <div class="amount-right">
                <div class="amount-value">
                    ${{ number_format($receipt->amount, 2) }}<span class="amount-currency">{{ $receipt->currency }}</span>
                </div>
            </div>
        </div>

        {{-- In Memory tribute block --}}
        @if($receipt->donation->isInMemory() && $receipt->donation->tribute_name)
            <div class="tribute-box">
                <div class="tribute-label">
                    {{ $receipt->donation->tribute_type === 'in_honour' ? 'In Honour of' : 'In Memory of' }}
                </div>
                <div class="tribute-name">{{ $receipt->donation->tribute_name }}</div>
            </div>
        @endif

        {{-- Org details --}}
        <hr class="divider">

        <div class="info-row">
            <div class="info-col">
                <div class="info-label">Organisation</div>
                <div class="info-value">{{ $receipt->org_name }}</div>
                @if($receipt->org_address)
                    <div class="info-value-light">{{ $receipt->org_address }}</div>
                @endif
            </div>
            @if($receipt->org_abn)
            <div class="info-col info-col-right">
                <div class="info-label">ABN</div>
                <div class="info-value">{{ $receipt->org_abn }}</div>
            </div>
            @endif
        </div>

        {{-- Legal note --}}
        <div class="legal">
            <strong>Please retain this receipt for tax purposes.</strong>
            @if($receipt->org_is_dgr)
                This organisation is registered as a Deductible Gift Recipient (DGR).
                Donations of $2 or more to DGR organisations may be claimed as a tax deduction
                in your Australian income tax return. Please consult your tax advisor for guidance
                specific to your circumstances.
            @else
                Please consult your tax advisor to determine the deductibility of this donation
                in your circumstances.
            @endif
            This receipt was issued on {{ $receipt->created_at->format('d F Y') }} and is valid
            for the {{ $receipt->financial_year }} Australian financial year (1 July – 30 June).
        </div>

        {{-- Footer --}}
        <div class="footer">
            <div class="footer-left">
                {{ $receipt->org_name }}<br>
                @if($receipt->org_address){{ $receipt->org_address }}<br>@endif
                @if($receipt->org_abn)ABN: {{ $receipt->org_abn }}@endif
            </div>
            <div class="footer-right">
                <div class="footer-logo">{{ $receipt->org_name }}</div>
                <div class="footer-tagline">Official Tax Receipt</div>
            </div>
        </div>

    </div>

</body>
</html>
