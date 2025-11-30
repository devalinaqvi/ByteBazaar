<?php

namespace App\Services;

use App\Repositories\CategoryRepository;

class CategoryService
{
    public function __construct(private CategoryRepository $repo) {}

    public function list(): array
    {
        return $this->repo->getAll();
    }

    public function get(int $id)
    {
        return $this->repo->find($id);
    }

    public function create(array $data): int
    {
        $data['slug'] = $this->slugify($data['name']);
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $data['slug'] = $this->slugify($data['name']);
        return $this->repo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    private function slugify(string $text): string
    {
        $slug = strtolower(trim($text));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        return trim($slug, '-');
    }
}
