<div class="row">
    <div class="col-sm-12">
        <x-form id="save-account-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('Add Account')
                </h4>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="account_code" :fieldLabel="__('Account Code')"
                            fieldName="account_code" fieldRequired="true" />
                    </div>
                    <div class="col-md-6">
                        <x-forms.text fieldId="account_name" :fieldLabel="__('Account Name')"
                            fieldName="account_name" fieldRequired="true" />
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.select fieldId="account_type" :fieldLabel="__('Account Type')"
                            fieldName="account_type" fieldRequired="true">
                            <option value="">@lang('Select Type')</option>
                            @foreach($accountTypes as $key => $type)
                                <option value="{{ $key }}">{{ $type }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-md-6">
                        <x-forms.select fieldId="account_sub_type" :fieldLabel="__('Account Sub Type')"
                            fieldName="account_sub_type" fieldRequired="true">
                            <option value="">@lang('Select Sub Type')</option>
                            @foreach($accountSubTypes as $key => $subType)
                                <option value="{{ $key }}">{{ $subType }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.select fieldId="parent_id" :fieldLabel="__('Parent Account')"
                            fieldName="parent_id">
                            <option value="">@lang('None')</option>
                            @foreach($parentAccounts as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->account_code }} - {{ $parent->account_name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-md-6">
                        <x-forms.number fieldId="opening_balance" :fieldLabel="__('Opening Balance')"
                            fieldName="opening_balance" :fieldValue="0" />
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-12">
                        <x-forms.textarea fieldId="description" :fieldLabel="__('Description')"
                            fieldName="description" />
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-account" class="mr-3" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('accounting.chart-of-accounts.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#save-account').click(function() {
        const url = "{{ route('accounting.chart-of-accounts.store') }}";
        
        $.easyAjax({
            url: url,
            container: '#save-account-form',
            type: "POST",
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-account",
            data: $('#save-account-form').serialize(),
            success: function(response) {
                if (response.status == 'success') {
                    window.location.href = response.redirectUrl;
                }
            }
        });
    });
});
</script>