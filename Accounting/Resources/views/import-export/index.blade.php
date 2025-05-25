@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-6">
            <x-cards.data :title="__('Import Chart of Accounts')">
                <x-form id="import-form" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>@lang('Select File')</label>
                        <input type="file" name="file" class="form-control" accept=".csv,.xlsx" required>
                        <small class="text-muted">@lang('Supported formats: CSV, Excel')</small>
                    </div>

                    <x-forms.button-primary id="import-accounts" icon="upload">
                        @lang('Import Accounts')
                    </x-forms.button-primary>
                </x-form>

                <hr>

                <div class="text-center">
                    <a href="#" class="btn btn-outline-secondary" id="download-template">
                        <i class="fa fa-download mr-1"></i>@lang('Download Template')
                    </a>
                </div>
            </x-cards.data>
        </div>

        <div class="col-md-6">
            <x-cards.data :title="__('Export Chart of Accounts')">
                <p>@lang('Export your complete chart of accounts to Excel format.')</p>

                <a href="{{ route('accounting.export.chart-of-accounts') }}" class="btn btn-success">
                    <i class="fa fa-download mr-1"></i>@lang('Export to Excel')
                </a>
            </x-cards.data>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <x-cards.data :title="__('Import Results')" id="import-results" style="display: none;">
                <div id="results-content"></div>
            </x-cards.data>
        </div>
    </div>
</div>

<script>
$('#import-accounts').click(function(e) {
    e.preventDefault();

    const formData = new FormData($('#import-form')[0]);

    $.ajax({
        url: "{{ route('accounting.import.chart-of-accounts') }}",
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.status === 'success') {
                $('#import-results').show();
                let html = `
                    <div class="alert alert-success">
                        <strong>Import Successful!</strong><br>
                        ${response.data.imported} accounts imported successfully.
                    </div>
                `;

                if (response.data.errors.length > 0) {
                    html += `
                        <div class="alert alert-warning">
                            <strong>Errors:</strong>
                            <ul class="mb-0">
                    `;
                    response.data.errors.forEach(error => {
                        html += `<li>${error}</li>`;
                    });
                    html += '</ul></div>';
                }

                $('#results-content').html(html);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            Swal.fire({
                title: 'Import Failed',
                text: response.message || 'An error occurred during import',
                icon: 'error'
            });
        }
    });
});
</script>
@endsection
