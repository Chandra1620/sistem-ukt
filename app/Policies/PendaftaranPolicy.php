<?php

namespace App\Policies;

use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PendaftaranPolicy
{
    public function view(User $user, Pendaftaran $pendaftaran): bool
    {
        return $user->id === $pendaftaran->user_id;
    }

    public function update(User $user, Pendaftaran $pendaftaran): bool
    {
        return $user->id === $pendaftaran->user_id;
    }
}
