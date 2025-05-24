<div class="table-responsive p-20">
    <x-table class="table-bordered">
        <x-slot name="thead">
            <th>#</th>
            <th>@lang('accounting::app.Accounting.accounttypes')</th>
            <th>@lang('accounting::app.Accounting.name')</th>
            <th>@lang('accounting::app.Accounting.code')</th>
            <th>@lang('accounting::app.Accounting.description')</th>
            <th>@lang('app.status')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>

        @forelse($accountcategories as $key => $categories)
            <tr class="row{{ $categories->id }}">
                <td>{{ ($key+1) }}</td>
                <td>{{ $categories->accountType->name ?? '-' }}</td>
                <td>{{ $categories->name }}</td>
                <td>{{ $categories->code }}</td>
                <td>{{ $categories->description }}</td>
                <td>
                    <div class="custom-control custom-switch">
                        <input type="checkbox"
                               class="cursor-pointer custom-control-input change-accountcategories-status"
                               id="accountcategories-{{ $categories->id }}"
                               data-accountcategories-id="{{ $categories->id }}"
                               @if ($categories->disable == 'y') checked @endif>
                        <label class="custom-control-label cursor-pointer" for="accountcategories-{{ $categories->id }}"></label>
                    </div>
                </td>
                <td class="text-right">
                    <div class="task_view">
                        <a href="javascript:;" data-accountcategories-id="{{ $categories->id }}"
                            class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle edit-accountcategories">
                            <i class="fa fa-edit icons mr-2"></i> @lang('app.edit')
                        </a>
                    </div>
                    <div class="task_view mt-1 mt-lg-0 mt-md-0">
                        <a href="javascript:;" data-accountcategories-id="{{ $categories->id }}"
                            class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle delete-accountcategories">
                            <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8">
                    <x-cards.no-record icon="list" :message="__('messages.noAccountCategoriesAdded')" />
                </td>
            </tr>
        @endforelse
    </x-table>
</div>
<script>
    $(document).on('change', '.change-accountcategories-status', function () {
        var accountcategoriesId = $(this).data('accountcategories-id');
        var status = $(this).is(':checked') ? 'y' : 'n'; // 'n' for enabled, 'y' for disabled

        $.easyAjax({
            url: "{{ route('acc-categories-settings.update-status') }}", // Replace with your route
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                accountcategories_id: accountcategoriesId,
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

