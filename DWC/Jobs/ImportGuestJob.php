<?php

namespace Modules\DWC\Jobs;

use App\Traits\ExcelImportable;
use App\Traits\UniversalSearchTrait;
use Carbon\Exceptions\InvalidFormatException;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\DWC\Entities\DwcGuest;
use Modules\DWC\Entities\DwcHotelReservation;
use Modules\DWC\Entities\DwcFlightTicket;
use Modules\DWC\Entities\DwcGuestType;
use Illuminate\Support\Facades\Log;
use App\Models\Country;
use Modules\DWC\Entities\DwcAirport;
use Modules\DWC\Entities\DwcBillingCode;
use Modules\DWC\Entities\DwcHotel;

class ImportGuestJob implements ShouldQueue, ShouldBeUnique
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use UniversalSearchTrait;
    use ExcelImportable;

    private $row;
    private $columns;
    private $company;

    public function __construct($row, $columns, $company = null)
    {
        $this->row = $row;
        $this->columns = $columns;
        $this->company = $company;
    }

    public function handle()
    {
        Log::info('cHECKIN exist', [
            'checkin' => $this->isColumnExists('checkin_date'),
            'checkout' => $this->isColumnExists('checkout_date'),
        ]);
        if ($this->isColumnExists('first_name')) {
            // Log the mandatory fields before validation
            Log::info('Validating mandatory fields', [
                'checkin_datetime'  => $this->getColumnValue('checkin_datetime'),
                'ticket_1_departure_datetime' => $this->getColumnValue('ticket_1_departure_datetime'),
                'passport_no'  => $this->getColumnValue('passport_no'),
                'first_name'  => $this->getColumnValue('first_name'),

            ]);
            if (DwcGuest::where('first_name', $this->getColumnValue('first_name'))
                ->orWhere('passport_number', $this->getColumnValue('passport_no'))
                ->exists()
            ) {
                $this->failJobWithMessage(__('messages.duplicateEntry'));
                return;
            }

            DB::beginTransaction();
            try {
                $guestType = DwcGuestType::where('name', $this->getColumnValue('guest_type'))->first();

                if (!$guestType) {
                    $this->failJobWithMessage(__('messages.invalidGuestType', ['type' => $this->getColumnValue('guest_type')]));
                    return;
                }
                $country = Country::where('name', $this->getColumnValue('nationality'))->first();
                $nationalityId = $country ? $country->id : 0;
                // Store Guest Data
                $guest = new DwcGuest();
                $guest->guest_type = $guestType->id;
                $guest->first_name = $this->getColumnValue('first_name');
                $guest->last_name = $this->getColumnValue('last_name');
                $guest->state = $this->getColumnValue('state');
                $guest->mobile = $this->getColumnValue('mobile');
                $guest->email = $this->getColumnValue('email');
                $guest->nationality = $nationalityId;
                $guest->visa_required = strtolower($this->getColumnValue('visa_required')) === 'yes' ? 1 : 0;
                $guest->passport_number = $this->getColumnValue('passport_no');
                $guest->amendment_date = Carbon::now();
                $guest->save();

                // Store Flight Ticket 1
                if ($this->isColumnExists('ticket_1_flight_no')) {
                    //$departureDateTime = date('Y-m-d H:i:s',$this->getColumnValue('ticket_1_departure_datetime'));
                    // $arrivalDateTime = date('Y-m-d H:i:s',$this->getColumnValue('ticket_1_arrival_datetime'));
                    $departureDateTime = Carbon::createFromFormat('d/m/Y H:i', $this->getColumnValue('ticket_1_departure_datetime'));
                    $arrivalDateTime = Carbon::createFromFormat('d/m/Y H:i', $this->getColumnValue('ticket_1_arrival_datetime'));

                    Log::info('Flight', [
                        'departureDateTime' => $departureDateTime,
                        'arrivalDateTime' => $arrivalDateTime,
                    ]);

                    $from_airport = DwcAirport::where('key', $this->getColumnValue('ticket_1_from'))->first();
                    $to_airport = DwcAirport::where('key', $this->getColumnValue('ticket_1_to'))->first();
                    $flight1 = new DwcFlightTicket();
                    // $flight1->guest_id = $guest->id;
                    $flight1->flight_no = $this->getColumnValue('ticket_1_flight_no');
                    $flight1->departure_date = $departureDateTime->format('Y-m-d');
                    $flight1->departure_time = $departureDateTime->format('H:i:s');
                    $flight1->arrival_date = $arrivalDateTime->format('Y-m-d');
                    $flight1->arrival_time = $arrivalDateTime->format('H:i:s');
                    $flight1->flight_from = $from_airport ? $from_airport->id : 0;
                    $flight1->flight_to = $to_airport ? $to_airport->id : 0;
                    $flight1->flight_class = $this->getColumnValue('flight_class');
                    $flight1->locator = $this->getColumnValue('locator');
                    $flight1->ticket_number = $this->getColumnValue('ticket_number');
                    $flight1->save();
                    if ($flight1) {
                        $guest->flightTickets()->attach($flight1->id);
                    }
                }

                // Store Flight Ticket 2
                if ($this->isColumnExists('ticket_2_flight_no')) {
                    // $departureDateTime = $this->parseDateTime($this->getColumnValue('ticket_2_departure_datetime'));
                    // $arrivalDateTime = $this->parseDateTime($this->getColumnValue('ticket_2_arrival_datetime'));

                    $departureDateTime = Carbon::createFromFormat('d/m/Y H:i', $this->getColumnValue('ticket_2_departure_datetime'));
                    $arrivalDateTime = Carbon::createFromFormat('d/m/Y H:i', $this->getColumnValue('ticket_2_arrival_datetime'));

                    $from_airport_2 = DwcAirport::where('key', $this->getColumnValue('ticket_2_from'))->first();
                    $to_airport_2 = DwcAirport::where('key', $this->getColumnValue('ticket_2_to'))->first();
                    $flight2 = new DwcFlightTicket();
                    // $flight2->guest_id = $guest->id;
                    $flight2->flight_no = $this->getColumnValue('ticket_2_flight_no');
                    $flight2->departure_date = $departureDateTime->format('Y-m-d');
                    $flight2->departure_time = $departureDateTime->format('H:i:s');
                    $flight2->arrival_date = $arrivalDateTime->format('Y-m-d');
                    $flight2->arrival_time = $arrivalDateTime->format('H:i:s');
                    $flight2->flight_from = $from_airport_2 ? $from_airport_2->id : 0;
                    $flight2->flight_to = $to_airport_2 ? $to_airport_2->id : 0;
                    $flight2->flight_class = $this->getColumnValue('flight_class');
                    $flight2->locator = $this->getColumnValue('locator');
                    $flight2->ticket_number = $this->getColumnValue('ticket_number');
                    $flight2->save();
                    if ($flight2) {
                        $guest->flightTickets()->attach($flight2->id);
                    }
                }


                $checkin_date = Carbon::createFromFormat('d/m/Y H:i', $this->getColumnValue('checkin_datetime'))->format('Y-m-d');
                $checkout_date = Carbon::createFromFormat('d/m/Y H:i', $this->getColumnValue('checkout_datetime'))->format('Y-m-d');
                // Store Hotel Reservation Data
                $hotel = DwcHotel::where('name', 'like', '%'.$this->getColumnValue('hotel_name').'%')->first();
                $billing_code = DwcBillingCode::where('name', $this->getColumnValue('billing_code'))->first();
                $hotelReservation = new DwcHotelReservation();
                $hotelReservation->hotel_id = $hotel ? $hotel->id : 0;
                $hotelReservation->others = !$hotel ? $this->getColumnValue('hotel_name') : '';
                $hotelReservation->room_type = $this->getColumnValue('room_type');
                $hotelReservation->checkin_date = $checkin_date;
                $hotelReservation->checkout_date = $checkout_date;
                $hotelReservation->no_of_nights = $this->getColumnValue('nights');
                $hotelReservation->billing_code = $billing_code ? $billing_code->id : '';
                $hotelReservation->confirmation_no = $this->getColumnValue('confirmation_no');
                $hotelReservation->sharing_with = $this->getColumnValue('sharing_with');
                $hotelReservation->note_2 = $this->getColumnValue('notes');
                $hotelReservation->save();
                if ($hotelReservation) {
                    $guest->hotelReservations()->attach($hotelReservation->id);
                }



                DB::commit();
            } catch (InvalidFormatException $e) {
                DB::rollBack();
                $this->failJob(__('messages.invalidDate'));
            } catch (Exception $e) {
                DB::rollBack();
                $this->failJobWithMessage($e->getMessage());
            }
        } else {
            $this->failJob(__('messages.invalidData'));
        }
    }

    private function parseDateTime($dateTime)
    {
        if (!$dateTime) {
            return null;
        }

        try {
            return Carbon::parse($dateTime); // Automatically detects format
        } catch (Exception $e) {
            return null; // Returns null if parsing fails
        }
    }
}
