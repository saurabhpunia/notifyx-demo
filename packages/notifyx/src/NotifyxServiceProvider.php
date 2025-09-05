<?php

namespace Notifyx;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Notifyx\Services\Notifyx as NotifyxService;
use Notifyx\Livewire\NotificationBell;
use Notifyx\Livewire\NotificationDropdown;
use Notifyx\Livewire\NotificationPage;
use Notifyx\Livewire\NotificationPreferences;

class NotifyxServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/notifyx.php',
            'notifyx'
        );

        $this->app->singleton('notifyx', function ($app) {
            return new NotifyxService();
        });

        $this->app->bind(NotifyxService::class, function ($app) {
            return $app['notifyx'];
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'notifyx');
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->publishes([
            __DIR__.'/../config/notifyx.php' => config_path('notifyx.php'),
        ], 'notifyx-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/notifyx'),
        ], 'notifyx-views');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'notifyx-migrations');

        // Register Livewire components
        if (class_exists(Livewire::class)) {
            Livewire::component('notification-bell', NotificationBell::class);
            Livewire::component('notification-dropdown', NotificationDropdown::class);
            Livewire::component('notification-page', NotificationPage::class);
            Livewire::component('notification-preferences', NotificationPreferences::class);
            
            // Also register with namespaced versions
            Livewire::component('notifyx::notification-bell', NotificationBell::class);
            Livewire::component('notifyx::notification-dropdown', NotificationDropdown::class);
            Livewire::component('notifyx::notification-page', NotificationPage::class);
            Livewire::component('notifyx::notification-preferences', NotificationPreferences::class);
        }

        // Register Blade directives
        $this->registerBladeDirectives();

        // Register helper functions
        $this->registerHelpers();
    }

    /**
     * Register Blade directives
     */
    protected function registerBladeDirectives(): void
    {
        Blade::directive('notificationBell', function () {
            return '<?php echo view("notifyx::components.notification-bell"); ?>';
        });

        Blade::directive('svg', function ($expression) {
            return "<?php echo app('blade.compiler')->compileString('@svg' . $expression); ?>";
        });
    }

    /**
     * Register helper functions
     */
    protected function registerHelpers(): void
    {
        if (! function_exists('notify')) {
            require_once __DIR__.'/helpers.php';
        }
    }
}
