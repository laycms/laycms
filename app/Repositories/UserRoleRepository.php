<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\UserRoleEntity;
use App\Models\UserRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Juling\Foundation\Contracts\RepositoryInterface;
use Juling\Foundation\Repositories\CurdRepository;

class UserRoleRepository extends CurdRepository implements RepositoryInterface
{
    private static ?UserRoleRepository $instance = null;

    /**
     * 单例 UserRoleRepository
     */
    public static function getInstance(): UserRoleRepository
    {
        if (is_null(self::$instance)) {
            self::$instance = new UserRoleRepository();
        }

        return self::$instance;
    }

    /**
     * 添加 UserRoleEntity
     */
    public function saveEntity(UserRoleEntity $entity): int
    {
        return $this->save($entity->toArray());
    }

    /**
     * 按照ID查询返回对象
     */
    public function findOneById(int $id): ?UserRoleEntity
    {
        $data = $this->findById($id);
        if (empty($data)) {
            return null;
        }

        $entity = new UserRoleEntity();
        $entity->setData($data);

        return $entity;
    }

    /**
     * 按照条件查询返回对象
     */
    public function findOne(array $condition = []): ?UserRoleEntity
    {
        $data = $this->find($condition);
        if (empty($data)) {
            return null;
        }

        $entity = new UserRoleEntity();
        $entity->setData($data);

        return $entity;
    }

    /**
     * 定义数据表查询构造器
     */
    public function builder(): Builder
    {
        return DB::table('user_roles');
    }

    /**
     * 定义数据表模型类
     */
    public function model(): Model
    {
        return new UserRole();
    }
}
