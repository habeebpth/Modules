<!doctype html>
<html lang="en">

<head>
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

    <style>
        .logo {
            height: 50px;
        }

        .preloader-container {
            height: 100vh;
            width: 100%;
        }

        .signature_wrap {
            position: relative;
            height: 150px;
            width: 400px;
        }

        .signature-pad {
            position: absolute;
            left: 0;
            top: 0;
            width: 400px;
            height: 150px;
        }

        .event-name {
            font-size: 24px;
            /* Adjust size as needed */
            font-weight: bold;
        }
    </style>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery/modernizr.min.js') }}"></script>

</head>

<body id="body" class="h-100 bg-additional-grey {{ isRtl('rtl') }}">

    <div class="container content-wrapper">
        <div class="border-0 card invoice">
            <div class="card-body">
                <div class="invoice-table-wrapper">
                    <table width="100%">
                        <tr class="inv-logo-heading">
                            <td><img src="{{ $EvntEvent->icon_url }}" alt="{{ $EvntEvent->name }}" class="logo"></td>
                        </tr>
                        <tr class="inv-num">

                        </tr>
                    </table>
                </div>
                <div class="row justify-content-center">
                    <div class="col-sm-12 text-center">
                        <p class="mb-1 event-name">
                            <strong>{{ $EvntEvent->name }}</strong>
                        </p>

                        <p class="mb-1"><strong>Student ID:</strong> {{ $registration->student_id }}</p>
                        <p class="mb-1"> {{ strtoupper($student->student_name) }}</p>
                        <p class="mb-1"><strong>Ref Name:</strong> {{ strtoupper($registration->name) }}</p>
                        {{-- <p class="mb-1"><strong>Allowed Seats:</strong>
                            {{ implode(', ', range($registration->allotted_seats_start, $registration->allotted_seats_start + $registration->no_of_participants - 1)) }}
                        </p> --}}
                               <p class="mb-1"><strong>No Of Tickets:</strong> {{ $registration->no_of_participants }}</p>
                        <h2 class="mt-3">Scan the QR Code</h2>
                        <img src="{{ $qrCodeUrl }}" alt="Event QR Code" class="img-fluid">
                    </div>

                    <x-forms.link-secondary :link="route('front.event.qr.download', [
                        'slug' => $EvntEvent->slug,
                        'registration_code' => $registration->registration_code,
                    ])" class="mb-2 mr-3" icon="download">
                        @lang('app.download')
                    </x-forms.link-secondary>
                </div>

            </div>
        </div>
    </div>

    <script src="{{ asset('js/main.js') }}"></script>
</body>

</html>
