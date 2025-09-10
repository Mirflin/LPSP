<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Plāna PDF</title>
    <style>

        @font-face {
            font-family: 'AbhayaLibre';
            src: url('{{ storage_path('fonts/AbhayaLibre-Regular.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: 'AbhayaLibre';
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
<table style="width:187mm; height: 100%; background:#fff; font-size:14px; border-collapse:separate; border-spacing:0;">
    <tr>
        <td colspan="2" style="padding-bottom:8px;">
            <table style="width:100%;">
                <tr>
                    <td style="width:120px; vertical-align:middle;">
                        <img src="{{ public_path('images/main-logo.jpg') }}" alt="LPSP Logo" style="height:120px;" />
                    </td>
                    <td style="text-align:center; vertical-align:middle;">
                        <h1 style="margin-top:80px; margin-right:140px; font-size:22px; font-weight:600;">Invoice No. LPSP-{{ now()->year }} {{$options['nr']}}</h1>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    {{ now() }}
    <tr>
        <td colspan="2" style="padding-bottom:6px;">
            <table style="width:100%;">
                <tr>
                    <td style="vertical-align:top; width:50%;">
                        <table>
                            <tr><td style="width:150px;">Supplier</td><td style="width:200px;">{{ $creds['name'] ?? '-' }}</td></tr>
                            <tr><td>Legal Address</td><td style="width:200px;">{{ $creds['address'] ?? '-' }}</td></tr>
                            <tr><td>Bank</td><td style="width:200px;">{{ $creds['bank'] ?? '-' }}</td></tr>
                            <tr><td>Export Address</td><td style="width:200px;">{{ $creds['export_address'] ?? '-' }}</td></tr>
                            <tr><td>Phone</td><td style="width:200px;">{{ $creds['phone'] ?? '-' }}</td></tr>
                        </table>
                    </td>
                    <td style="vertical-align:top; width:50%;">
                        <table>
                            <tr><td style="width:150px;">TAX No.</td><td style="width:200px;">{{ $creds['registration_nr'] ?? '-' }}</td></tr>
                            <tr><td style="width:150px;">VAT No.</td><td style="width:200px;">{{ $creds['vat_nr'] ?? '-' }}</td></tr>
                            <tr><td style="width:150px;">Iban</td><td style="width:200px;">{{ $creds['iban'] ?? '-' }}</td></tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr><td colspan="2"><hr style="border:1px solid #ccc; margin:4px 0;"></td></tr>
    <tr>
        <td colspan="2" style="padding-bottom:6px;">
            <table style="width:100%;">
                <tr>
                    <td style="vertical-align:top; width:50%;">
                        <table>
                            <tr><td>Customer</td> <td style="width:200px;">{{ $client['name'] ?? '-' }}</td></tr>
                            <tr><td>Legal Address</td><td style="width:200px;">{{ $client['address'] ?? '-' }}</td></tr>
                            <tr><td>Bank</td><td style="width:200px;">{{ $client['bank'] ?? '-' }}</td></tr>
                            <tr><td style="width:150px;">Shipping Address</td><td style="width:200px;">{{ $client['delivery_address'] ?? '-' }}</td></tr>
                            <tr><td>Phone</td><td style="width:200px;">{{ $client['phone'] ?? '-' }}</td></tr>
                        </table>
                    </td>
                    <td style="vertical-align:top; width:50%;">
                        <table>
                            <tr><td style="width:150px;">TAX No.</td><td style="width:200px;">{{ $client['registration_nr'] ?? '-' }}</td></tr>
                            <tr><td>VAT No.</td><td style="width:200px;">{{ $client['vat_nr'] ?? '-' }}</td></tr>
                            <tr><td>Iban</td><td style="width:200px;">{{ $client['iban'] ?? '-' }}</td></tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr><td colspan="2"><hr style="border:1px solid #ccc; margin:4px 0;"></td></tr>
    <tr>
        <td colspan="2" style="padding-bottom:6px;">
            <table style="width:100%;">
                <tr>
                    <td style="vertical-align:top; width:50%;">
                        <table>
                            <tr><td>Payment terms</td> <td style="width:250px;">{{ $options['payment_term'] }}</td></tr>
                            <tr><td>Terms delivery</td><td style="width:250px;">{{ $options['terms_delivery'] }}</td></tr>
                            <tr><td>Description</td><td style="width:250px;">{{ $options['description'] }}</td></tr>
                        </table>
                    </td>
                    <td style="vertical-align:top; width:50%;">
                        <table>
                            <tr><td style="width:150px;">Dispatch date</td><td style="width:250px;">{{ $options['dispathch'] ?? '-' }}</td></tr>
                            <tr><td>Delivery with</td><td style="width:250px;">{{ $options['delivery_with'] ?? '-' }}</td></tr>
                            <tr><td>Country of origin</td><td style="width:250px;">{{ $options['country_of_origin'] ?? '-' }}</td></tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr><td colspan="2"><hr style="border:1px solid #ccc; margin:4px 0;"></td></tr>
    <tr>
        <table style="width:187mm; margin:0 auto; font-size:11px; border-spacing:0; border-collapse:collapse;">
        <tr>
            <td colspan="2" style="padding-top:12px;">
                <h3 style="margin:0; font-size:14px;">Justification</h3>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table style="width:100%; border-collapse:collapse; text-align:center;">
                    <thead>
                        <tr>
                            <th style="width:10%; border:1px solid #000; padding:4px; font-size:11px; background:#f8f8f8;">Department</th>
                            <th style="width:15%; border:1px solid #000; padding:4px; font-size:11px; background:#f8f8f8;">Purchase order</th>
                            <th style="width:8%; border:1px solid #000; padding:4px; font-size:11px; background:#f8f8f8;">Unit</th>
                            <th style="width:20%; border:1px solid #000; padding:4px; font-size:11px; background:#f8f8f8;">Item</th>
                            <th style="width:10%; border:1px solid #000; padding:4px; font-size:11px; background:#f8f8f8;">QTY</th>
                            <th style="width:12%; border:1px solid #000; padding:4px; font-size:11px; background:#f8f8f8;">Price Eur/pcs</th>
                            <th style="width:10%; border:1px solid #000; padding:4px; font-size:11px; background:#f8f8f8;">Discount (%)</th>
                            <th style="width:10%; border:1px solid #000; padding:4px; font-size:11px; background:#f8f8f8;">VAT (%)</th>
                            <th style="width:15%; border:1px solid #000; padding:4px; font-size:11px; background:#f8f8f8;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                        <tr>
                            <td style="border:1px solid #000; padding:4px; font-size:11px;">{{ $row['dep'] }}</td>
                            <td style="border:1px solid #000; padding:4px; font-size:11px;">{{ $row['order'] }}</td>
                            <td style="border:1px solid #000; padding:4px; font-size:11px;">pcs.</td>
                            <td style="border:1px solid #000; padding:4px; font-size:11px;">{{ $row['item'] }}</td>
                            <td style="border:1px solid #000; padding:4px; font-size:11px;">{{ $row['qty'] }}</td>
                            <td style="border:1px solid #000; padding:4px; font-size:11px; text-align:right;">{{ number_format($row['price'], 2) }}</td>
                            <td style="border:1px solid #000; padding:4px; font-size:11px; text-align:right;">{{ $row['discount'] }}</td>
                            <td style="border:1px solid #000; padding:4px; font-size:11px; text-align:right;">{{ $row['VAT'] }}</td>
                            <td style="border:1px solid #000; padding:4px; font-size:11px; text-align:right;">{{ number_format($row['total'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="2" style="padding-top:12px;">
                <table style="width:100%; border:0; font-size:12px; border-collapse:collapse;">
                    <tr>
                        <td style="width:70%; border:0;"></td>
                        <td style="width:30%; border:0;">
                            <table style="width:100%; border:0; border-collapse:collapse;">
                                <tr>
                                    <td style="border:0; text-align:right;">Discount (EUR):</td>
                                    <td style="border:0; text-align:right;">{{ number_format($discountEur, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="border:0; text-align:right;">Untaxed Amount (EUR):</td>
                                    <td style="border:0; text-align:right;">{{ number_format($sumWithoutVAT, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="border:0; text-align:right;">VAT (EUR):</td>
                                    <td style="border:0; text-align:right;">{{ number_format($vatSum, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="border:0; text-align:right; font-weight:bold;">Total (EUR):</td>
                                    <td style="border:0; text-align:right; font-weight:bold;">{{ number_format($sumWithVAT, 2) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="2" style="padding-top:8px; padding-bottom:8px;">
                <hr style="border:1px solid #ccc; margin:0;">
                <a href="https://drive.google.com/file/d/1ZCXJOuqZk_hw8OhAESjU4BeaxpscIja8/view" style="font-size:11px; text-decoration:none; color:#000;">
                    LPSP Sales and delivery terms
                </a>
                <hr style="border:1px solid #ccc; margin-top:8px;">
            </td>
        </tr>

        <tr>
            <td colspan="2" style="padding-top:8px; font-size:12px;">
                <p style="margin:4px 0;">Total amount in Words: {{ $words ?? '' }}</p>
                <p style="margin:4px 0;">Sales person: Lauris Puteklis</p>
                <hr style="border:1px solid #ccc; margin:8px 0;">
                <p style="text-align:center; margin:4px 0;">{{ now()->format('d/m/Y') }}</p>
                <hr style="border:1px solid #ccc; margin:8px 0;">
                <p style="font-size:11px; margin:4px 0;">
                    The invoice is produced electronically and valid without signature in accordance 
                    with Article 11 part 7 of the Law "About Accounting".
                </p>
            </td>
        </tr>
    </table>
    </tr>
</table>

</body>
</html>