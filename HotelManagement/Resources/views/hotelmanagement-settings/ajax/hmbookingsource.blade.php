<div class="table-responsive p-20">
    <x-table class="table-bordered">
        <x-slot name="thead">
            <th>#</th>
            <th>@lang('app.name')</th>
            <th>@lang('app.description')</th>
            <th>@lang('app.url')</th>
            <th>@lang('app.status')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>

        @forelse($hmbookingsources as $key => $hmbookingsource)
            <tr class="row{{ $hmbookingsource->id }}">
                <td>{{ ($key+1) }}</td>
                {{-- <td>{{ $hmbookingsource->name }}</td> --}}
                <td>
                    <div class="media align-items-center mw-250">
                        <!-- Image with a link -->
                        <img src="{{ $hmbookingsource->image_url ? $hmbookingsource->image_url : 'https://via.placeholder.com/50' }}"
                             class="mr-2 taskEmployeeImg rounded-circle"
                             alt="{{ $hmbookingsource->name }}"
                             title="{{ $hmbookingsource->name }}">

                        <!-- Text content -->
                        <div class="media-body active text-truncate">
                            <h5 class="mb-0 f-12">{{ $hmbookingsource->name }}</h5>
                        </div>
                    </div>
                </td>
                <td>{{$hmbookingsource->description }}</td>
                <td>{{$hmbookingsource->url }}</td>
                <td>
                    <div class="custom-control custom-switch">
                        <input type="checkbox"
                               class="cursor-pointer custom-control-input change-hmbookingsource-status"
                               id="hmbookingsource-{{ $hmbookingsource->id }}"
                               data-hmbookingsource-id="{{ $hmbookingsource->id }}"
                               @if ($hmbookingsource->disable == 'y') checked @endif>
                        <label class="custom-control-label cursor-pointer" for="hmbookingsource-{{ $hmbookingsource->id }}"></label>
                    </div>
                </td>
                <td class="text-right">
                    <div class="task_view">
                        <a href="javascript:;" data-hmbookingsource-id="{{ $hmbookingsource->id }}"
                            class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle edit-hmbookingsource">
                            <i class="fa fa-edit icons mr-2"></i> @lang('app.edit')
                        </a>
                    </div>
                    <div class="task_view mt-1 mt-lg-0 mt-md-0">
                        <a href="javascript:;" data-hmbookingsource-id="{{ $hmbookingsource->id }}"
                            class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle delete-hmbookingsource">
                            <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8">
                    <x-cards.no-record icon="list" :message="__('messages.nohmbookingsourceAdded')" />
                </td>
            </tr>
        @endforelse
    </x-table>
</div>
<script>
    $(document).on('change', '.change-hmbookingsource-status', function () {
        var hmbookingsourceId = $(this).data('hmbookingsource-id');
        var status = $(this).is(':checked') ? 'y' : 'n'; // 'n' for enabled, 'y' for disabled

        $.easyAjax({
            url: "{{ route('hmbookingsource-settings.update-status') }}", // Replace with your route
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                hmbookingsource_id: hmbookingsourceId,
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

