<div class="row">
    <div class="col-sm-12">
        <x-form id="save-account-data-form">
            <div class="add-account bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('app.addAccount')</h4>

                <input type="hidden" name="company_id" value="{{ $company_id }}">

                <!-- Account Name -->
                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="name" :fieldLabel="__('app.name')" fieldName="name" fieldRequired="true"
                            :fieldPlaceholder="__('placeholders.name')">
                        </x-forms.text>
                    </div>

                    <!-- Account Code -->
                    <div class="col-md-6">
                        <x-forms.text fieldId="code" :fieldLabel="__('app.code')" fieldName="code" fieldRequired="true"
                            :fieldPlaceholder="__('placeholders.code')">
                        </x-forms.text>
                    </div>
                </div>

                <!-- Account Category -->
                <div class="row px-4">
                    @if($type === 'categories')
                    <!-- Dropdown for categories with pre-selection based on the 'id' -->
                    <div class="col-md-6">
                        <x-forms.select fieldId="account_category_id" :fieldLabel="__('app.menu.accountCategory')"
                                        fieldName="account_category_id" fieldRequired="true">
                            <option value="">@lang('placeholders.select')</option>
                            @foreach ($accountCategories as $category)
                                <option value="{{ $category->id }}"
                                        {{ isset($id) && $category->id == $id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>
                @else
                    <!-- Dropdown for categories without pre-selection -->
                    <div class="col-md-6">
                        <x-forms.select fieldId="account_category_id" :fieldLabel="__('app.menu.accountCategory')"
                                        fieldName="account_category_id" fieldRequired="true">
                            <option value="">@lang('placeholders.select')</option>
                            @foreach ($accountCategories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>
                @endif

                <!-- Parent Account -->
                @if($type === 'accounts')
                    <div class="col-md-6">
                        <x-forms.select fieldId="account_parent_id" :fieldLabel="__('app.menu.parentAccount')"
                                        fieldName="account_parent_id" fieldRequired="false">
                            <option value="">@lang('placeholders.select')</option>
                            @foreach($parentAccounts as $parent)
                                <option value="{{ $parent->id }}"
                                        {{ isset($id) && $parent->id == $id ? 'selected' : '' }}>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    @else
                    <div class="col-md-6">
                        <x-forms.select fieldId="account_parent_id" :fieldLabel="__('app.menu.parentAccount')"
                                        fieldName="account_parent_id" fieldRequired="false">
                            <option value="">@lang('placeholders.select')</option>
                            @foreach ($parentAccounts as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>
                @endif

                </div>

                <!-- Description -->
                <div class="row px-4">
                    <div class="col-md-12">
                        <x-forms.textarea fieldId="description" :fieldLabel="__('app.description')" fieldName="description"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.description')">
                        </x-forms.textarea>
                    </div>
                </div>

                <!-- Form Actions -->
                <x-form-actions>
                    <x-forms.button-primary id="save-account-form" class="mr-3" icon="check">@lang('app.save')
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
        $('.custom-date-picker').each(function(ind, el) {
            datepicker(el, {
                position: 'bl',
                ...datepickerConfig
            });
        });
        $('#date').each(function (ind, el) {
            datepicker(el, {
                position: 'bl',
                ...datepickerConfig
            });
        });

        $('#save-account-form').click(function() {

            const url = "{{ route('accounts.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-account-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-account-form",
                data: $('#save-account-data-form').serialize(),
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
