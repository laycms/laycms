<?php

declare(strict_types=1);

namespace App\Modules\Admin\Responses\Permission;

use App\Http\Responses\PaginateLinkVo;
use Juling\Foundation\Support\ArrayHelper;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'PermissionQueryResponse')]
class PermissionQueryResponse
{
    use ArrayHelper;

    #[OA\Property(property: 'current_page', description: '当前页码', type: 'integer')]
    private int $current_page;

    #[OA\Property(property: 'data', description: '数据列表', type: 'array', items: new OA\Items(ref: PermissionResponse::class))]
    private array $data;

    #[OA\Property(property: 'first_page_url', description: '首页URL', type: 'string')]
    private string $first_page_url;

    #[OA\Property(property: 'from', description: '当前页面上的开始位置', type: 'integer')]
    private int $from;

    #[OA\Property(property: 'last_page', description: '最后页码', type: 'integer')]
    private int $last_page;

    #[OA\Property(property: 'last_page_url', description: '最后页URL', type: 'string')]
    private string $last_page_url;

    #[OA\Property(property: 'links', description: '分页链接的数组', type: 'array', items: new OA\Items(ref: PaginateLinkVo::class))]
    private array $links;

    #[OA\Property(property: 'next_page_url', description: '下一页URL', type: 'string')]
    private string $next_page_url;

    #[OA\Property(property: 'path', description: '分页URL', type: 'string')]
    private string $path;

    #[OA\Property(property: 'per_page', description: '每页显示的记录数量', type: 'integer')]
    private int $per_page;

    #[OA\Property(property: 'prev_page_url', description: '上一页URL', type: 'string')]
    private string $prev_page_url;

    #[OA\Property(property: 'to', description: '当前页面上的最后位置', type: 'integer')]
    private int $to;

    #[OA\Property(property: 'total', description: '数据总数', type: 'integer')]
    private int $total;

    public function getCurrentPage(): int
    {
        return $this->current_page;
    }

    public function setCurrentPage(int $current_page): void
    {
        $this->current_page = $current_page;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getFirstPageUrl(): string
    {
        return $this->first_page_url;
    }

    public function setFirstPageUrl(string $first_page_url): void
    {
        $this->first_page_url = $first_page_url;
    }

    public function getFrom(): int
    {
        return $this->from;
    }

    public function setFrom(int $from): void
    {
        $this->from = $from;
    }

    public function getLastPage(): int
    {
        return $this->last_page;
    }

    public function setLastPage(int $last_page): void
    {
        $this->last_page = $last_page;
    }

    public function getLastPageUrl(): string
    {
        return $this->last_page_url;
    }

    public function setLastPageUrl(string $last_page_url): void
    {
        $this->last_page_url = $last_page_url;
    }

    public function getLinks(): array
    {
        return $this->links;
    }

    public function setLinks(array $links): void
    {
        $this->links = $links;
    }

    public function getNextPageUrl(): string
    {
        return $this->next_page_url;
    }

    public function setNextPageUrl(string $next_page_url): void
    {
        $this->next_page_url = $next_page_url;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getPerPage(): int
    {
        return $this->per_page;
    }

    public function setPerPage(int $per_page): void
    {
        $this->per_page = $per_page;
    }

    public function getPrevPageUrl(): string
    {
        return $this->prev_page_url;
    }

    public function setPrevPageUrl(string $prev_page_url): void
    {
        $this->prev_page_url = $prev_page_url;
    }

    public function getTo(): int
    {
        return $this->to;
    }

    public function setTo(int $to): void
    {
        $this->to = $to;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): void
    {
        $this->total = $total;
    }
}
