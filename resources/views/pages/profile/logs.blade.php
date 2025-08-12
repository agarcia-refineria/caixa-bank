<x-app-layout>
    @include('partials.profile.navigation')

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="min-h-screen text-white p-6">
            <h1 class="text-3xl font-bold mb-6">📄 Logs</h1>

            <div class="bg-gray-800 rounded-2xl p-4 shadow-lg mb-8">
                @foreach ($userLogs as $log)
                    <div class="rounded-xl">
                        <div class="flex justify-between mb-2">
                            <span class="font-semibold flex gap-4 justify-center items-center">
                                {{ $log['filename'] }}

                                <!-- button to clear log -->
                                <form action="{{ route('profile.logs.clear', ['filename' => $log['filename']]) }}" method="POST" class="inline-block ml-4">
                                    @csrf
                                    <x-buttons.danger-button
                                        class="text-red-500 hover:text-red-700 text-sm"
                                        id="clear-log-{{ $log['filename'] }}"
                                        shepherd-text="{{ __('shepherd.profile-logs-clear-log') }}"
                                        type="submit">
                                        <i class="fas fa-trash-alt"></i> {{ __('Clear Log') }}
                                    </x-buttons.danger-button>
                                </form>
                            </span>
                            <span class="text-sm text-gray-400">
                            {{ \Carbon\Carbon::createFromTimestamp($log['updated_at'])->diffForHumans() }}
                        </span>
                        </div>
                        <pre class="text-sm whitespace-pre-wrap overflow-x-scroll py-6" style="overflow-x: scroll">{{ $log['content'] }}</pre>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
