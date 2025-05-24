<div class="table-responsive p-20">
    <x-table class="table-bordered">
        <x-slot name="thead">
            <th>#</th>
            <th>@lang('hotelmanagement::app.hotelManagement.roomTypeName')</th>
            <th>@lang('hotelmanagement::app.hotelManagement.maxOccupancy')</th>
            <th>@lang('hotelmanagement::app.hotelManagement.basePrice')</th>
            <th>@lang('hotelmanagement::app.hotelManagement.description')</th>
            <th>@lang('app.status')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>

        @forelse($RoomTypes as $key => $RoomType)
            <tr class="row{{ $RoomType->id }}">
                <td>{{ ($key+1) }}</td>
                <td>{{ $RoomType->room_type_name }}</td>
                <td>{{ $RoomType->max_occupancy }}</td>
                <td>{{ $RoomType->base_price }}</td>
                <td>{{ $RoomType->description }}</td>
                <td>
                    <div class="custom-control custom-switch">
                        <input type="checkbox"
                               class="cursor-pointer custom-control-input change-roomtype-status"
                               id="roomtype-{{ $RoomType->id }}"
                               data-RoomType-id="{{ $RoomType->id }}"
                               @if ($RoomType->disable == 'y') checked @endif>
                        <label class="custom-control-label cursor-pointer" for="roomtype-{{ $RoomType->id }}"></label>
                    </div>
                </td>
                <td class="text-right">
                    <div class="task_view">
                        <a href="javascript:;" data-roomtype-id="{{ $RoomType->id }}"
                            class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle edit-roomtype">
                            <i class="fa fa-edit icons mr-2"></i> @lang('app.edit')
                        </a>
                    </div>
                    <div class="task_view mt-1 mt-lg-0 mt-md-0">
                        <a href="javascript:;" data-roomtype-id="{{ $RoomType->id }}"
                            class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle delete-roomtype">
                            <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8">
                    <x-cards.no-record icon="list" :message="__('messages.noRoomTypeAdded')" />
                </td>
            </tr>
        @endforelse
    </x-table>
</div>
<script>
    $(document).on('change', '.change-roomtype-status', function () {
        var roomtypeId = $(this).data('roomtype-id');
        var status = $(this).is(':checked') ? 'y' : 'n'; // 'n' for enabled, 'y' for disabled

        $.easyAjax({
            url: "{{ route('hotelmanagement.roomtypes.update-status') }}", // Replace with your route
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                roomtype_id: roomtypeId,
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

