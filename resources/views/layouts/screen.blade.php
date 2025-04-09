@extends('layouts.dependency')

@section('body')
    <div class="w-full flex justify-center text-black transition overflow-x-hidden overflow-y-auto custom-scrollbar">
        <div class="h-full w-full min-h-dvh max-w-[28rem] flex flex-nowrap relative break-words whitespace-normal">
            <div class="w-full bg-primary-dark h-[22rem] rounded-b-[6rem] ">
                @yield('screen')
            </div>
        </div>
    </div>
    <script>
        // Wait for the DOM to load
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.custom-scrollbar').forEach(scrollable => {
                let scrollTimeout;
                scrollable.addEventListener('scroll', () => {
                    scrollable.classList.add('scrolling');
                    clearTimeout(scrollTimeout);
                    scrollTimeout = setTimeout(() => {
                        scrollable.classList.remove('scrolling');
                    }, 500); // Adjust the timeout as needed
                });
            });
        });
    </script>
@endsection
