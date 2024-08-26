<?php

declare(strict_types=1);

namespace App\Modules\Admin\Controllers;

use App\Entities\SubsidiaryEntity;
use App\Http\Controllers\Controller;
use App\Modules\Admin\Requests\Subsidiary\SubsidiaryCreateRequest;
use App\Modules\Admin\Requests\Subsidiary\SubsidiaryQueryRequest;
use App\Modules\Admin\Requests\Subsidiary\SubsidiaryUpdateRequest;
use App\Modules\Admin\Responses\Subsidiary\SubsidiaryDestroyResponse;
use App\Modules\Admin\Responses\Subsidiary\SubsidiaryQueryResponse;
use App\Modules\Admin\Responses\Subsidiary\SubsidiaryResponse;
use App\Services\SubsidiaryService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Juling\Foundation\Enums\BusinessCodeEnum;
use Juling\Foundation\Exceptions\CustomException;
use OpenApi\Attributes as OA;
use Throwable;

class SubsidiaryController extends Controller
{
    #[OA\Get(path: '/subsidiary', summary: '显示列表页面', security: [['bearerAuth' => []]], tags: ['模块'])]
    public function index(): Renderable
    {
        return view('admin::subsidiary.index');
    }

    #[OA\Get(path: '/subsidiary/create', summary: '新增表单页面', security: [['bearerAuth' => []]], tags: ['模块'])]
    public function create(): Renderable
    {
        return view('admin::subsidiary.create');
    }

    #[OA\Get(path: '/subsidiary/edit', summary: '编辑表单页面', security: [['bearerAuth' => []]], tags: ['模块'])]
    public function edit(): Renderable
    {
        return view('admin::subsidiary.edit');
    }

    #[OA\Post(path: '/subsidiary/query', summary: '查询列表接口', security: [['bearerAuth' => []]], tags: ['模块'])]
    #[OA\Parameter(name: 'page', description: '当前页码', in: 'query', required: true, example: 1)]
    #[OA\Parameter(name: 'pageSize', description: '每页分页数', in: 'query', required: false, example: 10)]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: SubsidiaryQueryRequest::class))]
    #[OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: SubsidiaryQueryResponse::class))]
    public function query(SubsidiaryQueryRequest $queryRequest): JsonResponse
    {
        $page = intval($queryRequest->query('page', 1));
        $pageSize = intval($queryRequest->query('pageSize', 10));
        $request = $queryRequest->validated();

        try {
            $condition = [];

            $subsidiaryService = new SubsidiaryService();
            $result = $subsidiaryService->page($condition, $page, $pageSize);

            foreach ($result['data'] as $key => $item) {
                $response = new SubsidiaryResponse();
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

    #[OA\Post(path: '/subsidiary/store', summary: '新增接口', security: [['bearerAuth' => []]], tags: ['模块'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: SubsidiaryCreateRequest::class))]
    #[OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: SubsidiaryResponse::class))]
    public function store(SubsidiaryCreateRequest $createRequest): JsonResponse
    {
        $request = $createRequest->validated();

        DB::beginTransaction();
        try {
            $input = new SubsidiaryEntity();
            $input->setData($request);

            $subsidiaryService = new SubsidiaryService();
            if ($subsidiaryService->save($input->toArray())) {
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

    #[OA\Get(path: '/subsidiary/show', summary: '获取详情接口', security: [['bearerAuth' => []]], tags: ['模块'])]
    #[OA\Parameter(name: 'id', description: 'ID', in: 'query', required: true, example: 1)]
    #[OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: SubsidiaryResponse::class))]
    public function show(Request $request): JsonResponse
    {
        $id = intval($request->query('id', 0));

        try {
            $subsidiaryService = new SubsidiaryService();

            $subsidiary = $subsidiaryService->getOneById($id);
            if (empty($subsidiary)) {
                throw new CustomException(BusinessCodeEnum::NOT_FOUND);
            }

            $response = new SubsidiaryResponse();
            $response->setData($subsidiary);

            return $this->success($response->toArray());
        } catch (Throwable $e) {
            if ($e instanceof CustomException) {
                return $this->error($e);
            }

            Log::error($e);

            return $this->error(BusinessCodeEnum::SHOW_ERROR);
        }
    }

    #[OA\Put(path: '/subsidiary/update', summary: '更新接口', security: [['bearerAuth' => []]], tags: ['模块'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: SubsidiaryUpdateRequest::class))]
    #[OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: SubsidiaryResponse::class))]
    public function update(SubsidiaryUpdateRequest $updateRequest): JsonResponse
    {
        $request = $updateRequest->validated();
        $id = intval($request->query('id', 0));

        DB::beginTransaction();
        try {
            $subsidiaryService = new SubsidiaryService();

            $subsidiary = $subsidiaryService->getOneById($id);
            if (empty($subsidiary)) {
                throw new CustomException(BusinessCodeEnum::NOT_FOUND);
            }

            $input = new SubsidiaryEntity();
            $input->setData($request);

            $subsidiaryService->updateById($input->toArray(), $id);

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

    #[OA\Delete(path: '/subsidiary/destroy', summary: '删除接口', security: [['bearerAuth' => []]], tags: ['模块'])]
    #[OA\Parameter(name: 'id', description: 'ID', in: 'query', required: true, example: 1)]
    #[OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: SubsidiaryDestroyResponse::class))]
    public function destroy(Request $request): JsonResponse
    {
        $id = intval($request->query('id', 0));

        DB::beginTransaction();
        try {
            $subsidiaryService = new SubsidiaryService();

            $subsidiary = $subsidiaryService->getOneById($id);
            if (empty($subsidiary)) {
                throw new CustomException(BusinessCodeEnum::NOT_FOUND);
            }

            if ($subsidiaryService->removeById($id)) {
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
