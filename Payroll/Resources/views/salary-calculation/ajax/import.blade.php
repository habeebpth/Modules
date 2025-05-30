<div class="row" id="import_table">
    <div class="col-sm-12">
        <x-form id="import-guest-data-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('app.importsalaryCalculation')</h4>
                <div class="col-sm-12 pt-2">
                    <div class="alert alert-warning" role="alert">
                        @lang('app.importExcelInfo')
                    </div>
                </div>
                <div class="row py-20">
                    <div class="col-md-12">
                        <x-forms.file :fieldLabel="__('modules.import.file')" fieldName="import_file"
                                      fieldId="guest_import"/>
                    </div>
                    <div class="col-md-12">
                        <x-forms.toggle-switch class="mr-0 mr-lg-12"
                                               :fieldLabel="__('modules.import.containsHeadings')"
                                               fieldName="heading"
                                               fieldId="heading"/>
                    </div>
                </div>
                <x-form-actions>
                    <x-forms.button-primary id="import-guest-form" class="mr-3"
                                            icon="arrow-right">@lang('app.uploadNext')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('salary-calculation.index')" class="border-0">@lang('app.back')
                    </x-forms.button-cancel>

                </x-form-actions>
            </div>
        </x-form>

    </div>
</div>

<script>

    $(document).ready(function () {

        $("#guest_import").dropify({
            messages: dropifyMessages
        });

        $('body').on('click', '#import-guest-form', function () {
            const url = "{{ route('salary-calculation.import.store') }}";

            $.easyAjax({
                url: url,
                container: '#import-guest-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#import-guest-form",
                file: true,
                data: $('#import-guest-data-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        $('#import_table').html(response.view);
                    }
                }
            });
        });
    });
</script>
