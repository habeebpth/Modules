<x-form id="editacccategories" method="PUT" class="ajax-form">
    @csrf
    @method('PUT') {{-- Use the PUT method for updating data --}}
    <div class="modal-header">
        <h5 class="modal-title" id="modelHeading">@lang('accounting::app.Accounting.EditCatogories')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="portlet-body">
            <div class="form-body row">
                <!-- Account Type Field -->
                <div class="col-sm-12 col-md-6">
                    <x-forms.select fieldId="account_type_id" :fieldLabel="__('accounting::app.Accounting.accounttypes')"
                                    fieldName="account_type_id" fieldRequired="true">
                        <option value="">@lang('app.select')</option>
                        @foreach($accountTypes as $accountType)
                            <option value="{{ $accountType->id }}"
                                {{ $accountCategory->account_type_id == $accountType->id ? 'selected' : '' }}>
                                {{ $accountType->name }}
                            </option>
                        @endforeach
                    </x-forms.select>
                </div>

                <!-- Name Field -->
                <div class="col-sm-12 col-md-6">
                    <x-forms.text fieldId="name" :fieldLabel="__('accounting::app.Accounting.name')"
                                  fieldName="name" fieldRequired="true"
                                  :fieldValue="$accountCategory->name" />
                </div>

                <!-- Code Field -->
                <div class="col-sm-12 col-md-6">
                    <x-forms.text fieldId="code" :fieldLabel="__('accounting::app.Accounting.code')"
                                  fieldName="code" fieldRequired="true"
                                  :fieldValue="$accountCategory->code" />
                </div>

                <div class="col-sm-12 col-md-6">
                    <div class="form-group my-3">
                        <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('app.description')"
                            fieldName="description" fieldId="description" :fieldPlaceholder="__('placeholders.description')"
                            :fieldValue="$accountCategory->description">
                        </x-forms.textarea>
                    </div>
            </div>

            </div>
        </div>
    </div>
    <div class="modal-footer">
        <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
        <x-forms.button-primary id="update-acccategories" icon="check">@lang('app.update')</x-forms.button-primary>
    </div>
</x-form>

<script>
    // save channel
    $('.select-picker').selectpicker('refresh');
    $('#update-acccategories').click(function() {
        $.easyAjax({
            url: "{{route('acc-categories-settings.update', $accountCategory->id)}}",
            container: '#editacccategories',
            type: "POST",
            blockUI: true,
            disableButton: true,
            buttonSelector: "#update-acccategories",
            data: $('#editacccategories').serialize(),
            success: function(response) {
                if (response.status == "success") {
                    window.location.reload();
                }
            }
        })
    });
</script>
