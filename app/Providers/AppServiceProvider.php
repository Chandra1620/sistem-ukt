<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Pendaftaran;
use App\Policies\PendaftaranPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Pendaftaran::class, PendaftaranPolicy::class);

        // Layout memakai Bootstrap 5, jadi paginasi harus memakai markup Bootstrap.
        // Tanpa ini, ->links() memakai template Tailwind (SVG h-5 w-5) yang
        // tidak berlaku di Bootstrap sehingga ikon panah tampil sangat besar.
        Paginator::useBootstrapFive();
    }
}
