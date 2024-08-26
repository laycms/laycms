<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\SubsidiaryEntity;
use App\Models\Subsidiary;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Juling\Foundation\Contracts\RepositoryInterface;
use Juling\Foundation\Repositories\CurdRepository;

class SubsidiaryRepository extends CurdRepository implements RepositoryInterface
{
    private static ?SubsidiaryRepository $instance = null;

    /**
     * 单例 SubsidiaryRepository
     */
    public static function getInstance(): SubsidiaryRepository
    {
        if (is_null(self::$instance)) {
            self::$instance = new SubsidiaryRepository();
        }

        return self::$instance;
    }

    /**
     * 添加 SubsidiaryEntity
     */
    public function saveEntity(SubsidiaryEntity $entity): int
    {
        return $this->save($entity->toArray());
    }

    /**
     * 按照ID查询返回对象
     */
    public function findOneById(int $id): ?SubsidiaryEntity
    {
        $data = $this->findById($id);
        if (empty($data)) {
            return null;
        }

        $entity = new SubsidiaryEntity();
        $entity->setData($data);

        return $entity;
    }

    /**
     * 按照条件查询返回对象
     */
    public function findOne(array $condition = []): ?SubsidiaryEntity
    {
        $data = $this->find($condition);
        if (empty($data)) {
            return null;
        }

        $entity = new SubsidiaryEntity();
        $entity->setData($data);

        return $entity;
    }

    /**
     * 定义数据表查询构造器
     */
    public function builder(): Builder
    {
        return DB::table('subsidiaries');
    }

    /**
     * 定义数据表模型类
     */
    public function model(): Model
    {
        return new Subsidiary();
    }
}
