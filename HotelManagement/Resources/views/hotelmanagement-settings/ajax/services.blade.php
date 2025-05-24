<div class="table-responsive p-20">
    <x-table class="table-bordered">
        <x-slot name="thead">
            <th>#</th>
            <th>@lang('hotelmanagement::app.hotelManagement.serviceTypeName')</th>
            <th>@lang('hotelmanagement::app.hotelManagement.basePrice')</th>
            <th>@lang('hotelmanagement::app.hotelManagement.description')</th>
            <th>@lang('app.status')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>

        @forelse($services as $key => $service)
            <tr class="row{{ $service->id }}">
                <td>{{ ($key+1) }}</td>
                <td>{{ $service->service_name }}</td>
                <td>{{ $service->base_price }}</td>
                <td>{{ $service->description }}</td>
                <td>
                    <div class="custom-control custom-switch">
                        <input type="checkbox"
                               class="cursor-pointer custom-control-input change-service-status"
                               id="service-{{ $service->id }}"
                               data-service-id="{{ $service->id }}"
                               @if ($service->disable == 'y') checked @endif>
                        <label class="custom-control-label cursor-pointer" for="service-{{ $service->id }}"></label>
                    </div>
                </td>
                <td class="text-right">
                    <div class="task_view">
                        <a href="javascript:;" data-service-id="{{ $service->id }}"
                            class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle edit-service">
                            <i class="fa fa-edit icons mr-2"></i> @lang('app.edit')
                        </a>
                    </div>
                    <div class="task_view mt-1 mt-lg-0 mt-md-0">
                        <a href="javascript:;" data-service-id="{{ $service->id }}"
                            class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle delete-service">
                            <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8">
                    <x-cards.no-record icon="list" :message="__('messages.noServiceAdded')" />
                </td>
            </tr>
        @endforelse
    </x-table>
</div>
<script>
    $(document).on('change', '.change-service-status', function () {
        var serviceId = $(this).data('service-id');
        var status = $(this).is(':checked') ? 'y' : 'n'; // 'n' for enabled, 'y' for disabled

        $.easyAjax({
            url: "{{ route('hotelmanagement.services.update-status') }}", // Replace with your route
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                service_id: serviceId,
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

