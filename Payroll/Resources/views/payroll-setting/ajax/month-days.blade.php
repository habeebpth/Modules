<div class="col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-4">
    @method('POST')
    <div class="row">
        @foreach ($monthDays as $monthDay)
            <div class="col-md-4 mb-3">
                <label class="f-14 font-weight-bold d-block">{{ ucfirst($monthDay->month) }}</label>
                <input type="text" class="form-control height-35 f-14 w-auto"
                    name="days_in_month[]" value="{{ $monthDay->days_in_month }}"
                    data-month="{{ $monthDay->month }}"
                    style="width: 80px;">
            </div>
        @endforeach
    </div>
</div>

<!-- Buttons Start -->
<div class="w-100 border-top-grey">
    <x-setting-form-actions>
        <x-forms.button-primary id="update-month-days-form" class="mr-3" icon="check">@lang('app.update')
        </x-forms.button-primary>
    </x-setting-form-actions>
</div>
<!-- Buttons End -->

<script>
    $('#update-month-days-form').click(function() {
        let data = [];

        // Collect all month and days values
        $('input[name="days_in_month[]"]').each(function() {
            data.push({
                month: $(this).data('month'),
                days_in_month: $(this).val()
            });
        });

        $.easyAjax({
            url: "{{ route('month_days.update') }}", // Change this to the correct route
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                month_days: data
            },
            success: function(response) {
                if (response.status === "success") {
                    window.location.reload();
                }
            }
        });
    });
</script>
