<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/css/all.min.css') }}">

    <!-- Simple Line Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/css/simple-line-icons.css') }}">

    <!-- Template CSS -->
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('css/main.css') }}">

    <title>@lang($pageTitle)</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $EvntEvent->icon_url }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ $EvntEvent->icon_url }}">
    <meta name="theme-color" content="#ffffff">

    {{-- @include('sections.theme_css', ['company' => $company]) --}}

    @isset($activeSettingMenu)
        <style>
            .preloader-container {
                margin-left: 510px;
                width: calc(100% - 510px)
            }
        </style>
    @endisset

    <style>
        .logo {
            height: 50px;
        }

        .signature_wrap {
            position: relative;
            height: 150px;
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
            width: 400px;
        }

        .signature-pad {
            position: absolute;
            left: 0;
            top: 0;
            width: 400px;
            height: 150px;
        }
    </style>

    @stack('styles')

    <style>
        :root {
            --fc-border-color: #E8EEF3;
            --fc-button-text-color: #99A5B5;
            --fc-button-border-color: #99A5B5;
            --fc-button-bg-color: #ffffff;
            --fc-button-active-bg-color: #171f29;
            --fc-today-bg-color: #f2f4f7;
        }

        .preloader-container {
            height: 100vh;
            width: 100%;
            margin-left: 0;
            margin-top: 0;
        }

        .rtl .preloader-container {
            margin-right: 0;
        }

        .fc a[data-navlink] {
            color: #99a5b5;
        }
    </style>
    <style>
        #logo {
            height: 80px;
        }
    </style>


    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery/modernizr.min.js') }}"></script>

    <script>
        var checkMiniSidebar = localStorage.getItem("mini-sidebar");
    </script>

</head>

<body id="body" class="h-100 bg-additional-grey {{ isRtl('rtl') }}">

    <div class="container content-wrapper">

        <div class="border-0 card invoice">
            <!-- CARD BODY START -->
            <div class="card-body">
                <div class="invoice-table-wrapper">
                    <table width="100%" class="">
                        <tr class="inv-logo-heading">
                            <td><img src="{{ $EvntEvent->icon_url }}" alt="{{ $EvntEvent->name }}" class="logo" />
                            </td>
                            <td align="right"
                                class="mt-4 font-weight-bold f-21 text-dark text-uppercase mt-lg-0 mt-md-0">
                                {{ $EvntEvent->name }}</td>
                        </tr>
                        <tr class="inv-num">
                            <td class="f-14 text-dark">
                                <p class="mt-3 mb-0">
                                    {{ strip_tags($EvntEvent->description) }}
                                </p><br>
                            </td>

                            <td align="right">

                            </td>
                        </tr>
                        <tr>
                            <td height="20"></td>
                        </tr>
                    </table>

                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <x-form id="save-event-registration-form">
                            <div class="add-client bg-white rounded">
                                <h4
                                    class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey text-center">
                                    {{-- @lang('app.menu.EventRegistration') --}}
                                    Registration
                                </h4>

                                <input type="hidden" name="event_id" value="{{ $EvntEvent->id }}">

                                <div class="row px-4">
                                    <div class="col-md-4">
                                        <x-forms.text fieldId="name" :fieldLabel="__('app.name')" fieldName="name"
                                            fieldRequired="true" :fieldPlaceholder="__('placeholders.fullname')">
                                        </x-forms.text>
                                    </div>

                                    <div class="col-md-3">
                                        <x-forms.label class="my-3" fieldId="mobile"
                                            :fieldLabel="__('app.mobile')"></x-forms.label>
                                        <x-forms.input-group style="margin-top:-4px">
                                            <x-forms.select fieldId="country_phonecode" fieldName="country_phonecode"
                                                search="true">
                                                @foreach ($countries as $item)
                                                    <option data-tokens="{{ $item->name }}"
                                                        data-country-iso="{{ $item->iso }}"
                                                        data-content="{{ $item->flagSpanCountryCode() }}"
                                                        value="{{ $item->phonecode }}"
                                                        {{ $item->phonecode == 91 ? 'selected' : '' }}>
                                                        {{ $item->phonecode }}
                                                    </option>
                                                @endforeach
                                            </x-forms.select>
                                            <input type="tel" class="form-control height-35 f-14"
                                                placeholder="@lang('placeholders.mobile')" name="mobile" id="mobile">
                                        </x-forms.input-group>
                                    </div>
                                    <div class="col-md-2">
                                        {{-- Label above checkbox --}}
                                        <x-forms.label class="my-3" fieldId="sameAsMobile"
                                            :fieldLabel="__('WhatsApp same as Mobile')"></x-forms.label>

                                        {{-- Checkbox below label --}}
                                        <div class="form-group" style="margin-top:-8px">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="sameAsMobile">
                                                <label class="custom-control-label" for="sameAsMobile">
                                                    {{ __('Yes') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <x-forms.label class="my-3" fieldId="whatsapp"
                                            :fieldLabel="__('app.whatsapp')"></x-forms.label>
                                        <x-forms.input-group style="margin-top:-4px">
                                            <x-forms.select fieldId="country_wtsap_phonecode"
                                                fieldName="country_wtsap_phonecode" search="true">
                                                @foreach ($countries as $item)
                                                    <option data-tokens="{{ $item->name }}"
                                                        data-country-iso="{{ $item->iso }}"
                                                        data-content="{{ $item->flagSpanCountryCode() }}"
                                                        value="{{ $item->phonecode }}"
                                                        {{ $item->phonecode == 91 ? 'selected' : '' }}>
                                                        {{ $item->phonecode }}
                                                    </option>
                                                @endforeach
                                            </x-forms.select>
                                            <input type="tel" class="form-control height-35 f-14"
                                                placeholder="@lang('placeholders.mobile')" name="whatsapp" id="whatsapp">
                                        </x-forms.input-group>
                                    </div>
                                </div>
                                <div class="row px-4">
                                    <div class="col-md-2">
                                        <x-forms.number fieldId="age" fieldLabel="Age" fieldName="age"
                                            fieldRequired="true" fieldPlaceholder="Enter your age">
                                        </x-forms.number>
                                    </div>
                                    <div class="col-md-2">
                                        <x-forms.select fieldId="sex" fieldLabel="Sex" fieldName="sex"
                                            fieldRequired="true">
                                            <option value="">--</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            {{-- <option value="other">Other</option> --}}
                                        </x-forms.select>
                                    </div>
                                    <div class="col-lg-4 col-md-4">
                                        <x-forms.label class="my-3" fieldId="" :fieldLabel="__('app.District')">
                                        </x-forms.label>
                                        <x-forms.input-group>
                                            <select class="form-control select-picker" name="district" id="district"
                                                data-live-search="true">
                                                <option value="">--</option>
                                                @foreach ($districts as $district)
                                                    <option value="{{ $district->id }}">{{ $district->name }}
                                                    </option>
                                                @endforeach
                                                <option value="0">Out Of Kerala</option>
                                            </select>
                                        </x-forms.input-group>
                                    </div>
                                    <div class="col-lg-4 col-md-4" id="place_field" style="display: none;">
                                        <x-forms.text fieldId="place" fieldLabel="Place" fieldName="place"
                                             fieldPlaceholder="Enter your place">
                                        </x-forms.text>
                                    </div>
                                    <div class="col-lg-4 col-md-4">
                                        <x-forms.label class="my-3" fieldId="" :fieldLabel="__('app.Panchayat')">
                                        </x-forms.label>
                                        <x-forms.input-group>
                                            <select class="form-control select-picker" name="panchayath"
                                                id="panchayath" data-live-search="true">
                                                <option value="">--</option>
                                                @foreach ($panchayath as $panchayat)
                                                    <option value="{{ $panchayat->id }}">{{ $panchayat->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </x-forms.input-group>
                                    </div>
                                    {{-- <div class="col-md-6">
                                        <x-forms.text fieldId="place" fieldLabel="Place" fieldName="place"
                                            fieldRequired="true" fieldPlaceholder="Enter your place">
                                        </x-forms.text>
                                    </div> --}}

                                </div>

                                <div class="row px-4 mb-2">
                                    <div class="col-md-3">
                                        <x-forms.text fieldId="pincode" fieldLabel="Pincode" fieldName="pincode"
                                            fieldPlaceholder="Enter your pincode">
                                        </x-forms.text>
                                    </div>
                                    <div class="col-lg-3 col-md-4">
                                        <x-forms.label class="my-3" fieldId="" :fieldLabel="__('app.kids_under_12')">
                                        </x-forms.label>
                                        <x-forms.input-group>
                                            <select class="form-control select-picker" name="kids_under_12"
                                                id="kids_under_12">
                                                <option value="">--</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                            </select>
                                        </x-forms.input-group>
                                    </div>
                                    <div class="col-md-6">
                                        {{-- Label above checkbox --}}
                                        <x-forms.label class="my-3" fieldId="iAgree"
                                            :fieldLabel="__('I agree to be added to the official WhatsApp group')"></x-forms.label>

                                        {{-- Checkbox below label --}}
                                        <div class="form-group" style="margin-top:-8px">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" value="Y" name="whatsapp_group_permission" id="iAgree">
                                                <label class="custom-control-label" for="iAgree">
                                                    {{ __('Yes') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="py-0 mb-4 bg-white border-0 card-footer d-flex justify-content-end py-lg-4 py-md-4 mb-lg-3 mb-md-3 ">
                                    <x-forms.button-cancel :link="route('events.index')"
                                        class="mb-2 mr-3 border-0">@lang('app.cancel')</x-forms.button-cancel>
                                    <x-forms.link-primary class="mb-2" link="javascript:;"
                                        id="save-event-registration-form-btn" icon="check">
                                        @lang('app.Register')
                                    </x-forms.link-primary>
                                </div>
                            </div>
                        </x-form>
                    </div>
                </div>
                <!-- INVOICE CARD END -->

                {{-- Custom fields data --}}
                @if (isset($fields) && count($fields) > 0)
                    <div class="mt-4 row">
                        <!-- TASK STATUS START -->
                        <div class="col-md-12">
                        </div>
                    </div>
                @endif
            </div>

            <!-- also the modal itself -->
            <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog d-flex justify-content-center align-items-center modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modelHeading">Modal title</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">×</span></button>
                        </div>
                        <div class="modal-body">
                            {{ __('app.loading') }}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="mr-3 rounded btn-cancel"
                                data-dismiss="modal">Close</button>
                            <button type="button" class="rounded btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Global Required Javascript -->
            <script src="{{ asset('js/main.js') }}"></script>

            <script>
                $('#sameAsMobile').on('change', function() {
                    if ($(this).is(':checked')) {
                        let mobileCode = $('#country_phonecode').val();
                        $('#country_wtsap_phonecode').val(mobileCode).trigger('change');
                        let mobile = $('#mobile').val();
                        $('#whatsapp').val(mobile);
                    }
                });


                // $('#district').change(function(e) {
                //     let districtId = $(this).val();
                //     let url = "{{ route('get_panchayats', ':id') }}";
                //     // let url = "{{ route('get_product_sub_categories', ':id') }}";

                //     url = (districtId) ? url.replace(':id', districtId) : url.replace(':id', null);

                //     $.easyAjax({
                //         url: url,
                //         type: "GET",
                //         success: function(response) {
                //             if (response.status == 'success') {
                //                 var options = [];
                //                 var rData;
                //                 rData = response.data;
                //                 $.each(rData, function(index, value) {
                //                     var selectData;
                //                     selectData = '<option value="' + value.id + '">' + value
                //                         .name + '</option>';
                //                     options.push(selectData);
                //                 });

                //                 $('#panchayath').html('<option value="">--</option>' + options);
                //                 $('#panchayath').selectpicker('refresh');
                //             }
                //         }
                //     })
                // });

                $('#district').change(function(e) {
                    let districtId = $(this).val();

                    // Show place field if Out Of Kerala is selected
                    if (districtId === '0') {
                        $('#place_field').show();
                        $('#panchayath').val('').selectpicker('refresh');
                        $('#panchayath').closest('.col-md-4').hide();
                    } else {
                        $('#place_field').hide();
                        $('#panchayath').closest('.col-md-4').show();

                        // Original functionality for loading panchayaths
                        let url = "{{ route('get_panchayats', ':id') }}";
                        url = (districtId) ? url.replace(':id', districtId) : url.replace(':id', null);

                        $.easyAjax({
                            url: url,
                            type: "GET",
                            success: function(response) {
                                if (response.status == 'success') {
                                    var options = [];
                                    var rData = response.data;
                                    $.each(rData, function(index, value) {
                                        var selectData = '<option value="' + value.id + '">' + value
                                            .name + '</option>';
                                        options.push(selectData);
                                    });

                                    $('#panchayath').html('<option value="">--</option>' + options);
                                    $('#panchayath').selectpicker('refresh');
                                }
                            }
                        });
                    }
                });
                $(document).ready(function() {
                    $('#save-event-registration-form-btn').click(function() {
                        const url = "{{ route('front.events.new-register') }}";
                        $.easyAjax({
                            url: url,
                            container: '#save-event-registration-form',
                            type: "POST",
                            disableButton: true,
                            blockUI: true,
                            buttonSelector: "#save-event-registration-form-btn",
                            data: $('#save-event-registration-form').serialize(),
                            success: function(response) {
                                if (response.status == 'success') {
                                    window.location.href = response.redirectUrl;
                                }
                            }
                        });
                    });
                });

                // Optional Loader Handling
                $(window).on('load', function() {
                    init();
                    $(".preloader-container").fadeOut("slow", function() {
                        $(this).removeClass("d-flex");
                    });
                });
            </script>

</html>
