<?php

namespace App\Services;

use App\Repositories\ProductRepository;

class ProductService
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly FileUploadService $fileUploadService,
        private readonly CategoryService $categoryService
    ) {}

    public function list(): array
    {
        return $this->products->getAll();
    }
    public function listFiltered(array $categoryIds = []): array
    {
        if (empty($categoryIds)) {
            return $this->products->getAll();
        }

        return $this->products->getByCategoryIds($categoryIds);
    }


    public function categories()
    {
        return $this->categoryService->list();
    }

    public function get(int $id)
    {
        return $this->products->find($id);
    }

    public function getBySlug(string $slug): ?\App\Models\Product
    {
        return $this->products->findBySlug($slug);
    }

    public function create(array $data, ?array $file = null): int
    {
        if ($file && isset($file['error']) && $file['error'] === 0) {
            $data['image_url'] = $this->fileUploadService->uploadImage($file);
        } else {
            $data['image_url'] = null;
        }

        $data['slug'] = $this->slugify($data['name']);
        return $this->products->create($data);
    }

    public function update(int $id, array $data, ?array $file = null): bool
    {
        if ($file && isset($file['error']) && $file['error'] === 0) {
            $data['image'] = $this->fileUploadService->uploadImage($file);
        } else {
            // Keep existing image if not updating
            $existing = $this->products->find($id);
            $data['image'] = $existing?->image_url;
        }

        $data['slug'] = $this->slugify($data['name']);
        return $this->products->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->products->softDelete($id);
    }

    private function slugify(string $text): string
    {
        $slug = strtolower(trim($text));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        return trim($slug, '-');
    }
    public function search(string $q, int $page): array
    {
        $limit = 10;

        return [
            'products' => $this->products->searchPaginated($q, $page, $limit),
            'total' => $this->products->countSearch($q),
            'page' => $page,
            'limit' => $limit
        ];
    }
}
