@if (!in_array('client', user_roles()) && in_array('reward', user_modules()))
    <x-menu-item icon="gift" :text="__('app.Reward')" :addon="App::environment('demo')">
        <x-slot name="iconPath">
            <path
                d="M20 12v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-7m16 0H4m16 0H4m0-4h16v4H4v-4zM7.5 8A1.5 1.5 0 1 1 9 6.5c0 .828-.672 1.5-1.5 1.5zM16.5 8A1.5 1.5 0 1 1 18 6.5c0 .828-.672 1.5-1.5 1.5z"
                stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
        </x-slot>



        <div class="accordionItemContent pb-2">
            <x-sub-menu-item :link="route('reward-customers.index')" :text="__('app.RewardCustomers')" />
             <x-sub-menu-item :link="route('reward-transactions.index')" :text="__('app.RewardTransactions')" />
           {{-- <x-sub-menu-item :link="route('hm-rooms.index')" :text="__('app.hmRooms')" />
            <x-sub-menu-item :link="route('hm-checkin.index')" :text="__('app.hmcheckin')" /> --}}
        </div>
    </x-menu-item>
@endif
