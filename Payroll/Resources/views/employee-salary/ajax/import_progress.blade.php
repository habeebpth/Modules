@include('import.process-form', [
    'headingTitle' => __('app.importExcel') . ' ' . __('payroll::app.menu.employeeSalary'),
    'processRoute' => route('employee-salary.import.process'),
    'backRoute' => route('employee-salary.index'),
    'backButtonText' => __('app.backToEmployeeSalary'),
])
