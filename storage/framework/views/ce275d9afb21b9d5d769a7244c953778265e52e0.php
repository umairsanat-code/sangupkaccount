<?php $__env->startSection('title', 'Agencies / Suppliers'); ?>

<?php $__env->startSection('content'); ?>
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Agencies / Suppliers</h3>
                    <a href="<?php echo e(route('visa_umrah.agencies.create')); ?>" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Agency</a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover" id="agencyTable">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Phone</th>
                            <th>City / Country</th>
                            <th>Services</th>
                            <th>Bookings</th>
                            <th>Total Payable</th>
                            <th>Total Paid</th>
                            <th>Outstanding</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $agencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><a href="<?php echo e(route('visa_umrah.agencies.show', $a->id)); ?>"><strong><?php echo e($a->name); ?></strong></a></td>
                                <td><?php echo e($a->contact_person ?? '-'); ?></td>
                                <td><?php echo e($a->phone ?? '-'); ?></td>
                                <td><?php echo e($a->city ?? '-'); ?>, <?php echo e($a->country ?? '-'); ?></td>
                                <td>
                                    <?php $__currentLoopData = ($a->services ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge badge-info"><?php echo e(ucfirst($s)); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </td>
                                <td><?php echo e($a->booking_count); ?></td>
                                <td class="text-right"><?php echo e(number_format($a->agency_payable, 2)); ?></td>
                                <td class="text-right text-success"><?php echo e(number_format($a->agency_paid, 2)); ?></td>
                                <td class="text-right <?php echo e($a->outstanding > 0 ? 'text-danger' : ''); ?>"><?php echo e(number_format($a->outstanding, 2)); ?></td>
                                <td>
                                    <a href="<?php echo e(route('visa_umrah.agencies.show', $a->id)); ?>" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                                    <a href="<?php echo e(route('visa_umrah.agencies.edit', $a->id)); ?>" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                                    <form action="<?php echo e(route('visa_umrah.agencies.destroy', $a->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Delete agency?');">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#agencyTable').DataTable({order: [[0, 'asc']]});
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\testing_project\resources\views/admin/visa_umrah/agencies/index.blade.php ENDPATH**/ ?>