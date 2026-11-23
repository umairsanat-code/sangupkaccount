

<?php $__env->startSection('title', 'Home'); ?>

<?php $__env->startSection('style'); ?>
    <style>
        .dashboard-filter-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
            padding: 14px;
            margin-bottom: 14px;
        }

        .info-box {
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
        }

        .info-box .info-box-number {
            font-weight: 700;
            font-size: 1.2rem;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Dashboard</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content home-section">
            <div class="container-fluid">
                <div class="dashboard-filter-card">
                    <form method="GET" action="<?php echo e(route('home')); ?>">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <label class="form-label mb-1">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="<?php echo e(optional($startDate)->format('Y-m-d')); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label mb-1">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="<?php echo e(optional($endDate)->format('Y-m-d')); ?>">
                            </div>
                            <div class="col-md-3 d-flex">
                                <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                                <a href="<?php echo e(route('home')); ?>" class="btn btn-outline-secondary ml-2 w-100">Reset</a>
                            </div>
                            <div class="col-md-3 text-md-right mt-2 mt-md-0">
                                <small class="text-muted">
                                    Showing: <?php echo e(optional($startDate)->format('d M Y')); ?> - <?php echo e(optional($endDate)->format('d M Y')); ?>

                                </small>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Info boxes -->
                <div class="row">
    
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box bg-info">
            <span class="info-box-icon"><i class="fas fa-ticket-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Issued</span>
                <span class="info-box-number"><?php echo e($totalTickets ?? 0); ?></span>
            </div>
        </div>
    </div>

    
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box bg-primary">
            <span class="info-box-icon"><i class="fas fa-user-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Issues (Date Range)</span>
                <span class="info-box-number"><?php echo e($totalCustomers ?? 0); ?></span>
            </div>
        </div>
    </div>

    
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box bg-success">
            <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Sales (Date Range)</span>
                <span class="info-box-number"><?php echo e(number_format($todaySales ?? 0, 2)); ?></span>
            </div>
        </div>
    </div>

    
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box bg-warning">
            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Vendor Pending Payments</span>
                <span class="info-box-number"><?php echo e(number_format($pendingPayments ?? 0, 2)); ?></span>
            </div>
        </div>
    </div>

    <?php if(auth()->id()=='1'): ?>
    
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box bg-cyan">
            <span class="info-box-icon"><i class="fas fa-calendar-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Booked (Date Range)</span>
                <span class="info-box-number"><?php echo e(number_format($todayReservation ?? 0, 2)); ?></span>
            </div>
        </div>
    </div>

    
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box bg-teal">
            <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Profit (Date Range)</span>
                <span class="info-box-number"><?php echo e(number_format($todayProfit ?? 0, 2)); ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box bg-danger">
            <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Expenses</span>
                <span class="info-box-number"><?php echo e(number_format($totalExpenses ?? 0, 2)); ?></span>
            </div>
        </div>
    </div>

    <?php if(auth()->id()=='1'): ?>
    
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box bg-purple">
            <span class="info-box-icon"><i class="fas fa-balance-scale"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Net Profit/Loss</span>
                <span class="info-box-number"><?php echo e(number_format($netProfitLoss ?? 0, 2)); ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
<div class="col-12 col-sm-6 col-md-3">
    <div class="info-box bg-gradient-indigo">
        <span class="info-box-icon"><i class="fas fa-credit-card"></i></span>
        <div class="info-box-content">
            <span class="info-box-text">Payments (Card)</span>
            <span class="info-box-number"><?php echo e(number_format($cardPayments ?? 0, 2)); ?></span>
        </div>
    </div>
</div>


<div class="col-12 col-sm-6 col-md-3">
    <div class="info-box bg-gradient-success">
        <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>


        <div class="info-box-content">
            <span class="info-box-text">Payments (Cash)</span>
            <span class="info-box-number"><?php echo e(number_format($cashPayments ?? 0, 2)); ?></span>
        </div>
    </div>
</div>


<div class="col-12 col-sm-6 col-md-3">
    <div class="info-box bg-gradient-warning">
        <span class="info-box-icon"><i class="fas fa-university"></i></span>
        <div class="info-box-content">
            <span class="info-box-text">Payments (Bank)</span>
            <span class="info-box-number"><?php echo e(number_format($bankPayments ?? 0, 2)); ?></span>
        </div>
    </div>
</div>

</div>

                <!-- /.row -->

                <!-- Graph and Table Section -->
                <div class="row">
                    <!-- Tickets Graph -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Tickets Overview (2024)</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="tickets-chart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Customers Graph -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Customers Overview (2024)</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="customers-chart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <h2 class="mt-3 ml-4 text-dark text-bold">Monthly Profit and Loss</h2>
            <label for="yearDropdown" class="mt-3 ml-4 text-dark">Select Year:</label>
            <select id="yearDropdown" class="form-control mt-1 mb-1 ml-4" style="width: 200px;">
                <?php $__currentLoopData = $availableYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($year); ?>" <?php echo e($currentYear == $year ? 'selected' : ''); ?>>
                        <?php echo e($year); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <table class="table table-bordered table-striped">
                <thead class="bg-dark text-white">
                    <tr>
                        <th>Month</th>
                        <th>Purchase Total</th>
                        <th>Gross Profit/Loss</th>
                        <th>Expenses</th>
                        <th>Net Profit/Loss</th>
                    </tr>
                </thead>
                <tbody id="profitLossTableBody">
                    <?php 
                        // Bootstrap row colors
                        $colors = ['table-primary','table-success','table-warning','table-danger','table-info','table-secondary'];
                    ?>

                    <?php $__currentLoopData = $profitAndLoss; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $monthData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="<?php echo e($colors[$index % count($colors)]); ?>">
                            <td><?php echo e(\Carbon\Carbon::createFromDate($monthData['year'], $monthData['month'], 1)->format('F-Y')); ?></td>
                            <td><?php echo e(number_format($monthData['purchase_total'], 2)); ?></td>
                            <td><?php echo e(number_format($monthData['gross_profit'], 2)); ?></td>
                            <td><?php echo e(number_format($monthData['expenses'], 2)); ?></td>
                            <td class="<?php echo e($monthData['net_profit_or_loss'] >= 0 ? 'text-success' : 'text-danger'); ?>">
                                <?php echo e(number_format($monthData['net_profit_or_loss'], 2)); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <div class="p-3">
                <canvas id="profit-loss-chart" style="height: 320px;"></canvas>
            </div>

            <!-- Pagination -->
            <div class="mt-4 ml-3" style="float: right">
                
            </div>
        </div>
    </div>
</div>

                <!-- /.row -->

                <!-- Team Performance Section -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Team Performance (Ticket Earnings)</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="team-performance-chart" style="height: 400px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if(auth()->id()=='1'): ?>
                    
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h3 class="card-title mb-0"><i class="fas fa-users"></i> Agent-wise Vendor Remaining</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php if(count($agentsVendorData) > 0): ?>
                                            <?php
                                                $colors = ['bg-primary','bg-success','bg-warning text-dark','bg-danger','bg-info text-dark','bg-secondary','bg-dark text-white'];
                                            ?>
                                            <?php $__currentLoopData = $agentsVendorData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="col-md-3 mb-3">
                                                    <div class="p-3 rounded <?php echo e($colors[$loop->index % count($colors)]); ?> shadow-sm" style="min-height: 120px;">
                                                        <strong><?php echo e($agent['name']); ?></strong><br>
                                                        <small>Purchase: <?php echo e($agent['total_purchase']); ?></small><br>
                                                        <small>Paid: <?php echo e($agent['total_paid']); ?></small><br>
                                                        <strong>Remaining: <?php echo e($agent['total_remaining']); ?></strong>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <div class="col-12">
                                                <div class="alert alert-info mb-0">No agent data available for the selected range.</div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>

        // Tickets Graph
        var ticketsCtx = document.getElementById('tickets-chart').getContext('2d');
        new Chart(ticketsCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [
                    {
                        label: 'Tickets Count',
                        data: <?php echo json_encode($ticketsCount); ?>,
                        borderColor: '#00bfff', // Light blue
                        backgroundColor: 'rgba(0, 191, 255, 0.2)',
                        borderWidth: 2,
                        fill: true
                    },
                    {
                        label: 'Sales Amount',
                        data: <?php echo json_encode($salesAmount); ?>,
                        borderColor: '#28a745', // Green
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        borderWidth: 2,
                        fill: true
                    },
                    {
                        label: 'Profit Amount',
                        data: <?php echo json_encode($profitAmount); ?>,
                        borderColor: '#6f42c1', // Purple
                        backgroundColor: 'rgba(111, 66, 193, 0.2)',
                        borderWidth: 2,
                        fill: true
                    },
                    {
                        label: 'Loss Amount',
                        data: <?php echo json_encode($lossAmount); ?>,
                        borderColor: '#dc3545', // Red
                        backgroundColor: 'rgba(220, 53, 69, 0.2)',
                        borderWidth: 2,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            color: '#334155',
                            font: {
                                size: 14
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: '#334155',
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)' // Light grid lines
                        }
                    },
                    y: {
                        ticks: {
                            color: '#334155',
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)' // Light grid lines
                        }
                    }
                }
            }
        });

        // Customers Graph
        var customersCtx = document.getElementById('customers-chart').getContext('2d');
        new Chart(customersCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Customers Count',
                    data: <?php echo json_encode($customersCount); ?>,
                    backgroundColor: 'rgba(255, 206, 86, 0.2)', // Light yellow
                    borderColor: 'rgba(255, 206, 86, 1)', // Yellow border
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            color: '#334155',
                            font: {
                                size: 14
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: '#334155',
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)' // Light grid lines
                        }
                    },
                    y: {
                        ticks: {
                            color: '#334155',
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)' // Light grid lines
                        }
                    }
                }
            }
        });

    </script>
    <script>
        let profitLossChart;
        const rowColors = ['table-primary', 'table-success', 'table-warning', 'table-danger', 'table-info', 'table-secondary'];

        function toNumber(value) {
            return Number.parseFloat(value || 0);
        }

        function renderProfitLossChart(data) {
            const ctx = document.getElementById('profit-loss-chart').getContext('2d');
            const labels = data.map((row) => row.month_name);
            const grossProfit = data.map((row) => toNumber(row.gross_profit));
            const expenses = data.map((row) => toNumber(row.expenses));
            const netProfitLoss = data.map((row) => toNumber(row.net_profit_or_loss));

            if (profitLossChart) {
                profitLossChart.destroy();
            }

            profitLossChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Gross Profit/Loss',
                            data: grossProfit,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Expenses',
                            data: expenses,
                            backgroundColor: 'rgba(255, 159, 64, 0.6)',
                            borderColor: 'rgba(255, 159, 64, 1)',
                            borderWidth: 1
                        },
                        {
                            type: 'line',
                            label: 'Net Profit/Loss',
                            data: netProfitLoss,
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.2)',
                            borderWidth: 2,
                            tension: 0.25,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function renderProfitLossTable(data) {
            const tableBody = document.getElementById('profitLossTableBody');
            tableBody.innerHTML = '';

            data.forEach((row, index) => {
                const tableRow = document.createElement('tr');
                tableRow.className = rowColors[index % rowColors.length];
                const netClass = toNumber(row.net_profit_or_loss) >= 0 ? 'text-success' : 'text-danger';

                tableRow.innerHTML = `
                    <td>${row.month_name}</td>
                    <td>${toNumber(row.purchase_total).toFixed(2)}</td>
                    <td>${toNumber(row.gross_profit).toFixed(2)}</td>
                    <td>${toNumber(row.expenses).toFixed(2)}</td>
                    <td class="${netClass}">${toNumber(row.net_profit_or_loss).toFixed(2)}</td>
                `;

                tableBody.appendChild(tableRow);
            });
        }

        function loadProfitLossData(year) {
            fetch(`/profit-and-loss-data?year=${year}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    renderProfitLossTable(data);
                    renderProfitLossChart(data);
                })
                .catch((error) => console.error('Error fetching data:', error));
        }

        document.getElementById('yearDropdown').addEventListener('change', function () {
            loadProfitLossData(this.value);
        });

        document.addEventListener('DOMContentLoaded', function () {
            loadProfitLossData(document.getElementById('yearDropdown').value);
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const data = <?php echo json_encode($teamEarnings); ?>;

            const totalEarnings = data.reduce((sum, member) => sum + (Number(member.earnings) || 0), 0);

            const backgroundColors = [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                '#9966FF', '#FF9F40', '#EB3B5A', '#00C897'
            ];

            const ctx = document.getElementById('team-performance-chart').getContext('2d');

            const chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.map(member => member.name),
                    datasets: [{
                        data: data.map(member => Number(member.earnings) || 0),
                        backgroundColor: backgroundColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const member = data[context.dataIndex];
                                    const value = Number(member.earnings) || 0;
                                    const profit = Number(member.profit) || 0;
                                    const percent = totalEarnings ? ((value / totalEarnings) * 100).toFixed(1) : 0;

                                    return `${member.name}: €${value.toLocaleString()} | Profit: €${profit.toLocaleString()} (${percent}%)`;
                                }
                            }
                        },
                        datalabels: {
                            formatter: function (value, context) {
                                const member = data[context.dataIndex];
                                const profit = Number(member.profit) || 0;
                                const percent = totalEarnings ? ((value / totalEarnings) * 100).toFixed(1) : 0;

                                // Avoid clutter from very small slices
                                if (percent < 5) return '';

                                return `${member.name}\n€${value.toLocaleString()}\nProfit: €${profit.toLocaleString()}`;
                            },
                            color: function (context) {
                                // Contrast-based label color (black for light backgrounds, white for dark)
                                const bgColor = context.dataset.backgroundColor[context.dataIndex];
                                const hex = bgColor.replace('#', '');
                                const r = parseInt(hex.substring(0, 2), 16);
                                const g = parseInt(hex.substring(2, 4), 16);
                                const b = parseInt(hex.substring(4, 6), 16);
                                const luminance = (0.299 * r + 0.587 * g + 0.114 * b);

                                return luminance > 140 ? '#000000' : '#FFFFFF';
                            },
                            font: {
                                weight: 'bold',
                                size: 12
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        });
    </script>



<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\testing_project\resources\views/home.blade.php ENDPATH**/ ?>