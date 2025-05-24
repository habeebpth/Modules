@include('import.process-form', [
    'headingTitle' => __('app.importExcel') . ' ' . __('app.Guest'),
    'processRoute' => route('guests.import.process'),
    'backRoute' => route('guests.index'),
    'backButtonText' => __('app.backToguests'),
])
