<?php

namespace App\Services;

use App\Dto\TechnicianDto;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Collection;

class TechnicianService
{
    public function getTechnicians(): Collection
    {
        $technicians = User::role(UserRole::Technician->value)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return TechnicianDto::collect($technicians);

    }
}
