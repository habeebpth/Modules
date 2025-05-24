<div class="row">
    <div class="col-sm-12">
        <x-form id="save-billing-code-data-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('app.addBillingCode')</h4>

                <input type="hidden" name="company_id" value="{{ $company_id }}">

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="name" :fieldLabel="__('app.name')"
                            fieldName="name" fieldRequired="true"
                            :fieldPlaceholder="__('placeholders.name')">
                        </x-forms.text>
                    </div>

                    <div class="col-md-6">
                        <x-forms.text fieldId="position" :fieldLabel="__('app.position')"
                            fieldName="position" fieldRequired="false"
                            :fieldPlaceholder="__('placeholders.position')">
                        </x-forms.text>
                    </div>
                </div>
                <x-form-actions>
                    <x-forms.button-primary id="save-billing-code-form" class="mr-3" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('billing-code.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>

            </div>

        </x-form>

    </div>
</div>

<script>
    $(document).ready(function() {
        $('#save-billing-code-form').click(function() {

            const url = "{{ route('billing-code.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-billing-code-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-billing-code-form",
                data: $('#save-billing-code-data-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    }
                }
            })
        });
        init(RIGHT_MODAL);
    });
</script>
