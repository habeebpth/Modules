@if (user()->permission('view_events') != 'none')
<x-menu-item icon="calendar" :text="__('app.menu.events')" :addon="App::environment('demo')">
    <x-slot name="iconPath">
        <path
            d="M3 1a1 1 0 0 1 1 1v1h8V2a1 1 0 1 1 2 0v1h1a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h1V2a1 1 0 0 1 1-1zm0 4v10h14V5H3zm3 3h2v2H6V8zm4 0h2v2h-2V8zm4 0h2v2h-2V8z"/>
    </x-slot>

    <div class="accordionItemContent pb-2">
        <x-sub-menu-item :link="route('events.index')" :text="__('app.menu.events')" />
        <x-sub-menu-item :link="route('event-registration.index')" :text="__('app.menu.EventRegistration')" />
        <x-sub-menu-item :link="route('event.participation-report')" :text="__('app.menu.EventParticipantReport')" />
        <x-sub-menu-item :link="route('event.checked-in-report')" :text="__('app.menu.CheckedInParticipantsReport')" />
    </div>
</x-menu-item>

@endif
