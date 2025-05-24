<div class="table-responsive p-20">
    <x-table class="table-bordered">
        <x-slot name="thead">
            <th>#</th>
            <th>@lang('app.name')</th>
            <th>@lang('travels::app.travels.vehicletype')</th>
            <th>@lang('travels::app.travels.vehicleNumber')</th>
            <th>@lang('travels::app.travels.vehicleCode')</th>
            <th>@lang('travels::app.travels.noOfSeats')</th>
            <th>@lang('app.country')</th>
            <th>@lang('app.status')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>

        @forelse($vehicles as $key => $vehicle)
            <tr class="row{{$vehicle->id }}">
                <td>{{ ($key+1) }}</td>
                <td>{{$vehicle->name }}</td>
                <td>{{ $vehicle->vehicletype->name ?? 'N/A' }}</td>
                <td>{{$vehicle->vehicle_number }}</td>
                <td>{{$vehicle->vehicle_code }}</td>
                <td>{{$vehicle->no_of_seats }}</td>
                <td>{{$vehicle->country_id }}</td>
                <td>
                    <div class="custom-control custom-switch">
                        <input type="checkbox"
                               class="cursor-pointer custom-control-input change-vehicle-status"
                               id="vehicle-{{$vehicle->id }}"
                               data-vehicle-id="{{$vehicle->id }}"
                               @if ($vehicle->disable == 'y') checked @endif>
                        <label class="custom-control-label cursor-pointer" for="vehicle-{{$vehicle->id }}"></label>
                    </div>
                </td>
                <td class="text-right">
                    <div class="task_view">
                        <a href="javascript:;" data-vehicle-id="{{$vehicle->id }}"
                            class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle edit-vehicle">
                            <i class="fa fa-edit icons mr-2"></i> @lang('app.edit')
                        </a>
                    </div>
                    <div class="task_view mt-1 mt-lg-0 mt-md-0">
                        <a href="javascript:;" data-vehicle-id="{{$vehicle->id }}"
                            class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle delete-vehicle">
                            <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10">
                    <x-cards.no-record icon="list" :message="__('messages.noVehicleAdded')" />
                </td>
            </tr>
        @endforelse
    </x-table>
</div>
<script>
    $(document).on('change', '.change-vehicle-status', function () {
        var vehicleId = $(this).data('vehicle-id');
        var status = $(this).is(':checked') ? 'y' : 'n'; // 'n' for enabled, 'y' for disabled

        $.easyAjax({
            url: "{{ route('vehicle-settings.update-status') }}", // Replace with your route
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                vehicle_id: vehicleId,
                disable: status
            },
            success: function (response) {
                if (response.status === "success") {
                    // Optional: Display a success message or perform any additional action
                }
            }
        });
    });
</script>

