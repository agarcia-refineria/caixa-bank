<div class="bg-[#1c1d20] p-4 rounded-xl shadow {{ $containerClass ?? '' }}">
    <h2 class="text-xl mb-4">{{ $title }}</h2>
    <canvas id="{{ $id }}" class="{{ $class ?? '' }}" {{ $attributes }}></canvas>
    <div id="categoryChart-legend" class="legend-container"></div>
</div>
