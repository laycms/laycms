<?php

declare(strict_types=1);

namespace App\Modules\Admin\Controllers;

use App\Entities\UserEntity;
use App\Http\Controllers\Controller;
use App\Modules\Admin\Requests\User\UserCreateRequest;
use App\Modules\Admin\Requests\User\UserQueryRequest;
use App\Modules\Admin\Requests\User\UserUpdateRequest;
use App\Modules\Admin\Responses\User\UserDestroyResponse;
use App\Modules\Admin\Responses\User\UserQueryResponse;
use App\Modules\Admin\Responses\User\UserResponse;
use App\Services\UserService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Juling\Foundation\Enums\BusinessCodeEnum;
use Juling\Foundation\Exceptions\CustomException;
use OpenApi\Attributes as OA;
use Throwable;

class UserController extends Controller
{
    #[OA\Get(path: '/user', summary: '显示列表页面', security: [['bearerAuth' => []]], tags: ['模块'])]
    public function index(): Renderable
    {
        return view('admin::user.index');
    }

    #[OA\Get(path: '/user/create', summary: '新增表单页面', security: [['bearerAuth' => []]], tags: ['模块'])]
    public function create(): Renderable
    {
        return view('admin::user.create');
    }

    #[OA\Get(path: '/user/edit', summary: '编辑表单页面', security: [['bearerAuth' => []]], tags: ['模块'])]
    public function edit(): Renderable
    {
        return view('admin::user.edit');
    }

    #[OA\Post(path: '/user/query', summary: '查询列表接口', security: [['bearerAuth' => []]], tags: ['模块'])]
    #[OA\Parameter(name: 'page', description: '当前页码', in: 'query', required: true, example: 1)]
    #[OA\Parameter(name: 'pageSize', description: '每页分页数', in: 'query', required: false, example: 10)]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: UserQueryRequest::class))]
    #[OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: UserQueryResponse::class))]
    public function query(UserQueryRequest $queryRequest): JsonResponse
    {
        $page = intval($queryRequest->query('page', 1));
        $pageSize = intval($queryRequest->query('pageSize', 10));
        $request = $queryRequest->validated();

        try {
            $condition = [];

            $userService = new UserService();
            $result = $userService->page($condition, $page, $pageSize);

            foreach ($result['data'] as $key => $item) {
                $response = new UserResponse();
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

    #[OA\Post(path: '/user/store', summary: '新增接口', security: [['bearerAuth' => []]], tags: ['模块'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: UserCreateRequest::class))]
    #[OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: UserResponse::class))]
    public function store(UserCreateRequest $createRequest): JsonResponse
    {
        $request = $createRequest->validated();

        DB::beginTransaction();
        try {
            $input = new UserEntity();
            $input->setData($request);

            $userService = new UserService();
            if ($userService->save($input->toArray())) {
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

    #[OA\Get(path: '/user/show', summary: '获取详情接口', security: [['bearerAuth' => []]], tags: ['模块'])]
    #[OA\Parameter(name: 'id', description: 'ID', in: 'query', required: true, example: 1)]
    #[OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: UserResponse::class))]
    public function show(Request $request): JsonResponse
    {
        $id = intval($request->query('id', 0));

        try {
            $userService = new UserService();

            $user = $userService->getOneById($id);
            if (empty($user)) {
                throw new CustomException(BusinessCodeEnum::NOT_FOUND);
            }

            $response = new UserResponse();
            $response->setData($user);

            return $this->success($response->toArray());
        } catch (Throwable $e) {
            if ($e instanceof CustomException) {
                return $this->error($e);
            }

            Log::error($e);

            return $this->error(BusinessCodeEnum::SHOW_ERROR);
        }
    }

    #[OA\Put(path: '/user/update', summary: '更新接口', security: [['bearerAuth' => []]], tags: ['模块'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: UserUpdateRequest::class))]
    #[OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: UserResponse::class))]
    public function update(UserUpdateRequest $updateRequest): JsonResponse
    {
        $request = $updateRequest->validated();
        $id = intval($request->query('id', 0));

        DB::beginTransaction();
        try {
            $userService = new UserService();

            $user = $userService->getOneById($id);
            if (empty($user)) {
                throw new CustomException(BusinessCodeEnum::NOT_FOUND);
            }

            $input = new UserEntity();
            $input->setData($request);

            $userService->updateById($input->toArray(), $id);

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

    #[OA\Delete(path: '/user/destroy', summary: '删除接口', security: [['bearerAuth' => []]], tags: ['模块'])]
    #[OA\Parameter(name: 'id', description: 'ID', in: 'query', required: true, example: 1)]
    #[OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: UserDestroyResponse::class))]
    public function destroy(Request $request): JsonResponse
    {
        $id = intval($request->query('id', 0));

        DB::beginTransaction();
        try {
            $userService = new UserService();

            $user = $userService->getOneById($id);
            if (empty($user)) {
                throw new CustomException(BusinessCodeEnum::NOT_FOUND);
            }

            if ($userService->removeById($id)) {
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
