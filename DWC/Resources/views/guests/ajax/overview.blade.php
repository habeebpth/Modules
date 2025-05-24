<script src="{{ asset('vendor/jquery/frappe-charts.min.iife.js') }}"></script>
<script src="{{ asset('vendor/jquery/Chart.min.js') }}"></script>
<script src="{{ asset('vendor/jquery/gauge.js') }}"></script>



<div class="d-lg-flex">
    <div class="w-100 py-0 py-lg-3 py-md-0 ">

        <!-- PROJECT DETAILS START -->
        <div class="row">
            <div class="col-md-12 mb-4">
                <x-cards.data class="mb-4">
                    <div class="d-flex justify-content-between">
                        <div class="col-6">
                            <strong class="f-14 text-dark-grey">@lang('app.name')</strong>
                            <p>
                                {{ $guests->first_name ? ucfirst($guests->salutation ?? '') . ' ' . $guests->first_name . ' ' . $guests->last_name : '--' }}
                            </p>
                        </div>

                        @if ($guests->guesttype->name)
                            <div class="col-6">
                                <strong class="f-14 text-dark-grey">@lang('modules.guests.guest_type')</strong>
                                <p>{{ $guests->guesttype->name ?? '--' }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between">
                        <div class="col-6">
                            <strong class="f-14 text-dark-grey">@lang('modules.guests.horse')</strong>
                            <p>{{ $guests->horse->name ?? '--' }}</p>
                        </div>

                        <div class="col-6">
                            <strong class="f-14 text-dark-grey">@lang('app.company')</strong>
                            <p>{{ $guests->company ?? '--' }}</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div class="col-6">
                            <strong class="f-14 text-dark-grey">@lang('app.email')</strong>
                            <p>{{ $guests->email ?? '--' }}</p>
                        </div>

                        <div class="col-6">
                            <strong class="f-14 text-dark-grey">@lang('app.phone')</strong>
                            <p>{{ $guests->guestcountrycode->phonecode ?? '' }} {{ $guests->mobile ?? '--' }}</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div class="col-6">
                            <strong class="f-14 text-dark-grey">@lang('app.address')</strong>
                            <p>{{ $guests->address_1 ?? '--' }}</p>
                        </div>

                        <div class="col-6">
                            <strong class="f-14 text-dark-grey">@lang('modules.guests.state')</strong>
                            <p>{{ $guests->state ?? '--' }}</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div class="col-6">
                            <strong class="f-14 text-dark-grey">@lang('app.country')</strong>
                            <p>{{ $guests->guestcountry->name ?? '--' }}</p>
                        </div>

                        <div class="col-6">
                            <strong class="f-14 text-dark-grey">@lang('app.nationality')</strong>
                            <p>{{ $guests->guestnationality->name ?? '--' }}</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div class="col-6">
                            <strong class="f-14 text-dark-grey">@lang('modules.guests.visa_required')</strong>
                            <p>{{ $guests->visa_required ? __('app.yes') : __('app.no') }}</p>
                        </div>

                        <div class="col-6">
                            <strong class="f-14 text-dark-grey">@lang('app.passportNumber')</strong>
                            <p>{{ $guests->passport_number ?? '--' }}</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div class="col-6">
                            <strong class="f-14 text-dark-grey">@lang('modules.guests.amendment_date')</strong>
                            <p>{{ $guests->amendment_date ?? '--' }}</p>
                        </div>
                    </div>
                </x-cards.data>

            </div>
        </div>


        <!-- PROJECT DETAILS END -->

    </div>
</div>

<script>

</script>
