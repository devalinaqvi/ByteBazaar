<?php
namespace App\Services;
use App\Core\{Validation, HttpException};
use App\Repositories\ProductRepository;
class ProductService
{
    public function __construct(
        private ProductRepository $products,
        private FileUploadService $uploads,
        private CategoryService $categoriesService,
    ) {}
    public function list(): array
    {
        return $this->products->getAll();
    }
    public function catalog(
        string $q = "",
        array $categories = [],
        int $page = 1,
        string $sort = "newest",
    ): array {
        return $this->products->catalog($q, $categories, $page, $sort);
    }
    public function categories(): array
    {
        return $this->categoriesService->list();
    }
    public function get(int $id): \App\Models\Product
    {
        return $this->products->find($id) ??
            throw new HttpException(404, "Product not found.");
    }
    public function getBySlug(string $slug): ?\App\Models\Product
    {
        return $this->products->findBySlug($slug);
    }
    private function validate(array $data): array
    {
        $name = Validation::text($data, "name", 255);
        $description = Validation::text($data, "description", 10000);
        $price = $data["price"] ?? "";
        if (
            !is_scalar($price) ||
            !preg_match('/^\d{1,6}(\.\d{1,2})?$/', (string) $price) ||
            (float) $price <= 0
        ) {
            throw new HttpException(
                422,
                "Price must be positive with at most two decimal places.",
            );
        }
        $stock = Validation::integer($data["stock"] ?? null, 0, 1000000);
        $category = Validation::integer(
            $data["category_id"] ?? null,
            1,
            PHP_INT_MAX,
        );
        $categoryRecord = $this->categoriesService->get($category);
        if (!$categoryRecord) {
            throw new HttpException(422, "Select an existing category.");
        }
        if (preg_match('/\{(?:name|brand|price|cpu|ram|storage|display|category|gpu|specs|feature_[123])\}/', $description)) {
            $description = render_description($description, [
                "name" => $name,
                "price" => money($price),
                "category" => $categoryRecord->name,
            ]);
            $description = Validation::text(["description" => $description], "description", 10000);
        }
        return [
            "name" => $name,
            "description" => $description,
            "price" => number_format((float) $price, 2, ".", ""),
            "stock" => $stock,
            "category_id" => $category,
        ];
    }
    private function slug(string $name, int $id = 0): string
    {
        $base =
            substr(
                trim(preg_replace("/[^a-z0-9]+/", "-", strtolower($name)), "-"),
                0,
                220,
            ) ?:
            "product";
        $slug = $base;
        $n = 2;
        while ($this->products->slugExists($slug, $id)) {
            $slug = $base . "-" . $n++;
        }
        return $slug;
    }
    public function create(array $data, ?array $file = null): int
    {
        $clean = $this->validate($data);
        $clean["slug"] = $this->slug($clean["name"]);
        $clean["image_url"] = $this->uploads->optionalImage($file);
        try {
            return $this->products->create($clean);
        } catch (\Throwable $e) {
            $this->uploads->deleteImage($clean["image_url"]);
            throw $e;
        }
    }
    public function update(int $id, array $data, ?array $file = null): bool
    {
        $existing = $this->get($id);
        $clean = $this->validate($data);
        $clean["slug"] = $existing->slug ?: $this->slug($clean["name"], $id);
        $newImage = $this->uploads->optionalImage($file);
        $clean["image_url"] = $newImage ?? $existing->image_url;
        try {
            return $this->products->update($id, $clean);
        } catch (\Throwable $e) {
            $this->uploads->deleteImage($newImage);
            throw $e;
        }
    }
    public function delete(int $id): bool
    {
        $this->get($id);
        return $this->products->softDelete($id);
    }
}
