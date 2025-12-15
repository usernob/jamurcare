<?php
?>

<button 
    {{ $attributes->merge(['class' => 'w-full py-3 px-4 rounded-lg text-white font-medium text-lg transition-all duration-300 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 transform hover:translate-y-[-1px] shadow-lg']) }}>
    {{ $slot }}
</button>