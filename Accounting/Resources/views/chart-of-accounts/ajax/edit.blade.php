<div class="row">
    <div class="col-sm-12">
        <x-form id="edit-account-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('Edit Account')
                </h4>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="account_code" :fieldLabel="__('Account Code')"
                            fieldName="account_code" fieldRequired="true"
                            fieldPlaceholder="@lang('Enter Account Code')"
                            :fieldValue="$account->account_code" />
                    </div>
                    <div class="col-md-6">
                        <x-forms.text fieldId="account_name" :fieldLabel="__('Account Name')"
                            fieldName="account_name" fieldRequired="true"
                            fieldPlaceholder="@lang('Enter Account Name')"
                            :fieldValue="$account->account_name" />
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.select fieldId="account_type" :fieldLabel="__('Account Type')"
                            fieldName="account_type" fieldRequired="true">
                            <option value="">@lang('Select Type')</option>
                            @foreach($accountTypes as $key => $type)
                                <option value="{{ $key }}" {{ $account->account_type == $key ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-md-6">
                        <x-forms.select fieldId="account_sub_type" :fieldLabel="__('Account Sub Type')"
                            fieldName="account_sub_type" fieldRequired="true">
                            <option value="">@lang('Select Sub Type')</option>
                            @foreach($accountSubTypes as $key => $subType)
                                <option value="{{ $key }}" {{ $account->account_sub_type == $key ? 'selected' : '' }}>{{ $subType }}</option>
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
                                <option value="{{ $parent->id }}" {{ $account->parent_id == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->account_code }} - {{ $parent->account_name }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            <label class="f-14 text-dark-grey mb-12 w-100" for="is_active">
                                @lang('Status')
                            </label>
                            <div class="d-flex">
                                <x-forms.radio fieldId="active" :fieldLabel="__('Active')" fieldName="is_active"
                                    fieldValue="1" :checked="$account->is_active == 1" />
                                <x-forms.radio fieldId="inactive" :fieldLabel="__('Inactive')" fieldValue="0"
                                    fieldName="is_active" :checked="$account->is_active == 0" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-12">
                        <x-forms.textarea fieldId="description" :fieldLabel="__('Description')"
                            fieldName="description" fieldPlaceholder="@lang('Enter Description')"
                            :fieldValue="$account->description" />
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle mr-2"></i>
                            <strong>@lang('Current Balance'):</strong> {{ currency_format($account->current_balance) }}
                            <br>
                            <small class="text-muted">@lang('Note: Opening balance cannot be changed after account creation. Current balance is automatically calculated from journal entries.')</small>
                        </div>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="update-account" class="mr-3" icon="check">@lang('app.update')
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
    $('#update-account').click(function() {
        const url = "{{ route('accounting.chart-of-accounts.update', $account->id) }}";

        $.easyAjax({
            url: url,
            container: '#edit-account-form',
            type: "PUT",
            disableButton: true,
            blockUI: true,
            buttonSelector: "#update-account",
            data: $('#edit-account-form').serialize(),
            success: function(response) {
                if (response.status == 'success') {
                    window.location.href = response.redirectUrl;
                }
            }
        });
    });
});
</script>
