@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('content')
<div class="content-wrapper">
    <div class="d-grid d-lg-flex d-md-flex action-bar">
        <div id="table-actions" class="flex-grow-1 align-items-center">
            <x-forms.link-primary :link="route('accounting.chart-of-accounts.create')" 
                class="mr-3 openRightModal float-left" icon="plus">
                @lang('Add Account')
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
$('body').on('click', '.delete-account', function() {
    var id = $(this).data('id');
    Swal.fire({
        title: "@lang('messages.sweetAlertTitle')",
        text: "@lang('messages.recoverRecord')",
        icon: 'warning',
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: "@lang('messages.confirmDelete')",
        cancelButtonText: "@lang('app.cancel')",
        customClass: {
            confirmButton: 'btn btn-primary mr-3',
            cancelButton: 'btn btn-secondary'
        },
        showClass: {
            popup: 'swal2-noanimation',
            backdrop: 'swal2-noanimation'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            var url = "{{ route('accounting.chart-of-accounts.destroy', ':id') }}";
            url = url.replace(':id', id);
            var token = "{{ csrf_token() }}";

            $.easyAjax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'DELETE'
                },
                success: function(response) {
                    if (response.status == "success") {
                        window.LaravelDataTables["chart-of-accounts-table"].draw(false);
                    }
                }
            });
        }
    });
});
</script>
@endpush