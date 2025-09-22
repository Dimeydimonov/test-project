@extends('layouts.gallery')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Подтверждение email
            </h2>
        </div>
        
        <div class="bg-white shadow rounded-lg p-6">
            <div class="mb-4 text-sm text-gray-600">
                {{ __('Спасибо за регистрацию! Прежде чем начать, не могли бы вы подтвердить свой адрес электронной почты, перейдя по ссылке, которую мы только что отправили вам по электронной почте? Если вы не получили письмо, мы с радостью отправим вам другое.') }}
            </div>

            @if (session('status') === 'verification-link-sent')
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ __('На ваш адрес электронной почты была отправлена новая ссылка для подтверждения.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-6 flex items-center justify-between space-x-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-envelope mr-2"></i>
                        {{ __('Отправить повторно') }}
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        {{ __('Выйти') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
