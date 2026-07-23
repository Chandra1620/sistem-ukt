<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Pendaftaran;
use App\Policies\PendaftaranPolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Pendaftaran::class, PendaftaranPolicy::class);
    }
}
