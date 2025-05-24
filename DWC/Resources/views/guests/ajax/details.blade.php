<!-- ROW START -->
<div class="row">
    <!--  USER CARDS START -->
    <div class="col-xl-12 col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4 mb-md-0">

        <x-cards.data :title="__('modules.guest.details')">

            <x-slot name="action">
                <div class="dropdown">
                    <button class="btn f-14 px-0 py-0 text-dark-grey dropdown-toggle" type="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-ellipsis-h"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                        aria-labelledby="dropdownMenuLink" tabindex="0">
                    </div>
                </div>
            </x-slot>
            <x-cards.data-row :label="__('app.name')" :value="$guest->name_salutation ?? '--'" />

            <x-cards.data-row :label="__('app.email')" :value="$guest->email ?? '--'" />

            <x-cards.data-row :label="__('app.company')" :value="$guest->company ?? '--'" />

            <x-cards.data-row :label="__('modules.guest.guest_type')" :value="$guest->guest_type ?? '--'" />

            <x-cards.data-row :label="__('modules.guest.address_1')" :value="$guest->address_1 ?? '--'" />

            <x-cards.data-row :label="__('modules.guest.address_2')" :value="$guest->address_2 ?? '--'" />

            <x-cards.data-row :label="__('modules.guest.state')" :value="$guest->state ?? '--'" />

            <x-cards.data-row :label="__('modules.guest.zip')" :value="$guest->zip ?? '--'" />

            <x-cards.data-row :label="__(key: 'modules.guest.country')" :value="$guest->country ?? '--'" />

            <x-cards.data-row :label="__('modules.guest.tel')" :value="$guest->tel ?? '--'" />

            <x-cards.data-row :label="__('modules.guest.fax')" :value="$guest->fax ?? '--'" />

            <x-cards.data-row :label="__('modules.guest.mobile')" :value="$guest->mobile_county_code ?? '+91' . ' ' . $guest->mobile ?? '--'" />

            <x-cards.data-row :label="__('modules.guest.nationality')" :value="$guest->nationality ?? '--'" />

            <x-cards.data-row :label="__('modules.guest.visa_required')" :value="$guest->visa_required ? 'Yes': 'No'" />

            <x-cards.data-row :label="__('modules.guest.passport_number')" :value="$guest->passport_number ?? '--'" />

            <x-cards.data-row :label="__('modules.guest.travel_with')" :value="DwcGuest::find($guest->travel_with)?->name ?? '--'" />

            {{-- Custom fields data --}}
            <x-forms.custom-field-show :fields="$fields" :model="$leadContact"></x-forms.custom-field-show>

        </x-cards.data>
    </div>
    <!--  USER CARDS END -->
</div>
<!-- ROW END -->
