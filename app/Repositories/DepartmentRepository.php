<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\DepartmentEntity;
use App\Models\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Juling\Foundation\Contracts\RepositoryInterface;
use Juling\Foundation\Repositories\CurdRepository;

class DepartmentRepository extends CurdRepository implements RepositoryInterface
{
    private static ?DepartmentRepository $instance = null;

    /**
     * 单例 DepartmentRepository
     */
    public static function getInstance(): DepartmentRepository
    {
        if (is_null(self::$instance)) {
            self::$instance = new DepartmentRepository();
        }

        return self::$instance;
    }

    /**
     * 添加 DepartmentEntity
     */
    public function saveEntity(DepartmentEntity $entity): int
    {
        return $this->save($entity->toArray());
    }

    /**
     * 按照ID查询返回对象
     */
    public function findOneById(int $id): ?DepartmentEntity
    {
        $data = $this->findById($id);
        if (empty($data)) {
            return null;
        }

        $entity = new DepartmentEntity();
        $entity->setData($data);

        return $entity;
    }

    /**
     * 按照条件查询返回对象
     */
    public function findOne(array $condition = []): ?DepartmentEntity
    {
        $data = $this->find($condition);
        if (empty($data)) {
            return null;
        }

        $entity = new DepartmentEntity();
        $entity->setData($data);

        return $entity;
    }

    /**
     * 定义数据表查询构造器
     */
    public function builder(): Builder
    {
        return DB::table('departments');
    }

    /**
     * 定义数据表模型类
     */
    public function model(): Model
    {
        return new Department();
    }
}
