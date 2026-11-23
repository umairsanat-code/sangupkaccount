{{--<!DOCTYPE html>--}}
{{--<html lang="en">--}}
{{--<head>--}}
{{--    <meta charset="UTF-8">--}}
{{--    <meta name="viewport" content="width=device-width, initial-scale=1.0">--}}
{{--    <title>Invoice final</title>--}}
{{--    <style>--}}
{{--        body {--}}
{{--            font-family: Arial, sans-serif;--}}
{{--            margin: 0;--}}
{{--            padding: 20px;--}}
{{--            background-color: #f9f9f9;--}}
{{--        }--}}

{{--        .invoice-container {--}}
{{--            height: auto;--}}
{{--            width: 70%;--}}
{{--            margin: 20px auto;--}}
{{--            background: #fff;--}}
{{--            padding: 20px;--}}
{{--            border: 1px solid #ddd;--}}
{{--            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);--}}
{{--            position: relative;--}}
{{--        }--}}

{{--        .no-print {--}}
{{--            text-align: right;--}}
{{--            margin-bottom: 20px;--}}
{{--        }--}}

{{--        .no-print button, .no-print a {--}}
{{--            margin: 0 5px;--}}
{{--            padding: 10px 15px;--}}
{{--            border: none;--}}
{{--            border-radius: 4px;--}}
{{--            cursor: pointer;--}}
{{--            font-size: 14px;--}}
{{--            text-decoration: none;--}}
{{--        }--}}

{{--        .no-print button.print-btn {--}}
{{--            background-color: #4CAF50;--}}
{{--            color: white;--}}
{{--        }--}}

{{--        .no-print a.download-btn {--}}
{{--            background-color: #007BFF;--}}
{{--            color: white;--}}
{{--            display: none; /* Hide download button initially */--}}
{{--        }--}}

{{--        @media print {--}}
{{--            .no-print {--}}
{{--                display: none;--}}
{{--            }--}}
{{--        }--}}

{{--        .header {--}}
{{--            display: flex;--}}
{{--            justify-content: space-between;--}}
{{--            align-items: center;--}}
{{--            margin-bottom: 20px;--}}
{{--        }--}}

{{--        .header img {--}}
{{--            max-width: 150px;--}}
{{--            float: right;--}}
{{--        }--}}

{{--        .company-info {--}}
{{--            text-align: left;--}}
{{--            font-size: 14px;--}}
{{--        }--}}

{{--        .details {--}}
{{--            margin-bottom: 20px;--}}
{{--        }--}}

{{--        .details h3, .details h4 {--}}
{{--            margin: 5px 0;--}}
{{--        }--}}

{{--        table {--}}
{{--            width: 100%;--}}
{{--            border-collapse: collapse;--}}
{{--            margin-bottom: 20px;--}}
{{--        }--}}

{{--        table th, table td {--}}
{{--            border: 1px solid #ddd;--}}
{{--            padding: 8px;--}}
{{--            text-align: left;--}}
{{--        }--}}

{{--        table th {--}}
{{--            background-color: #f2f2f2;--}}
{{--        }--}}

{{--        .summary {--}}
{{--            margin-top: 20px;--}}
{{--            padding: 10px;--}}
{{--            background-color: #f9f9f9;--}}
{{--            border: 1px solid #ddd;--}}
{{--            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);--}}
{{--            text-align: right;--}}
{{--        }--}}

{{--        .summary h4 {--}}
{{--            margin: 5px 0;--}}
{{--        }--}}

{{--        .footer {--}}
{{--            text-align: center;--}}
{{--            margin-top: 20px;--}}
{{--            font-size: 12px;--}}
{{--            color: #666;--}}
{{--        }--}}

{{--        .signature-contact {--}}
{{--            margin-top: 40px;--}}
{{--            font-size: 14px;--}}
{{--            line-height: 1.6;--}}
{{--            text-align: left;--}}
{{--        }--}}
{{--    </style>--}}
{{--</head>--}}
{{--<body>--}}
{{--<div class="invoice-container">--}}
{{--    <!-- Action Buttons -->--}}
{{--    <div class="no-print">--}}
{{--        <a class="download-btn" id="download-btn" href="{{ route('invoice.download', ['id' => $customer->id]) }}">Download Invoice</a>--}}
{{--    </div>--}}

{{--    <!-- Header Section -->--}}
{{--    <div class="header" style="display: flex; justify-content: space-between; align-items: flex-start;">--}}
{{--        <div class="company-info" style="flex: 1;">--}}
{{--            <h2>SANGU TRAVEL & TOURS SL</h2>--}}
{{--            <p>CIF:B22630552</p>--}}
{{--            <p>Dirección: Calle Maria Ward  num 17 Planta  1 puerta 2 badalona  08913 Spain </p>--}}
{{--            <p>Teléfono: 0034631028871</p>--}}
{{--        </div>--}}
{{--        <img src="{{ asset('/public/img/sangulogo1.png') }}" alt="Company Logo" style="max-width: 150px;">--}}
{{--    </div>--}}

{{--    <!-- Invoice Details Section -->--}}
{{--    <div class="details" style="margin-top: 20px; display: flex; justify-content: flex-end;">--}}
{{--        <div style="text-align: right;">--}}
{{--            <h3>Factura</h3>--}}
{{--            <h4>Fecha de Factura: {{ date('F d, Y') }}</h4>--}}
{{--            <h4>Número de Factura: {{ '2025' . str_pad($customer->id, 6, '0', STR_PAD_LEFT) }}</h4>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <!-- Customer Details -->--}}
{{--    <div class="details">--}}
{{--        <h4>Cliente: {{ $customer->name }}</h4>--}}
{{--        <p>Dirección: {{ $customer->address }}</p>--}}
{{--        <p>Email: sangutravel@gmail.com</p>--}}
{{--    </div>--}}

{{--    <!-- Tickets Table -->--}}
{{--    <table>--}}
{{--        <thead>--}}
{{--        <tr>--}}
{{--            <th>Ticket#</th>--}}
{{--            <th>Descripción</th>--}}
{{--            <th>PNR</th>--}}
{{--            <th>Pagado (€)</th>--}}
{{--            <th>Pendiente (€)</th>--}}
{{--            <th>Total (€)</th>--}}
{{--        </tr>--}}
{{--        </thead>--}}
{{--        <tbody>--}}
{{--        @foreach($invoices as $invoice)--}}
{{--            <tr>--}}
{{--                <td>{{ $invoice['ticket']->ticket_number ?? 'Ticket' }}</td>--}}
{{--                <td>{{ $invoice['ticket']->customer_name ?? $customer->name  }}</td>--}}
{{--                <td>{{$invoice['ticket']->pnr ?? 'N/A' }}</td>--}}
{{--                <td>{{ number_format($invoice['totalPaid'], 2) }}</td>--}}
{{--                <td>{{ number_format($invoice['totalPending'], 2) }}</td>--}}
{{--                <td>{{ number_format($invoice['totalPaid'] + $invoice['totalPending'], 2) }}</td>--}}
{{--            </tr>--}}
{{--        @endforeach--}}
{{--        </tbody>--}}
{{--    </table>--}}

{{--    <!-- Summary -->--}}
{{--    <div class="summary">--}}
{{--        <h4>Total Pagado: €{{ number_format($totalPaid, 2) }}</h4>--}}
{{--        <h4>Total Pendiente: €{{ number_format($totalPending, 2) }}</h4>--}}
{{--        <h4>Total General: €{{ number_format($totalPaid + $totalPending, 2) }}</h4>--}}
{{--    </div>--}}

{{--    <!-- Footer -->--}}
{{--    <div class="footer">--}}
{{--        <p>Gracias por su compra.</p>--}}
{{--    </div>--}}

{{--    <!-- Signature & Contact -->--}}
{{--    <div class="signature-contact" >--}}
{{--        <p><strong>Signature:</strong> _______________ &nbsp;&nbsp;&nbsp;&nbsp; <strong>Date:</strong> _______________</p>--}}
{{--        <p>Calle Maria Ward  num 17 Planta  1 puerta 2 badalona<br>--}}
{{--        Phone: 0034 933 884 277 &nbsp;&nbsp;&nbsp; Mobile: 0034631028871</p>--}}
{{--    </div>--}}
{{--</div>--}}

{{--<script>--}}
{{--    // Show the download button after the document has loaded--}}
{{--    window.onload = function() {--}}
{{--        document.getElementById("download-btn").style.display = 'inline-block';--}}
{{--    }--}}
{{--</script>--}}
{{--</body>--}}
{{--</html>--}}
    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SANGU TRAVELS & TOURS - FACTURA</title>
    <style>
        /* same CSS from your version */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #f4f4f4; padding: 20px; display: flex; justify-content: center; }
        .invoice-container { width: 210mm; min-height: 297mm; background: white; padding: 15mm; box-shadow: 0 0 10px rgba(0,0,0,0.15); position: relative; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; margin-bottom: 15px; }
        .logo { width: 100px; }
        .factura-badge { background: #003366; color: white; font-weight: bold; padding: 8px 18px; border-radius: 50px; font-size: 18px; }
        .company-client-section { display: flex; justify-content: space-between; margin-top: 15px; }
        .company-info, .invoice-info { width: 48%; font-size: 12px; line-height: 1.5; }
        .company-info .title, .invoice-info .title { font-weight: bold; font-size: 14px; margin-bottom: 5px; text-transform: uppercase; }
        .invoice-info .subtitle { font-weight: bold; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #333; color: white; text-align: left; }
        .summary-table { width: 50%; margin-left: auto; margin-top: 10px; font-size: 12px; }
        .summary-table td { padding: 6px; }
        .summary-table td:first-child { font-weight: bold; }
        .summary-table tr:last-child td { font-weight: bold; }
        .signature-section { margin-top: 40px; font-size: 12px; }
        .footer { position: absolute; bottom: 0; left: 0; width: 100%; background: #333; color: white; text-align: center; padding: 10px 0; font-size: 11px; }
        .footer p { margin: 2px 0; }
        .no-print { text-align: center; margin-bottom: 20px; }
        .no-print button { padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        @media print { .no-print { display: none; } body { padding: 0; background: white; } .invoice-container { box-shadow: none; padding: 15mm; width: 100%; } }
    </style>
</head>
<body>
<div class="no-print">
    <button onclick="window.print()">Print Invoice</button>
</div>

<div class="invoice-container">
    <!-- Header -->
    <div class="header">
        <img src="{{ asset('/public/img/sangulogo1.png') }}" alt="Company Logo" class="logo">
        <div class="factura-badge">FACTURA</div>
    </div>

    <!-- Company + Client Info -->
    <div class="company-client-section">
        <div class="company-info">
            <div class="title">SANGU TRAVELS & TOURS S.L</div>
            <p>
                CIF : B-22630552<br>
                DIRECCIÓN: CALLE MARIA WARD 17 P 1 -2<br>
                08913 BARCELONA, ESPAÑA<br>
                TELÉFONO : 0034653502901<br><br>

                <strong>FACTURA A</strong><br>
                {{ strtoupper($customer->name ?? 'N/A') }}<br>
                NIE: {{ $customer->id_card ?? 'N/A' }}<br>
                Passport #: {{ $customer->passport_no ?? 'N/A' }}<br>
                Teléfono: {{ $customer->phone ?? 'N/A' }}<br>
                Correo: {{ $customer->email ?? 'N/A' }}<br>
                Dirección: {{ $customer->address ?? 'N/A' }}
            </p>
        </div>

        <div class="invoice-info">
            <div class="title">INFORMACIÓN DE FACTURA:</div>
            <p>
                <strong>Número:</strong> #{{ now()->format('Y') }}{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}<br>
                <strong>Fecha:</strong> {{ now()->format('d/m/Y') }}
            </p>

            <div class="subtitle">INFORMACIÓN BANCARIA:</div>
            <p>
                Banco La Caixa<br>
                ES59 2100 3146 1222 0052 1367<br><br>

                Banco BBVA<br>
                ES83 0182 8738 7702 0262 4121
            </p>
        </div>
    </div>

    <!-- Services Table -->
    <table>
        <thead>
        <tr>
            <th>PX NAME</th>
            <th>TICKET NO</th>
            <th>TYPE OF PAYMENT</th>
            <th>PRECIO</th>
            <th>TOTAL</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($invoices as $invoice)
            @php
                $amount = $invoice['totalPaid'] + $invoice['totalPending'];
                $paymentType = $invoice['payment_type'] ?? 'N/A';
            @endphp
            <tr>
                <!-- PX NAME -->
                <td>{{ $invoice['ticket']->customer_name ?? $customer->name }}</td>

                <!-- TICKET NUMBER -->
                <td>{{  $invoice['ticket']->ticket_number ?? 'Ticket'}}</td>

                <!-- PAYMENT TYPE -->
                <td>
                    @switch($paymentType)
                        @case('Paid with Cash') {{ $paymentType }} @break
                        @case('Paid in Bank') {{ $paymentType }} @break
                        @case('Paid with Card') {{ $paymentType }} @break
                        @default {{ $paymentType }}
                    @endswitch
                </td>

                <!-- PRICE / TOTAL -->
                <td>€{{ number_format($amount, 2) }}</td>
                <td>€{{ number_format($amount, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <!-- Summary Table -->
    <table class="summary-table">
        <tr>
            <td></td>
            <td>Sub Total</td>
            <td>€{{ number_format($totalPaid + $totalPending, 2) }}</td>
        </tr>
        <tr>
            <td></td>
            <td>SIN IVA %</td>
            <td>0.00</td>
        </tr>
        <tr>
            <td></td>
            <td><strong>TOTAL FACTURA</strong></td>
            <td><strong>€{{ number_format($totalPaid + $totalPending, 2) }}</strong></td>
        </tr>
    </table>

    <!-- Signature -->
    <div class="signature-section">
        <p><strong>Signature:</strong> _______________ &nbsp;&nbsp;&nbsp;
            <strong>Date:</strong> {{ now()->format('d/m/Y') }}
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>0034 653502901 | sanguetravel@yahoo.com</p>
        <p>CALLE MARIA WARD 17 1 PTA 2, 08913 BARCELONA, ESPAÑA</p>
    </div>
</div>




</body>
</html>



