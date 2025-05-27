@php  
$navigations_1 = [  
    [  
        'name' => 'Inicio',  
        'route' => route('dashboard.home'),
        'icon' => 'heroicon-s-home',  
    ],  
    [  
        'name' => 'Usuarios',  
        'route' => route('dashboard.my_account'),
        'icon' => 'heroicon-s-user',  
    ],  
    [  
        'name' => 'Ventas',  
        'route' => route('dashboard.ventas'),
        'icon' => 'heroicon-s-shopping-bag',  
    ],  
    [  
        'name' => 'Finanzas',  
        'route' => route('dashboard.finanzas'),  
        'icon' => 'heroicon-s-chart-bar',  
    ],  
];
@endphp  

<div class="w-72 flex bg-gray-700 z-40">  
    <div class="px-6 pb-4 pt-0 flex flex-col gap-y-6 overflow-y-auto grow">  
        <div class="text-white h-16 flex items-center">  
            <x-application-logo />  
        </div>  
        <nav class="flex flex-col flex-1">  
            <ul role="list" class="flex flex-col gap-y-7 flex-1">  
                <li>  
                    <ul role="list" class="space-y-1 -mx-2">  
                        @foreach ($navigations_1 as $item)  
                            <li>  
                                <a href="{{ $item['route'] }}"  
                                   class="text-gray-300 hover:bg-gray-800 hover:text-white font-semibold text-sm leading-6 rounded-md flex gap-x-3 p-2 transition ease-in-out duration-150">  
                                    @svg($item['icon'], 'w-6 h-6 text-gray-400 group-hover:text-gray-300')  
                                    {{ $item['name'] }}  
                                </a>  
                            </li>  
                        @endforeach  
                    </ul>  
                </li>  
            </ul>  
        </nav>  
    </div>  
</div>