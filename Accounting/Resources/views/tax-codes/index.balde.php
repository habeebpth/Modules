@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('content')
<div class="content-wrapper">
    <div class="d-grid d-lg-flex d-md-flex action-bar">
        <div id="table-actions" class="flex-grow-1 align-items-center">
            <x-forms.link-primary :link="route('accounting.tax-codes.create')"
                class="mr-3 openRightModal float-left" icon="plus">
                @lang('Create Tax Code')
            </x-forms.link-primary>
        </div>
    </div>

    <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
        {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}
    </div>
</div>
@endsection

@push('scripts')
@include('sections.datatable_js')

<script>
$('body').on('click', '.delete-tax-code', function() {
    var id = $(this).data('id');
    Swal.fire({
        title: "@lang('messages.sweetAlertTitle')",
        text: "@lang('messages.recoverRecord')",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "@lang('messages.confirmDelete')",
        cancelButtonText: "@lang('app.cancel')",
        customClass: {
            confirmButton: 'btn btn-primary mr-3',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            var url = "{{ route('accounting.tax-codes.destroy', ':id') }}";
            url = url.replace(':id', id);

            $.easyAjax({
                type: 'DELETE',
                url: url,
                success: function(response) {
                    if (response.status == "success") {
                        window.LaravelDataTables["tax-codes-table"].draw(false);
                    }
                }
            });
        }
    });
});
</script>
@endpush
