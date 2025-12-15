<?php

?>

<div class="mb-4">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
    <input 
        id="{{ $name }}" 
        type="{{ $type ?? 'text' }}" 
        name="{{ $name }}" 
        value="{{ old($name) }}" 
        {{ $attributes->merge(['class' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200']) }}
        placeholder="{{ $placeholder ?? '' }}"
        {{ $required ? 'required' : '' }}
        {{ $autofocus ? 'autofocus' : '' }}
    >
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>