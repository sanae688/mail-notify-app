<?php

namespace App\Services;

use App\Models\MUser;
use App\Repositories\MUserRepository;

/**
 * ユーザーマスタサービス
 *
 * @package App\Services
 * @author naito
 * @version ver1.0.0 2024/08/16
 */
class MUserService
{
    /**
     * コンストラクタ
     *
     * @param MUserRepository $mUserRepository ユーザーマスタリポジトリ
     */
    public function __construct(protected MUserRepository $mUserRepository) {}

    /**
     * ユーザー登録処理
     *
     * @param string $contractId 契約ID
     * @return MUser ユーザーマスタ
     *
     */
    public function createUser(string $contractId)
    {
        try {
            $data = [
                'smaregi_contract_id' => $contractId
            ];
            return $this->mUserRepository->create($data);
        } catch (\Exception $error) {
            throw new \Exception($error . '：ユーザーの登録に失敗しました。');
        }
    }

    /**
     * ユーザー検索処理（条件：契約ID）
     *
     * @param string $contractId 契約ID
     * @return mixed ユーザーマスタ | null
     *
     */
    public function findByContractId(string $contractId)
    {
        return $this->mUserRepository->findByContractId($contractId);
    }

    /**
     * APIトークンマスタ検索処理（条件：ユーザーマスタ）
     *
     * @param string $contractId 契約ID
     * @return mixed APIトークンマスタ | null
     *
     */
    public function findMApiTokenByMUser(MUser $mUser)
    {
        return $this->mUserRepository->findMApiTokenByMUser($mUser);
    }
}
