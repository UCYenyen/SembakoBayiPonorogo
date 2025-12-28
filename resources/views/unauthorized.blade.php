@extends('layouts.app')
@section('title', 'Unauthorized')

@section('content')
   <main class="bg-[#FFF3F3] text-[#3F3142] flex items-center lg:justify-center min-h-[80vh] flex-col">
        <div class="bg-white rounded-lg shadow-lg p-8 md:p-12 max-w-md w-full text-center flex flex-col justify-center items-center">
            <svg class="w-24 h-24 text-red-500 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Akses Ditolak</h1>
            
            <p class="text-gray-600 mb-6">
                Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.
            </p>
            
            <a href="/" class="flex items-center gap-2 hover:bg-[#3F3142]/80 border-2 border-[#3F3142] shadow-lg rounded-lg text-sm bg-[#3F3142] text-white px-4 py-2 transition-all duration-200 group font-bold text-center w-fit justify-center">
                Kembali
            </a>
        </div>
    </main>
@endsection
