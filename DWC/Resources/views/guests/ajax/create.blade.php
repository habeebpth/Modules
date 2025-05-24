<link rel="stylesheet" href="{{ asset('vendor/css/tagify.css') }}">
<style>
    .flight-item {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
        padding: 1.5rem;
        position: relative;
    }

    .remove-flight {
        position: absolute;
        right: 15px;
        top: 15px;
        color: #dc3545;
        cursor: pointer;
    }

    .flight-header {
        border-bottom: 2px solid #eee;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
    }

    .hotel-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        position: relative;
    }

    .remove-hotel {
        position: absolute;
        right: 15px;
        top: 15px;
        color: #dc3545;
        cursor: pointer;
    }

    .flight_from label {
        margin-top: 0px !important;
        margin-bottom: 0.5rem !important;
    }

    .flight_to label {
        margin-top: 0px !important;
        margin-bottom: 0.5rem !important;
    }

    .hotel_id label {
        margin-top: 0px !important;
        margin-bottom: 0.5rem !important;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <x-form id="save-guest-data-form">

            <div class="bg-white rounded add-client">
                <h4 class="p-20 mb-0 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('modules.guests.guest_details')</h4>
                <div class="p-20 row">
                    <div class="col-lg-4 col-md-4">
                        <x-forms.select fieldId="horse" :fieldLabel="__('modules.guests.horse')" fieldName="horse" search="true"
                            fieldRequired="true">
                            <option value="">--</option>
                            @foreach ($horses as $horse)
                                <option value="{{ $horse->id }}"
                                    data-race="{{ $horse->races->pluck('id')->implode(',') }}">
                                    {{ $horse->name }}
                                    ({{ $horse->races->isNotEmpty() ? $horse->races->pluck('name')->implode(', ') : 'No Race' }})
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <x-forms.select fieldId="guest_type" :fieldLabel="__('modules.guests.guest_type')" fieldName="guest_type" search="true"
                            fieldRequired="true">
                            <option value="">--</option>
                            @foreach ($guesttypes as $guesttype)
                                <option value="{{ $guesttype->id }}">{{ $guesttype->name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <x-forms.datepicker fieldId="amendment_date" :fieldLabel="__('modules.guests.amendment_date')" fieldName="amendment_date"
                            :fieldPlaceholder="__('placeholders.date')" fieldRequired="true" :fieldValue="now(company()->timezone)->translatedFormat(company()->date_format)" />
                    </div>
                    <div class="col-lg-2 col-md-2">
                        <x-forms.select fieldId="salutation" fieldName="salutation" :fieldLabel="__('modules.client.salutation')">
                            <option value="">--</option>
                            @foreach ($salutations as $salutation)
                                <option value="{{ $salutation->value }}">{{ $salutation->label() }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <x-forms.text fieldId="first_name" :fieldLabel="__('modules.guests.guestFirstName')" fieldName="first_name" fieldRequired="true"
                            :fieldPlaceholder="__('placeholders.name')">
                        </x-forms.text>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <x-forms.text fieldId="last_name" :fieldLabel="__('modules.guests.guestLastName')" fieldName="last_name" :fieldPlaceholder="__('placeholders.name')">
                        </x-forms.text>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <x-forms.text fieldId="company" :fieldLabel="__('modules.guests.company')" fieldName="company" :fieldPlaceholder="__('placeholders.company')">
                        </x-forms.text>
                    </div>
                    <div class="col-md-3">
                        <div class="my-3 form-group">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('app.address')" fieldName="address"
                                fieldId="address" :fieldPlaceholder="__('placeholders.address')" fieldRequired="true">
                            </x-forms.textarea>
                        </div>
                    </div>
                    {{-- <div class="col-md-6">
                        <div class="my-3 form-group">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('modules.guests.address_2')" fieldName="address_2"
                                fieldId="address_2" :fieldPlaceholder="__('placeholders.address')">
                            </x-forms.textarea>
                        </div>
                    </div> --}}
                    {{-- <div class="col-lg-3 col-md-6">
                        <x-forms.text fieldId="zip" :fieldLabel="__('modules.guests.zip')" fieldName="zip" :fieldPlaceholder="__('placeholders.zip')">
                        </x-forms.text>
                    </div> --}}
                    <div class="col-lg-3 col-md-6">
                        <x-forms.select fieldId="country" :fieldLabel="__('app.country')" fieldName="country" search="true">
                            <option value="">--</option>
                            @foreach ($countries as $item)
                                <option data-tokens="{{ $item->iso3 }}" data-phonecode="{{ $item->phonecode }}"
                                    data-iso="{{ $item->iso }}"
                                    data-content="<span class='flag-icon flag-icon-{{ strtolower($item->iso) }} flag-icon-squared'></span> {{ $item->nicename }}"
                                    value="{{ $item->id }}" {{ old('country') == $item->id ? 'selected' : '' }}>
                                    {{ $item->nicename }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-forms.text fieldId="state" :fieldLabel="__('modules.guests.state')" fieldName="state" :fieldPlaceholder="__('placeholders.state')">
                        </x-forms.text>
                    </div>
                    {{-- <div class="col-lg-3 col-md-6">
                        <x-forms.text fieldId="tel" :fieldLabel="__('modules.guests.tel')" fieldName="tel" :fieldPlaceholder="__('placeholders.mobile')">
                        </x-forms.text>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-forms.text fieldId="fax" :fieldLabel="__('modules.guests.fax')" fieldName="fax" :fieldPlaceholder="__('placeholders.mobile')">
                        </x-forms.text>
                    </div> --}}
                    <div class="col-lg-3 col-md-6">
                        <x-forms.label class="my-3" fieldId="mobile" :fieldLabel="__('app.mobile')"></x-forms.label>
                        <x-forms.input-group style="margin-top:-4px">
                            <x-forms.select fieldId="country_phonecode" fieldName="country_phonecode" search="true">
                                <option value="">--</option>
                                @foreach ($countries as $item)
                                    <option data-tokens="{{ $item->name }}" data-country-iso="{{ $item->iso }}"
                                        data-content="{{ $item->flagSpanCountryCode() }}"
                                        value="{{ $item->phonecode }}">{{ $item->phonecode }}
                                    </option>
                                @endforeach
                            </x-forms.select>

                            <input type="tel" class="form-control height-35 f-14" placeholder="@lang('placeholders.mobile')"
                                name="mobile" id="mobile" required>
                        </x-forms.input-group>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-forms.text fieldId="email" :fieldLabel="__('modules.guests.email')" fieldName="email" :fieldPlaceholder="__('placeholders.email')">
                        </x-forms.text>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-forms.select fieldId="nationality" :fieldLabel="__('app.nationality')" fieldName="nationality" search="true">
                            <option value="">--</option>
                            @foreach ($countries as $item)
                                <option data-tokens="{{ $item->iso3 }}" data-phonecode="{{ $item->phonecode }}"
                                    data-iso="{{ $item->iso }}"
                                    data-content="<span class='flag-icon flag-icon-{{ strtolower($item->iso) }} flag-icon-squared'></span> {{ $item->nicename }}"
                                    value="{{ $item->id }}" {{ old('country') == $item->id ? 'selected' : '' }}>
                                    {{ $item->nicename }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="my-3 form-group">
                            <label class="mb-12 f-14 text-dark-grey w-100" for="usr">@lang('modules.guests.visa_required')</label>
                            <div class="d-flex">
                                <x-forms.radio fieldId="login-yes" :fieldLabel="__('app.yes')" fieldName="visa_required"
                                    fieldValue="1">
                                </x-forms.radio>
                                <x-forms.radio fieldId="login-no" :fieldLabel="__('app.no')" fieldValue="0"
                                    fieldName="visa_required" checked="true">
                                </x-forms.radio>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <x-forms.text fieldId="passport_number" :fieldLabel="__('modules.guests.passport_number')" fieldName="passport_number"
                            :fieldPlaceholder="__('placeholders.zip')">
                        </x-forms.text>
                    </div>
                </div>
                <hr>

                <div class="container-fluid">
                    <div id="flightItemsContainer">
                        <!-- Initial Item -->
                        <div class="flight-item card">
                            <div class="flight-header">
                                <h5 class="mb-0">Flight Ticket #1</h5>
                                <span class="remove-flight"><i class="fas fa-times-circle"></i></span>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="f-14 text-dark-grey" data-label="true" for="flight_no">Flight
                                            Number
                                            <sup class="f-14 mr-1">*</sup>
                                        </label>
                                        <input type="text" class="form-control height-35 f-14" value=""
                                            name="flight_no[]" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="f-14 text-dark-grey" data-label="true">
                                            Departure Date
                                            <sup class="f-14 mr-1">*</sup>
                                        </label>
                                        <input type="date" class="form-control height-35 f-14" value=""
                                            name="departure_date[]" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="f-14 text-dark-grey" data-label="true">
                                            Departure Time
                                            <sup class="f-14 mr-1">*</sup>
                                        </label>
                                        <input type="time" class="form-control height-35 f-14" value=""
                                            name="departure_time[]" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="f-14 text-dark-grey" data-label="true">
                                            Arrival Date
                                            <sup class="f-14 mr-1">*</sup>
                                        </label>
                                        <input type="date" class="form-control height-35 f-14" value=""
                                            name="arrival_date[]" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="f-14 text-dark-grey" data-label="true">
                                            Arrival Time
                                            <sup class="f-14 mr-1">*</sup>
                                        </label>
                                        <input type="time" class="form-control height-35 f-14" value=""
                                            name="arrival_time[]" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="col-md-2 flight_from">
                                    <x-forms.select2-ajax fieldId="flight_from" fieldName="flight_from[]"
                                        class="mb-0 mt-0" route="{{ route('guests.airports') }}" fieldLabel="From"
                                        search="true">
                                    </x-forms.select2-ajax>
                                </div>
                                <div class="col-md-2 flight_to">
                                    <x-forms.select2-ajax fieldId="flight_to" fieldName="flight_to[]"
                                        route="{{ route('guests.airports') }}" fieldLabel="To" search="true">
                                    </x-forms.select2-ajax>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="f-14 text-dark-grey" data-label="true">
                                            Class
                                        </label>
                                        <select class="form-select form-control height-35 f-14" name="flight_class[]">
                                            <option value>---</option>
                                            <option value="First Class">First Class</option>
                                            <option value="Business Class">Business Class</option>
                                            <option value="Premium Economy">Premium Economy</option>
                                            <option value="Economy Class">Economy Class</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">

                                    <div class="form-group">
                                        <label class="f-14 text-dark-grey" data-label="true">
                                            Locator
                                        </label>
                                        <input type="text" class="form-control height-35 f-14" value=""
                                            name="locator[]" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="f-14 text-dark-grey" data-label="true">
                                            Ticket Number
                                        </label>
                                        <input type="text" class="form-control height-35 f-14" value=""
                                            name="ticket_number[]" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-2">
                                    <div class="form-group">
                                        <label class="f-14 text-dark-grey" data-label="true">
                                            Notes
                                        </label>
                                        <textarea class="form-control" name="note_1[]" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2 mb-2">
                        <button type="button" id="addFlight" class="btn btn-sm btn-success">
                            <i class="fas fa-plus me-2"></i>Add Ticket
                        </button>
                    </div>
                </div>

                <hr>

                <!-- Hotel Section -->
                <div class="container-fluid mt-4">
                    <div id="hotelItemsContainer">
                        <!-- Initial Hotel Item -->
                        <div class="hotel-item card">
                            <div class="hotel-header mb-3">
                                <h5>Hotel Reservation</h5>
                                {{-- <h5>Hotel Reservation #1</h5> --}}
                                {{-- <span class="remove-hotel"><i class="fas fa-times-circle"></i></span> --}}
                            </div>
                            <div class="row g-3">
                                {{-- <div class="col-md-3 hotel_id">
                                    <x-forms.select2-ajax fieldId="hotel_id" fieldName="hotel_id[]"
                                        fieldClass="hotel-select" fieldLabel="Select Hotel"
                                        route="{{ route('guests.hotels') }}" search="true">
                                    </x-forms.select2-ajax>
                                </div> --}}

                                <div class="col-lg-2 col-md-2 hotel_id">
                                    <x-forms.label class="my-3" fieldId="hotel_id" :fieldLabel="__('app.Hotel')"
                                        fieldRequired="true">
                                    </x-forms.label>
                                    <x-forms.input-group>
                                        <select class="form-control select-picker" name="hotel_id[]" id="hotel_id"
                                            data-live-search="true">
                                            <option value="">--</option>
                                            @foreach ($selectedHotels as $hotel)
                                                <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                                            @endforeach
                                            <option value="other">Other</option> <!-- Add "Other" option -->
                                        </select>
                                    </x-forms.input-group>
                                </div>

                                <div class="col-md-2" id="hotel_name_field" style="display: none;">
                                    <!-- Initially hidden -->
                                    <div class="form-group">
                                        <label class="f-14 text-dark-grey" data-label="true">
                                            Hotel Name
                                            <sup class="f-14 mr-1">*</sup>
                                        </label>
                                        <input type="text" class="form-control height-35 f-14" value=""
                                            name="others[]" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="f-14 text-dark-grey" data-label="true">
                                            Room Type
                                            <sup class="f-14 mr-1">*</sup>
                                        </label>
                                        <input type="text" class="form-control height-35 f-14" value=""
                                            name="room_type[]" autocomplete="off">
                                    </div>
                                </div>

                                <!-- Other Fields -->
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="f-14 text-dark-grey" data-label="true">
                                            Checkin Date
                                            <sup class="f-14 mr-1">*</sup>
                                        </label>
                                        <input type="date" class="form-control height-35 f-14 check-in"
                                            id="check_in_1" value="" name="checkin_date[]" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="f-14 text-dark-grey" data-label="true">
                                            Checkout Date
                                            <sup class="f-14 mr-1">*</sup>
                                        </label>
                                        <input type="date" class="form-control height-35 f-14 check-out"
                                            id="check_out_1" value="" name="checkout_date[]"
                                            autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="f-14 text-dark-grey" data-label="true">
                                            Nights
                                            <sup class="f-14 mr-1">*</sup>
                                        </label>
                                        <input type="number" class="form-control height-35 f-14 nights"
                                            id="nights_1" value="1" name="no_of_nights[]" autocomplete="off">
                                    </div>
                                </div>
                                {{-- <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="f-14 text-dark-grey" data-label="true">
                                            Category
                                        </label>
                                        <input type="text" class="form-control height-35 f-14" value=""
                                            name="category[]" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="f-14 text-dark-grey" data-label="true">
                                            Sub Category
                                            <sup class="f-14 mr-1">*</sup>
                                        </label>
                                        <input type="text" class="form-control height-35 f-14" value=""
                                            name="sub_category[]" autocomplete="off">
                                    </div>
                                </div> --}}
                                <div class="col-lg-2 col-md-2 billing_code hotel_id">
                                    <x-forms.label class="my-3" fieldId="billing_code" :fieldLabel="__('app.billingCode')"
                                        fieldRequired="true">
                                    </x-forms.label>
                                    <x-forms.input-group>
                                        <select class="form-control select-picker" name="billing_code[]"
                                            id="billing_code" data-live-search="true">
                                            <option value="">--</option>
                                            @foreach ($billingcodes as $billingcode)
                                                <option value="{{ $billingcode->id }}">{{ $billingcode->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </x-forms.input-group>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="f-14 text-dark-grey" data-label="true">
                                            Confirmation No
                                            <sup class="f-14 mr-1">*</sup>
                                        </label>
                                        <input type="number" class="form-control height-35 f-14" value="1"
                                            name="confirmation_no[]" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="f-14 text-dark-grey" data-label="true">
                                            Sharing With
                                        </label>
                                        <input type="text" class="form-control height-35 f-14" value=""
                                            name="sharing_with[]" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="f-14 text-dark-grey" data-label="true">
                                            Notes
                                        </label>
                                        <textarea class="form-control height-35 f-14" name="note_2[]" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- <button type="button" id="addHotel" class="btn btn btn-success mt-2 mb-2">
                        <i class="fas fa-plus me-2"></i>Add Hotel Reservation
                    </button> --}}
                </div>


                <x-form-actions>
                    <x-forms.button-primary id="save-employee-form" class="mr-3" icon="check">
                        @lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-secondary class="mr-3" id="save-more-guest-form"
                        icon="check-double">@lang('app.saveAddMore')
                    </x-forms.button-secondary>
                    <x-forms.button-cancel class="border-0 " data-dismiss="modal">@lang('app.cancel')
                    </x-forms.button-cancel>

                </x-form-actions>
            </div>
        </x-form>

    </div>
</div>

<script src="{{ asset('vendor/jquery/tagify.min.js') }}"></script>
@if (function_exists('sms_setting') && sms_setting()->telegram_status)
    <script src="{{ asset('vendor/jquery/clipboard.min.js') }}"></script>
@endif
<script>
    document.getElementById('hotel_id').addEventListener('change', function() {
        let hotelField = document.getElementById('hotel_name_field');
        if (this.value === 'other') {
            hotelField.style.display = 'block'; // Show input field
        } else {
            hotelField.style.display = 'none'; // Hide input field
        }
    });
    $(document).ready(function() {

        let flightCount = 1;

        $('#addFlight').click(function() {
            flightCount++;
            const newItem = $('#flightItemsContainer .flight-item:first').clone();

            // Clear input values
            newItem.find('input').val('');
            newItem.find('textarea').val('');
            newItem.find('select').prop('selectedIndex', 0);

            // Update header
            newItem.find('.flight-header h5').text('Flight Ticket #' + flightCount);

            // For creating dynamic ajax select boxes
            newItem.find('.flight_from').empty();
            newItem.find('.flight_to').empty();
            $.ajax({
                url: '{{ route('genarateSelect') }}',
                type: 'GET',
                data: {
                    name: 'flight_from',
                    route: "{{ route('guests.airports') }}",
                    flight: flightCount + 100,
                    label: "From"
                },
                success: function(response) {
                    newItem.find('.flight_from').html(response);
                }
            })

            $.ajax({
                url: '{{ route('genarateSelect') }}',
                type: 'GET',
                data: {
                    name: 'flight_to',
                    route: "{{ route('guests.airports') }}",
                    flight: flightCount + 101,
                    label: "To"
                },
                success: function(response) {
                    newItem.find('.flight_to').html(response);
                }
            })

            // Add remove button
            newItem.find('.remove-flight').show();

            // Add to container
            newItem.appendTo('#flightItemsContainer').hide().slideDown(300);
        });

        $(document).on('click', '.remove-flight', function() {
            if ($('#flightItemsContainer .flight-item').length > 1) {
                $(this).closest('.flight-item').slideUp(300, function() {
                    $(this).remove();
                    updateFlightNumbers();
                });
            }
        });

        function updateFlightNumbers() {
            $('#flightItemsContainer .flight-item').each(function(index) {
                $(this).find('.flight-header h5').text('Flight #' + (index + 1));
            });
            flightCount = $('#flightItemsContainer .flight-item').length;
        }


        //Hotel Script

        let hotelCounter = 1;

        // Initialize Select2 for hotels
        function initHotelSelect(selector) {
            $(selector).select2({
                ajax: {
                    url: $(selector).data('ajax-url'),
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                },
                minimumInputLength: 2,
                placeholder: 'Search for hotel',
                allowClear: true
            });
        }

        // Initialize Select2 for room types
        function initRoomTypeSelect(selector, hotelId) {
            $(selector).select2({
                ajax: {
                    url: $(selector).data('ajax-url'),
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term,
                            hotel_id: hotelId
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                },
                minimumInputLength: 0,
                placeholder: 'Select room type',
                allowClear: true
            }).prop('disabled', false);
        }

        // Hotel selection handler
        $(document).on('change', '.select2-hotel', function() {
            const hotelId = $(this).val();
            const roomSelect = $('#' + $(this).data('room-target'));

            // Clear previous room types
            roomSelect.val(null).html('').trigger('change').prop('disabled', true);

            if (hotelId) {
                initRoomTypeSelect(roomSelect, hotelId);
            }
        });

        function calculateNights(checkInSelector, checkOutSelector, nightsSelector) {
            $(document).on('change', checkInSelector + ', ' + checkOutSelector, function() {
                let checkInDate = new Date($(checkInSelector).val());
                let checkOutDate = new Date($(checkOutSelector).val());

                if (checkInDate && checkOutDate && checkOutDate > checkInDate) {
                    let nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
                    $(nightsSelector).val(nights);
                } else {
                    $(nightsSelector).val('');
                }
            });
        }
        calculateNights('#check_in_1', '#check_out_1', '#nights_1');


        // Add new hotel item
        $('#addHotel').click(function() {
            hotelCounter++;
            const newId = Date.now();
            const newItem = $('#hotelItemsContainer .hotel-item:first').clone();

            // Update IDs and data attributes
            newItem.find('.select2-hotel')
                .attr('data-room-target', `room_type_${newId}`)
                .val(null)
                .trigger('change');

            newItem.find('.select2-room-type')
                .attr('id', `room_type_${newId}`)
                .prop('disabled', true)
                .html('<option value="">Select hotel first</option>');

            // Update header
            newItem.find('h5').text(`Hotel Reservation #${hotelCounter}`);

            // Clear values
            newItem.find('input').val('');
            newItem.find('select').val(null).trigger('change');
            newItem.find('textarea').val('');

            // updating select hotel select box
            newItem.find('.hotel_id').empty();

            $.ajax({
                url: '{{ route('genarateSelect') }}',
                type: 'GET',
                data: {
                    name: 'hotel_id',
                    route: "{{ route('guests.hotels') }}",
                    flight: hotelCounter + 100,
                    label: "Hotel"
                },
                success: function(response) {
                    newItem.find('.hotel_id').html(response);
                }
            })
            // newItem.find('.billing_code select').empty(); // Clear only the <select>, not the whole div

            // $.ajax({
            //     url: '{{ route('guests.billingcodes') }}',
            //     type: 'GET',
            //     success: function(response) {
            //         console.log("Billing Codes Response:", response);
            //         let options = '<option value="">Select Billing Code</option>';
            //         response.forEach(function(item) {
            //             options +=
            //                 `<option value="${item.id}">${item.name}</option>`;
            //         });
            //         newItem.find('.billing_code select').html(options).selectpicker(
            //             'refresh');
            //     },
            //     error: function(xhr, status, error) {
            //         console.error("AJAX Error:", status, error);
            //         console.log("Response Text:", xhr.responseText);
            //     }
            // });



            // Initialize Select2 for new hotel select
            initHotelSelect(newItem.find('.select2-hotel'));
            // Update ID and name attributes for new fields
            newItem.find('.check-in')
                .attr('id', `check_in_${hotelCounter}`)
                .attr('name', 'checkin_date[]')
                .val('');

            newItem.find('.check-out')
                .attr('id', `check_out_${hotelCounter}`)
                .attr('name', 'checkout_date[]')
                .val('');

            newItem.find('.nights')
                .attr('id', `nights_${hotelCounter}`)
                .attr('name', 'no_of_nights[]')
                .val('1');

            // Attach change event to trigger night calculation
            newItem.find('.check-in, .check-out').change(function() {
                calculateNights(`#check_in_${hotelCounter}`, `#check_out_${hotelCounter}`,
                    `#nights_${hotelCounter}`);
            });
            // Add to container
            newItem.appendTo('#hotelItemsContainer').hide().slideDown(300);
        });

        // Remove hotel item
        $(document).on('click', '.remove-hotel', function() {
            if ($('#hotelItemsContainer .hotel-item').length > 1) {
                $(this).closest('.hotel-item').slideUp(300, function() {
                    $(this).remove();
                    hotelCounter--;
                    // Update numbering
                    $('#hotelItemsContainer .hotel-item h5').each(function(index) {
                        $(this).text(`Hotel Reservation #${index + 1}`);
                    });
                });
            }
        });

        // Initialize first hotel select
        initHotelSelect('.select2-hotel');

        datepicker('#amendment_date', {
            position: 'bl',
            ...datepickerConfig
        });

        var input = document.querySelector('input[name=tags]'),
            // init Tagify script on the above inputs
            tagify = new Tagify(input);

        $('#save-more-guest-form').click(function() {

            $('#add_more').val(true);

            const url = "{{ route('guests.store') }}";
            var data = $('#save-guest-data-form').serialize();
            saveGuest(data, url, "#save-more-guest-form");


        });

        $('#save-employee-form').click(function() {

            const url = "{{ route('guests.store') }}";
            var data = $('#save-guest-data-form').serialize();
            saveGuest(data, url, "#save-employee-form");

        });

        function saveGuest(data, url, buttonSelector) {
            $.easyAjax({
                url: url,
                container: '#save-guest-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: buttonSelector,
                file: true,
                data: data,
                success: function(response) {
                    if (response.status == 'success') {
                        if ($(MODAL_XL).hasClass('show')) {
                            $(MODAL_XL).modal('hide');
                            window.location.reload();
                        } else if (response.add_more == true) {

                            var right_modal_content = $.trim($(RIGHT_MODAL_CONTENT).html());

                            if (right_modal_content.length) {

                                $(RIGHT_MODAL_CONTENT).html(response.html.html);
                                $('#add_more').val(false);
                            } else {

                                $('.content-wrapper').html(response.html.html);
                                init('.content-wrapper');
                                $('#add_more').val(false);
                            }

                        } else {

                            window.location.href = response.redirectUrl;

                        }

                        if (typeof showTable !== 'undefined' && typeof showTable === 'function') {
                            showTable();
                        }

                    }

                }
            });
        }

        $('#country').change(function() {
            var phonecode = $(this).find(':selected').data('phonecode');
            var iso = $(this).find(':selected').data('iso');

            // $('#country_phonecode').find('option').each(function() {
            //     if ($(this).data('country-iso') === iso) {
            //         $(this).val(phonecode);
            //         $(this).prop('selected', true); // Set the option as selected
            //     }
            // });
            $('.select-picker').selectpicker('refresh');
        });

        init(RIGHT_MODAL);
    });
</script>
