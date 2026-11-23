@extends('layouts.master')

@section('title', 'Visa & Umrah Dashboard')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-12 d-flex justify-content-between">
                    <h4 class="mb-0">Visa & Umrah Dashboard</h4>
                    <div>
                        <a href="{{ route('visa_umrah.reports') }}" class="btn btn-success btn-sm"><i class="fas fa-chart-line"></i> Reports</a>
                        <a href="{{ route('visa_umrah.agencies.index') }}" class="btn btn-info btn-sm"><i class="fas fa-building"></i> Agencies</a>
                    </div>
                </div>
            </div>

            <div class="row">
                @php
                    $cards = [
                        ['label' => 'Total Visa Bookings', 'value' => $totalVisa, 'icon' => 'fa-file-invoice', 'color' => 'primary', 'url' => route('visa_umrah.bookings.index', 'visa')],
                        ['label' => 'Pending Visa', 'value' => $pendingVisa, 'icon' => 'fa-clock', 'color' => 'warning', 'url' => route('visa_umrah.bookings.index', 'visa')],
                        ['label' => 'Approved Visa', 'value' => $approvedVisa, 'icon' => 'fa-check-circle', 'color' => 'success', 'url' => route('visa_umrah.bookings.index', 'visa')],
                        ['label' => 'Total Umrah Bookings', 'value' => $totalUmrah, 'icon' => 'fa-mosque', 'color' => 'info', 'url' => route('visa_umrah.bookings.index', 'umrah')],
                        ['label' => 'Active Umrah Groups', 'value' => $activeUmrah, 'icon' => 'fa-users', 'color' => 'primary', 'url' => route('visa_umrah.bookings.index', 'umrah')],
                        ['label' => 'Total Clients', 'value' => $totalClients, 'icon' => 'fa-user-tie', 'color' => 'secondary', 'url' => null],
                        ['label' => 'Total Sales (€)', 'value' => number_format($totalSales, 2), 'icon' => 'fa-euro-sign', 'color' => 'success', 'url' => route('visa_umrah.reports')],
                        ['label' => 'Client Pending (€)', 'value' => number_format($clientPending, 2), 'icon' => 'fa-exclamation-triangle', 'color' => 'danger', 'url' => route('visa_umrah.reports')],
                        ['label' => 'Total Agency Payable (€)', 'value' => number_format($totalAgencyPayable, 2), 'icon' => 'fa-hand-holding-usd', 'color' => 'warning', 'url' => route('visa_umrah.reports')],
                        ['label' => 'Paid to Agencies (€)', 'value' => number_format($agencyPaid, 2), 'icon' => 'fa-money-check-alt', 'color' => 'info', 'url' => route('visa_umrah.agencies.index')],
                        ['label' => 'Total Profit (€)', 'value' => number_format($totalProfit, 2), 'icon' => 'fa-chart-pie', 'color' => 'success', 'url' => route('visa_umrah.reports')],
                        ['label' => 'This Month Profit (€)', 'value' => number_format($monthProfit, 2), 'icon' => 'fa-calendar-check', 'color' => 'primary', 'url' => route('visa_umrah.reports')],
                    ];
                @endphp
                @foreach($cards as $c)
                    <div class="col-xl-2 col-md-4 col-6 mb-3">
                        @if($c['url'])
                            <a href="{{ $c['url'] }}" class="text-decoration-none">
                        @endif
                        <div class="border rounded p-3 text-center bg-white h-100">
                            <i class="fas {{ $c['icon'] }} text-{{ $c['color'] }} mb-2" style="font-size:1.4rem;"></i>
                            <div class="font-weight-bold h5 mb-0">{{ $c['value'] }}</div>
                            <small class="text-muted">{{ $c['label'] }}</small>
                        </div>
                        @if($c['url'])
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header bg-white"><h5 class="mb-0">Monthly Sales & Profit (Last 6 Months)</h5></div>
                        <div class="card-body"><canvas id="monthlyChart" height="120"></canvas></div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header bg-white"><h5 class="mb-0">Visa Country Wise</h5></div>
                        <div class="card-body"><canvas id="countryChart" height="120"></canvas></div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header bg-white"><h5 class="mb-0">Agency Wise Bookings</h5></div>
                        <div class="card-body"><canvas id="agencyChart" height="120"></canvas></div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header bg-white"><h5 class="mb-0">Pending Client Payments (Top 10)</h5></div>
                        <div class="card-body">
                            <table class="table table-sm table-bordered">
                                <thead><tr><th>Booking</th><th>Client</th><th>Pending</th></tr></thead>
                                <tbody>
                                @forelse($pendingClientPayments as $b)
                                    <tr>
                                        <td>
                                            <a href="{{ route('visa_umrah.bookings.show', ['type' => $b->booking_type, 'booking' => $b->id]) }}">{{ $b->booking_no }}</a>
                                        </td>
                                        <td>{{ $b->client_name }}</td>
                                        <td class="text-danger text-right">
                                            @php $pend = ($b->main_client_total + (float)$b->sc_sum) - (float)$b->cr_sum; @endphp
                                            {{ number_format($pend, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted">No pending client payments</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        var monthlyLabels = @json($monthLabels);
        var monthlySales = @json($monthlySales);
        var monthlyProfit = @json($monthlyProfit);

        new Chart(document.getElementById('monthlyChart'), {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [
                    {label: 'Sales', data: monthlySales, backgroundColor: 'rgba(37,99,235,0.7)'},
                    {label: 'Profit', data: monthlyProfit, backgroundColor: 'rgba(22,163,74,0.7)'}
                ]
            },
            options: {responsive: true, plugins: {legend: {position: 'bottom'}}}
        });

        var countries = @json($countryWise);
        new Chart(document.getElementById('countryChart'), {
            type: 'pie',
            data: {
                labels: countries.map(function (c) { return c.visa_country; }),
                datasets: [{data: countries.map(function (c) { return c.total; })}]
            },
            options: {responsive: true}
        });

        var agencies = @json($agencyWise);
        new Chart(document.getElementById('agencyChart'), {
            type: 'bar',
            data: {
                labels: agencies.map(function (a) { return a.agency_name; }),
                datasets: [{label: 'Bookings', data: agencies.map(function (a) { return a.total; }), backgroundColor: 'rgba(245,158,11,0.75)'}]
            },
            options: {responsive: true, indexAxis: 'y', plugins: {legend: {display: false}}}
        });
    </script>
@endsection