<div class="modal-header">
    <h5 class="modal-title">@lang('travels::app.travels.editVehicle')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<x-form id="editVehicle" method="PUT" class="ajax-form">
    <div class="modal-body">
        <div class="portlet-body">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <x-forms.text :fieldLabel="__('app.name')" :fieldPlaceholder="__('placeholders.name')"
                                  fieldName="name" fieldId="name" fieldRequired="true" :fieldValue="$vehicle->name"/>
                </div>

                <div class="col-sm-12 col-md-6 ">
                    <x-forms.select fieldId="vehicle_type_id" :fieldLabel="__('travels::app.travels.vehicletype')" fieldName="vehicle_type_id"
                        search="true">
                        @foreach ($vehicleTypes as $type)
                           <option value="{{ $type->id }}" @if ($type->id == $vehicle->vehicle_type_id) selected @endif>
                                  {{ $type->name }}
                           </option>
                       @endforeach
                    </x-forms.select>
                </div>

                <div class="col-sm-12 col-md-6">
                    <x-forms.text :fieldLabel="__('travels::app.travels.vehicleNumber')" :fieldPlaceholder="__('placeholders.vehicleNumber')"
                                  fieldName="vehicle_number" fieldId="vehicle_number" fieldRequired="true" :fieldValue="$vehicle->vehicle_number"/>
                </div>

                <div class="col-sm-12 col-md-6">
                    <x-forms.text :fieldLabel="__('travels::app.travels.vehicleCode')" :fieldPlaceholder="__('placeholders.vehicleCode')"
                                  fieldName="vehicle_code" fieldId="vehicle_code" fieldRequired="true" :fieldValue="$vehicle->vehicle_code"/>
                </div>

                <div class="col-sm-12 col-md-6">
                    <x-forms.number :fieldLabel="__('travels::app.travels.noOfSeats')" :fieldPlaceholder="__('placeholders.noOfSeats')"
                                    fieldName="no_of_seats" fieldId="no_of_seats" fieldRequired="true" :fieldValue="$vehicle->no_of_seats"/>
                </div>

                <div class="col-sm-12 col-md-6">
                    <x-forms.select fieldId="country" :fieldLabel="__('app.country')" fieldName="country_id" search="true">
                        @foreach ($countries as $item)
                            <option data-tokens="{{ $item->iso3 }}" data-phonecode="{{ $item->phonecode }}"
                                    data-content="<span class='flag-icon flag-icon-{{ strtolower($item->iso) }} flag-icon-squared'></span> {{ $item->nicename }}"
                                    value="{{ $item->id }}" @if ($item->id == $vehicle->country_id) selected @endif>{{ $item->nicename }}</option>
                        @endforeach
                    </x-forms.select>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
        <x-forms.button-primary id="update-vehicle" icon="check">@lang('app.update')</x-forms.button-primary>
    </div>
</x-form>

<script>
    $('.select-picker').selectpicker('refresh');
    $('#update-vehicle').click(function () {
        $.easyAjax({
            container: '#editVehicle',
            type: "POST",
            disableButton: true,
            blockUI: true,
            buttonSelector: "#update-vehicle",
            url: "{{ route('vehicle-settings.update', $vehicle->id) }}", // Pass the vehicle ID for updating
            data: $('#editVehicle').serialize(),
            success: function (response) {
                if (response.status == 'success') {
                    window.location.reload();
                }
            }
        })
    });
</script>
