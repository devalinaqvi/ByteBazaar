<?php
namespace App\Controllers;
use App\Core\{BaseController, Request, HttpException, Validation};
use App\Services\ProductService;
class ProductController extends BaseController
{
    public function __construct(
        private ProductService $products,
        private Request $request,
    ) {}
    public function index(): void
    {
        $categories = $this->request->get("categories", []);
        if (!is_array($categories) || count($categories) > 50) {
            throw new HttpException(422, "Choose valid category filters.");
        }
        $categories = array_map(
            fn($v) => Validation::integer($v, 1, PHP_INT_MAX),
            $categories,
        );
        $q = $this->request->get("q", "");
        $sort = $this->request->get("sort", "newest");
        if (!is_string($q) || strlen($q) > 200 || !is_string($sort)) {
            throw new HttpException(422, "Invalid search.");
        }
        $this->render(
            "pages/public_products",
            array_merge(
                $this->products->catalog(
                    trim($q),
                    $categories,
                    max(1, (int) $this->request->get("page", 1)),
                    $sort,
                ),
                [
                    "title" => "Find your next upgrade",
                    "categories" => $this->products->categories(),
                    "selected" => $categories,
                    "q" => $q,
                    "sort" => $sort,
                ],
            ),
        );
    }
    public function show(string $slug): void
    {
        $product =
            $this->products->getBySlug($slug) ??
            throw new HttpException(404, "Product not found.");
        $this->render("pages/public_product_show", [
            "product" => $product,
            "title" => $product->name,
        ]);
    }
}
