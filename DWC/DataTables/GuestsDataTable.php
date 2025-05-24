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
use Modules\DWC\Entities\DwcGuest;
use Modules\DWC\Entities\DwcHorse;

class GuestsDataTable extends BaseDataTable
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $datatables = datatables()->eloquent($query);
        $datatables->addColumn('check', function ($row) {
            if ($row->id != user()->id) {
                return $this->checkBox($row);
            }
            return '--';
        });
        $datatables->addColumn('nationality', function ($row) {
            return $row->guestnationality ? $row->guestnationality->name : null;
        });
        $datatables->addColumn('country', function ($row) {
            return $row->guestcountry ? $row->guestcountry->name : null;
        });

        $datatables->addColumn('action', function ($row) {
            $action = '<div class="task_view">

                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

            $action .= '<a href="' . route('guests.show', [$row->id]) . '" class="dropdown-item"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';

            if (user()->permission('add_dwc') == 'all') {
                $action .= '<a class="dropdown-item openRightModal" href="' . route('guests.edit', [$row->id]) . '">
                                <i class="fa fa-edit mr-2"></i>
                                ' . trans('app.edit') . '
                            </a>';
            }
            if (user()->permission('delete_dwc') == 'all') {
                $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-user-id="' . $row->id . '">
                                <i class="fa fa-trash mr-2"></i>
                                ' . trans('app.delete') . '
                            </a>';
            }
            $action .= '</div>
                    </div>
                </div>';

            return $action;
        });
        $datatables->editColumn('created_at', fn ($row) => Carbon::parse($row->created_at)->translatedFormat($this->company->date_format));
        $datatables->editColumn('flight_1_from', function ($row) {
            $flight_1_from = $row->flightTickets->map(function ($tickets) {
                return $tickets->departure ? $tickets->departure->name : null;
            })->filter()->implode(', ');

            return $flight_1_from ?: 'Not Selected';
        });
        $datatables->editColumn('hotel', function ($row) {
            $hotels = $row->hotelReservations->map(function ($reservation) {
                return $reservation->hotel ? $reservation->hotel->name : null;
            })->filter()->implode(', ');

            return $hotels ?: 'Not Selected';
        });
        $datatables->editColumn('race', function ($row) {
            if (!$row->horse) {
                return 'Not Selected';
            }
            $races = $row->horse->races->pluck('name')->filter()->implode(', ');
            return $races ?: 'Not Selected';
        });
        $datatables->editColumn('billing_code', function ($row) {
            $billingcodes = $row->hotelReservations->map(function ($reservation) {
                return $reservation->billingcode ? $reservation->billingcode->name : null;
            })->filter()->implode(', ');

            return $billingcodes ?: 'Not Selected';
        });
        $datatables->editColumn('roomtype', function ($row) {
            if (!$row->hotelReservations || $row->hotelReservations->isEmpty()) {
                return 'Not Selected';
            }
            $roomtypes = $row->hotelReservations->pluck('room_type')->filter()->implode(', ');
            return $roomtypes ?: 'Not Selected';
        });
        $datatables->editColumn('ticket_1_flight_no', function ($row) {
            if (!$row->flightTickets || $row->flightTickets->isEmpty()) {
                return 'Not Selected';
            }
            return optional($row->flightTickets->first())->flight_no ?: 'Not Selected';
        });
        $datatables->editColumn('departure_1_date', function ($row) {
            if (!$row->flightTickets || $row->flightTickets->isEmpty()) {
                return 'Not Selected';
            }
            return optional($row->flightTickets->first())->departure_date ?: 'Not Selected';
        });
        $datatables->editColumn('departure_1_time', function ($row) {
            if (!$row->flightTickets || $row->flightTickets->isEmpty()) {
                return 'Not Selected';
            }
            return optional($row->flightTickets->first())->departure_time ?: 'Not Selected';
        });
        $datatables->editColumn('arrival_1_date', function ($row) {
            if (!$row->flightTickets || $row->flightTickets->isEmpty()) {
                return 'Not Selected';
            }
            return optional($row->flightTickets->first())->arrival_date ?: 'Not Selected';
        });
        $datatables->editColumn('arrival_1_time', function ($row) {
            if (!$row->flightTickets || $row->flightTickets->isEmpty()) {
                return 'Not Selected';
            }
            return optional($row->flightTickets->first())->arrival_time ?: 'Not Selected';
        });
        $datatables->editColumn('flight_1_from', function ($row) {
            return ($row->flightTickets && $row->flightTickets->count() >= 1)
                ? optional($row->flightTickets->get(0)->departure)->name
                : 'Not Selected';
        });
        $datatables->editColumn('flight_1_to', function ($row) {
            return ($row->flightTickets && $row->flightTickets->count() >= 1)
                ? optional($row->flightTickets->get(0)->arrival)->name
                : 'Not Selected';
        });
        $datatables->editColumn('locator_1', function ($row) {
            if (!$row->flightTickets || $row->flightTickets->isEmpty()) {
                return 'Not Selected';
            }
            return optional($row->flightTickets->first())->locator ?: 'Not Selected';
        });
        $datatables->editColumn('ticket_number_1', function ($row) {
            if (!$row->flightTickets || $row->flightTickets->isEmpty()) {
                return 'Not Selected';
            }
            return optional($row->flightTickets->first())->ticket_number ?: 'Not Selected';
        });
        $datatables->editColumn('ticket_note_1', function ($row) {
            if (!$row->flightTickets || $row->flightTickets->isEmpty()) {
                return 'Not Selected';
            }
            return optional($row->flightTickets->first())->note_1 ?: 'Not Selected';
        });
        $datatables->editColumn('flight_1_class', function ($row) {
            if (!$row->flightTickets || $row->flightTickets->isEmpty()) {
                return 'Not Selected';
            }
            return optional($row->flightTickets->first())->flight_class ?: 'Not Selected';
        });

        $datatables->editColumn('ticket_2_flight_no', function ($row) {
            return ($row->flightTickets && $row->flightTickets->count() >= 2)
                ? optional($row->flightTickets->get(1))->flight_no
                : 'Not Selected';
        });

        $datatables->editColumn('departure_2_date', function ($row) {
            return ($row->flightTickets && $row->flightTickets->count() >= 2)
                ? optional($row->flightTickets->get(1))->departure_date
                : 'Not Selected';
        });

        $datatables->editColumn('departure_2_time', function ($row) {
            return ($row->flightTickets && $row->flightTickets->count() >= 2)
                ? optional($row->flightTickets->get(1))->departure_time
                : 'Not Selected';
        });

        $datatables->editColumn('arrival_2_date', function ($row) {
            return ($row->flightTickets && $row->flightTickets->count() >= 2)
                ? optional($row->flightTickets->get(1))->arrival_date
                : 'Not Selected';
        });

        $datatables->editColumn('arrival_2_time', function ($row) {
            return ($row->flightTickets && $row->flightTickets->count() >= 2)
                ? optional($row->flightTickets->get(1))->arrival_time
                : 'Not Selected';
        });
        $datatables->editColumn('flight_2_from', function ($row) {
            return ($row->flightTickets && $row->flightTickets->count() >= 2)
                ? optional($row->flightTickets->get(1)->departure)->name
                : 'Not Selected';
        });
        $datatables->editColumn('flight_2_to', function ($row) {
            return ($row->flightTickets && $row->flightTickets->count() >= 2)
                ? optional($row->flightTickets->get(1)->arrival)->name
                : 'Not Selected';
        });
        $datatables->editColumn('locator_2', function ($row) {
            return ($row->flightTickets && $row->flightTickets->count() >= 2)
                ? optional($row->flightTickets->get(1))->locator
                : 'Not Selected';
        });

        $datatables->editColumn('ticket_number_2', function ($row) {
            return ($row->flightTickets && $row->flightTickets->count() >= 2)
                ? optional($row->flightTickets->get(1))->ticket_number
                : 'Not Selected';
        });

        $datatables->editColumn('ticket_note_2', function ($row) {
            return ($row->flightTickets && $row->flightTickets->count() >= 2)
                ? optional($row->flightTickets->get(1))->note_1
                : 'Not Selected';
        });

        $datatables->editColumn('flight_2_class', function ($row) {
            return ($row->flightTickets && $row->flightTickets->count() >= 2)
                ? optional($row->flightTickets->get(1))->flight_class
                : 'Not Selected';
        });

        $datatables->editColumn('checkin_date', function ($row) {
            if (!$row->hotelReservations || $row->hotelReservations->isEmpty()) {
                return 'Not Selected';
            }
            // Convert each check-in date to dd/mm/yyyy format
            $checkindate = $row->hotelReservations->pluck('checkin_date')
                ->filter()
                ->map(function ($date) {
                    return Carbon::parse($date)->format('d/m/Y');
                })
                ->implode(', ');
            return $checkindate ?: 'Not Selected';
        });

        $datatables->editColumn('no_of_nights', function ($row) {
            if (!$row->hotelReservations || $row->hotelReservations->isEmpty()) {
                return 'Not Selected';
            }
            $noofnights = $row->hotelReservations->pluck('no_of_nights')->filter()->implode(', ');
            return $noofnights ?: 'Not Selected';
        });
        $datatables->editColumn('checkout_date', function ($row) {
            if (!$row->hotelReservations || $row->hotelReservations->isEmpty()) {
                return 'Not Selected';
            }
            $checkoutdate = $row->hotelReservations->pluck('checkout_date')
                ->filter()
                ->map(function ($date) {
                    return Carbon::parse($date)->format('d/m/Y');
                })
                ->implode(', ');
            return $checkoutdate ?: 'Not Selected';
        });
        $datatables->editColumn('confirmation_no', function ($row) {
            if (!$row->hotelReservations || $row->hotelReservations->isEmpty()) {
                return 'Not Selected';
            }
            $confirmation_nos = $row->hotelReservations->pluck('confirmation_no')->filter()->implode(', ');
            return $confirmation_nos ?: 'Not Selected';
        });
        $datatables->editColumn('sharing_with', function ($row) {
            if (!$row->hotelReservations || $row->hotelReservations->isEmpty()) {
                return 'Not Selected';
            }
            $sharing_withs = $row->hotelReservations->pluck('sharing_with')->filter()->implode(', ');
            return $sharing_withs ?: 'Not Selected';
        });
        $datatables->editColumn('note_2', function ($row) {
            if (!$row->hotelReservations || $row->hotelReservations->isEmpty()) {
                return 'Not Selected';
            }
            $note_2s = $row->hotelReservations->pluck('note_2')->filter()->implode(', ');
            return $note_2s ?: 'Not Selected';
        });

        $datatables->editColumn('full_name', function ($row) {
            $salutation = $row->salutation ? ucfirst(strtolower($row->salutation)) : '';
            $fullName = trim("{$salutation} {$row->first_name} {$row->last_name}");
            return $fullName ?: 'Not Selected';
        });
        $datatables->editColumn('amendment_date', function ($row) {
            return $row->amendment_date ? Carbon::parse($row->amendment_date)->format('d/m/Y') : 'Not Selected';
        });
        $datatables->editColumn('guesttype', fn ($row) => $row->guesttype ? $row->guesttype['name'] : 'Not Selected');
        $datatables->editColumn('horse', fn ($row) => $row->horse ? $row->horse['name'] : 'Not Selected');
        $datatables->editColumn('visa_required', fn ($row) => $row->visa_required == 1 ? 'Yes' : 'No');

        $datatables->addIndexColumn();
        $datatables->setRowId(fn ($row) => 'row-' . $row->id);

        $datatables->rawColumns(['action', 'check']);

        return $datatables;
    }


    /**
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(DwcGuest $model)
    {
        $request = $this->request();
        $GuesttypeId = $request->GuesttypeId;
        $countryId = $request->countryId;
        $raceId = $request->raceId;
        $searchText = $request->searchText;

        $query = $model->newQuery();

        if ($raceId !== '0' && $raceId !== null && $raceId !== 'all') {
            $query->whereHas('horse', function ($q) use ($raceId) {
                $q->whereHas('races', function ($raceQuery) use ($raceId) {
                    $raceQuery->where('dwc_race_horse.dwc_races_id', $raceId);
                });
            });
        }

        if (!empty($searchText)) {
            $query->where(function ($q) use ($searchText) {
                $q->where('dwc_guests.company', 'like', "%$searchText%")
                    ->orWhere('dwc_guests.passport_number', 'like', "%$searchText%")
                    ->orWhere('dwc_guests.id', 'like', "%$searchText%")
                    ->orWhere('dwc_guests.first_name', 'like', "%$searchText%")
                    ->orWhere('dwc_guests.last_name', 'like', "%$searchText%")
                    ->orWhere('dwc_guests.mobile', 'like', "%$searchText%")
                    ->orWhere('dwc_guests.email', 'like', "%$searchText%")
                    ->orWhereHas('guesttype', function ($q) use ($searchText) {
                        $q->where('name', 'like', "%$searchText%");
                    })
                    ->orWhereHas('horse', function ($q) use ($searchText) {
                        $q->where('name', 'like', "%$searchText%");
                    })
                    ->orWhereHas('guestcountry', function ($q) use ($searchText) {
                        $q->where('name', 'like', "%$searchText%");
                    })
                    ->orWhereHas('hotelReservations.hotel', function ($q) use ($searchText) {
                        $q->where('name', 'like', "%$searchText%");
                    });
            });
        }

        if ($GuesttypeId !== '0' && $GuesttypeId !== null && $GuesttypeId !== 'all') {
            $query->where('guest_type', $GuesttypeId);
        }

        if ($countryId !== '0' && $countryId !== null && $countryId !== 'all') {
            $query->where('country', $countryId);
        }

        return $query;
    }


    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $dataTable = $this->setBuilder('guests-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["guests-table"].buttons().container()
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
            __('app.guestid') => ['data' => 'id', 'name' => 'id', 'title' => __('app.guestid')],
            __('app.Guesttype') => ['data' => 'guesttype', 'name' => 'guesttype', 'title' => __('app.Guesttype')],
            __('app.name') => ['data' => 'full_name', 'name' => 'full_name', 'title' => __('app.name')],
            __('modules.guests.horse') => ['data' => 'horse', 'name' => 'horse', 'title' => __('modules.guests.horse')],
            __('app.Race') => ['data' => 'race', 'name' => 'race', 'title' => __('app.Race')],
            __('modules.guests.amendment_date') => ['data' => 'amendment_date', 'visible' => false, 'name' => 'amendment_date', 'title' => __('modules.guests.amendment_date')],
            __('app.address') => ['data' => 'address_1', 'name' => 'address_1', 'visible' => false, 'title' => __('app.address')],
            __('modules.guests.company') => ['data' => 'company', 'name' => 'company', 'title' => __('modules.guests.company')],
            __('modules.guests.country') => ['data' => 'country', 'name' => 'country', 'title' => __('modules.guests.country')],
            __('modules.guests.state') => ['data' => 'state', 'name' => 'state', 'visible' => false, 'title' => __('modules.guests.state')],
            __('app.nationality') => ['data' => 'nationality', 'name' => 'nationality', 'visible' => false, 'title' => __('app.nationality')],
            __('modules.guests.mobile') => ['data' => 'mobile', 'name' => 'mobile', 'title' => __('modules.guests.mobile')],
            __('modules.guests.email') => ['data' => 'email', 'name' => 'email', 'title' => __('modules.guests.email')],
            __('modules.guests.visa_required') => ['data' => 'visa_required', 'name' => 'visa_required', 'title' => __('modules.guests.visa_required')],
            __('modules.guests.passport_number') => ['data' => 'passport_number', 'name' => 'passport_number', 'title' => __('modules.guests.passport_number')],
            __('app.ticket_1_flight_no') => ['data' => 'ticket_1_flight_no', 'name' => 'ticket_1_flight_no', 'visible' => false, 'title' => __('app.ticket_1_flight_no')],
            __('app.departure_1_date') => ['data' => 'departure_1_date', 'name' => 'departure_1_date', 'visible' => false, 'title' => __('app.departure_1_date')],
            __('app.departure_1_time') => ['data' => 'departure_1_time', 'name' => 'departure_1_time', 'visible' => false, 'title' => __('app.departure_1_time')],
            __('app.arrival_1_date') => ['data' => 'arrival_1_date', 'name' => 'arrival_1_date', 'visible' => false, 'title' => __('app.arrival_1_date')],
            __('app.arrival_1_time') => ['data' => 'arrival_1_time', 'name' => 'arrival_1_time', 'visible' => false, 'title' => __('app.arrival_1_time')],
            __('app.flight_1_from') => ['data' => 'flight_1_from', 'name' => 'flight_1_from', 'visible' => false, 'title' => __('app.flight_1_from')],
            __('app.flight_1_to') => ['data' => 'flight_1_to', 'name' => 'flight_1_to', 'visible' => false, 'title' => __('app.flight_1_to')],
            __('app.flight_1_class') => ['data' => 'flight_1_class', 'name' => 'flight_1_class', 'visible' => false, 'title' => __('app.flight_1_class')],
            __('app.locator_1') => ['data' => 'locator_1', 'name' => 'locator_1', 'visible' => false, 'title' => __('app.locator_1')],
            __('app.ticket_number_1') => ['data' => 'ticket_number_1', 'name' => 'ticket_number_1', 'visible' => false, 'title' => __('app.ticket_number_1')],
            __('app.FlightTcketnote') => ['data' => 'ticket_note_1', 'name' => 'ticket_note_1', 'visible' => false, 'title' => __('app.FlightTcketnote')],
            __('app.ticket_2_flight_no') => ['data' => 'ticket_2_flight_no', 'name' => 'ticket_2_flight_no', 'visible' => false, 'title' => __('app.ticket_2_flight_no')],
            __('app.departure_2_date') => ['data' => 'departure_2_date', 'name' => 'departure_2_date', 'visible' => false, 'title' => __('app.departure_2_date')],
            __('app.departure_2_time') => ['data' => 'departure_2_time', 'name' => 'departure_2_time', 'visible' => false, 'title' => __('app.departure_2_time')],
            __('app.arrival_2_date') => ['data' => 'arrival_2_date', 'name' => 'arrival_2_date', 'visible' => false, 'title' => __('app.arrival_2_date')],
            __('app.arrival_2_time') => ['data' => 'arrival_2_time', 'name' => 'arrival_2_time', 'visible' => false, 'title' => __('app.arrival_2_time')],
            __('app.flight_2_from') => ['data' => 'flight_2_from', 'name' => 'flight_2_from', 'visible' => false, 'title' => __('app.flight_2_from')],
            __('app.flight_2_to') => ['data' => 'flight_2_to', 'name' => 'flight_2_to', 'visible' => false, 'title' => __('app.flight_2_to')],
            __('app.flight_2_class') => ['data' => 'flight_2_class', 'name' => 'flight_2_class', 'visible' => false, 'title' => __('app.flight_2_class')],
            __('app.locator_2') => ['data' => 'locator_2', 'name' => 'locator_2', 'visible' => false, 'title' => __('app.locator_2')],
            __('app.ticket_number_2') => ['data' => 'ticket_number_2', 'name' => 'ticket_number_2', 'visible' => false, 'title' => __('app.ticket_number_2')],
            __('app.FlightTcketnote_2') => ['data' => 'ticket_note_2', 'name' => 'ticket_note_2', 'visible' => false, 'title' => __('app.FlightTcketnote_2')],
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
