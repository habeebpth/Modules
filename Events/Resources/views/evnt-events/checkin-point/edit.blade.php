<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">

<div class="modal-header">
    <h5 class="modal-title">@lang('app.edit') @lang('app.CheckinPoint')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>

<div class="modal-body">
    <div class="portlet-body">
        <x-form id="update-checkin-form" method="PUT" class="ajax-form">
            <input type="hidden" name="event_id" value="{{ $checkinPoint->event_id }}">

            <div class="row">
                <div class="col-lg-5">
                    <x-forms.text :fieldLabel="__('app.name')" fieldName="name" fieldId="name"
                        :fieldValue="$checkinPoint->name" fieldRequired="true" />
                </div>

                <div class="col-lg-3">
                    <x-forms.text :fieldLabel="__('app.code')" fieldName="code" fieldId="code"
                        :fieldValue="$checkinPoint->code" fieldRequired="true" />
                </div>

                <div class="col-lg-4">
                    <x-forms.text :fieldLabel="__('app.number')" fieldName="number" fieldId="number"
                        :fieldValue="$checkinPoint->number" fieldRequired="true" />
                </div>

                <div class="col-lg-12">
                    <x-forms.textarea :fieldLabel="__('app.description')" fieldName="description" fieldId="description"
                        :fieldValue="$checkinPoint->description" />
                <div class="col-lg-12">
                    <x-forms.file allowedFileExtensions="png jpg jpeg svg pdf doc docx" class="mr-0 mr-lg-2 mr-md-2"
                        :fieldLabel="__('app.image')" fieldName="image"
                        :fieldValue="$checkinPoint->image ? $checkinPoint->image_url : '' "
                        fieldId="image">
                    </x-forms.file>
                </div>


            </div>
        </x-form>
    </div>
</div>

<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="update-checkin-point" icon="check">@lang('app.update')</x-forms.button-primary>
</div>

<script>
    $('#update-checkin-point').click(function () {
        const id = '{{ $checkinPoint->id }}';

        $.easyAjax({
            url: "{{ route('event-checkin-points.update', ':id') }}".replace(':id', id),
            container: '#update-checkin-form',
            type: "POST",
            disableButton: true,
            blockUI: true,
            buttonSelector: '#update-checkin-point',
            file: true,
            data: new FormData(document.getElementById('update-checkin-form')),
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
