<?php

namespace Modules\DWC\DataTables;

use App\Helper\Common;
use App\Models\EmployeeDetails;
use App\Scopes\ActiveScope;
use Carbon\Carbon;
use App\Models\Role;
use App\DataTables\BaseDataTable;
use App\Models\User;
use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\DB;
use Modules\DWC\Entities\DwcFlightTicket;

class DwcArrivalDataTable extends BaseDataTable
{
    private $editEmployeePermission;
    private $deleteEmployeePermission;
    private $viewEmployeePermission;
    private $changeEmployeeRolePermission;

    public function __construct()
    {
        parent::__construct();
        $this->editEmployeePermission = user()->permission('edit_employees');
        $this->deleteEmployeePermission = user()->permission('delete_employees');
        $this->viewEmployeePermission = user()->permission('view_employees');
        $this->changeEmployeeRolePermission = user()->permission('change_employee_role');
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $datatables = datatables()->eloquent($query)
            ->addColumn('check', fn ($row) => $this->checkBox($row))
            ->addColumn('horse', function ($row) {
                $horses = $row->guests->map(function ($guest) {
                    return $guest->horse ? $guest->horse->name : null;
                })->filter()->implode(', ');

                return $horses ?: 'Not Selected';
            })
            ->addColumn('hotel', function ($row) {
                $hotels = $row->guests->flatMap(function ($guest) {
                    return $guest->hotelReservations->map(function ($reservation) {
                        return $reservation->hotel ? $reservation->hotel->name : $reservation->others;
                    });
                })->filter()->unique()->implode(', ');

                return $hotels ?: 'Not Selected';
            })
            ->addColumn('roomtype', function ($row) {
                $roomtypes = $row->guests
                    ->flatMap(fn ($guest) => $guest->hotelReservations ? $guest->hotelReservations->pluck('room_type') : collect())
                    ->filter()
                    ->unique()
                    ->implode(', ');

                return $roomtypes ?: 'Not Selected';
            })
            ->addColumn('checkin_date', function ($row) {
                $checkindates = $row->guests
                    ->flatMap(fn ($guest) => $guest->hotelReservations ? $guest->hotelReservations->pluck('checkin_date') : collect())
                    ->filter()
                    ->unique()
                    ->map(fn ($date) => Carbon::parse($date)->format('d/m/Y'))
                    ->implode(', ');
                return $checkindates ?: 'Not Selected';
            })
            ->addColumn('checkout_date', function ($row) {
                $checkoutdates = $row->guests
                    ->flatMap(fn ($guest) => $guest->hotelReservations ? $guest->hotelReservations->pluck('checkout_date') : collect())
                    ->filter()
                    ->unique()
                    ->map(fn ($date) => Carbon::parse($date)->format('d/m/Y'))
                    ->implode(', ');
                return $checkoutdates ?: 'Not Selected';
            })
            ->addColumn('no_of_nights', function ($row) {
                $noofnights = $row->guests
                    ->flatMap(fn ($guest) => $guest->hotelReservations ? $guest->hotelReservations->pluck('no_of_nights') : collect())
                    ->filter()
                    ->unique()
                    ->implode(', ');

                return $noofnights ?: 'Not Selected';
            })
            ->addColumn('billing_code', function ($row) {
                $billing_codes = $row->guests
                    ->flatMap(fn ($guest) => $guest->hotelReservations ? $guest->hotelReservations->pluck('billing_code') : collect())
                    ->filter()
                    ->unique()
                    ->implode(', ');

                return $billing_codes ?: 'Not Selected';
            })
            ->addColumn('confirmation_no', function ($row) {
                $confirmation_nos = $row->guests
                    ->flatMap(fn ($guest) => $guest->hotelReservations ? $guest->hotelReservations->pluck('confirmation_no') : collect())
                    ->filter()
                    ->unique()
                    ->implode(', ');

                return $confirmation_nos ?: 'Not Selected';
            })
            ->addColumn('sharing_with', function ($row) {
                $sharingwiths = $row->guests
                    ->flatMap(fn ($guest) => $guest->hotelReservations ? $guest->hotelReservations->pluck('sharing_with') : collect())
                    ->filter()
                    ->unique()
                    ->implode(', ');

                return $sharingwiths ?: 'Not Selected';
            })
            ->addColumn('note_2', function ($row) {
                $notes_2 = $row->guests
                    ->flatMap(fn ($guest) => $guest->hotelReservations ? $guest->hotelReservations->pluck('note_2') : collect())
                    ->filter()
                    ->unique()
                    ->implode(', ');

                return $notes_2 ?: 'Not Selected';
            })
            ->addColumn('amendment_date', function ($row) {
                $amendmentdate = $row->guests
                    ->map(fn ($guest) => $guest->amendment_date ? Carbon::parse($guest->amendment_date)->format('d/m/Y') : null)
                    ->filter()
                    ->implode(', ');
                return $amendmentdate ?: 'Not Selected';
            })
            ->addColumn('guesttype', function ($row) {
                $guesttypes = $row->guests->map(fn ($guest) => $guest->guesttype ? $guest->guesttype->name : null)->filter()->implode(', ');
                return $guesttypes ?: 'Not Selected';
            })
            ->addColumn('nationality', function ($row) {
                $nationalities = $row->guests->map(fn ($guest) => $guest->guestnationality ? $guest->guestnationality->name : null)->filter()->implode(', ');
                return $nationalities ?: 'Not Selected';
            })
            ->addColumn('country', function ($row) {
                $nationalities = $row->guests->map(fn ($guest) => $guest->guestcountry ? $guest->guestcountry->name : null)->filter()->implode(', ');
                return $nationalities ?: 'Not Selected';
            })
            ->addColumn('passport_number', function ($row) {
                $passportNumbers = $row->guests->map(fn ($guest) => $guest->passport_number)->filter()->implode(', ');
                return $passportNumbers ?: 'Not Available';
            })
            ->addColumn('visa_required', function ($row) {
                $visaRequired = $row->guests->map(fn ($guest) => $guest->visa_required ? 'Yes' : 'No')->filter()->implode(', ');
                return $visaRequired ?: 'Not Specified';
            })
            ->addColumn('email', function ($row) {
                $emails = $row->guests->map(fn ($guest) => $guest->email)->filter()->implode(', ');
                return $emails ?: 'Not Provided';
            })
            ->addColumn('mobile_with_code', function ($row) {
                $contacts = $row->guests->map(
                    fn ($guest) =>
                    $guest->guestcountrycode && $guest->mobile
                        ? "{$guest->guestcountrycode->phonecode} {$guest->mobile}"
                        : null
                )->filter()->implode(', ');

                return $contacts ?: 'Not Available';
            })
            ->addColumn('full_name', function ($row) {
                $fullNames = $row->guests->map(fn ($guest) => trim("{$guest->salutation} {$guest->first_name} {$guest->last_name}"))->filter()->implode(', ');
                return $fullNames ?: 'Not Provided';
            })
            ->addColumn('company', function ($row) {
                $companies = $row->guests->map(fn ($guest) => $guest->company)->filter()->implode(', ');
                return $companies ?: 'Not Provided';
            })
            ->addColumn('address_1', function ($row) {
                $addresses = $row->guests->map(fn ($guest) => $guest->address_1)->filter()->implode(', ');
                return $addresses ?: 'Not Provided';
            })
            ->addColumn('state', function ($row) {
                $states = $row->guests->map(fn ($guest) => $guest->state)->filter()->implode(', ');
                return $states ?: 'Not Provided';
            })
            ->addColumn('action', function ($row) {
                $action = '<div class="task_view">
                        <div class="dropdown">
                            <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle"
                                id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon-options-vertical icons"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

                $action .= '<a href="' . route('dwc.departures.show', [$row->id]) . '" class="dropdown-item"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';
                $action .= '<a class="dropdown-item openRightModal" href="' . route('dwc.departures.edit', [$row->id]) . '">
                            <i class="fa fa-edit mr-2"></i>' . trans('app.edit') . '
                        </a>';
                $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-user-id="' . $row->id . '">
                            <i class="fa fa-trash mr-2"></i>' . trans('app.delete') . '
                        </a>';

                $action .= '</div>
                    </div>
                </div>';

                return $action;
            })
            ->editColumn('created_at', fn ($row) => Carbon::parse($row->created_at)->translatedFormat($this->company->date_format))
            ->addIndexColumn()
            ->setRowId(fn ($row) => 'row-' . $row->id)
            ->rawColumns(['action', 'check']);

        return $datatables;
    }


    /**
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(DwcFlightTicket $model)
    {
        $request = $this->request();
        $startDate = null;
        $endDate = null;
        $startTime = null;
        $endTime = null;
        $AirportId = $request->AirportId;
        // dd($request->searchText);
        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = $request->startDate;
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = $request->endDate;
        }
        if ($request->startTime !== null && $request->startTime != 'null' && $request->startTime != '') {
            $startTime = Carbon::createFromFormat('h:i A', $request->startTime)->format('H:i:s');
        }
        if ($request->endTime !== null && $request->endTime != 'null' && $request->endTime != '') {
            $endTime = Carbon::createFromFormat('h:i A', $request->endTime)->format('H:i:s');
        }
        $query = $model->leftJoin('dwc_airports as departure_airport', 'dwc_flight_tickets.flight_from', '=', 'departure_airport.id')
            ->leftJoin('dwc_airports as arrival_airport', 'dwc_flight_tickets.flight_to', '=', 'arrival_airport.id')
            ->select(
                'dwc_flight_tickets.*',
                DB::raw("CONCAT('(', departure_airport.key, ') ', departure_airport.name) as departure_airport_name"),
                DB::raw("CONCAT('(', arrival_airport.key, ') ', arrival_airport.name) as arrival_airport_name")
            );
        if ($request->searchText != '') {

            $query->where(function ($query) use ($request) {
                $search = '%' . $request->searchText . '%';
                $query->where('dwc_flight_tickets.arrival_date', 'like', $search)
                    ->orWhere('dwc_flight_tickets.arrival_time', 'like', $search)
                    ->orWhere('dwc_flight_tickets.ticket_number', 'like', $search)
                    ->orWhereHas('guests', function ($subQuery) use ($search) {
                        $subQuery->where('passport_number', 'like', $search)
                            ->orWhereRaw("CONCAT(salutation, ' ', first_name, ' ', last_name) LIKE ?", [$search]);
                    })
                    ->orWhereHas('guests.hotelReservations', function ($subQuery) use ($search) {
                        $subQuery->whereHas('hotel', function ($hotelQuery) use ($search) {
                            $hotelQuery->where('name', 'like', $search);
                        })->orWhere('others', 'like', $search);
                    })
                    ->orWhereHas('departure', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', $search)->orWhere('key', 'like', $search);
                    })
                    ->orWhereHas('arrival', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', $search)->orWhere('key', 'like', $search);
                    });
            });
        }

        if ($startDate && $endDate) {
            $query->whereBetween('dwc_flight_tickets.arrival_date', [$startDate, $endDate]);
        }

        if ($startTime && $endTime) {
            $query->whereBetween('dwc_flight_tickets.arrival_time', [$startTime, $endTime]);
        }
        if ($AirportId != 0 && $AirportId != null && $AirportId != 'all') {
            $query->where('flight_to', '=', $AirportId);
        }
        $query->whereIn('flight_to', [1517, 1515, 4822, 1520, 1518, 1519, 3708]);
        return $query;
    }





    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $dataTable = $this->setBuilder('dwc-arrival-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["dwc-arrival-table"].buttons().container()
                     .appendTo( "#table-actions")
                 }',
                'fnDrawCallback' => 'function( oSettings ) {
                   $(".select-picker").selectpicker();
                 }',
            ]);

        if (canDataTableExport()) {
            $dataTable->buttons(Button::make(['extend' => 'excel', 'text' => '<i class="fa fa-file-export"></i> ' . trans('app.exportExcel')]));
        }

        return $dataTable;
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $data = [
            'check' => [
                'title' => '<input type="checkbox" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                'exportable' => false,
                'orderable' => false,
                'searchable' => false
            ],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false, 'title' => '#'],
            __('app.id') => ['data' => 'id', 'name' => 'id', 'title' => __('app.id'), 'visible' => false],
            __('modules.guests.horse') => ['data' => 'horse', 'name' => 'horse', 'title' => __('modules.guests.horse')],
            __('app.Guesttype') => ['data' => 'guesttype', 'name' => 'guesttype', 'visible' => false, 'title' => __('app.Guesttype')],
            __('modules.guests.amendment_date') => ['data' => 'amendment_date', 'visible' => false, 'name' => 'amendment_date', 'title' => __('modules.guests.amendment_date')],
            __('app.name') => ['data' => 'full_name', 'name' => 'full_name', 'visible' => true, 'title' => __('app.name')],
            __('app.company') => ['data' => 'company', 'name' => 'company', 'visible' => false, 'title' => __('app.company')],
            __('app.address') => ['data' => 'address_1', 'name' => 'address_1', 'visible' => false, 'title' => __('app.address')],
            __('app.country') => ['data' => 'country', 'name' => 'country', 'visible' => false, 'title' => __('app.country')],
            __('modules.guests.state') => ['data' => 'state', 'name' => 'state', 'visible' => false, 'title' => __('modules.guests.state')],
            __('app.mobile') => ['data' => 'mobile_with_code', 'name' => 'mobile_with_code', 'visible' => false, 'title' => __('app.mobile')],
            __('app.email') => ['data' => 'email', 'name' => 'email', 'visible' => false, 'title' => __('app.email')],
            __('app.nationality') => ['data' => 'nationality', 'name' => 'nationality', 'visible' => false, 'title' => __('app.nationality')],
            __('modules.guests.visa_required') => ['data' => 'visa_required', 'name' => 'visa_required', 'visible' => false, 'title' => __('modules.guests.visa_required')],
            __('modules.guests.passport_number') => ['data' => 'passport_number', 'name' => 'passport_number', 'visible' => true, 'title' => __('modules.guests.passport_number')],
            __('app.flightNumber') => ['data' => 'flight_no', 'name' => 'flight_no', 'visible' => false, 'title' => __('app.flightNumber')],
            __('app.departure_date') => ['data' => 'departure_date', 'name' => 'departure_date', 'visible' => false, 'title' => __('app.departure_date')],
            __('app.departure_time') => ['data' => 'departure_time', 'name' => 'departure_time', 'visible' => false, 'title' => __('app.departure_time')],
            __('app.arrival_date') => ['data' => 'arrival_date', 'name' => 'arrival_date', 'title' => __('app.arrival_date')],
            __('app.arrival_time') => ['data' => 'arrival_time', 'name' => 'arrival_time', 'title' => __('app.arrival_time')],
            __('app.flight_from') => ['data' => 'departure_airport_name', 'name' => 'departure_airport_name', 'title' => __('app.flight_from')],
            __('app.flight_to') => ['data' => 'arrival_airport_name', 'name' => 'arrival_airport_name', 'title' => __('app.flight_to')],
            __('app.ArrivedAirport') => ['data' => 'arrival_airport_name', 'name' => 'arrival_airport_name', 'title' => __('app.ArrivedAirport')],
            __('app.flightClass') => ['data' => 'flight_class', 'name' => 'flight_class', 'visible' => false, 'title' => __('app.flightClass')],
            __('app.locator') => ['data' => 'locator', 'name' => 'locator', 'visible' => false, 'title' => __('app.locator')],
            __('app.ticket_number') => ['data' => 'ticket_number', 'name' => 'ticket_number', 'title' => __('app.ticket_number')],
            __('app.FlightTcketnote') => ['data' => 'note_1', 'name' => 'note_1', 'visible' => false, 'title' => __('app.FlightTcketnote')],
            __('app.Hotel') => ['data' => 'hotel', 'name' => 'hotel', 'title' => __('app.Hotel')],
            __('app.roomType') => ['data' => 'roomtype', 'name' => 'roomtype', 'visible' => false, 'title' => __('app.roomType')],
            __('app.checkin_date') => ['data' => 'checkin_date', 'name' => 'checkin_date', 'visible' => false, 'title' => __('app.checkin_date')],
            __('app.checkout_date') => ['data' => 'checkout_date', 'name' => 'checkout_date', 'visible' => false, 'title' => __('app.checkout_date')],
            __('app.noOfNights') => ['data' => 'no_of_nights', 'name' => 'no_of_nights', 'visible' => false, 'title' => __('app.noOfNights')],
            __('app.billingCode') => ['data' => 'billing_code', 'name' => 'billing_code', 'visible' => false, 'title' => __('app.billingCode')],
            __('app.ConfirmationNo') => ['data' => 'confirmation_no', 'name' => 'confirmation_no', 'visible' => false, 'title' => __('app.ConfirmationNo')],
            __('app.sharingWith') => ['data' => 'sharing_with', 'name' => 'sharing_with', 'visible' => false, 'title' => __('app.sharingWith')],
            __('app.HotelReservationNote') => ['data' => 'note_2', 'name' => 'note_2', 'visible' => false, 'title' => __('app.HotelReservationNote')],
        ];

        $action = [
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20')
        ];

        return array_merge($data, $action);
    }
}
