<style>
    /* Set the size of the div element that contains the map */
    #map {
        height: 300px;
        /* The height is 400 pixels */
        width: 100%;
        /* The width is the width of the web page */
    }

    #description {
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
    }

    #infowindow-content .title {
        font-weight: bold;
    }

    #infowindow-content {
        display: none;
    }

    #map #infowindow-content {
        display: inline;
    }

    .pac-card {
        background-color: #fff;
        border: 0;
        border-radius: 2px;
        box-shadow: 0 1px 4px -1px rgba(0, 0, 0, 0.3);
        margin: 10px;
        padding: 0 0.5em;
        font: 400 18px Roboto, Arial, sans-serif;
        overflow: hidden;
        font-family: Roboto;
        padding: 0;
    }

    #pac-container {
        padding-bottom: 12px;
        margin-right: 12px;
    }

    .pac-container {
        background-color: #FFF;
        z-index: 20;
        position: fixed;
        display: inline-block;
        float: left;
    }

    .modal {
        z-index: 20;
    }

    .modal-backdrop {
        z-index: 10;
    }

    ​
    .pac-controls {
        display: inline-block;
        padding: 5px 11px;
    }

    .pac-controls label {
        font-family: Roboto;
        font-size: 13px;
        font-weight: 300;
    }

    #pac-input {
        background-color: #fff;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 400px;
    }

    #pac-input:focus {
        border-color: #4d90fe;
    }

    #title {
        font-size: 18px;
        font-weight: 500;
        padding: 10px 12px;
    }

</style>

<div class="modal-header">
    <h5 class="modal-title">@lang('travels::app.travels.addvehicle')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<x-form id="createVehicle" method="POST" class="ajax-form">
    <div class="modal-body">
        <div class="portlet-body">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <x-forms.text :fieldLabel="__('app.name')" :fieldPlaceholder="__('placeholders.name')"
                                  fieldName="name" fieldId="name" fieldRequired="true"/>
                </div>

                <div class="col-sm-12 col-md-6 ">
                    <x-forms.select fieldId="vehicle_type_id" :fieldLabel="__('travels::app.travels.vehicletype')" fieldName="vehicle_type_id"
                        search="true">
                        @foreach ($vehicleTypes as $type)
                           <option value="{{ $type->id }}">
                                  {{ $type->name }}
                           </option>
                       @endforeach
                    </x-forms.select>
                </div>

                <div class="col-sm-12 col-md-6">
                    <x-forms.text :fieldLabel="__('travels::app.travels.vehicleNumber')" :fieldPlaceholder="__('placeholders.VehicleNumber')"
                                  fieldName="vehicle_number" fieldId="vehicle_number" fieldRequired="true"/>
                </div>

                <div class="col-sm-12 col-md-6">
                    <x-forms.text :fieldLabel="__('travels::app.travels.vehicleCode')" :fieldPlaceholder="__('placeholders.vehicleCode')"
                                  fieldName="vehicle_code" fieldId="vehicle_code" fieldRequired="true"/>
                </div>

                <div class="col-sm-12 col-md-6">
                    <x-forms.number :fieldLabel="__('travels::app.travels.noOfSeats')" :fieldPlaceholder="__('placeholders.noOfSeats')"
                                    fieldName="no_of_seats" fieldId="no_of_seats" fieldRequired="true"/>
                </div>

                <div class="col-sm-12 col-md-6">
                    <x-forms.select fieldId="country" :fieldLabel="__('app.country')" fieldName="country_id" search="true">
                        @foreach ($countries as $item)
                            <option data-tokens="{{ $item->iso3 }}" data-phonecode="{{ $item->phonecode }}"
                                    data-content="<span class='flag-icon flag-icon-{{ strtolower($item->iso) }} flag-icon-squared'></span> {{ $item->nicename }}"
                                    value="{{ $item->id }}">{{ $item->nicename }}</option>
                        @endforeach
                    </x-forms.select>
                </div>
                <input type="hidden" name="company_id" value="{{ $company_id }}">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
        <x-forms.button-primary id="save-vehicle" icon="check">@lang('app.save')</x-forms.button-primary>
    </div>
</x-form>

<script>
    $('.select-picker').selectpicker('refresh');
    $('#save-vehicle').click(function () {
        $.easyAjax({
            container: '#createVehicle',
            type: "POST",
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-vehicle",
            url: "{{ route('vehicle-settings.store') }}",
            data: $('#createVehicle').serialize(),
            success: function (response) {
                if (response.status == 'success') {
                    window.location.reload();
                }
            }
        })
    });
</script>

