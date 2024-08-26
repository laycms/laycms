<?php

declare(strict_types=1);

namespace App\Modules\Admin\Controllers;

use App\Entities\DepartmentEntity;
use App\Http\Controllers\Controller;
use App\Modules\Admin\Requests\Department\DepartmentCreateRequest;
use App\Modules\Admin\Requests\Department\DepartmentQueryRequest;
use App\Modules\Admin\Requests\Department\DepartmentUpdateRequest;
use App\Modules\Admin\Responses\Department\DepartmentDestroyResponse;
use App\Modules\Admin\Responses\Department\DepartmentQueryResponse;
use App\Modules\Admin\Responses\Department\DepartmentResponse;
use App\Services\DepartmentService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Juling\Foundation\Enums\BusinessCodeEnum;
use Juling\Foundation\Exceptions\CustomException;
use OpenApi\Attributes as OA;
use Throwable;

class DepartmentController extends Controller
{
    #[OA\Get(path: '/department', summary: '显示列表页面', security: [['bearerAuth' => []]], tags: ['模块'])]
    public function index(): Renderable
    {
        return view('admin::department.index');
    }

    #[OA\Get(path: '/department/create', summary: '新增表单页面', security: [['bearerAuth' => []]], tags: ['模块'])]
    public function create(): Renderable
    {
        return view('admin::department.create');
    }

    #[OA\Get(path: '/department/edit', summary: '编辑表单页面', security: [['bearerAuth' => []]], tags: ['模块'])]
    public function edit(): Renderable
    {
        return view('admin::department.edit');
    }

    #[OA\Post(path: '/department/query', summary: '查询列表接口', security: [['bearerAuth' => []]], tags: ['模块'])]
    #[OA\Parameter(name: 'page', description: '当前页码', in: 'query', required: true, example: 1)]
    #[OA\Parameter(name: 'pageSize', description: '每页分页数', in: 'query', required: false, example: 10)]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: DepartmentQueryRequest::class))]
    #[OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: DepartmentQueryResponse::class))]
    public function query(DepartmentQueryRequest $queryRequest): JsonResponse
    {
        $page = intval($queryRequest->query('page', 1));
        $pageSize = intval($queryRequest->query('pageSize', 10));
        $request = $queryRequest->validated();

        try {
            $condition = [];

            $departmentService = new DepartmentService();
            $result = $departmentService->page($condition, $page, $pageSize);

            foreach ($result['data'] as $key => $item) {
                $response = new DepartmentResponse();
                $response->setData($item);
                $result['data'][$key] = $response->toArray();
            }

            return $this->success($result);
        } catch (Throwable $e) {
            if ($e instanceof CustomException) {
                return $this->error($e);
            }

            Log::error($e);

            return $this->error(BusinessCodeEnum::QUERY_ERROR);
        }
    }

    #[OA\Post(path: '/department/store', summary: '新增接口', security: [['bearerAuth' => []]], tags: ['模块'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: DepartmentCreateRequest::class))]
    #[OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: DepartmentResponse::class))]
    public function store(DepartmentCreateRequest $createRequest): JsonResponse
    {
        $request = $createRequest->validated();

        DB::beginTransaction();
        try {
            $input = new DepartmentEntity();
            $input->setData($request);

            $departmentService = new DepartmentService();
            if ($departmentService->save($input->toArray())) {
                DB::commit();

                return $this->success();
            }

            throw new CustomException(BusinessCodeEnum::CREATE_FAIL);
        } catch (Throwable $e) {
            DB::rollBack();

            if ($e instanceof CustomException) {
                return $this->error($e);
            }

            Log::error($e);

            return $this->error(BusinessCodeEnum::CREATE_ERROR);
        }
    }

    #[OA\Get(path: '/department/show', summary: '获取详情接口', security: [['bearerAuth' => []]], tags: ['模块'])]
    #[OA\Parameter(name: 'id', description: 'ID', in: 'query', required: true, example: 1)]
    #[OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: DepartmentResponse::class))]
    public function show(Request $request): JsonResponse
    {
        $id = intval($request->query('id', 0));

        try {
            $departmentService = new DepartmentService();

            $department = $departmentService->getOneById($id);
            if (empty($department)) {
                throw new CustomException(BusinessCodeEnum::NOT_FOUND);
            }

            $response = new DepartmentResponse();
            $response->setData($department);

            return $this->success($response->toArray());
        } catch (Throwable $e) {
            if ($e instanceof CustomException) {
                return $this->error($e);
            }

            Log::error($e);

            return $this->error(BusinessCodeEnum::SHOW_ERROR);
        }
    }

    #[OA\Put(path: '/department/update', summary: '更新接口', security: [['bearerAuth' => []]], tags: ['模块'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: DepartmentUpdateRequest::class))]
    #[OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: DepartmentResponse::class))]
    public function update(DepartmentUpdateRequest $updateRequest): JsonResponse
    {
        $request = $updateRequest->validated();
        $id = intval($request->query('id', 0));

        DB::beginTransaction();
        try {
            $departmentService = new DepartmentService();

            $department = $departmentService->getOneById($id);
            if (empty($department)) {
                throw new CustomException(BusinessCodeEnum::NOT_FOUND);
            }

            $input = new DepartmentEntity();
            $input->setData($request);

            $departmentService->updateById($input->toArray(), $id);

            DB::commit();

            return $this->success();
        } catch (Throwable $e) {
            DB::rollBack();

            if ($e instanceof CustomException) {
                return $this->error($e);
            }

            Log::error($e);

            return $this->error(BusinessCodeEnum::UPDATE_ERROR);
        }
    }

    #[OA\Delete(path: '/department/destroy', summary: '删除接口', security: [['bearerAuth' => []]], tags: ['模块'])]
    #[OA\Parameter(name: 'id', description: 'ID', in: 'query', required: true, example: 1)]
    #[OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: DepartmentDestroyResponse::class))]
    public function destroy(Request $request): JsonResponse
    {
        $id = intval($request->query('id', 0));

        DB::beginTransaction();
        try {
            $departmentService = new DepartmentService();

            $department = $departmentService->getOneById($id);
            if (empty($department)) {
                throw new CustomException(BusinessCodeEnum::NOT_FOUND);
            }

            if ($departmentService->removeById($id)) {
                DB::commit();

                return $this->success();
            }

            throw new CustomException(BusinessCodeEnum::DESTROY_FAIL);
        } catch (Throwable $e) {
            DB::rollBack();

            if ($e instanceof CustomException) {
                return $this->error($e);
            }

            Log::error($e);

            return $this->error(BusinessCodeEnum::DESTROY_ERROR);
        }
    }
}
