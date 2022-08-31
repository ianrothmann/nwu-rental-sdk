<?php


namespace IanRothmann\Ain\ServiceProviders;

use Illuminate\Support\ServiceProvider;

class NWURentalServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../Config/nwurental.php' => config_path('nwurental.php'),
        ],'ain-config');
    }

    public function register()
    {
        $this->app->bind('NWURental', function()
        {
            return new NWURentalServiceProviderHandler('');
        });
    }
}
