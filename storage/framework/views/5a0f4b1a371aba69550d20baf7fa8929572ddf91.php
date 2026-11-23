





















































































































































































































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
        @media  print { .no-print { display: none; } body { padding: 0; background: white; } .invoice-container { box-shadow: none; padding: 15mm; width: 100%; } }
    </style>
</head>
<body>
<div class="no-print">
    <button onclick="window.print()">Print Invoice</button>
</div>

<div class="invoice-container">
    <!-- Header -->
    <div class="header">
        <img src="<?php echo e(asset('/public/img/sangulogo1.png')); ?>" alt="Company Logo" class="logo">
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
                <?php echo e(strtoupper($customer->name ?? 'N/A')); ?><br>
                NIE: <?php echo e($customer->id_card ?? 'N/A'); ?><br>
                Passport #: <?php echo e($customer->passport_no ?? 'N/A'); ?><br>
                Teléfono: <?php echo e($customer->phone ?? 'N/A'); ?><br>
                Correo: <?php echo e($customer->email ?? 'N/A'); ?><br>
                Dirección: <?php echo e($customer->address ?? 'N/A'); ?>

            </p>
        </div>

        <div class="invoice-info">
            <div class="title">INFORMACIÓN DE FACTURA:</div>
            <p>
                <strong>Número:</strong> #<?php echo e(now()->format('Y')); ?><?php echo e(str_pad($customer->id, 4, '0', STR_PAD_LEFT)); ?><br>
                <strong>Fecha:</strong> <?php echo e(now()->format('d/m/Y')); ?>

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
        <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $amount = $invoice['totalPaid'] + $invoice['totalPending'];
                $paymentType = $invoice['payment_type'] ?? 'N/A';
            ?>
            <tr>
                <!-- PX NAME -->
                <td><?php echo e($invoice['ticket']->customer_name ?? $customer->name); ?></td>

                <!-- TICKET NUMBER -->
                <td><?php echo e($invoice['ticket']->ticket_number ?? 'Ticket'); ?></td>

                <!-- PAYMENT TYPE -->
                <td>
                    <?php switch($paymentType):
                        case ('Paid with Cash'): ?> <?php echo e($paymentType); ?> <?php break; ?>
                        <?php case ('Paid in Bank'): ?> <?php echo e($paymentType); ?> <?php break; ?>
                        <?php case ('Paid with Card'): ?> <?php echo e($paymentType); ?> <?php break; ?>
                        <?php default: ?> <?php echo e($paymentType); ?>

                    <?php endswitch; ?>
                </td>

                <!-- PRICE / TOTAL -->
                <td>€<?php echo e(number_format($amount, 2)); ?></td>
                <td>€<?php echo e(number_format($amount, 2)); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <!-- Summary Table -->
    <table class="summary-table">
        <tr>
            <td></td>
            <td>Sub Total</td>
            <td>€<?php echo e(number_format($totalPaid + $totalPending, 2)); ?></td>
        </tr>
        <tr>
            <td></td>
            <td>SIN IVA %</td>
            <td>0.00</td>
        </tr>
        <tr>
            <td></td>
            <td><strong>TOTAL FACTURA</strong></td>
            <td><strong>€<?php echo e(number_format($totalPaid + $totalPending, 2)); ?></strong></td>
        </tr>
    </table>

    <!-- Signature -->
    <div class="signature-section">
        <p><strong>Signature:</strong> _______________ &nbsp;&nbsp;&nbsp;
            <strong>Date:</strong> <?php echo e(now()->format('d/m/Y')); ?>

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



<?php /**PATH D:\laragon\www\testing_project\resources\views/admin/tickets/final_invoice.blade.php ENDPATH**/ ?>