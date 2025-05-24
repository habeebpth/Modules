<div class="row">
    <div class="col-sm-12">
        <x-form id="update-reward-data-form" method="PUT" class="ajax-form" enctype="multipart/form-data">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('app.editReward')
                </h4>

                {{-- <input type="hidden" name="company_id" value="{{ $company_id }}"> --}}

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.select fieldId="customer_id" :fieldLabel="__('app.customer')" fieldName="customer_id"
                            fieldRequired="true" search="true">
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    {{ $rewardCustomer->customer_id == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-md-6">
                        <x-forms.text fieldId="total_points_earned" :fieldLabel="__('app.totalPointsEarned')"
                            fieldName="total_points_earned" fieldType="number" fieldRequired="true"
                            fieldValue="{{ $rewardCustomer->total_points_earned }}">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="total_points_redeemed" :fieldLabel="__('app.totalPointsRedeemed')"
                            fieldName="total_points_redeemed" fieldType="number" fieldRequired="true"
                            fieldValue="{{ $rewardCustomer->total_points_redeemed }}">
                        </x-forms.text>
                    </div>

                    <div class="col-md-6">
                        <x-forms.text fieldId="onhold_balance" :fieldLabel="__('app.onHoldBalance')" fieldName="onhold_balance"
                            fieldType="number" fieldRequired="true" fieldValue="{{ $rewardCustomer->onhold_balance }}">
                        </x-forms.text>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="update-rewardcustomer-form" class="mr-3"
                        icon="check">@lang('app.update')</x-forms.button-primary>
                    <x-forms.button-cancel :link="route('reward-customers.index')" class="border-0">@lang('app.cancel')</x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>

<script>
    $('.select-picker').selectpicker('refresh');

    $(document).ready(function() {
        $('#update-rewardcustomer-form').click(function() {
            const url = "{{ route('reward-customers.update', $rewardCustomer->id) }}";

            $.easyAjax({
                url: url,
                container: '#update-reward-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                file: false,
                buttonSelector: "#update-rewardcustomer-form",
                data: $('#update-reward-data-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    }
                }
            });
        });
    });
</script>
