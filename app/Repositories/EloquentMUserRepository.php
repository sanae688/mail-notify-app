<?php

namespace App\Repositories;

use App\Models\MUser;

class EloquentMUserRepository implements MUserRepository
{
    public function create(array $data)
    {
        return MUser::create($data);
    }

    public function findByContractId(string $contractId)
    {
        return MUser::where('smaregi_contract_id', $contractId)->first();
    }

    public function findMApiTokenByMUser(MUser $mUser)
    {
        return $mUser->mApiToken;
    }
}
