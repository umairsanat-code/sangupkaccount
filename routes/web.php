<?php

use App\Http\Controllers\Admin\AgencyController;
use App\Http\Controllers\Admin\AirlineController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\CardPaymentController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\Admin\ImportMessageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DailySaleTicketController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VisaUmrahBookingController;
use App\Http\Controllers\Admin\VisaUmrahAgencyController;
use App\Http\Controllers\Admin\VisaUmrahServiceController;
use App\Http\Controllers\Admin\VisaUmrahPaymentController;
use App\Http\Controllers\Admin\VisaUmrahDocumentController;
use App\Http\Controllers\Admin\VisaUmrahExpenseController;
use App\Http\Controllers\Admin\VisaUmrahDashboardController;
use App\Http\Controllers\Admin\VisaUmrahReportController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('coming-soon', function () {
    return view('coming-soon');
});

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('artisan-login', [App\Http\Controllers\ArtisanCommandController::class, 'configurationPassword'])->middleware('artisan_view');
Route::post('artisanlogin', [App\Http\Controllers\ArtisanCommandController::class, 'checkConfigurationPassword'])->name('artisanlogin');
Route::get('php_artisan', [App\Http\Controllers\ArtisanCommandController::class, 'runCommand'])->middleware('artisan_view');
Route::get('artisan', [App\Http\Controllers\ArtisanCommandController::class, 'index'])->middleware(['artisan_view','artisan']);
Route::post('/webhook-sunny', [WhatsAppController::class, 'handleWebhook']);

Route::group(['middleware' => ['auth', 'admin.only']], function () {
    //whatsapp twilio route
    /*============================Admin auth routes goes here========================== */
    Route::get('categories', [\App\Http\Controllers\Admin\CategoryController::class, 'listing'])->name('categories.listing');
    Route::post('categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
    Route::delete('categories/{id}/delete', [\App\Http\Controllers\Admin\CategoryController::class, 'delete']);
    Route::get('categories/{id}/edit', [\App\Http\Controllers\Admin\CategoryController::class, 'edit']);
    Route::post('categories/{id}/update', [\App\Http\Controllers\Admin\CategoryController::class, 'update']);
    Route::get('subcategories/{id}/categories', [\App\Http\Controllers\Admin\SubcategoryController::class, 'categories']);
    Route::post('subcategories', [\App\Http\Controllers\Admin\SubcategoryController::class, 'store']);
    Route::get('subcategories', [\App\Http\Controllers\Admin\SubcategoryController::class, 'get']);
    Route::delete('subcategories/{id}/delete', [\App\Http\Controllers\Admin\SubcategoryController::class, 'delete']);
    Route::get('commissions', [\App\Http\Controllers\Admin\AppCommissionController::class, 'listing']);
    Route::post('commissions', [\App\Http\Controllers\Admin\AppCommissionController::class, 'update']);

      Route::get('reset-pwd',[\App\Http\Controllers\Admin\TeamController::class,'passwordRest']);
      Route::post('update-reset-pwd',[\App\Http\Controllers\Admin\TeamController::class,'passwordupdateM'])->name('update-reset-pwd');
    /*============================User Module goes here========================== */
    Route::get('users/buyers', [\App\Http\Controllers\Admin\BuyerController::class, 'listing'])->name('buyers.listing');
    Route::get('users/buyers/{id}/details', [\App\Http\Controllers\Admin\BuyerController::class, 'get']);
    Route::get('users/buyers/{status}/{id}/status', [\App\Http\Controllers\Admin\BuyerController::class, 'status']);
    Route::get('users/sellers', [\App\Http\Controllers\Admin\SellerController::class, 'listing'])->name('sellers.listing');
    Route::get('users/sellers/{id}/details', [\App\Http\Controllers\Admin\SellerController::class, 'get']);
    Route::get('users/sellers/{status}/{id}/status', [\App\Http\Controllers\Admin\SellerController::class, 'status']);
    Route::get('users/sellers/{status}/{id}/block', [\App\Http\Controllers\Admin\SellerController::class, 'block']);
    /*============================Roles Management Module goes here========================== */
    Route::get('roles', [\App\Http\Controllers\Admin\RoleController::class, 'listing'])->name('roles.listing');
    Route::post('roles', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('roles.store');
    Route::delete('roles/{id}/delete', [\App\Http\Controllers\Admin\RoleController::class, 'delete']);
    Route::delete('roles/{pid}/{rid}/permissions', [\App\Http\Controllers\Admin\RoleController::class, 'deletePermission']);
    Route::get('roles/{id}/edit', [\App\Http\Controllers\Admin\RoleController::class, 'edit']);
    Route::get('roles/{id}/permissions', [\App\Http\Controllers\Admin\RoleController::class, 'permissions']);
    Route::post('roles/{id}/update', [\App\Http\Controllers\Admin\RoleController::class, 'update']);
    /*============================Team Management Module goes here========================== */
    Route::get('teams', [\App\Http\Controllers\Admin\TeamController::class, 'listing'])->name('teams.listing');
    Route::get('teams/all', [\App\Http\Controllers\Admin\TeamController::class, 'teams']);
    Route::post('teams', [\App\Http\Controllers\Admin\TeamController::class, 'store'])->name('teams.store');
    Route::get('teams/{id}/delete', [\App\Http\Controllers\Admin\TeamController::class, 'delete']);
        Route::delete('/teams/delete/{id}', [\App\Http\Controllers\Admin\TeamController::class, 'destroy']);

    Route::post('teams/{id}/password', [\App\Http\Controllers\Admin\TeamController::class, 'password']);
    Route::get('teams/{id}/edit', [\App\Http\Controllers\Admin\TeamController::class, 'edit']);
    Route::post('/teams/update/{id}', [\App\Http\Controllers\Admin\TeamController::class, 'update']);
        Route::get('/getImage', [\App\Http\Controllers\Admin\TeamController::class, 'getImage'])->name('files.get');
    Route::post('/teams/upload-files', [\App\Http\Controllers\Admin\TeamController::class, 'uploadFiles'])->name('teams.uploadFiles');

    Route::get('/tickets-detail/{id}', [TicketController::class, 'ticketDetail'])->name('tickets-detail');
    Route::post('/daily-report/add-ticket', [DailySaleTicketController::class, 'addTicket'])->name('daily_report.addTicket');


    Route::resource('tickets', TicketController::class);
    Route::post('/payment-clear', [TicketController::class, 'clearPayment'])->name('payment.clear');
    Route::post('/process-refund', [TicketController::class, 'processRefund'])->name('ticket.processRefund');
    Route::get('/tickets/{id}/payment-history', [TicketController::class, 'paymentHistory']);
//    Route::get('/tickets/{id}/details', [TicketController::class, 'ticketDetails'])->name('tickets.details');
    Route::get('/tickets/{id}/invoice', [TicketController::class, 'generateInvoice'])->name('tickets.invoice');
    Route::get('/tickets/{id}/final_invoice', [TicketController::class, 'finalgenerateInvoice'])->name('tickets.invoice');
    Route::resource('airlines', AirlineController::class);
    Route::resource('agents', AgencyController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::resource('card-payments', CardPaymentController::class)->parameters([
        'card-payments' => 'cardPayment',
    ]);
    Route::get('agents/{agent}/tickets/view', [AgencyController::class, 'showAgentTickets'])->name('agents.tickets.page');
//    Route::get('agents/tickets-show/{start_date}/{end_date}/{agent_id}', [AgencyController::class, 'showAgentTickets'])->name('agents.tickets-show');
    Route::get('agents/tickets-data-sanat', [AgencyController::class, 'show'])->name('agents.tickets');

    /*============================                Vendor               ========================== */
    Route::get('vendors/payment/{agentId}', [VendorController::class, 'calculatePayments'])->name('vendors.payment');
    Route::get('vendors/payment', [VendorController::class, 'showPaymentForm'])->name('vendors.payment.form');
    Route::post('vendors/payment', [VendorController::class, 'processPayment'])->name('vendors.payment.process');
        Route::get('/profit-and-loss-data', [App\Http\Controllers\HomeController::class, 'fetchProfitAndLossData']);
            Route::post('/vendors/{id}', [VendorController::class, 'destroy'])->name('vendors.destroy');
             Route::get('vendors/payment/{id}/edit', [VendorController::class, 'edit'])->name('vendors.payment.edit');
    Route::put('vendors/payment/{id}', [VendorController::class, 'update'])->name('vendors.payment.update');


   Route::get('import_messsage',[\App\Http\Controllers\Admin\ImportMessageController::class,'index'])->name('import_messsage');
    Route::post('/messages', [ImportMessageController::class, 'store'])->name('messages');
    Route::get('/unread-messages', [ImportMessageController::class, 'getUnreadMessages'])->name('unread.messages');
    Route::post('/mark-as-read', [ImportMessageController::class, 'markMessageAsRead'])->name('mark.as.read');
    Route::post('/mark-all-read', [ImportMessageController::class, 'markAllMessagesAsRead'])->name('mark.all.read');
        Route::post('/messages/{id}', [ImportMessageController::class, 'destroy'])->name('messages.destroy');

//        Route::resource('daily_report',DailySaleTicketController::class);
    Route::get('/daily-report/{id}/invoice/download', [DailySaleTicketController::class, 'download'])->name('daily_report.invoice.download');

        Route::resource('members',UserController::class);
          Route::post('/members/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('members.toggleStatus');
    // Route::get('/members/{id}/pending-tickets', [UserController::class, 'getPendingTickets'])->name('members.pendingTickets');
        Route::get('/users/{userId}/pending-tickets', [UserController::class, 'getPendingTickets'])->name('user.pending.tickets');



//    Route::get('vendors/total-purchase-amount', [VendorController::class, 'getTotalPurchaseAmount'])->name('vendors.getTotalPurchaseAmount');
    Route::post('/get-total-purchase-amount', [VendorController::class, 'getTotalPurchaseAmount'])->name('get.total.purchase.amount');
    Route::get('/vendors', [VendorController::class, 'index'])->name('vendors');
//    Route::get('/invoice/pdf/{id}', [TicketController::class, 'downloadPDF'])->name('invoice.pdf');
    Route::get('/invoice/pdf/{id}', [TicketController::class, 'downloadPDF'])->name('invoice.download');

    /* Send Notifications*/
    Route::get('notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('create.notifications');
    Route::post('notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'send'])->name('send.notifications');

    Route::get('clients', [\App\Http\Controllers\Admin\ClientController::class, 'index'])->name('clients');

    Route::post('/clients', [\App\Http\Controllers\Admin\ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{id}/edit', [\App\Http\Controllers\Admin\ClientController::class, 'edit'])->name('clients.edit');
    Route::post('/client-update/{id}', [\App\Http\Controllers\Admin\ClientController::class, 'update'])->name('clients.update');
    Route::post('/send-sms', [\App\Http\Controllers\Admin\ClientController::class, 'sendSms'])->name('send-sms');
    Route::post('/load-previous-chats', [\App\Http\Controllers\Admin\ClientController::class,'loadPreviousChats'])->name('load.previous.chats');
  Route::get('/with-ticket', [DailySaleTicketController::class, 'indexWithTicket'])->name('daily_report.with_ticket');
  Route::get('/daily-report/export-ticket-excel', [DailySaleTicketController::class, 'exportTicketExcel'])
    ->name('daily_report.export_ticket_excel');
  Route::get('/daily-report/export-ticket-pdf', [DailySaleTicketController::class, 'exportTicketPdf'])
    ->name('daily_report.export_ticket_pdf');

    // Add this route in your routes/web.php file
    Route::get('/ticket/details/{id}', [DailySaleTicketController::class, 'getTicketDetails'])->name('ticket.details');

    Route::get('/ticket/payment-history/{id}',
        [DailySaleTicketController::class,'history']
    )->name('ticket.payment.history');



//    Route::post('/sms/received', [\App\Http\Controllers\Admin\ClientController::class, 'handleWebhook']);
Route::get('/test-cron', function () {
    \Artisan::call('schedule:run');
    return 'Scheduler executed manually!';
});
Route::get('/config-clear', function () {
    Artisan::call('optimize:clear');
    Artisan::call('event:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    return "✅ All Caches (Config, Event, View, Route) cleared successfully!";
});

// Temporary: run activity_logs migration
Route::get('/run-activity-log-migration', function () {
    Artisan::call('migrate', [
        '--path' => 'database/migrations/2026_06_20_000001_create_activity_logs_table.php',
        '--force' => true,
    ]);
    return '<pre>✅ Done!<br>' . Artisan::output() . '</pre>';
});
// Ticket Detail Page
    Route::get('/tickets/{id}', [App\Http\Controllers\Admin\TicketController::class, 'show'])->name('tickets.show');

// AJAX for Active Tickets
    Route::get('/tickets/{id}/active-ajax', [App\Http\Controllers\Admin\TicketController::class, 'ticketActiveAjax'])
        ->name('tickets.active-ajax');

// AJAX for Refunded Tickets
    Route::get('/tickets/{id}/refunded-ajax', [App\Http\Controllers\Admin\TicketController::class, 'ticketRefundedAjax'])
        ->name('tickets.refunded-ajax');
    Route::post('/tickets/{id}/activate-refund', [TicketController::class, 'activateRefundedTicket'])
        ->name('tickets.activateRefund');

    /* Activity Log */
    Route::get('activity-log', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity_log.index');
    Route::get('activity-log/data', [\App\Http\Controllers\Admin\ActivityLogController::class, 'getData'])->name('activity_log.data');
    Route::post('activity-log/reset', [\App\Http\Controllers\Admin\ActivityLogController::class, 'resetLog'])->name('activity_log.reset');

    /*============================ Visa & Umrah Module ========================== */
    Route::prefix('visa-umrah')->name('visa_umrah.')->group(function () {
        // Dashboard
        Route::get('/', [VisaUmrahDashboardController::class, 'index'])->name('dashboard');

        // Agencies / Suppliers
        Route::resource('agencies', VisaUmrahAgencyController::class);

        // Bookings (shared controller, handles both visa & umrah)
        Route::get('{type}/bookings', [VisaUmrahBookingController::class, 'index'])->name('bookings.index');
        Route::get('{type}/bookings/create', [VisaUmrahBookingController::class, 'create'])->name('bookings.create');
        Route::post('{type}/bookings', [VisaUmrahBookingController::class, 'store'])->name('bookings.store');
        Route::get('{type}/bookings/{booking}', [VisaUmrahBookingController::class, 'show'])->name('bookings.show');
        Route::get('{type}/bookings/{booking}/edit', [VisaUmrahBookingController::class, 'edit'])->name('bookings.edit');
        Route::put('{type}/bookings/{booking}', [VisaUmrahBookingController::class, 'update'])->name('bookings.update');
        Route::delete('{type}/bookings/{booking}', [VisaUmrahBookingController::class, 'destroy'])->name('bookings.destroy');
        Route::get('{type}/bookings/{booking}/print', [VisaUmrahBookingController::class, 'print'])->name('bookings.print');
        Route::get('{type}/bookings/{booking}/invoice', [VisaUmrahBookingController::class, 'print'])->name('bookings.invoice');

        // Services (hotel / transport) - AJAX
        Route::post('bookings/{booking}/services', [VisaUmrahServiceController::class, 'store'])->name('bookings.services.store');
        Route::delete('bookings/{booking}/services/{service}', [VisaUmrahServiceController::class, 'destroy'])->name('bookings.services.destroy');

        // Payments - AJAX
        Route::post('bookings/{booking}/payments', [VisaUmrahPaymentController::class, 'store'])->name('bookings.payments.store');
        Route::delete('bookings/{booking}/payments/{payment}', [VisaUmrahPaymentController::class, 'destroy'])->name('bookings.payments.destroy');

        // Documents - AJAX
        Route::post('bookings/{booking}/documents', [VisaUmrahDocumentController::class, 'store'])->name('bookings.documents.store');
        Route::delete('bookings/{booking}/documents/{document}', [VisaUmrahDocumentController::class, 'destroy'])->name('bookings.documents.destroy');

        // Expenses - AJAX
        Route::post('bookings/{booking}/expenses', [VisaUmrahExpenseController::class, 'store'])->name('bookings.expenses.store');
        Route::delete('bookings/{booking}/expenses/{expense}', [VisaUmrahExpenseController::class, 'destroy'])->name('bookings.expenses.destroy');

        // Reports
        Route::get('reports', [VisaUmrahReportController::class, 'index'])->name('reports');
    });

});

Route::post('/sms/received', [\App\Http\Controllers\Admin\ClientController::class, 'receiveSMS']);
Route::get('/test-email', function () {
    Mail::raw('This is just a testing email from Laravel SMTP setup.', function ($message) {
        $message->to('sanat.saad@gmail.com')
                ->subject('Laravel SMTP Testing');
    });

    return "Test email sent to sanat.saad@gmail.com";
});
Route::middleware(['auth'])->group(function () {
Route::resource('daily_report',DailySaleTicketController::class);
    Route::post('/daily-report/update-status', [DailySaleTicketController::class, 'updateStatus'])->name('daily_report.update_status');
    Route::post('/ticket/payment/store',
        [DailySaleTicketController::class,'storePayment']
    )->name('ticket.payment.store');
    Route::get('/payment-clear-tickets', [\App\Http\Controllers\HomeController::class, 'fetchPaymentClearTickets'])->name('payment.clear.tickets');

    Route::prefix('inquiries')->name('inquiries.')->group(function () {
        Route::get('/', [InquiryController::class, 'index'])->name('index');
        Route::get('/counts', [InquiryController::class, 'counts'])->name('counts');
        Route::get('/export', [InquiryController::class, 'export'])->name('export');
        Route::post('/add', [InquiryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [InquiryController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [InquiryController::class, 'update'])->name('update');
        Route::post('/update-status', [InquiryController::class, 'updateStatus'])->name('update_status');
        Route::delete('/{id}', [InquiryController::class, 'destroy'])->name('destroy');
    });
});

