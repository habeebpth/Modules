<?php

namespace Modules\DWC\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class GuestImport implements ToArray
{
    public static function fields(): array
    {
        return array(
            ['id' => 'guest_type', 'name' => __('app.Guesttype'), 'required' => 'Yes'],
            ['id' => 'first_name', 'name' => __('app.first_name'), 'required' => 'Yes'],
            ['id' => 'last_name', 'name' => __('app.last_name'), 'required' => 'No'],
            ['id' => 'state', 'name' => __('modules.guests.state'), 'required' => 'No'],
            ['id' => 'mobile', 'name' => __('modules.guests.mobile'), 'required' => 'Yes'],
            ['id' => 'email', 'name' => __('modules.guests.email'), 'required' => 'Yes'],
            ['id' => 'nationality', 'name' => __('app.nationality'), 'required' => 'No'],
            ['id' => 'visa_required', 'name' => __('modules.guests.visa_required'), 'required' => 'No'],
            ['id' => 'passport_no', 'name' => 'Passport No', 'required' => 'No'],
            ['id' => 'ticket_1_flight_no', 'name' => 'Ticket 1 Flight Number', 'required' => 'No'],
            ['id' => 'ticket_1_departure_datetime', 'name' => __('app.ticket_1_departure_datetime'), 'required' => 'No'],
            ['id' => 'ticket_1_arrival_datetime', 'name' => __('app.ticket_1_arrival_datetime'), 'required' => 'No'],
            ['id' => 'ticket_1_from', 'name' => __('app.ticket_1_from'), 'required' => 'No'],
            ['id' => 'ticket_1_to', 'name' => __('app.ticket_1_to'), 'required' => 'No'],
            ['id' => 'ticket_2_flight_no', 'name' => __('app.ticket_2_flight_no'), 'required' => 'No'],
            ['id' => 'ticket_2_departure_datetime', 'name' => __('app.ticket_2_departure_datetime'), 'required' => 'No'],
            ['id' => 'ticket_2_arrival_datetime', 'name' => __('app.ticket_2_arrival_datetime'), 'required' => 'No'],
            ['id' => 'ticket_2_from', 'name' => __('app.ticket_2_from'), 'required' => 'No'],
            ['id' => 'ticket_2_to', 'name' => __('app.ticket_2_to'), 'required' => 'No'],
            ['id' => 'flight_class', 'name' => __('app.flight_class'), 'required' => 'No'],
            ['id' => 'locator', 'name' => __('app.locator'), 'required' => 'No'],
            ['id' => 'ticket_number', 'name' => __('app.ticket_number'), 'required' => 'No'],
            ['id' => 'hotel_name', 'name' => __('app.hotel_name'), 'required' => 'No'],
            ['id' => 'room_type', 'name' => __('app.room_type'), 'required' => 'No'],
            ['id' => 'checkin_datetime', 'name' => 'Checkin Date', 'required' => 'No'],
            ['id' => 'checkout_datetime', 'name' => 'Checkout Date', 'required' => 'No'],
            ['id' => 'nights', 'name' => __('app.nights'), 'required' => 'No'],
            ['id' => 'billing_code', 'name' => __('app.billingCode'), 'required' => 'No'],
            ['id' => 'confirmation_no', 'name' => __('app.ConfirmationNo'), 'required' => 'No'],
            ['id' => 'sharing_with', 'name' => __('app.sharingWith'), 'required' => 'No'],
            ['id' => 'notes', 'name' => __('app.notes'), 'required' => 'No']
        );
    }

    public function array(array $array): array
    {
        return $array;
    }
}
