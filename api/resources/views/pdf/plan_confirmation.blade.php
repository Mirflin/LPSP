<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Plāna PDF</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ public_path('fonts/DejaVuSans.ttf') }}') format('truetype');
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
<table style="width:187mm; height: 100%; background:#fff; font-size:14px; font-family:sans-serif; border-collapse:separate; border-spacing:0;">
    <tr>
        <td colspan="2" style="padding-bottom:16px;">
            <table style="width:100%;">
                <tr>
                    <td style="width:120px; vertical-align:middle;">
                        <img src="{{ public_path('images/main-logo.jpg') }}" alt="LPSP Logo" style="height:120px;" />
                    </td>
                    <td style="text-align:center; vertical-align:middle;">
                        <h1 style="margin-top:80px; margin-right:140px; font-size:22px; font-weight:600;">Purchase order confirmation</h1>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="padding-bottom:12px;">
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
        <td colspan="2" style="padding-bottom:4px;">
            <a style="margin:12px 0; display:inline-block;" href="https://drive.google.com/file/d/1ZCXJOuqZk_hw8OhAESjU4BeaxpscIja8/view">LPSP Sales and delivery terms</a>
        </td>
    </tr>
    <tr><td colspan="2"><hr style="border:1px solid #ccc; margin:8px 0;"></td></tr>
    <tr>
        <td colspan="2" style="padding-bottom:12px;">
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
        <td colspan="2" style="padding-top:12px;">
            <table style="width:100%; border-collapse:collapse; font-size:10px; text-align:center;">
                <thead>
                    <tr style="background:#f3f3f3;">
                        <th style="border:1px solid #ccc; padding:2px;  height:15px;">Purchase order No.</th>
                        <th style="border:1px solid #ccc; padding:2px; min-width:120px;">Customer</th>
                        <th style="border:1px solid #ccc; padding:2px;">Shipping date</th>
                        <th style="border:1px solid #ccc; padding:2px; min-width:200px;">Item description</th>
                        <th style="border:1px solid #ccc; padding:2px;">Rev</th>
                        <th style="border:1px solid #ccc; padding:2px;">Amount</th>
                        <th style="border:1px solid #ccc; padding:2px;">Price(EUR)/Pcs Excl. VAT</th>
                        <th style="border:1px solid #ccc; padding:2px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $n=1 ?>
                    @foreach($rows as $row)
                    <tr>
                        <td style="border:1px solid #ccc; padding:2px;  height:15px;">{{ $row['po_nr'] ?? '-' }}</td>
                        <td style="border:1px solid #ccc; padding:2px;">{{ $row['customer'] ?? '-' }}</td>
                        <td style="border:1px solid #ccc; padding:2px;">{{ $row['ship_date'] ?? '-' }}</td>
                        <td style="border:1px solid #ccc; padding:2px;">{{ $row['desc'] ?? '-' }}</td>
                        <td style="border:1px solid #ccc; padding:2px;">{{ $row['rev'] ?? '-' }}</td>
                        <td style="border:1px solid #ccc; padding:2px;">{{ $row['ammount'] ?? '-' }}</td>
                        <td style="border:1px solid #ccc; padding:2px;">{{ $row['price'] ?? '-' }}</td>
                        <td style="border:1px solid #ccc; padding:2px;">{{ $row['total'] ?? '-' }}</td>
                    </tr>
                        @if ( $n % 25 == 0 )
                            <div style="page-break-before:always;"> </div>
                        @endif

                        <?php 
                            if($n == count($rows) && $n % 25 > 0) {
                                for($i = $n % 25; $i < 25; $i++) {
                                    if($i == 2){
                                        echo '<tr>
                                                <td style="border:1px solid #ccc; padding:2px; height:15px;"></td>
                                                <td style="border:1px solid #ccc; padding:2px;"></td>
                                                <td style="border:1px solid #ccc; padding:2px;"></td>
                                                <td style="border:1px solid #ccc; padding:2px;"></td>
                                                <td style="border:1px solid #ccc; padding:2px;"></td>
                                                <td style="border:1px solid #ccc; padding:2px;"></td>
                                                <td style="border:1px solid #ccc; padding:2px; font-weight: bold;">Order sum</td>
                                                <td style="border:1px solid #ccc; padding:2px;">'. $totalPrice .'</td>
                                             </tr>';
                                    }else{
                                        echo '<tr>
                                                <td style="border:1px solid #ccc; padding:2px; height:15px;"></td>
                                                <td style="border:1px solid #ccc; padding:2px;"></td>
                                                <td style="border:1px solid #ccc; padding:2px;"></td>
                                                <td style="border:1px solid #ccc; padding:2px;"></td>
                                                <td style="border:1px solid #ccc; padding:2px;"></td>
                                                <td style="border:1px solid #ccc; padding:2px;"></td>
                                                <td style="border:1px solid #ccc; padding:2px;"></td>
                                                <td style="border:1px solid #ccc; padding:2px;"></td>
                                             </tr>';
                                    }
                                }
                            }
                        ?>

                        <?php $n++ ?>

                    @endforeach
                </tbody>
            </table>
        </td>
    </tr>
</table>

</body>
</html>