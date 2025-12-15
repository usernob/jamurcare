<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Add custom Blade directives if needed
        Blade::directive('socialLoginButtons', function () {
            return '<div class="grid grid-cols-3 gap-3">
                        <a href="{{ route(\'social.login\', [\'provider\' => \'google\']) }}" 
                           class="btn-social flex items-center justify-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="social-icon social-google">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12.545 10.239v3.821h5.445c-.712 2.315-2.647 3.972-5.445 3.972a6.38 6.38 0 01-6.38-6.38 6.38 6.38 0 016.38-6.38c1.875 0 3.667.682 5.018 1.806l3.218-3.218A11.29 11.29 0 0012.545 0C5.623 0 0 5.623 0 12.545 0 19.468 5.623 25.09 12.545 25.09c6.923 0 12.545-5.622 12.545-12.545 0-6.922-5.622-12.545-12.545-12.545z"/>
                                </svg>
                            </div>
                        </a>
                        <a href="{{ route(\'social.login\', [\'provider\' => \'facebook\']) }}" 
                           class="btn-social flex items-center justify-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="social-icon social-facebook">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.991 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </div>
                        </a>
                        <a href="{{ route(\'social.login\', [\'provider\' => \'microsoft\']) }}" 
                           class="btn-social flex items-center justify-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="social-icon social-microsoft">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M2 2h8v8H2zm10 0h8v8h-8zM2 12h8v8H2zm10 0h8v8h-8z"/>
                                </svg>
                            </div>
                        </a>
                    </div>';
        });
    }
}