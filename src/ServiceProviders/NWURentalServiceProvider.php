<?php


namespace IanRothmann\Ain\ServiceProviders;

use App\Classes\Api\RentalListing\RentalListingClass;
use Illuminate\Support\ServiceProvider;

class NWURentalServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../Config/nwurental.php' => config_path('nwurental.php'),
        ],'nwu-rental-config');
    }

    public function register()
    {
        $this->app->bind('NWURental', function()
        {
            return new RentalListingClass(config('nwurental.url'),config('nwurental.key'),config('nwurental.agencyid'));
        });
    }
}
