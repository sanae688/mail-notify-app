<?php

namespace App\Repositories;

use App\Models\MApiToken;

class EloquentMApiTokenRepository implements MApiTokenRepository
{
    public function create(array $data)
    {
        return MApiToken::create($data);
    }

    public function updateByUserId(int $userId, array $data)
    {
        return MApiToken::where('user_id', $userId)->update($data);
    }
}
