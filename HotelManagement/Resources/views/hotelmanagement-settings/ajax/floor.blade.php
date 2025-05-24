<div class="table-responsive p-20">
    <x-table class="table-bordered">
        <x-slot name="thead">
            <th>#</th>
            <th>@lang('app.number')</th>
            <th>@lang('app.name')</th>
            <th>@lang('app.status')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>

        @forelse($hmfloor as $key => $floor)
            <tr class="row{{ $floor->id }}">
                <td>{{ ($key+1) }}</td>
                <td>{{ $floor->floor_number }}</td>
                <td>{{ $floor->floor_name }}</td>
                <td>
                    <div class="custom-control custom-switch">
                        <input type="checkbox"
                               class="cursor-pointer custom-control-input change-floor-status"
                               id="floor-{{ $floor->id }}"
                               data-floor-id="{{ $floor->id }}"
                               @if ($floor->disable == 'y') checked @endif>
                        <label class="custom-control-label cursor-pointer" for="floor-{{ $floor->id }}"></label>
                    </div>
                </td>
                <td class="text-right">
                    <div class="task_view">
                        <a href="javascript:;" data-floor-id="{{ $floor->id }}"
                            class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle edit-floor">
                            <i class="fa fa-edit icons mr-2"></i> @lang('app.edit')
                        </a>
                    </div>
                    <div class="task_view mt-1 mt-lg-0 mt-md-0">
                        <a href="javascript:;" data-floor-id="{{ $floor->id }}"
                            class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle delete-floor">
                            <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8">
                    <x-cards.no-record icon="list" :message="__('messages.noFloorAdded')" />
                </td>
            </tr>
        @endforelse
    </x-table>
</div>
<script>
    $(document).on('change', '.change-floor-status', function () {
        var floorId = $(this).data('floor-id');
        var status = $(this).is(':checked') ? 'y' : 'n'; // 'n' for enabled, 'y' for disabled

        $.easyAjax({
            url: "{{ route('hotelmanagement.floor.update-status') }}", // Replace with your route
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                floor_id: floorId,
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

