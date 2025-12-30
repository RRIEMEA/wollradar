<?php

namespace App\Providers;

use App\Models\Brand;
use App\Models\Color;
use App\Models\Location;
use App\Models\Material;
use App\Models\Project;
use App\Models\Yarn;
use App\Policies\BrandPolicy;
use App\Policies\ColorPolicy;
use App\Policies\LocationPolicy;
use App\Policies\MaterialPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\YarnPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Yarn::class, YarnPolicy::class);
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(Color::class, ColorPolicy::class);
        Gate::policy(Material::class, MaterialPolicy::class);
        Gate::policy(Brand::class, BrandPolicy::class);
        Gate::policy(Location::class, LocationPolicy::class);
    }
}
