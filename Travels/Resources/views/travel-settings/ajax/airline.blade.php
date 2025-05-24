<div class="table-responsive p-20">
    <x-table class="table-bordered">
        <x-slot name="thead">
            <th>#</th>
            <th>@lang('app.name')</th>
            <th>@lang('app.code')</th>
            <th>@lang('app.country')</th>
            <th>@lang('app.number')</th>
            <th>@lang('app.website')</th>
            <th>@lang('app.status')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>

        @forelse($airlines as $key => $airline)
            <tr class="row{{ $airline->id }}">
                <td>{{ ($key+1) }}</td>
                <td>
                    <div class="media align-items-center mw-250">
                        <!-- Image with a link -->
                        <img src="{{ $airline->image_url ? $airline->image_url : 'https://via.placeholder.com/50' }}"
                             class="mr-2 taskEmployeeImg rounded-circle"
                             alt="{{ $airline->name }}"
                             title="{{ $airline->name }}">

                        <!-- Text content -->
                        <div class="media-body active text-truncate">
                            <h5 class="mb-0 f-12">{{ $airline->name }}</h5>
                            <p class="mb-0 f-12 text-dark-grey"> {{ $airline->code }} </p>
                        </div>
                    </div>
                </td>
                <td>{{ $airline->code }}</td>
                <td>{{ $airline->country }}</td>
                <td>{{ $airline->contact_number }}</td>
                <td>{{ $airline->website }}</td>
                <td>
                    <div class="custom-control custom-switch">
                        <input type="checkbox"
                               class="cursor-pointer custom-control-input change-airline-status"
                               id="airline-{{ $airline->id }}"
                               data-airline-id="{{ $airline->id }}"
                               @if ($airline->disable == 'y') checked @endif>
                        <label class="custom-control-label cursor-pointer" for="airline-{{ $airline->id }}"></label>
                    </div>
                </td>
                <td class="text-right">
                    <div class="task_view">
                        <a href="javascript:;" data-airline-id="{{ $airline->id }}"
                            class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle edit-airline">
                            <i class="fa fa-edit icons mr-2"></i> @lang('app.edit')
                        </a>
                    </div>
                    <div class="task_view mt-1 mt-lg-0 mt-md-0">
                        <a href="javascript:;" data-airline-id="{{ $airline->id }}"
                            class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle delete-airline">
                            <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8">
                    <x-cards.no-record icon="list" :message="__('messages.noAirlineAdded')" />
                </td>
            </tr>
        @endforelse
    </x-table>
</div>
<script>
    $(document).on('change', '.change-airline-status', function () {
        var airlineId = $(this).data('airline-id');
        var status = $(this).is(':checked') ? 'y' : 'n'; // 'n' for enabled, 'y' for disabled

        $.easyAjax({
            url: "{{ route('travel-airline-settings.update-status') }}", // Replace with your route
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                airline_id: airlineId,
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

