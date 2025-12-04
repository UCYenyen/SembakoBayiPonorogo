<section class="w-screen overflow-hidden flex flex-col gap-8 {{ $extraClasses ?? '' }}">
    <h1 class="text-4xl md:text-5xl w-full text-center font-bold text-[#3F3142]">{{ $title }}</h1>
    {{ $slot }}
</section>