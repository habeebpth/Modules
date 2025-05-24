<div class="table-responsive p-20">
    <x-table class="table-bordered">
        <x-slot name="thead">
            <th>#</th>
            <th>@lang('hotelmanagement::app.hotelManagement.facilityName')</th>
            <th>@lang('hotelmanagement::app.hotelManagement.description')</th>
            <th>@lang('app.status')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>

        @forelse($facilities as $key => $facility)
            <tr class="row{{ $facility->id }}">
                <td>{{ ($key+1) }}</td>
                <td>{{ $facility->facility_name }}</td>
                <td>{{ $facility->description }}</td>
                <td>
                    <div class="custom-control custom-switch">
                        <input type="checkbox"
                               class="cursor-pointer custom-control-input change-facilities-status"
                               id="facilities-{{ $facility->id }}"
                               data-facilities-id="{{ $facility->id }}"
                               @if ($facility->disable == 'y') checked @endif>
                        <label class="custom-control-label cursor-pointer" for="facilities-{{ $facility->id }}"></label>
                    </div>
                </td>
                <td class="text-right">
                    <div class="task_view">
                        <a href="javascript:;" data-facilities-id="{{ $facility->id }}"
                            class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle edit-facilities">
                            <i class="fa fa-edit icons mr-2"></i> @lang('app.edit')
                        </a>
                    </div>
                    <div class="task_view mt-1 mt-lg-0 mt-md-0">
                        <a href="javascript:;" data-facilities-id="{{ $facility->id }}"
                            class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle delete-facilities">
                            <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8">
                    <x-cards.no-record icon="list" :message="__('messages.noFacilitiesAdded')" />
                </td>
            </tr>
        @endforelse
    </x-table>
</div>
<script>
    $(document).on('change', '.change-facilities-status', function () {
        var facilitiesId = $(this).data('facilities-id');
        var status = $(this).is(':checked') ? 'y' : 'n'; // 'n' for enabled, 'y' for disabled

        $.easyAjax({
            url: "{{ route('hotelmanagement.facilities.update-status') }}", // Replace with your route
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                facilities_id: facilitiesId,
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

