<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseResource extends ResourceCollection
{
    protected string $resourceClass;

    public function __construct($resource, string $resourceClass)
    {
        parent::__construct($resource);
        $this->resourceClass = $resourceClass;
    }

    public function toArray($request): array
    {
        return [
            'data' => $this->resourceClass::collection($this->collection),
            'meta' => [
                'version' => '1.0',
                'api_version' => 'v1',
                'copyright' => config('app.name'),
                'authors' => ['Your Company'],
                'timestamp' => now()->toISOString(),
                'total' => $this->total(),
                'current_page'    => $this->currentPage(),
                'per_page'        => $this->perPage(),
                'total'           => $this->total(),
                'from'            => $this->firstItem(),
                'to'              => $this->lastItem(),
                'path'            => $this->path(),
                'start'           => $this->firstItem(),
            ],
            'pagination' => [
                'current_page'    => $this->currentPage(),
                'per_page'        => $this->perPage(),
                'total'           => $this->total(),
                'last_page'       => $this->lastPage(),
                'from'            => $this->firstItem(),
                'to'              => $this->lastItem(),
                'has_more_pages'  => $this->hasMorePages(),
                'next_page_url'   => $this->nextPageUrl(),
                'prev_page_url'   => $this->previousPageUrl(),
                'first_page_url'  => $this->url(1),
                'last_page_url'   => $this->url($this->lastPage()),
                'path'            => $this->path(),
            ],
        ];
    }
}
