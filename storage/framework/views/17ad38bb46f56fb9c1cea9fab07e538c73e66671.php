<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?php echo e($ticket->pnr); ?></title>
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .invoice-container {
            max-width: 850px;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            margin: auto;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #ff8000;
            margin-bottom: 20px;
            position: relative;
        }
        .header h2 {
            color: #ff8000;
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header strong {
            position: absolute;
            left: 0;
            top: 10px;
            font-size: 18px;
            color: #333;
        }
        .header p {
            color: #555;
            font-size: 16px;
            margin-top: 5px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .details {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 25px;
        }
        .details div {
            flex: 1;
            padding: 20px;
            background: #fff8f2;
            border-radius: 8px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.05);
        }
        .details h3 {
            margin: 0 0 15px 0;
            color: #ff8000;
            font-size: 18px;
        }
        .details p {
            margin: 5px 0;
            color: #555;
            font-size: 14px;
        }

        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .item-table th {
            background: #ff8000;
            color: white;
            font-weight: bold;
            padding: 12px;
            text-align: center;
        }
        .item-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
            color: #555;
        }

        .total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #ddd;
        }

        .notes {
            width: 100%;
            min-height: 100px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            resize: none;
            font-size: 14px;
            margin-top: 15px;
            background: #fff8f2;
            color: #555;
            text-align: left; /* Ensures paragraph text aligns left */
        }

        .signature {
            text-align: left;
            font-size: 13px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            color: #333;
            line-height: 1.6;
        }

        .signature ul {
            list-style-type: disc;
            padding-left: 20px;
            margin: 0;
            text-align: left;
        }

        .signature p {
            margin: 5px 0;
            text-align: left;
        }

        .btn-print {
            display: block;
            width: 100%;
            text-align: center;
            background: #ff8000;
            color: white;
            padding: 15px;
            margin-top: 25px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            border: none;
            transition: background 0.3s ease;
        }
        .btn-print:hover {
            background: #e67300;
        }

        /* Address Section */
        .address-section {
            background: #fff8f2;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.05);
            text-align: left;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .address-section h3 {
            color: #ff8000;
            font-size: 18px;
            margin-bottom: 15px;
            text-align: left;
        }
        .address-section p {
            margin: 5px 0;
            color: #555;
            font-size: 14px;
            text-align: left;
        }

        /* Print Styles */
        @media  print {
            body {
                background-color: #fff;
                margin: 0;
                padding: 0;
            }
            .invoice-container {
                box-shadow: none;
                border: none;
                padding: 20px;
            }
            .btn-print {
                display: none;
            }
            .item-table {
                border: 1px solid #000;
            }
            .item-table th,
            .item-table td {
                border: 1px solid #000;
                color: #000;
            }
            .notes {
                border: 1px solid #000;
                background: #fff;
                color: #000;
            }
            .address-section {
                background: #fff;
                border: 1px solid #000;
            }
        }
    </style>
</head>
<body>

<div class="invoice-container">
    <div class="header">
        <h2>Planet Sangu Travel & Tours</h2>
        <strong>Invoice</strong>
    </div>

    <p style="text-align:left;">Invoice for PNR: <strong><?php echo e($ticket->pax_name); ?></strong></p>

    <div class="details">
        <div>
            <h3>Passenger Details</h3>
            <p><strong>PNR:</strong> <?php echo e($ticket->pnr); ?></p>
            <p><strong>System:</strong> <?php echo e($ticket->system); ?></p>
            <p><strong>Ticket Number:</strong> <?php echo e($ticket->ticket_number); ?></p>
        </div>
        <div>
            <h3>Payment Details</h3>
            <p><strong>Sale Price:</strong> €<?php echo e(number_format($ticket->sale_price, 2)); ?></p>
            <p><strong>Payment:</strong> €<?php echo e(number_format($ticket->payment, 2)); ?></p>
            <p><strong>Pending Amount:</strong> €<?php echo e(number_format($ticket->pendings, 2)); ?></p>
        </div>
    </div>

    <table class="item-table">
        <thead>
        <tr>
            <th>Date</th>
            <th>Payment Type</th>
            <th>Pagado</th>
            <th>Pendiente</th>
            <th>Total Price</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><?php echo e(\Carbon\Carbon::parse($ticket->date)->format('M d, Y')); ?></td>
            <td><?php echo e($ticket->type); ?></td>
            <td>€<?php echo e(number_format($ticket->payment, 2)); ?></td>
            <td>€<?php echo e(number_format($ticket->pendings, 2)); ?></td>
            <td><strong>€<?php echo e(number_format($ticket->sale_price, 2)); ?></strong></td>
        </tr>
        </tbody>
    </table>

    <div class="total">
        <h3 style="float: left; font-size:16px;">Additional Notes</h3>
        <textarea class="notes" placeholder="Enter additional details here..."></textarea>
    </div>

    <div class="total">
        <p><strong>Pending amount : €<?php echo e(number_format($ticket->pendings, 2)); ?></strong></p>
    </div>

    <div class="signature">
        <strong>NOTES</strong>
        <ul>
            <li>The maximum grace period for the next payment is 10 days or the next working day, whichever is earlier.</li>
            <li>Failure to complete payment by the due date will result in cancellation of the ticket, and a refund penalty will apply in accordance with the respective airline’s policy.</li>
            <li>Etihad Airways tickets are strictly non-refundable.</li>
            <li>Ticket change fees are variable, depending on seat availability and the selected travel dates. Please note that no airline offers free changes.</li>
            <li>It is the responsibility of the client to confirm the travel schedule and timings prior to departure. The agency shall not be held liable for any issues arising from failure to verify this information.</li>
        </ul>

        <br>

        <p><strong>Signature: _______________ &nbsp;&nbsp; Date: _______________</strong></p>
        <br>

        <p>
            Ab Alfonso XIII 496, Badalona, 08913, Spain<br>
            <strong>Phone:</strong> 0034 933 884 277 &nbsp;&nbsp;
            <strong>Mobile:</strong> 0034 631 028 871
        </p>
    </div>

    <button class="btn-print" onclick="window.print()">Print Invoice</button>
</div>

</body>
</html>

<?php /**PATH D:\laragon\www\testing_project\resources\views/admin/daily_report/invoice.blade.php ENDPATH**/ ?>