@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-8">
            <x-cards.data :title="__('Accounting Settings')">
                <x-form id="accounting-settings-form">
                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.checkbox fieldId="auto_post_journals"
                                :fieldLabel="__('Auto Post Journals')"
                                fieldName="auto_post_journals"
                                :checked="$settings['auto_post_journals']" />
                        </div>
                        <div class="col-md-6">
                            <x-forms.checkbox fieldId="require_journal_reference"
                                :fieldLabel="__('Require Journal Reference')"
                                fieldName="require_journal_reference"
                                :checked="$settings['require_journal_reference']" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.checkbox fieldId="allow_future_dates"
                                :fieldLabel="__('Allow Future Dates')"
                                fieldName="allow_future_dates"
                                :checked="$settings['allow_future_dates']" />
                        </div>
                    </div>

                    <x-form-actions>
                        <x-forms.button-primary id="save-settings" class="mr-3" icon="check">
                            @lang('app.save')
                        </x-forms.button-primary>
                    </x-form-actions>
                </x-form>
            </x-cards.data>
        </div>
    </div>
</div>

<script>
$('#save-settings').click(function() {
    const url = "{{ route('accounting.settings.update') }}";

    $.easyAjax({
        url: url,
        container: '#accounting-settings-form',
        type: "PUT",
        data: $('#accounting-settings-form').serialize(),
        success: function(response) {
            // Show success message
        }
    });
});
</script>
@endsection
