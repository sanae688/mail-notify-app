<?php

namespace App\Repositories;

use App\Models\MUser;

interface MUserRepository
{
    public function create(array $data);

    public function findByContractId(string $contractId);

    public function findMApiTokenByMUser(MUser $mUser);
}
