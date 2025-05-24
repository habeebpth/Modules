@extends('layouts.app')

@push('styles')
    <style>
        .card-counter {
            box-shadow: 2px 2px 10px #DADADA;
            padding: 20px 10px;
            background-color: #fff;
            height: 100px;
            border-radius: 5px;
            transition: .3s linear all;
        }

        .card-counter:hover {
            box-shadow: 4px 4px 20px #DADADA;
            transition: .3s linear all;
        }

        .card-counter i {
            font-size: 4em;
            opacity: 0.2;
        }

        .card-counter .count-numbers {
            position: absolute;
            right: 35px;
            top: 20px;
            font-size: 32px;
            display: block;
        }

        .card-counter .count-name {
            position: absolute;
            right: 35px;
            top: 65px;
            font-style: italic;
            text-transform: capitalize;
            opacity: 0.5;
            display: block;
            font-size: 18px;
        }

        .card-counter.primary {
            background-color: #007bff;
            color: #FFF;
        }

        .card-counter.success {
            background-color: #28a745;
            color: #FFF;
        }

        .card-counter.danger {
            background-color: #dc3545;
            color: #FFF;
        }

        .card-counter.info {
            background-color: #17a2b8;
            color: #FFF;
        }

        .card-counter.warning {
            background-color: #ffc107;
            color: #FFF;
        }

        .attendance-filter {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .attendance-table thead th {
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
            z-index: 1;
        }

        .attendance-summary-chart {
            height: 300px;
        }

        .employee-stats {
            margin-top: 20px;
        }

        .perfect-attendance {
            background-color: #d4edda;
            color: #155724;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 12px;
        }

        .late-warning {
            background-color: #fff3cd;
            color: #856404;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 12px;
        }

        .absent-warning {
            background-color: #f8d7da;
            color: #721c24;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 12px;
        }
    </style>
@endpush

@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <!-- ATTENDANCE DASHBOARD HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0 f-21 font-weight-bold text-dark">@lang('synktime::app.attendance_dashboard')</h2>
                <div class="d-flex mt-1">
                    <p class="mb-0 text-dark-grey">
                        @lang('app.lastUpdated'): {{ now()->format('d M Y, h:i A') }}
                    </p>
                </div>
            </div>
            <div>
                <a href="" class="btn btn-sm btn-secondary mr-2">
                    <i class="fa fa-chevron-left"></i> @lang('app.back')
                </a>
                <a href="#" id="refresh-dashboard" class="btn btn-sm btn-primary">
                    <i class="fa fa-sync"></i> @lang('app.refresh')
                </a>
            </div>
        </div>

        <!-- FILTER SECTION -->
        <div class="attendance-filter mb-4">
            <form id="filter-form" class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="month-filter">@lang('app.month')</label>
                        <select class="form-control" id="month-filter">
                            <option value="{{ now()->format('m') }}">{{ now()->format('F') }}</option>
                            <option value="{{ now()->subMonth()->format('m') }}">{{ now()->subMonth()->format('F') }}</option>
                            <option value="{{ now()->subMonths(2)->format('m') }}">{{ now()->subMonths(2)->format('F') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="year-filter">@lang('app.year')</label>
                        <select class="form-control" id="year-filter">
                            <option value="{{ now()->format('Y') }}">{{ now()->format('Y') }}</option>
                            <option value="{{ now()->subYear()->format('Y') }}">{{ now()->subYear()->format('Y') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="department-filter">@lang('app.department')</label>
                        <select class="form-control" id="department-filter">
                            <option value="all">@lang('app.all')</option>
                            @foreach(\App\Models\Team::all() as $department)
                                <option value="{{ $department->id }}">{{ $department->team_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">@lang('app.apply')</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- STATS CARDS -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card-counter primary">
                    <i class="fa fa-users"></i>
                    <span class="count-numbers" id="total-employees">{{ \App\Models\EmployeeDetails::count() }}</span>
                    <span class="count-name">@lang('synktime::app.total_employees')</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-counter success">
                    <i class="fa fa-check-circle"></i>
                    <span class="count-numbers" id="present-count">{{ \App\Models\Attendance::whereMonth('clock_in_time', now()->month)->whereYear('clock_in_time', now()->year)->count() }}</span>
                    <span class="count-name">@lang('synktime::app.present_days')</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-counter danger">
                    <i class="fa fa-times-circle"></i>
                    <span class="count-numbers" id="absent-count">0</span>
                    <span class="count-name">@lang('synktime::app.absent_days')</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-counter warning">
                    <i class="fa fa-clock-o"></i>
                    <span class="count-numbers" id="late-count">{{ \App\Models\Attendance::whereMonth('clock_in_time', now()->month)->whereYear('clock_in_time', now()->year)->where('late', 'yes')->count() }}</span>
                    <span class="count-name">@lang('synktime::app.late_days')</span>
                </div>
            </div>
        </div>

        <!-- CHARTS ROW -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-white border-0 b-shadow-4">
                    <div class="card-header bg-white border-0 text-capitalize d-flex justify-content-between p-20">
                        <h4 class="card-title mb-0">@lang('synktime::app.daily_attendance')</h4>
                    </div>
                    <div class="card-body">
                        <div id="daily-attendance-chart" class="attendance-summary-chart"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-white border-0 b-shadow-4">
                    <div class="card-header bg-white border-0 text-capitalize d-flex justify-content-between p-20">
                        <h4 class="card-title mb-0">@lang('synktime::app.attendance_summary')</h4>
                    </div>
                    <div class="card-body">
                        <div id="attendance-summary-chart" class="attendance-summary-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- EMPLOYEE ATTENDANCE TABLE -->
        <div class="row">
            <div class="col-md-12">
                <div class="card bg-white border-0 b-shadow-4">
                    <div class="card-header bg-white border-0 text-capitalize d-flex justify-content-between p-20">
                        <h4 class="card-title mb-0">@lang('synktime::app.employee_attendance')</h4>
                        <div>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-secondary btn-sm" id="export-csv">
                                    <i class="fa fa-file-excel-o"></i> CSV
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" id="export-pdf">
                                    <i class="fa fa-file-pdf-o"></i> PDF
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" id="print-table">
                                    <i class="fa fa-print"></i> @lang('app.print')
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover attendance-table" id="employee-attendance-table">
                                <thead>
                                    <tr>
                                        <th>@lang('app.id')</th>
                                        <th>@lang('app.employee')</th>
                                        <th>@lang('app.department')</th>
                                        <th>@lang('app.daysPresent')</th>
                                        <th>@lang('app.daysLate')</th>
                                        <th>@lang('app.daysAbsent')</th>
                                        <th>@lang('app.attendance')</th>
                                        <th>@lang('app.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- This will be filled with AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- CONTENT WRAPPER END -->
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    $(document).ready(function() {
        // Initialize Charts
        initializeDailyAttendanceChart();
        initializeAttendanceSummaryChart();

        // Load Employee Attendance Data
        loadEmployeeAttendanceData();

        // Handle Filter Form Submit
        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            refreshDashboard();
        });

        // Handle Refresh Button
        $('#refresh-dashboard').click(function(e) {
            e.preventDefault();
            refreshDashboard();
        });

        // Export and Print Functions
        $('#export-csv').click(function() {
            // Implement CSV export logic
            alert('CSV Export functionality will be implemented here');
        });

        $('#export-pdf').click(function() {
            // Implement PDF export logic
            alert('PDF Export functionality will be implemented here');
        });

        $('#print-table').click(function() {
            // Implement print logic
            window.print();
        });
    });

    function refreshDashboard() {
        // Refresh all dashboard components based on filters
        var month = $('#month-filter').val();
        var year = $('#year-filter').val();
        var department = $('#department-filter').val();

        // Show loading indicators
        showLoading();

        // Refresh charts and data
        updateDailyAttendanceChart(month, year, department);
        updateAttendanceSummaryChart(month, year, department);
        loadEmployeeAttendanceData(month, year, department);

        // Update stats
        updateStatCards(month, year, department);
    }

    function showLoading() {
        // Implement loading indicators for charts and tables
    }

    function initializeDailyAttendanceChart() {
        var options = {
            series: [{
                name: 'Present',
                data: [30, 28, 30, 29, 33, 25, 31, 35, 29, 30, 32, 31, 32, 34]
            }, {
                name: 'Late',
                data: [5, 4, 7, 3, 5, 6, 8, 3, 2, 5, 7, 4, 6, 3]
            }],
            chart: {
                type: 'bar',
                height: 300,
                stacked: false,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14'],
                title: {
                    text: 'Date of Month'
                }
            },
            yaxis: {
                title: {
                    text: 'Number of Employees'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " employees"
                    }
                }
            },
            colors: ['#28a745', '#ffc107']
        };

        var chart = new ApexCharts(document.querySelector("#daily-attendance-chart"), options);
        chart.render();

        // Store chart instance for later updates
        window.dailyAttendanceChart = chart;
    }

    function initializeAttendanceSummaryChart() {
        var options = {
            series: [70, 20, 10],
            chart: {
                type: 'donut',
                height: 300
            },
            labels: ['Present', 'Absent', 'Late'],
            colors: ['#28a745', '#dc3545', '#ffc107'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var chart = new ApexCharts(document.querySelector("#attendance-summary-chart"), options);
        chart.render();

        // Store chart instance for later updates
        window.attendanceSummaryChart = chart;
    }

    function updateDailyAttendanceChart(month, year, department) {
        // This would typically be an AJAX call to get updated data
        // For now, we'll simulate with random data

        var dates = [];
        var daysInMonth = new Date(year, month, 0).getDate();

        for (var i = 1; i <= daysInMonth; i++) {
            dates.push(i.toString());
        }

        var presentData = [];
        var lateData = [];

        for (var i = 0; i < daysInMonth; i++) {
            presentData.push(Math.floor(Math.random() * 20) + 20); // 20-40 present
            lateData.push(Math.floor(Math.random() * 10)); // 0-10 late
        }

        window.dailyAttendanceChart.updateOptions({
            series: [{
                name: 'Present',
                data: presentData
            }, {
                name: 'Late',
                data: lateData
            }],
            xaxis: {
                categories: dates
            }
        });
    }

    function updateAttendanceSummaryChart(month, year, department) {
        // This would typically be an AJAX call to get updated data
        // For now, we'll simulate with random data

        var present = Math.floor(Math.random() * 20) + 60; // 60-80% present
        var absent = Math.floor(Math.random() * 10) + 10; // 10-20% absent
        var late = 100 - present - absent; // Remainder are late

        window.attendanceSummaryChart.updateSeries([present, absent, late]);
    }

    function loadEmployeeAttendanceData(month, year, department) {
        // This would typically be an AJAX call to get employee data
        // For demonstration, we'll use sample data

        var sampleData = [
            { id: 1, name: 'John Doe', department: 'Engineering', present: 20, late: 2, absent: 1, status: 'Good' },
            { id: 2, name: 'Jane Smith', department: 'Design', present: 18, late: 1, absent: 4, status: 'Average' },
            { id: 3, name: 'Bob Johnson', department: 'Marketing', present: 15, late: 5, absent: 3, status: 'Poor' },
            { id: 4, name: 'Alice Brown', department: 'Engineering', present: 22, late: 0, absent: 1, status: 'Excellent' },
            { id: 5, name: 'Charlie Davis', department: 'Sales', present: 19, late: 3, absent: 1, status: 'Good' }
        ];

        var tableBody = '';

        sampleData.forEach(function(employee) {
            var statusClass = '';
            var statusText = '';

            if (employee.late > 3 || employee.absent > 2) {
                statusClass = 'absent-warning';
                statusText = 'Poor';
            } else if (employee.late > 0) {
                statusClass = 'late-warning';
                statusText = 'Average';
            } else {
                statusClass = 'perfect-attendance';
                statusText = 'Excellent';
            }

            tableBody += `<tr>
                <td>${employee.id}</td>
                <td>${employee.name}</td>
                <td>${employee.department}</td>
                <td>${employee.present}</td>
                <td>${employee.late}</td>
                <td>${employee.absent}</td>
                <td><span class="${statusClass}">${statusText}</span></td>
                <td>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-primary" onclick="viewEmployeeDetails(${employee.id})">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
        });

        $('#employee-attendance-table tbody').html(tableBody);
    }

    function updateStatCards(month, year, department) {
        // This would typically be an AJAX call to get updated stats
        // For now, we'll update with random values

        $('#total-employees').text(Math.floor(Math.random() * 20) + 30); // 30-50 employees
        $('#present-count').text(Math.floor(Math.random() * 100) + 400); // 400-500 present days
        $('#absent-count').text(Math.floor(Math.random() * 50) + 50); // 50-100 absent days
        $('#late-count').text(Math.floor(Math.random() * 40) + 20); // 20-60 late days
    }

    function viewEmployeeDetails(employeeId) {
        // Redirect to employee details page
        window.location.href = `/employees/${employeeId}/attendance`;
    }
</script>
@endpush
