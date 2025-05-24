<div class="table-responsive p-20">
    <x-table class="table-bordered">
        <x-slot name="thead">
            <th>#</th>
            <th>@lang('app.name')</th>
            <th>@lang('travels::app.travels.noOfSeats')</th>
            <th>@lang('app.status')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>

        @forelse($vehicletypes as $key => $vehicletype)
            <tr class="row{{$vehicletype->id }}">
                <td>{{ ($key+1) }}</td>
                <td>{{$vehicletype->name }}</td>
                <td>{{$vehicletype->no_of_seats }}</td>
                <td>
                    <div class="custom-control custom-switch">
                        <input type="checkbox"
                               class="cursor-pointer custom-control-input change-vehicletype-status"
                               id="vehicletype-{{$vehicletype->id }}"
                               data-vehicletype-id="{{$vehicletype->id }}"
                               @if ($vehicletype->disable == 'y') checked @endif>
                        <label class="custom-control-label cursor-pointer" for="vehicletype-{{$vehicletype->id }}"></label>
                    </div>
                </td>
                <td class="text-right">
                    <div class="task_view">
                        <a href="javascript:;" data-vehicletype-id="{{$vehicletype->id }}"
                            class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle edit-vehicletype">
                            <i class="fa fa-edit icons mr-2"></i> @lang('app.edit')
                        </a>
                    </div>
                    <div class="task_view mt-1 mt-lg-0 mt-md-0">
                        <a href="javascript:;" data-vehicletype-id="{{$vehicletype->id }}"
                            class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle delete-vehicletype">
                            <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8">
                    <x-cards.no-record icon="list" :message="__('messages.noVehicleTypeAdded')" />
                </td>
            </tr>
        @endforelse
    </x-table>
</div>
<script>
    $(document).on('change', '.change-vehicletype-status', function () {
        var vehicletypeId = $(this).data('vehicletype-id');
        var status = $(this).is(':checked') ? 'y' : 'n'; // 'n' for enabled, 'y' for disabled

        $.easyAjax({
            url: "{{ route('vehicletype-settings.update-status') }}", // Replace with your route
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                vehicletype_id: vehicletypeId,
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

