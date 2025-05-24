<div class="row">
    <!-- CHECK-IN POINTS LIST START -->
    <div class="col-xl-12 col-lg-12 col-md-12 mb-4 mt-5">
        <x-forms.button-primary icon="plus" id="add-checkin-point" class="mb-3">
            @lang('app.add') @lang('app.CheckinPoint')
        </x-forms.button-primary>

        <x-cards.data :title="__('app.CheckinPoints')"
                      otherClasses="border-0 p-0 d-flex justify-content-between align-items-center table-responsive-sm">
            <x-table class="border-0 pb-3 admin-dash-table table-hover">
                <x-slot name="thead">
                    <th class="pl-20">#</th>
                    <th>@lang('app.name')</th>
                    <th>@lang('app.code')</th>
                    <th>@lang('app.number')</th>
                    <th>@lang('app.description')</th>
                    <th class="text-right pr-20">@lang('app.action')</th>
                </x-slot>

                @forelse($checkinpoints as $key => $point)
                    <tr id="row-{{ $point->id }}">
                        <td class="pl-20">{{ $key + 1 }}</td>
                        <td>{{ $point->name }}</td>
                        <td>{{ $point->code }}</td>
                        <td>{{ $point->number }}</td>
                        <td>{{ $point->description }}</td>
                        <td class="text-right pr-20">
                            <div class="task_view">
                                <div class="dropdown">
                                    <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle"
                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-options-vertical icons"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        {{-- <a class="dropdown-item openRightModal" href="{{ route('event-checkin-points.show', $point->id) }}">
                                            <i class="fa fa-eye mr-2"></i> @lang('app.view')
                                        </a> --}}
                                        <a class="dropdown-item edit-checkin-point" href="javascript:;" data-id="{{ $point->id }}">
                                            <i class="fa fa-edit mr-2"></i> @lang('app.edit')
                                        </a>
                                        <a class="dropdown-item delete-checkin-point" href="javascript:;" data-id="{{ $point->id }}">
                                            <i class="fa fa-trash mr-2"></i> @lang('app.delete')
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-cards.no-record-found-list colspan="6" />
                @endforelse
            </x-table>
        </x-cards.data>
    </div>
    <!-- CHECK-IN POINTS LIST END -->
</div>

<script>
    $('body').on('click', '.delete-checkin-point', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.recoverRecord')",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "@lang('messages.confirmDelete')",
            cancelButtonText: "@lang('app.cancel')",
            customClass: {
                confirmButton: 'btn btn-primary mr-3',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                let url = "{{ route('event-checkin-points.destroy', ':id') }}".replace(':id', id);
                $.easyAjax({
                    type: 'POST',
                    url: url,
                    blockUI: true,
                    data: {
                        '_token': "{{ csrf_token() }}",
                        '_method': 'DELETE'
                    },
                    success: function (response) {
                        if (response.status == "success") {
                            window.location.reload();
                        }
                    }
                });
            }
        });
    });

    $('#add-checkin-point').click(function () {
        let url = "{{ route('event-checkin-points.create').'?evntid='.$event->id }}";
        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });

    $('.edit-checkin-point').click(function () {
        let id = $(this).data('id');
        let url = "{{ route('event-checkin-points.edit', ':id') }}".replace(':id', id);
        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });
</script>
