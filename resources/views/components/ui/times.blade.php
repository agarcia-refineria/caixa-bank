@php
    $color = 'bg-green-800';

    if (isset($defaultBg)) {
        $color = $defaultBg;
    } else {
        if (!isset($count)) {
            $count = 0;
        }
        if (!isset($maxTimes)) {
            $maxTimes = \App\Models\ScheduledTasks::$MAX_TIMES;
        }
        if (!isset($warningTimes)) {
            $warningTimes = \App\Models\ScheduledTasks::$WARNING_TIMES;
        }

        if ($count >= $maxTimes) {
            $color = 'bg-red-800';
        } elseif ($count >= $warningTimes) {
            $color = 'bg-yellow-800';
        }
    }
@endphp

<span class="{{ $color }} text-xs font-semibold px-2.5 py-0.5 rounded-full">
    {{ $count }} {{ __('times') }}
</span>
