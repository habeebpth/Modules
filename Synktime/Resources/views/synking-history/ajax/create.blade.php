@php
$addEmployeePermission = user()->permission('add_employees');
@endphp

<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('synktime::app.menu.dataSynk')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form id="attendance-sync-form">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group my-3">
                                    <label class="f-14 text-dark-grey mb-12" data-toggle="tooltip" data-original-title="Choose the date range for which you want to sync attendance data" for="from_date">@lang('app.fromDate')</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="from_date" name="from_date" placeholder="@lang('placeholders.date')" value="{{ now()->format(company()->date_format) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group my-3">
                                    <label class="f-14 text-dark-grey mb-12" data-toggle="tooltip" data-original-title="Choose the date range for which you want to sync attendance data" for="to_date">@lang('app.toDate')</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="to_date" name="to_date" placeholder="@lang('placeholders.date')" value="{{ now()->format(company()->date_format) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <p class="text-muted">
                                    <i class="fa fa-info-circle mr-1"></i> @lang('synktime::app.syncAttendanceDescription')
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="sync-attendance-submit" icon="sync">@lang('synktime::app.menu.dataSynk')</x-forms.button-primary>
</div>

<script>
    // $('.date-picker').datepicker({
    //     format: '{{ company()->moment_date_format }}',
    //     autoclose: true,
    //     todayHighlight: true
    // });

    // Sync attendance data
    $('#sync-attendance-submit').click(function() {
        var url = "{{ route('synking-history.store') }}";
        var data = $('#attendance-sync-form').serialize();

        var button = $(this);
        button.html('<i class="fa fa-spinner fa-spin"></i> @lang("app.processing")');
        button.attr('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 3000
                    });
                    window.location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        text: response.message
                    });
                }
            },
            error: function(error) {
                Swal.fire({
                    icon: 'error',
                    text: 'An error occurred while syncing attendance data.'
                });
            },
            complete: function() {
                button.html('<i class="fa fa-sync mr-1"></i> @lang("synktime::app.menu.dataSynk")');
                button.attr('disabled', false);
                $(MODAL_LG).modal('hide');
            }
        });
    });
</script>
