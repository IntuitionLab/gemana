{{-- app/Modules/Donations/Views/receipt-email.blade.php --}}
{{-- HTML email sent to donor with PDF attached --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your Tax Receipt — {{ $receipt->receipt_number }}</title>
    <!--[if mso]><noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript><![endif]-->
    <style>
        /* Email-safe reset */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        body { margin: 0 !important; padding: 0 !important; background-color: #f4f6fb; }
        * { box-sizing: border-box; }
    </style>
</head>
<body style="margin:0; padding:0; background-color:#f4f6fb; font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6fb; padding: 32px 16px;">
        <tr>
            <td align="center">

                {{-- ── Outer container ── --}}
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%;">

                    {{-- ── Header ── --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #0b1a75 0%, #2025b1 60%, #4d1e99 100%); border-radius: 14px 14px 0 0; padding: 28px 36px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <p style="margin:0; font-size:20px; font-weight:800; color:#ffffff; letter-spacing:3px; text-transform:uppercase;">
                                            {{ $receipt->org_name }}
                                        </p>
                                        @if($receipt->org_abn)
                                        <p style="margin:4px 0 0; font-size:10px; color:#21b7e7; letter-spacing:1.5px; text-transform:uppercase;">
                                            ABN {{ $receipt->org_abn }}
                                        </p>
                                        @endif
                                    </td>
                                    <td align="right" style="vertical-align:top;">
                                        <p style="margin:0; font-size:10px; color:#21b7e7; letter-spacing:2px; text-transform:uppercase;">Tax Receipt</p>
                                        <p style="margin:4px 0 0; font-size:16px; font-weight:700; color:#ffffff;">{{ $receipt->receipt_number }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ── DGR band ── --}}
                    @if($receipt->org_is_dgr)
                    <tr>
                        <td style="background:#21b7e7; padding: 8px 36px;">
                            <p style="margin:0; font-size:10px; color:#0b1a75; font-weight:700; letter-spacing:1px; text-transform:uppercase;">
                                ✓ &nbsp; Deductible Gift Recipient (DGR) — Donations of $2 or more may be tax deductible
                            </p>
                        </td>
                    </tr>
                    @endif

                    {{-- ── White body ── --}}
                    <tr>
                        <td style="background:#ffffff; padding: 36px 36px 28px; border-radius: 0 0 14px 14px; box-shadow: 0 8px 32px rgba(11,26,117,0.08);">

                            {{-- Greeting --}}
                            <p style="margin:0 0 8px; font-size:22px; font-weight:700; color:#0b1a75;">
                                Thank you, {{ $receipt->donor_name ?? 'Valued Donor' }}!
                            </p>
                            <p style="margin:0 0 28px; font-size:14px; color:#8492b4; line-height:1.6;">
                                Your generous donation has been received. Please find your official tax receipt details below.
                                A PDF copy is also attached to this email for your records.
                            </p>

                            {{-- Amount highlight box --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb; border-radius:10px; margin-bottom:24px;">
                                <tr>
                                    <td style="padding: 20px 24px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td>
                                                    <p style="margin:0 0 4px; font-size:9px; color:#8492b4; letter-spacing:2px; text-transform:uppercase;">Donation Amount</p>
                                                    <p style="margin:0; font-size:32px; font-weight:800; color:#0b1a75;">
                                                        ${{ number_format($receipt->amount, 2) }}
                                                        <span style="font-size:14px; color:#8492b4;">{{ $receipt->currency }}</span>
                                                    </p>
                                                </td>
                                                <td align="right" style="vertical-align:middle;">
                                                    <p style="margin:0; font-size:10px; font-weight:700; color:#2683d4; text-transform:uppercase; letter-spacing:1px;">
                                                        @if($receipt->donation->type === 'one_off') One-Off
                                                        @elseif($receipt->donation->type === 'recurring') Recurring
                                                        @elseif($receipt->donation->type === 'in_memory') In Memory
                                                        @endif
                                                    </p>
                                                    <p style="margin:4px 0 0; font-size:11px; color:#8492b4;">
                                                        {{ $receipt->created_at->format('d F Y') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- In memory --}}
                            @if($receipt->donation->isInMemory() && $receipt->donation->tribute_name)
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-left: 3px solid #21b7e7; background:#f0f8ff; border-radius: 0 8px 8px 0; margin-bottom:24px;">
                                <tr>
                                    <td style="padding: 10px 16px;">
                                        <p style="margin:0 0 2px; font-size:9px; color:#8492b4; letter-spacing:1.5px; text-transform:uppercase;">
                                            {{ $receipt->donation->tribute_type === 'in_honour' ? 'In Honour of' : 'In Memory of' }}
                                        </p>
                                        <p style="margin:0; font-size:14px; font-weight:700; color:#0b1a75;">
                                            {{ $receipt->donation->tribute_name }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            {{-- Receipt details table --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border: 1.5px solid #dde3f0; border-radius:8px; margin-bottom:24px;">
                                @foreach([
                                    ['Receipt Number',  $receipt->receipt_number],
                                    ['Financial Year',  $receipt->financial_year],
                                    ['Organisation',    $receipt->org_name],
                                    ['ABN',             $receipt->org_abn ?? '—'],
                                    ['Date Issued',     $receipt->created_at->format('d F Y')],
                                ] as [$label, $value])
                                <tr>
                                    <td style="padding:10px 16px; font-size:11px; color:#8492b4; text-transform:uppercase; letter-spacing:1px; border-bottom: 1px solid #dde3f0; width:40%;">
                                        {{ $label }}
                                    </td>
                                    <td style="padding:10px 16px; font-size:12px; font-weight:700; color:#0b1a75; border-bottom: 1px solid #dde3f0;">
                                        {{ $value }}
                                    </td>
                                </tr>
                                @endforeach
                            </table>

                            {{-- Portal CTA --}}
                            @if($receipt->user_id)
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin: 0 auto 24px;">
                                <tr>
                                    <td style="background: linear-gradient(135deg, #0b1a75, #2025b1); border-radius: 8px; text-align:center;">
                                        <a href="{{ route('portal.donations.index') }}"
                                           style="display:inline-block; padding: 13px 28px; color:#ffffff; font-size:11px; font-weight:700; letter-spacing:2px; text-transform:uppercase; text-decoration:none;">
                                            View My Donations
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            {{-- Legal note --}}
                            <p style="margin:0; font-size:11px; color:#8492b4; line-height:1.7; background:#f4f6fb; padding:14px 16px; border-radius:6px;">
                                @if($receipt->org_is_dgr)
                                    <strong style="color:#0b1a75;">Tax deductible:</strong>
                                    This organisation is a registered Deductible Gift Recipient (DGR).
                                    Donations of $2 or more may be claimed as a tax deduction in your Australian income tax return.
                                    Please consult your tax advisor for advice specific to your circumstances.
                                @else
                                    Please retain this receipt for your records. Consult your tax advisor
                                    to determine the deductibility of this donation.
                                @endif
                            </p>

                        </td>
                    </tr>

                    {{-- ── Footer ── --}}
                    <tr>
                        <td style="padding: 20px 0; text-align:center;">
                            <p style="margin:0 0 4px; font-size:13px; font-weight:800; color:#0b1a75; letter-spacing:2px; text-transform:uppercase;">
                                {{ $receipt->org_name }}
                            </p>
                            @if($receipt->org_address)
                            <p style="margin:0 0 4px; font-size:11px; color:#8492b4;">{{ $receipt->org_address }}</p>
                            @endif
                            <p style="margin:0; font-size:10px; color:#8492b4;">
                                This is an automated receipt. Please do not reply to this email.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
