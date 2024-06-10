@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-500 text-black focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm']) !!}>
