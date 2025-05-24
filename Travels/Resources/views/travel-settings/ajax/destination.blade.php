<div class="table-responsive p-20">
    <x-table class="table-bordered">
        <x-slot name="thead">
            <th>#</th>
            <th>@lang('app.name')</th>
            <th>@lang('modules.stripeCustomerAddress.city')</th>
            <th>@lang('modules.stripeCustomerAddress.state')</th>
            <th>@lang('app.country')</th>
            <th>@lang('app.description')</th>
            <th>@lang('app.status')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>

        @forelse($destinations as $key => $destination)
            <tr class="row{{$destination->id }}">
                <td>{{ ($key+1) }}</td>
                <td>
                    <div class="media align-items-center mw-250">
                        <!-- Image with a link -->
                        <img src="{{ $destination->image_url ? $destination->image_url : 'https://via.placeholder.com/50' }}"
                             class="mr-2 taskEmployeeImg rounded-circle"
                             alt="{{ $destination->name }}"
                             title="{{ $destination->name }}">

                        <!-- Text content -->
                        <div class="media-body active text-truncate">
                            <h5 class="mb-0 f-12">{{ $destination->name }}</h5>
                            <p class="mb-0 f-12 text-dark-grey"> {{ $destination->country }} </p>
                        </div>
                    </div>
                </td>
                <td>{{$destination->city }}</td>
                <td>{{$destination->state }}</td>
                <td>{{$destination->country }}</td>
                <td>{{$destination->description }}</td>
                <td>
                    <div class="custom-control custom-switch">
                        <input type="checkbox"
                               class="cursor-pointer custom-control-input change-destination-status"
                               id="destination-{{$destination->id }}"
                               data-destination-id="{{$destination->id }}"
                               @if ($destination->disable == 'y') checked @endif>
                        <label class="custom-control-label cursor-pointer" for="destination-{{$destination->id }}"></label>
                    </div>
                </td>
                <td class="text-right">
                    <div class="task_view">
                        <a href="javascript:;" data-destination-id="{{$destination->id }}"
                            class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle edit-destination">
                            <i class="fa fa-edit icons mr-2"></i> @lang('app.edit')
                        </a>
                    </div>
                    <div class="task_view mt-1 mt-lg-0 mt-md-0">
                        <a href="javascript:;" data-destination-id="{{$destination->id }}"
                            class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle delete-destination">
                            <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8">
                    <x-cards.no-record icon="list" :message="__('messages.noDestinationAdded')" />
                </td>
            </tr>
        @endforelse
    </x-table>
</div>
<script>
    $(document).on('change', '.change-destination-status', function () {
        var destinationId = $(this).data('destination-id');
        var status = $(this).is(':checked') ? 'y' : 'n'; // 'n' for enabled, 'y' for disabled

        $.easyAjax({
            url: "{{ route('destination-settings.update-status') }}", // Replace with your route
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                destination_id: destinationId,
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

