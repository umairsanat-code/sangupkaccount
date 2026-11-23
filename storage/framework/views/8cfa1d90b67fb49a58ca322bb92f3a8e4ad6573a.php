<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
     <link rel="icon" href="<?php echo e(asset('/img/logo/logo.svg')); ?>" type="image/svg+xml">

    <title><?php echo $__env->yieldContent('title'); ?> | <?php echo e(config('app.name', 'Sangu')); ?></title>
    <!-- Styles -->
    <link href="<?php echo e(asset('/css/app.css')); ?>" rel="stylesheet">
    <!--<link href="<?php echo e(asset('/css/custome.css')); ?>" rel="stylesheet">-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/v4-shims.min.css" />

    <style>
        body,
        .content-wrapper,
        .card,
        .form-control,
        .btn,
        .table {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f4f6fb;
            color: #1f2937;
        }

        .navbar-white {
            background: #ffffff !important;
            border-bottom: 1px solid #e5e7eb;
        }

        .main-sidebar {
            background: linear-gradient(180deg, #111827 0%, #1f2937 100%) !important;
        }

        .content-wrapper {
            background: transparent;
        }

        .card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(16, 24, 40, 0.06);
        }

        .card-header {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
        }

        .btn {
            border-radius: 10px;
            font-weight: 600;
        }

        .btn-primary {
            background: #2563eb;
            border-color: #2563eb;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
        }

        .form-control,
        .custom-select {
            border-radius: 10px;
            border-color: #d1d5db;
            min-height: 40px;
        }

        .form-control:focus,
        .custom-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
        }

        .table thead th {
            border-bottom: 1px solid #e5e7eb;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #64748b;
            background: #f8fafc;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background: #f8fbff;
        }

        .pagination .page-link {
            border-radius: 8px;
            margin: 0 2px;
        }

        .active-nav {
            background-color: rgba(59, 130, 246, 0.2) !important;
            color: #e2e8f0 !important;
            border-left: 3px solid #60a5fa;
        }

        /*.active-nav{*/
        /*    background-color: #19A2FF !important;*/
        /*    color:white !important;*/
        /*}*/
        /*.tooltip-primary~.tooltip>.tooltip-inner {*/
        /*  background-color: #19A2FF !important;*/
        /*  color:white;*/
        /*}*/
        /* .force-overflow
        {
            min-height: 450px;
        } */


        *::-webkit-scrollbar,
        *::-webkit-scrollbar-thumb {
            width: 26px;
            border-radius: 13px;
            background-clip: padding-box;
            border: 10px solid transparent;
            color: #9fb3c1 !important;
        }

        *::-webkit-scrollbar-thumb {
            box-shadow: inset 0 0 0 10px;
        }


        /* order inc aside */
        /*.order-aside-sidebar{*/
        /*    height:90vh;*/
        /*    overflow-y: scroll;*/
        /*}*/

        /* main left sidebar */
        /*.main-left-sidebar{*/
        /*    overflow-y: scroll;*/
        /*    max-height:78vh;*/
        /*}*/



        /* for datatable pagination responsiveness */
        @media(max-width: 575px) {
            /*div.dataTables_wrapper div.dataTables_paginate ul.pagination {*/
            /*    justify-content: center;*/
            /*    flex-wrap: wrap;*/
            /*}*/
        }
        /* Dark Mode Style */
        #notificationDropdownMenu {
            width: 350px;
            background-color: #222;
            color: #fff;
            max-height: 500px;
            overflow-y: auto;
            border-radius: 10px;
            padding: 20px;
            position: absolute;
            top: 60px;  /* Ensure no extra space at the top */
            left: 0;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
            z-index: 9999;
            display: none; /* Hide by default */
        }

        /* Header Section */
        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }

        .notification-header .dropdown-item.dropdown-header {
            font-size: 20px;
            font-weight: bold;
            color: #fff;
        }

        .notification-header #markAllAsRead {
            font-size: 14px;
            color: #1DB954;
            background: none;
            border: none;
            cursor: pointer;
            transition: color 0.3s;
        }

        .notification-header #markAllAsRead:hover {
            color: #1ED760;
        }

        /* Styling for Unread Messages */
        #notificationList .dropdown-item {
            padding: 15px;
            border-radius: 8px;
            background-color: #333;
            margin-bottom: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease-in-out;
        }

        #notificationList .dropdown-item:hover {
            background-color: #1ED760;
            transform: translateX(5px);
        }

        /* Ticket Number and Message Layout */
        #notificationList .dropdown-item strong {
            font-size: 16px;
            color: #fff;
        }
        /* Style for message text inside the box */
        #notificationList .dropdown-item p {
            font-size: 14px;
            color: #ccc;
            margin-top: 5px;
            white-space: pre-wrap; /* This ensures that long text will break into a new line */
            word-wrap: break-word; /* Allows long words to break and wrap within the container */
            overflow-wrap: break-word; /* Ensures words will break to fit within the container */
        }


        /* Unread Message Badge */
        #unreadCount {
            display: inline-block;
            background-color: #f44336;
            padding: 5px 10px;
            border-radius: 50%;
            color: white;
            font-size: 14px;
            position: absolute;
            top: -5px;
            right: -5px;
        }

        /* No Messages Placeholder */
        #noUnreadMessages {
            font-size: 14px;
            color: #ccc;
            text-align: center;
            padding: 20px;
        }

        /* Responsive Design */
        @media (max-width: 767px) {
            #notificationDropdownMenu {
                width: 300px;
            }

            #notificationList .dropdown-item {
                padding: 12px;
            }
        }

        /* No Extra Space when Clicking the Icon */
        #notificationDropdownMenu.show {
            display: block;
            top: 60px; /* Ensure the dropdown is aligned below the icon */
            opacity: 1;
        }

        #notificationDropdownMenu.hide {
            display: none;
        }

    </style>
    <?php echo $__env->yieldContent('style'); ?>
</head>

<body class="hold-transition sidebar-mini">

<div class="wrapper" id="app">
    <!-- Header -->
<?php echo $__env->make('partialPages.header.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<!-- Sidebar -->
<?php echo $__env->make('partialPages.sidebar.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php echo $__env->yieldContent('content'); ?>
<!-- Footer -->
    <?php echo $__env->make('partialPages.footer.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>

<script src="<?php echo e(asset('/js/app.js')); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo e(env('GOOGLE_MAP_API')); ?>&libraries"></script>

<script>
    <?php if(count($errors) > 0): ?>
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            toastr.error("<?php echo e($error); ?>");
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
    var baseUrl="<?php echo e(url('/')); ?>"



</script>

<?php echo app('toastr')->render(); ?>

<?php echo $__env->yieldContent('script'); ?>
<script>
    $(document).ready(function () {
        fetchUnreadMessages(); // Fetch messages when page loads

        function fetchUnreadMessages() {
            $.ajax({
                url: "<?php echo e(route('unread.messages')); ?>", // Your route to get unread messages
                type: "GET",
                success: function (response) {
                    let unreadCount = response.length;
                    let notificationList = $("#notificationList");
                    notificationList.empty();
                    $("#noUnreadMessages").hide(); // Hide 'No unread messages' placeholder

                    if (unreadCount > 0) {
                        $("#unreadCount").text(unreadCount).show();
                        response.forEach(function (message) {
                            notificationList.append(`
                    <a href="#" class="dropdown-item mark-as-read" data-id="${message.id}">
                        <div class="media">
                            <div class="media-body">
                                <strong>Message</strong>
                                <p>${message.message}</p> <!-- The message will now wrap with long text -->
                            </div>
                        </div>
                    </a>
                `);
                        });
                    } else {
                        $("#unreadCount").hide();
                        $("#noUnreadMessages").show(); // Show placeholder when no messages
                    }
                }
            });
        }

        // Mark message as read when clicked
        $(document).on("click", ".mark-as-read", function () {
            let messageId = $(this).data("id");
            $.ajax({
                url: "<?php echo e(route('mark.as.read')); ?>",
                type: "POST",
                data: { id: messageId, _token: "<?php echo e(csrf_token()); ?>" },
                success: function () {
                    fetchUnreadMessages(); // Refresh notifications
                }
            });
        });

        // Mark all messages as read
        $("#markAllAsRead").click(function () {
            $.ajax({
                url: "<?php echo e(route('mark.all.read')); ?>",
                type: "POST",
                data: { _token: "<?php echo e(csrf_token()); ?>" },
                success: function () {
                    fetchUnreadMessages(); // Refresh notifications
                }
            });
        });

        // Auto-refresh notifications every 30 seconds
        setInterval(fetchUnreadMessages, 30000);
    });

</script>

</body>
</html>
<?php /**PATH C:\laragon\www\testing_project\resources\views/layouts/master.blade.php ENDPATH**/ ?>