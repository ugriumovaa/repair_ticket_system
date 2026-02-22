<?php

namespace App\Dto;

use Spatie\LaravelData\Data;

class TechnicianDto extends Data
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}

}
