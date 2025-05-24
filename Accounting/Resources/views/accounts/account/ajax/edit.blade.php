<div class="row">
    <div class="col-sm-12">
        <x-form id="edit-account-data-form" method="PUT">
            <div class="edit-account bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('app.editAccount')</h4>

                <!-- Account Name -->
                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="name" :fieldLabel="__('app.name')" fieldName="name" fieldRequired="true"
                            :fieldValue="$account->name" :fieldPlaceholder="__('placeholders.name')">
                        </x-forms.text>
                    </div>

                    <!-- Account Code -->
                    <div class="col-md-6">
                        <x-forms.text fieldId="code" :fieldLabel="__('app.code')" fieldName="code" fieldRequired="true"
                            :fieldValue="$account->code" :fieldPlaceholder="__('placeholders.code')">
                        </x-forms.text>
                    </div>
                </div>

                <!-- Account Category -->
                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.select fieldId="account_category_id" :fieldLabel="__('app.menu.accountCategory')"
                            fieldName="account_category_id" fieldRequired="true">
                            <option value="">@lang('placeholders.select')</option>
                            @foreach ($accountCategories as $category)
                                <option value="{{ $category->id }}"
                                    @if ($account->account_category_id == $category->id) selected @endif>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <!-- Parent Account -->
                    <div class="col-md-6">
                        <x-forms.select fieldId="account_parent_id" :fieldLabel="__('app.menu.parentAccount')"
                            fieldName="account_parent_id" fieldRequired="false">
                            <option value="">@lang('placeholders.select')</option>
                            @foreach ($parentAccounts as $parent)
                                <option value="{{ $parent->id }}"
                                    @if ($account->account_parent_id == $parent->id) selected @endif>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>
                </div>
                    <div class="col-md-12">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('app.description')"
                                fieldName="description" fieldId="description" :fieldPlaceholder="__('placeholders.description')"
                                :fieldValue="$account->description">
                            </x-forms.textarea>
                        </div>
                    </div>

                <!-- Form Actions -->
                <x-form-actions>
                    <x-forms.button-primary id="update-account-form" class="mr-3" icon="check">@lang('app.update')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('accounts.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#update-account-form').click(function() {

            const url = "{{ route('accounts.update', $account->id) }}";

            $.easyAjax({
                url: url,
                container: '#edit-account-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#update-account-form",
                data: $('#edit-account-data-form').serialize(),
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
