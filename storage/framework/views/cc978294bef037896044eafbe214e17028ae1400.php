
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<?php $__env->startSection('content'); ?>
    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-ticket-alt"></i> Tickets for Agent: <?php echo e($agent->name); ?>

                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(url('dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Agent Tickets</li>
                    </ol>
                </nav>
            </div>

            <!-- Summary Cards -->
           <div style="display: flex; gap: 20px; margin-bottom: 20px;">
    <!-- Total Issue Price Card -->
    <div style="flex: 1; background-color: #007bff; color: white; border-radius: 8px; padding: 20px;">
        <h5 style="font-size: 18px; margin-bottom: 10px; margin-top: 0;">Total Issue Price</h5>
        <p style="font-size: 24px; margin: 0;">€<?php echo e(number_format($totalIssuePrice, 2)); ?></p>
    </div>

    <!-- Paid Amount Card -->
    <div style="flex: 1; background-color: #28a745; color: white; border-radius: 8px; padding: 20px;">
        <h5 style="font-size: 18px; margin-bottom: 10px; margin-top: 0;">Paid Amount</h5>
        <p style="font-size: 24px; margin: 0;">€<?php echo e(number_format($paidAmount, 2)); ?></p>
    </div>

    <!-- Remaining Amount Card -->
    <div style="flex: 1; background-color: red; color: white; border-radius: 8px; padding: 20px;">
        <h5 style="font-size: 18px; margin-bottom: 10px; margin-top: 0;">Remaining Amount</h5>
        <p style="font-size: 24px; margin: 0;">
            €<?php echo e($remainingAmount < 0 ? number_format(0, 2) : number_format($remainingAmount, 2)); ?>

        </p>
    </div>
</div>

            <!-- Date Range Info -->
            <div class="alert alert-info">
                <i class="fas fa-calendar-alt"></i> Date Range: <strong><?php echo e($start_date); ?></strong> to <strong><?php echo e($end_date); ?></strong>
            </div>

            <!-- Tickets Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tickets List</h3>
                </div>
                <div class="card-body">
                    <table id="ticketsTable" class="table table-bordered ">
                        <thead>
                        <tr>
                            <th>Ticket Number</th>
                            <th>Airline</th>
                            <th>Customer</th>
                            <th>Issue Date</th>
                            <th>PNR</th>
                            <th>Purchase Price</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Hidden Inputs for Date Range -->
        <input type="hidden" id="start_date" name="start_date" value="<?php echo e($start_date); ?>" class="form-control" required>
        <input type="hidden" id="end_date" name="end_date" value="<?php echo e($end_date); ?>" class="form-control" required>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.min.js"></script>

    <script>
        $(function () {
            var table = $('#ticketsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "<?php echo e(route('agents.tickets')); ?>",
                    data: function (d) {
                        d.agent_id = "<?php echo e($agent->id); ?>";
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                 columns: [
                    { data: 'ticket_number', name: 'ticket_number' },
                    { data: 'airline', name: 'airline' },
                    { data: 'customer_name', name: 'customer_name' },
                        { data: 'sale_date', name: 'sale_date' } // 👈 New column
                    ,
                    { data: 'pnr', name: 'pnr' },
                    { data: 'purchase_price', name: 'purchase_price' }
                ],
                dom: 'Blfrtip',  // Specifies the position of buttons and other elements
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], // Page length options
                
                buttons: [
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fas fa-file-csv"></i> CSV Export',
                        title: 'Tickets for Agent: <?php echo e($agent->name); ?>'
                    },
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Excel Export',
                        title: 'Tickets for Agent: <?php echo e($agent->name); ?>'
                    },
                  
                  
                ]
            });

            // Filter event
            $('#filterTickets').on('click', function () {
                table.draw();
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\testing_project\resources\views/admin/agents/tickets.blade.php ENDPATH**/ ?>