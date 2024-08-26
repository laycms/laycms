<?php

declare(strict_types=1);

namespace App\Modules\Admin\Controllers;

use App\Entities\UserRoleEntity;
use App\Http\Controllers\Controller;
use App\Modules\Admin\Requests\UserRole\UserRoleCreateRequest;
use App\Modules\Admin\Requests\UserRole\UserRoleQueryRequest;
use App\Modules\Admin\Requests\UserRole\UserRoleUpdateRequest;
use App\Modules\Admin\Responses\UserRole\UserRoleDestroyResponse;
use App\Modules\Admin\Responses\UserRole\UserRoleQueryResponse;
use App\Modules\Admin\Responses\UserRole\UserRoleResponse;
use App\Services\UserRoleService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Juling\Foundation\Enums\BusinessCodeEnum;
use Juling\Foundation\Exceptions\CustomException;
use OpenApi\Attributes as OA;
use Throwable;

class UserRoleController extends Controller
{
    #[OA\Get(path: '/userRole', summary: '显示列表页面', security: [['bearerAuth' => []]], tags: ['模块'])]
    public function index(): Renderable
    {
        return view('admin::userRole.index');
    }

    #[OA\Get(path: '/userRole/create', summary: '新增表单页面', security: [['bearerAuth' => []]], tags: ['模块'])]
    public function create(): Renderable
    {
        return view('admin::userRole.create');
    }

    #[OA\Get(path: '/userRole/edit', summary: '编辑表单页面', security: [['bearerAuth' => []]], tags: ['模块'])]
    public function edit(): Renderable
    {
        return view('admin::userRole.edit');
    }

    #[OA\Post(path: '/userRole/query', summary: '查询列表接口', security: [['bearerAuth' => []]], tags: ['模块'])]
    #[OA\Parameter(name: 'page', description: '当前页码', in: 'query', required: true, example: 1)]
    #[OA\Parameter(name: 'pageSize', description: '每页分页数', in: 'query', required: false, example: 10)]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: UserRoleQueryRequest::class))]
    #[OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: UserRoleQueryResponse::class))]
    public function query(UserRoleQueryRequest $queryRequest): JsonResponse
    {
        $page = intval($queryRequest->query('page', 1));
        $pageSize = intval($queryRequest->query('pageSize', 10));
        $request = $queryRequest->validated();

        try {
            $condition = [];

            $userRoleService = new UserRoleService();
            $result = $userRoleService->page($condition, $page, $pageSize);

            foreach ($result['data'] as $key => $item) {
                $response = new UserRoleResponse();
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

    #[OA\Post(path: '/userRole/store', summary: '新增接口', security: [['bearerAuth' => []]], tags: ['模块'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: UserRoleCreateRequest::class))]
    #[OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: UserRoleResponse::class))]
    public function store(UserRoleCreateRequest $createRequest): JsonResponse
    {
        $request = $createRequest->validated();

        DB::beginTransaction();
        try {
            $input = new UserRoleEntity();
            $input->setData($request);

            $userRoleService = new UserRoleService();
            if ($userRoleService->save($input->toArray())) {
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

    #[OA\Get(path: '/userRole/show', summary: '获取详情接口', security: [['bearerAuth' => []]], tags: ['模块'])]
    #[OA\Parameter(name: 'id', description: 'ID', in: 'query', required: true, example: 1)]
    #[OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: UserRoleResponse::class))]
    public function show(Request $request): JsonResponse
    {
        $id = intval($request->query('id', 0));

        try {
            $userRoleService = new UserRoleService();

            $userRole = $userRoleService->getOneById($id);
            if (empty($userRole)) {
                throw new CustomException(BusinessCodeEnum::NOT_FOUND);
            }

            $response = new UserRoleResponse();
            $response->setData($userRole);

            return $this->success($response->toArray());
        } catch (Throwable $e) {
            if ($e instanceof CustomException) {
                return $this->error($e);
            }

            Log::error($e);

            return $this->error(BusinessCodeEnum::SHOW_ERROR);
        }
    }

    #[OA\Put(path: '/userRole/update', summary: '更新接口', security: [['bearerAuth' => []]], tags: ['模块'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: UserRoleUpdateRequest::class))]
    #[OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: UserRoleResponse::class))]
    public function update(UserRoleUpdateRequest $updateRequest): JsonResponse
    {
        $request = $updateRequest->validated();
        $id = intval($request->query('id', 0));

        DB::beginTransaction();
        try {
            $userRoleService = new UserRoleService();

            $userRole = $userRoleService->getOneById($id);
            if (empty($userRole)) {
                throw new CustomException(BusinessCodeEnum::NOT_FOUND);
            }

            $input = new UserRoleEntity();
            $input->setData($request);

            $userRoleService->updateById($input->toArray(), $id);

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

    #[OA\Delete(path: '/userRole/destroy', summary: '删除接口', security: [['bearerAuth' => []]], tags: ['模块'])]
    #[OA\Parameter(name: 'id', description: 'ID', in: 'query', required: true, example: 1)]
    #[OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: UserRoleDestroyResponse::class))]
    public function destroy(Request $request): JsonResponse
    {
        $id = intval($request->query('id', 0));

        DB::beginTransaction();
        try {
            $userRoleService = new UserRoleService();

            $userRole = $userRoleService->getOneById($id);
            if (empty($userRole)) {
                throw new CustomException(BusinessCodeEnum::NOT_FOUND);
            }

            if ($userRoleService->removeById($id)) {
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
