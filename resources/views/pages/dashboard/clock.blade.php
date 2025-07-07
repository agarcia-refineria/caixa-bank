<x-app-layout>
    <div class="md:h-[90vh] flex flex-col justify-center items-center">
        <div class="pt-10 md:pt-0 w-full flex justify-center items-center text-primary" id="clock-current-time" shepherd-text="{{ trans('shepherd.clock-current-time') }}">
            <div class="u-clock" id="clock">00:00:00</div>
        </div>

        <div class="pt-10 md:pt-32 w-full flex justify-center items-center text-primary">
            @if (count($schedules) > 0)
                <div class="grid grid-cols-2 gap-6 sm:grid-cols-3" id="clock-schedule-times" shepherd-text="{{ trans('shepherd.clock-schedule-times') }}">
                    @foreach($schedules as $schedule)
                        <div class="u-clock">
                            <x-inputs.input-label for="schedule_time_{{ $loop->index }}" :value="__('Schedule Time') . ' ' . ($loop->index + 1)" />
                            <x-inputs.text-input id="schedule_time_{{ $loop->index }}" name="times[]" type="time" class="mt-1 block w-full" disabled :value="$schedule->hour" autocomplete="schedule_time_{{ $loop->index }}" />
                            <x-inputs.input-error class="mt-2" :messages="$errors->get('schedule_time_' . $loop->index)" />
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
