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
            height: 50px;
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
                                    {{ $EvntEvent->name }}<br>
                                    {{-- {!! nl2br($company->defaultAddress->address) !!}<br>
                                {{ $company->company_phone }} --}}
                                </p><br>
                            </td>
                            <td align="right">
                                {{-- <table class="mt-3 inv-num-date text-dark f-13">
                                <tr>
                                    <td class="bg-light-grey border-right-0 f-w-500">
                                        @lang('modules.contracts.contractNumber')</td>
                                    <td class="border-left-0"> {{ $contract->contract_number }}</td>
                                </tr>
                                <tr>
                                    <td class="bg-light-grey border-right-0 f-w-500">
                                        @lang('modules.projects.startDate')</td>
                                    <td class="border-left-0">{{ $contract->start_date->translatedFormat($company->date_format) }}
                                    </td>
                                </tr>
                                @if ($contract->end_date != null)
                                    <tr>
                                        <td class="bg-light-grey border-right-0 f-w-500">@lang('modules.contracts.endDate')
                                        </td>
                                        <td class="border-left-0">{{ $contract->end_date->translatedFormat($company->date_format) }}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="bg-light-grey border-right-0 f-w-500">
                                        @lang('modules.contracts.contractType')</td>
                                    <td class="border-left-0">{{ $contract->contractType->name }}
                                    </td>
                                </tr>
                            </table> --}}
                            </td>
                        </tr>
                        <tr>
                            <td height="20"></td>
                        </tr>
                    </table>
                    {{-- <table width="100%">
                    <tr class="inv-unpaid">
                        <td class="f-14 text-dark">
                            <p class="mb-0 text-left"><span
                                    class="text-dark-grey text-capitalize">@lang("app.client")</span><br>
                                {{ $contract->client->name_salutation }}<br>
                                {{ $contract->client->clientDetails->company_name }}<br>
                                {!! nl2br($contract->client->clientDetails->address) !!}</p>
                        </td>
                        <td align="right">
                            @if ($contract->client->clientDetails->company_logo)
                                <img src="{{ $contract->client->clientDetails->image_url }}"
                                     alt="{{ $contract->client->clientDetails->company_name }}"
                                     class="logo"/>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td height="30"></td>
                    </tr>
                </table> --}}
                </div>

                {{-- <div class="d-flex flex-column" id="save-event-registration-form">
                <h3 class="text-center font-weight-bold my-3" style="text-decoration: underline;">
                    Event Registration Form
                </h3>
                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="student_id" :fieldLabel="__('app.menu.studentId')" fieldName="student_id" fieldRequired="true"
                            :fieldPlaceholder="__('placeholders.studentId')">
                        </x-forms.text>
                    </div>
                <div class="col-md-6">
                    <x-forms.text fieldId="name" :fieldLabel="__('app.name')" fieldName="name" fieldRequired="true"
                        :fieldPlaceholder="__('placeholders.name')">
                    </x-forms.text>
                </div>
            </div>
            <div class="row px-4">
                <div class="col-md-6">
                    <x-forms.text fieldId="mobile" :fieldLabel="__('app.mobile')" fieldName="mobile" fieldRequired="true"
                        :fieldPlaceholder="__('placeholders.mobile')">
                    </x-forms.text>
                </div>
                <div class="col-md-6">
                    <x-forms.number fieldId="no_of_participants" :fieldLabel="__('app.menu.noOfParticipants')" fieldName="no_of_participants"
                        fieldRequired="true" fieldValue="1" min="1">
                    </x-forms.number>
                </div>
            </div>
        </div> --}}
                <div class="row">
                    <div class="col-sm-12">
                        <x-form id="save-event-registration-form">
                            <div class="add-client bg-white rounded">
                                <h4
                                    class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey text-center">
                                    @lang('app.menu.EventRegistration')
                                </h4>
                                <input type="hidden" name="event_id" value="{{ $EvntEvent->id }}">
                                <div class="row px-4">
                                    <div class="col-md-6">
                                        <x-forms.text fieldId="student_id" :fieldLabel="__('app.menu.studentId')" fieldName="student_id"
                                            fieldRequired="true" :fieldPlaceholder="__('placeholders.studentId')">
                                        </x-forms.text>
                                    </div>
                                    <div class="col-md-6">
                                        <x-forms.text fieldId="name" :fieldLabel="__('app.name')" fieldName="name"
                                            fieldRequired="true" :fieldPlaceholder="__('placeholders.name')">
                                        </x-forms.text>
                                    </div>
                                </div>

                                <div class="row px-4">
                                    {{-- <div class="col-md-6">
                                <x-forms.text fieldId="mobile" :fieldLabel="__('app.mobile')" fieldName="mobile" fieldRequired="true"
                                    :fieldPlaceholder="__('placeholders.mobile')">
                                </x-forms.text>
                            </div> --}}
                                    <div class="col-lg-6 col-md-6">
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
                                                        {{ $item->phonecode == 971 ? 'selected' : '' }}>
                                                        {{ $item->phonecode }}
                                                    </option>
                                                @endforeach
                                            </x-forms.select>
                                            <input type="tel" class="form-control height-35 f-14"
                                                placeholder="@lang('placeholders.mobile')" name="mobile" id="mobile">
                                        </x-forms.input-group>
                                    </div>
                                    <div class="col-md-6">
                                        <x-forms.select fieldId="no_of_participants" :fieldLabel="__('app.menu.noOfParticipants')"
                                            fieldName="no_of_participants" search="true" fieldRequired="true">
                                            @foreach ($allowedSeats as $seat)
                                                <option value="{{ $seat }}">{{ $seat }}</option>
                                            @endforeach
                                        </x-forms.select>
                                    </div>

                                </div>
                                <hr class="mt-1 mb-1">
                                {{-- <div id="signature-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog d-flex justify-content-center align-items-center modal-xl">
                    <div class="modal-content">
                        @include('estimates.ajax.accept-estimate')
                    </div>
                </div>
            </div> --}}

                            </div>
                            <!-- CARD BODY END -->

                            <!-- CARD FOOTER START -->
                            <div
                                class="py-0 mb-4 bg-white border-0 card-footer d-flex justify-content-end py-lg-4 py-md-4 mb-lg-3 mb-md-3 ">

                                <x-forms.button-cancel :link="route('events.index')" class="mb-2 mr-3 border-0">@lang('app.cancel')
                                </x-forms.button-cancel>

                                {{-- <x-forms.link-secondary :link="route('front.contract.download', $contract->hash)" class="mb-2 mr-3"
                                    icon="download">@lang('app.download')
            </x-forms.link-secondary> --}}

                                <x-forms.link-primary class="mb-2" link="javascript:;"
                                    id="save-event-registration-form-btn" icon="check">
                                    @lang('app.Register')
                                </x-forms.link-primary>


                            </div>
                        </x-form>
                    </div>
                    <!-- CARD FOOTER END -->
                </div>
                <!-- INVOICE CARD END -->

                {{-- Custom fields data --}}
                @if (isset($fields) && count($fields) > 0)
                    <div class="mt-4 row">
                        <!-- TASK STATUS START -->
                        <div class="col-md-12">
                            {{-- <x-cards.data>
                    <x-forms.custom-field-show :fields="$fields" :model="$contract"></x-forms.custom-field-show>
                </x-cards.data> --}}
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
                document.loading = '@lang('app.loading')';
                const MODAL_LG = '#myModal';
                const MODAL_HEADING = '#modelHeading';

                $(window).on('load', function() {
                    // Animate loader off screen
                    init();
                    $(".preloader-container").fadeOut("slow", function() {
                        $(this).removeClass("d-flex");
                    });
                });
                $(document).ready(function() {
                    $('#save-event-registration-form-btn').click(function() {
                        console.log('gfd');

                        const url = "{{ route('front.events.register') }}";

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
            </script>

            {{-- <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
<script>
    var canvas = document.getElementById('signature-pad');

    var signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)' // necessary for saving image as JPEG; can be removed is only saving as PNG or SVG
    });

    document.getElementById('clear-signature').addEventListener('click', function (e) {
        e.preventDefault();
        signaturePad.clear();
    });

    document.getElementById('undo-signature').addEventListener('click', function (e) {
        e.preventDefault();
        var data = signaturePad.toData();
        if (data) {
            data.pop(); // remove the last dot or line
            signaturePad.fromData(data);
        }
    });

    $('#toggle-pad-uploader').click(function () {
        var text = $('.signature').hasClass('d-none') ? '{{ __("modules.estimates.uploadSignature") }}' : '{{ __("app.sign") }}';

        $(this).html(text);

        $('.signature').toggleClass('d-none');
        $('.upload-image').toggleClass('d-none');
    });

    $('#save-signature').click(function () {
        var first_name = $('#first_name').val();
        var last_name = $('#last_name').val();
        var email = $('#email').val();
        var signature = signaturePad.toDataURL('image/png');
        var image = $('#image').val();

        // this parameter is used for type of signature used and will be used on validation and upload signature image
        var signature_type = !$('.signature').hasClass('d-none') ? 'signature' : 'upload';

        if (signaturePad.isEmpty() && !$('.signature').hasClass('d-none')) {
            Swal.fire({
                icon: 'error',
                text: '{{ __('messages.signatureRequired') }}',

                customClass: {
                    confirmButton: 'btn btn-primary',
                },
                showClass: {
                    popup: 'swal2-noanimation',
                    backdrop: 'swal2-noanimation'
                },
                buttonsStyling: false
            });
            return false;
        }

        $.easyAjax({
            url: "{{ route('front.contract.sign', $EvntEvent->id) }}",
            container: '#acceptEstimate',
            type: "POST",
            blockUI: true,
            file: true,
            disableButton: true,
            buttonSelector: '#save-signature',
            data: {
                first_name: first_name,
                last_name: last_name,
                email: email,
                signature: signature,
                image: image,
                signature_type: signature_type,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                if (response.status == 'success') {
                    window.location.reload();
                }
            }
        })
    });

</script> --}}

</body>

</html>
