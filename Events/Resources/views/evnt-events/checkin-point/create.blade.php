<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">

<div class="modal-header">
    <h5 class="modal-title">@lang('app.add') @lang('app.CheckinPoint')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>

<div class="modal-body">
    <div class="portlet-body">
        <x-form id="save-checkin-form" method="POST" class="ajax-form">
            <input type="hidden" value="{{ request()->evntid }}" name="event_id">
            <div class="row">
                <div class="col-lg-3">
                    <x-forms.text :fieldLabel="__('app.code')" fieldName="code" fieldId="code" fieldRequired="true" />
                </div>
                <div class="col-lg-5">
                    <x-forms.text :fieldLabel="__('app.name')" fieldName="name" fieldId="name" fieldRequired="true" />
                </div>

                <div class="col-lg-4">
                    <x-forms.text :fieldLabel="__('app.number')" fieldName="number" fieldId="number" fieldRequired="true" />
                </div>

                <div class="col-lg-12">
                    <x-forms.textarea :fieldLabel="__('app.description')" fieldName="description" fieldId="description" />
                </div>

                <div class="col-lg-12">
                    <x-forms.file allowedFileExtensions="png jpg jpeg svg" class="mr-0 mr-lg-2 mr-md-2"
                        :fieldLabel="__('app.image')" fieldName="image" fieldId="image" />
                </div>
            </div>

        </x-form>
    </div>
</div>

<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-checkin-point" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>
    $('#save-checkin-point').click(function () {
        $.easyAjax({
            url: "{{ route('event-checkin-points.store') }}",
            container: '#save-checkin-form',
            type: "POST",
            disableButton: true,
            blockUI: true,
            buttonSelector: '#save-checkin-point',
            file: true,
            data: new FormData(document.getElementById('save-checkin-form')),
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status === 'success') {
                    window.location.reload();
                }
            }
        });
    });

    init(MODAL_LG);
</script>
