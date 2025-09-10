@php
    function highlightColor($processName) {
        return match($processName) {
            'Sheet Laser'        => '#c1dcfaff', // light blue
            'Tube Laser'       => '#f9ecb9ff', // yellow
            'Surface treatment' => '#acedc4ff', // green
            'Welding'           => '#d7c9ffff', // purple
            default             => '#FFFFFF', // fallback white
        };
    }
@endphp

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

        .page {
            width: 100%;
            height: 90%;
            padding: 1mm;
            box-sizing: border-box;
            background: #fff;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .header .info-box {
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin-bottom: 10mm
        }
        .header .info-box p {
            font-weight: bold;
            margin: 0 0 2px 0;
        }
        .info-field {
            border: 1px solid black;
            padding: 1mm;
            text-align: center;
            min-width: 50mm;
            width: 200px;
            margin-bottom: 1px;
        }
        .logo {
            position: absolute;
            right: 10mm;
            top: 1mm;
            height: 35mm;
        }

        /* Client section */
        .client-section {
            margin-bottom: 20px;
        }
        .client-section p {
            font-weight: bold;
            margin-bottom: 2px;
        }
        .client-field {
            border: 1px solid black;
            padding: 1mm;
            width: 120mm;
            text-align: center;
        }

        /* Product details */
        .product-details td {
            padding: 6px;
            border: 1px solid #000;
            vertical-align: top;
            width: 50%;
        }
        .product-details p {
            font-weight: bold;
            margin: 0 0 4px;
        }
        .product-field {
            font-size: 14px;
        }

        /* Settings */
        .settings {
            display: flex;
            justify-content: space-between;
            gap: 6mm;
            margin-bottom: 10px;
        }
        .settings p {
            font-weight: bold;
            margin-bottom: 2px;
        }
        .settings-field {
            border: 1px solid black;
            padding: 1mm;
            width: 30%;
            text-align: center;
        }

        /* Processes table */
        .processes {
            margin-bottom: 10px;
        }
        .processes p {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding-left: 2mm;
            padding-right: 2mm;
            text-align: left;
        }
        th {
            text-align: center;
        }
        td p{
            padding: 0;
        }
        td{
            height: 8mm !important;
            overflow: hidden;
            white-space: nowrap;
            word-wrap: break-word;
            box-sizing: border-box;
            
        }
        td.center {
            text-align: center;
        }
        td.min-height {
            height: 3mm;
        }
        td.date_width{
            width: 15mm;
        }

        /* Materials */
        .materials {
            margin-top: 10px;
        }
        .materials p {
            font-weight: bold;
            margin-bottom: 2px;
        }
        .material-list, .material-note {
            border: 1px solid black;
            padding: 2mm;
            text-align: center;
            word-wrap: break-word;
        }
        .material-note {
            height: 30mm;
        }
        .page_break { page-break-before: always; }
        .auto{
            width: auto;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <div class="info-box">
                <div>
                    <p>Sagatavots:</p>
                    <div class="info-field">{{ \Carbon\Carbon::now()->format('d.m.Y') }}</div>
                </div>
                <div>
                    <p>Pasūtījuma Nr.:</p>
                    <div class="info-field">{{ $settings['po_nr'] }}</div>
                </div>
            </div>
            <div>
                <img src="{{ public_path('images/main-logo.jpg') }}" alt="Logo" class="logo">
            </div>
        </div>

        <!-- Client -->
        <div class="client-section">
            <p>Klients:</p>
            <div class="client-field">{{ $plan['client']['name'] }}</div>
        </div>

        <!-- Product details -->
        <table class="product-details" style="width: 100%; border-collapse: collapse;">
            <tr>
                <td>
                    <p>Detals Nr.</p>
                    <div class="product-field">{{ $plan['part_nr'] }}</div>
                </td>
                <td>
                    <p>Nosaukums</p>
                    <div class="product-field">{{ $plan['description'] ?? '-' }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <p>Rasējuma Nr.</p>
                    <div class="product-field">{{ $plan['drawing_nr'] }}</div>
                </td>
                <td>
                    <p>Revīzija</p>
                    <div class="product-field">{{ $plan['revision'] ?? '-' }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <p>Izpildīšanas datums</p>
                    <div class="product-field">{{ $settings['po_date'] }}</div>
                </td>
                <td>
                    <p>Daudzums</p>
                    <div class="product-field">{{ $settings['count'] }}</div>
                </td>
            </tr>
        </table>

        <!-- Processes -->
        <div class="processes">
            <p>Procesi:</p>
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>Process</th>
                        <th>Papild process</th>
                        <th>Kvalitāti pārbaudija / skaitīja</th>
                        <th>Skaits</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plan['processes'] as $process)
                        <tr>
                            <td class="date_width" style="width: 15%">{{ $process['date_completion'] }}</td>
                            <td class="min-height" style="width: 30%; background-color: {{ highlightColor($process['process_list']['name']) }}">{{ $process['process_list']['name'] }}</td>
                            <td class="center" style="width: 30%">{{ $process['sub_process'] ?? '-' }}</td>
                            <td style="width: 25%"></td>
                            <td style="width: 10%"></td>
                        </tr>
                    @endforeach
                    @for($i = 0; $i < 10 - count($plan['processes']); $i++)
                        <tr>
                            <td></td>
                            <td class="min-height"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <!-- Materials -->
        <div class="materials">
            <p>Materiāls:</p>
            <div class="material-list">{{ collect($plan['materials'])->pluck('name')->join(', ') ?: 'nav' }}</div>
            <div class="material-note">{{ $plan['additional_info'] }}</div>
        </div>
    </div>
    @foreach($plan['children'] as $plan)
        <div class="page_break"></div>
        <div class="page">
            <div class="header">
                <div class="info-box">
                    <div>
                        <p>Sagatavots:</p>
                        <div class="info-field">{{ \Carbon\Carbon::now()->format('d.m.Y') }}</div>
                    </div>
                    <div>
                        <p>Pasūtījuma Nr.:</p>
                        <div class="info-field">{{ $settings['po_nr'] }}</div>
                    </div>
                </div>
                <div>
                    <img src="{{ public_path('images/main-logo.jpg') }}" alt="Logo" class="logo">
                </div>
            </div>

            <!-- Client -->
            <div class="client-section">
                <p>Klients:</p>
                <div class="client-field">{{ $plan['client']['name'] }}</div>
            </div>

            <!-- Product details -->
            <table class="product-details" style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td>
                        <p>Detals Nr.</p>
                        <div class="product-field">{{ $plan['part_nr'] }}</div>
                    </td>
                    <td>
                        <p>Nosaukums</p>
                        <div class="product-field">{{ $plan['description'] ?? '-' }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p>Rasējuma Nr.</p>
                        <div class="product-field">{{ $plan['drawing_nr'] }}</div>
                    </td>
                    <td>
                        <p>Revīzija</p>
                        <div class="product-field">{{ $plan['revision'] ?? '-' }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p>Izpildīšanas datums</p>
                        <div class="product-field">{{ $settings['po_date'] }}</div>
                    </td>
                    <td>
                        <p>Daudzums</p>
                        <div class="product-field">{{ $settings['count'] * $plan['count'] }}</div>
                    </td>
                </tr>
            </table>

            <!-- Processes -->
            <div class="processes">
                <p>Procesi:</p>
                <table>
                    <thead>
                        <tr>
                            <th>Līdz</th>
                            <th>Process</th>
                            <th>Papild process</th>
                            <th>Kvalitāti pārbaudija / skaitīja</th>
                            <th>Skaits</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plan['processes'] as $process)
                            <tr class="processes">
                                <td class="date_width" style="width: 15%">{{ $process['date_completion'] }}</td>
                                <td class="min-height" style="background-color: {{ highlightColor($process['process_list']['name']) }}; width: 30%">{{ $process['process_list']['name'] }}</td>
                                <td class="center" style="width: 30%">{{ $process['sub_process'] ?? '-' }}</td>
                                <td style="width: 25%"></td>
                                <td style="width: 10%"></td>
                            </tr>
                        @endforeach
                        @for($i = 0; $i < 10 - count($plan['processes']); $i++)
                            <tr>
                                <td></td>
                                <td class="min-height"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <!-- Materials -->
            <div class="materials">
                <p>Materiāls:</p>
                <div class="material-list">{{ collect($plan['materials'])->pluck('name')->join(', ') ?: 'nav' }}</div>
                <div class="material-note">{{ $plan['additional_info'] }}</div>
            </div>
        </div>
    @endforeach
</body>
</html>
