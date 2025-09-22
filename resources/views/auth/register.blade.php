@extends('layouts.gallery')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Регистрация
            </h2>
        </div>
    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">
                {{ __('Имя') }}
            </label>
            <div class="mt-1">
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
                {{ __('Email') }}
            </label>
            <div class="mt-1">
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
                {{ __('Пароль') }}
            </label>
            <div class="mt-1">
                <input id="password" name="password" type="password" required autocomplete="new-password"
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500">
                    Пароль должен содержать не менее 8 символов, включая заглавные и строчные буквы, цифры и специальные символы.
                </p>
            </div>
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                {{ __('Подтвердите пароль') }}
            </label>
            <div class="mt-1">
                <input id="password_confirmation" name="password_confirmation" type="password" required
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
        </div>

        <div>
            <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Зарегистрироваться') }}
            </button>
        </div>

        <div class="text-center text-sm text-gray-600">
            <p>Уже зарегистрированы?
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    {{ __('Войти') }}
                </a>
            </p>
        </div>
    </form>
    </div>
</div>
@endsection
