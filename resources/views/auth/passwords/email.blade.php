@extends('layouts.gallery')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Сброс пароля
            </h2>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Забыли пароль? Без проблем. Просто сообщите нам свой email, и мы вышлем вам ссылку для сброса пароля.') }}
    </div>

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
                {{ __('Email') }}
            </label>
            <div class="mt-1">
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
        </div>

        <div class="flex items-center justify-end">
            <button type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Отправить ссылку для сброса пароля') }}
            </button>
        </div>
    </form>
        </div>
    </div>
</div>
@endsection
