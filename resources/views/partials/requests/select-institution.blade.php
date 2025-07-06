<form method="post" action="" class="p-6">
    @csrf

    <h2 class="text-lg font-medium text-primary text-center">
        {{ __('A que institucion quieres hacer la importacion?') }}
    </h2>

    <br/>

    <div class="w-full flex flex-wrap bg-main2 px-4 md:px-0 overflow-hidden shadow-sm rounded-lg">
        @foreach($user->institutions()->orderBy('name')->get() as $institution)
            <div class="w-full sm:px-6 lg:px-8 py-4">
                <a class="flex gap-4 items-center text-lg font-medium text-primary w-full justify-left cursor-pointer focus:outline-none" href="{{ route('nordigen.auth', ['institutionId' => $institution->id]) }}">
                    <img src="{{ $institution->logo }}" alt="{{ $institution->name }}" width="32" height="32" class="h-8 w-8 mr-2">
                    {{ $institution->name }}
                </a>
            </div>
        @endforeach
    </div>
</form>
