<?php

namespace App\Repositories;

interface MApiTokenRepository
{
    public function create(array $data);

    public function updateByUserId(int $userId, array $data);
}
