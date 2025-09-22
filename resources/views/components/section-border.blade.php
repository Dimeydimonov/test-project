@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'border-t border-gray-200 ' . $class]) }}></div>
