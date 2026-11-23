<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo e(url('/')); ?>" class="brand-link active-nav">
      SanguTravel
    <br>

    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php echo e(asset('/img/default-avatar.svg')); ?>" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo e(auth()->user()?auth()->user()->name:'Administrator'); ?></a>
        </div>
      </div>


        <!-- Sidebar Menu -->
        <nav class="mt-2 main-left-sidebar">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                <?php if(auth()->check() && auth()->user()->hasRole('admin')): ?>
                <li class="nav-item">
                    <a href="<?php echo e(url('home')); ?>" class="nav-link">
                      <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <?php endif; ?>



                <!--<?php if(auth()->id()=='1'): ?>-->
                <li class="nav-item">
                    <a href="<?php echo e(url('members')); ?>" class="nav-link <?php echo e((request()->is('members*')) ? 'active-nav' : ''); ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Members
                        </p>
                    </a>
                </li>
                <!--<?php endif; ?>-->
                <li class="nav-item">
                    <a href="<?php echo e(url('inquiries')); ?>" class="nav-link <?php echo e((request()->is('inquiries*')) ? 'active-nav' : ''); ?>">
                        <i class="nav-icon fas fa-question-circle"></i>
                        <p>
                            Query (Task)
                        </p>
                    </a>
                </li>
               <?php if(auth()->check() && auth()->user()->hasRole('admin')): ?>
                 <li class="nav-item">
                    <a href="<?php echo e(url('teams')); ?>" class="nav-link <?php echo e((request()->is('teams*')) ? 'active-nav' : ''); ?>">
                      <i class="nav-icon fas fa-users"></i>
                        <p>
                            Customer
                        </p>
                    </a>
                </li>
                <?php endif; ?>
                <?php if(auth()->check() && auth()->user()->hasRole('admin')): ?>

                <li class="nav-item">
                    <a href="<?php echo e(url('tickets')); ?>" class="nav-link <?php echo e((request()->is('tickets*')) ? 'active-nav' : ''); ?>">
                       <i class="nav-icon fas fa-users"></i>
                        <p>
                            Tickets
                        </p>
                    </a>
          </li>
                <?php endif; ?>

 <li class="nav-item has-treeview <?php echo e(request()->is('daily_report*') ? 'menu-open' : ''); ?>">
    <a href="#" class="nav-link <?php echo e(request()->is('daily_report*') ? 'active-nav' : ''); ?>">
        <i class="nav-icon fa fa-binoculars"></i>
        <p>
            Daily Cash Book
            <i class="right fa fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="<?php echo e(route('daily_report.index')); ?>" class="nav-link <?php echo e(request()->is('daily_report') ? 'active' : ''); ?>">
                <i class="fa fa-circle-o nav-icon"></i>
                <p>New Tickets</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo e(route('daily_report.with_ticket')); ?>" class="nav-link <?php echo e(request()->is('daily_report/with_ticket') ? 'active' : ''); ?>">
                <i class="fa fa-circle-o nav-icon"></i>
                <p>Confirm Ticket</p>
            </a>
        </li>
    </ul>
</li>

<?php if(auth()->check() && auth()->user()->hasRole('admin')): ?>
 <li class="nav-item has-treeview <?php echo e(request()->is('visa-umrah*') ? 'menu-open' : ''); ?>">
    <a href="#" class="nav-link <?php echo e(request()->is('visa-umrah*') ? 'active-nav' : ''); ?>">
        <i class="nav-icon fas fa-passport"></i>
        <p>
            Visa & Umrah
            <i class="right fa fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="<?php echo e(route('visa_umrah.dashboard')); ?>" class="nav-link <?php echo e(request()->is('visa-umrah') ? 'active' : ''); ?>">
                <i class="fa fa-circle-o nav-icon"></i>
                <p>Dashboard</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo e(route('visa_umrah.agencies.index')); ?>" class="nav-link <?php echo e(request()->is('visa-umrah/agencies*') ? 'active' : ''); ?>">
                <i class="fa fa-circle-o nav-icon"></i>
                <p>Agencies / Suppliers</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo e(route('visa_umrah.bookings.index', 'visa')); ?>" class="nav-link <?php echo e(request()->is('visa-umrah/visa/bookings*') ? 'active' : ''); ?>">
                <i class="fa fa-circle-o nav-icon"></i>
                <p>Visa Bookings</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo e(route('visa_umrah.bookings.index', 'umrah')); ?>" class="nav-link <?php echo e(request()->is('visa-umrah/umrah/bookings*') ? 'active' : ''); ?>">
                <i class="fa fa-circle-o nav-icon"></i>
                <p>Umrah Bookings</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo e(route('visa_umrah.reports')); ?>" class="nav-link <?php echo e(request()->is('visa-umrah/reports*') ? 'active' : ''); ?>">
                <i class="fa fa-circle-o nav-icon"></i>
                <p>Reports</p>
            </a>
        </li>
    </ul>
</li>
<?php endif; ?>


                 <?php if(auth()->check() && auth()->user()->hasRole('admin')): ?>








                <li class="nav-item">
                    <a href="<?php echo e(url('expenses')); ?>" class="nav-link <?php echo e((request()->is('expenses')) ? 'active-nav' : ''); ?>">
                        <i class="nav-icon fas fa-user-tag"></i>
                        <p>
                            Expenses Management
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(url('card-payments')); ?>" class="nav-link <?php echo e((request()->is('card-payments*')) ? 'active-nav' : ''); ?>">
                        <i class="nav-icon fas fa-credit-card"></i>
                        <p>
                            Card Payments
                        </p>
                    </a>
                </li>
                <?php endif; ?>
                <?php if(auth()->check() && auth()->user()->hasRole('admin')): ?>

                  <li class="nav-item">
                    <a href="<?php echo e(url('airlines')); ?>" class="nav-link <?php echo e((request()->is('airlines*')) ? 'active-nav' : ''); ?>">

                      <i class="nav-icon fa fa-user"></i>
                      <p>
                            Airlines
                        </p>
                    </a>
                </li>
                <?php endif; ?>

                <?php if(auth()->check() && auth()->user()->hasRole('admin')): ?>
                <li class="nav-item">
                    <a href="<?php echo e(url('agents')); ?>"
                       class="nav-link <?php echo e((request()->is('agents/*')) ? 'active-nav' : ''); ?>" >
                       <i class="nav-icon fa fa-binoculars"></i>
                       <p>
                           Agents
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(url('vendors')); ?>"
                       class="nav-link <?php echo e((request()->is('vendors/*')) ? 'active-nav' : ''); ?>" >
                       <i class="nav-icon fa fa-binoculars"></i>
                       <p>
                           Vendor
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(url('import_messsage')); ?>"
                       class="nav-link <?php echo e((request()->is('import_messsage/*')) ? 'active-nav' : ''); ?>" >
                       <i class="nav-icon fa fa-envelope-open-text"></i>
                       <p>
                           Messages
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route('activity_log.index')); ?>"
                       class="nav-link <?php echo e(request()->is('activity-log*') ? 'active-nav' : ''); ?>">
                       <i class="nav-icon fa fa-history"></i>
                       <p>Activity Log</p>
                    </a>
                </li>











                <?php endif; ?>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
<?php /**PATH C:\laragon\www\testing_project\resources\views/partialPages/sidebar/sidebar.blade.php ENDPATH**/ ?>