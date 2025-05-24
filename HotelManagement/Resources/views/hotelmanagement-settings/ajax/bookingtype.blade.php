<div class="table-responsive p-20">
    <x-table class="table-bordered">
        <x-slot name="thead">
            <th>#</th>
            <th>@lang('app.name')</th>
            <th>@lang('app.status')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>

        @forelse($bookingtypes as $key => $bookingtype)
            <tr class="row{{ $bookingtype->id }}">
                <td>{{ ($key+1) }}</td>
                <td>{{ $bookingtype->name }}</td>
                <td>
                    <div class="custom-control custom-switch">
                        <input type="checkbox"
                               class="cursor-pointer custom-control-input change-bookingtype-status"
                               id="bookingtype-{{ $bookingtype->id }}"
                               data-bookingtype-id="{{ $bookingtype->id }}"
                               @if ($bookingtype->disable == 'y') checked @endif>
                        <label class="custom-control-label cursor-pointer" for="bookingtype-{{ $bookingtype->id }}"></label>
                    </div>
                </td>
                <td class="text-right">
                    <div class="task_view">
                        <a href="javascript:;" data-bookingtype-id="{{ $bookingtype->id }}"
                            class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle edit-bookingtype">
                            <i class="fa fa-edit icons mr-2"></i> @lang('app.edit')
                        </a>
                    </div>
                    <div class="task_view mt-1 mt-lg-0 mt-md-0">
                        <a href="javascript:;" data-bookingtype-id="{{ $bookingtype->id }}"
                            class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle delete-bookingtype">
                            <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8">
                    <x-cards.no-record icon="list" :message="__('messages.nobookingtypeAdded')" />
                </td>
            </tr>
        @endforelse
    </x-table>
</div>
<script>
    $(document).on('change', '.change-bookingtype-status', function () {
        var bookingtypeId = $(this).data('bookingtype-id');
        var status = $(this).is(':checked') ? 'y' : 'n'; // 'n' for enabled, 'y' for disabled

        $.easyAjax({
            url: "{{ route('hmbookingtype-settings.update-status') }}", // Replace with your route
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                bookingtype_id: bookingtypeId,
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

