@php echo "<?php" @endphp

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {
        <x-register-local-only />
    }

    public function boot(): void
    {
        //
    }
}
