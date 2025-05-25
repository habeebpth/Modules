@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="d-grid d-lg-flex d-md-flex action-bar">
        <div id="table-actions" class="flex-grow-1 align-items-center">
            <x-forms.link-primary :link="route('accounting.budgets.create')" class="mr-3 openRightModal float-left" icon="plus">
                @lang('Create Budget')
            </x-forms.link-primary>
        </div>
    </div>

    <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
        {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}
    </div>
</div>
@endsection
