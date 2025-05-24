@include('import.process-form', [
    'headingTitle' => __('app.importExcel') . ' ' . __('app.menu.SalarayCalculation'),
    'processRoute' => route('salary-calculation.import.process'),
    'backRoute' => route('salary-calculation.index'),
    'backButtonText' => __('app.backTosalaryCalculation'),
])
