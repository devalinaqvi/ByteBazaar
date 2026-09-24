<?php
namespace App\Controllers;
use App\Core\{BaseController, Request, HttpException};
use App\Services\CategoryService;
class CategoryController extends BaseController
{
    public function __construct(
        private CategoryService $categories,
        private Request $request,
    ) {}
    public function index(): void
    {
        $this->render("pages/admin/admin_categories", [
            "title" => "Categories",
            "is_admin" => true,
            "categories" => $this->categories->list(),
        ]);
    }
    public function create(): void
    {
        $this->render("pages/admin/category_form", [
            "title" => "Add category",
            "is_admin" => true,
            "category" => null,
        ]);
    }
    public function edit(int $id): void
    {
        $category =
            $this->categories->get($id) ??
            throw new HttpException(404, "Category not found.");
        $this->render("pages/admin/category_form", [
            "title" => "Edit category",
            "is_admin" => true,
            "category" => $category,
        ]);
    }
    public function store(): void
    {
        $this->categories->create($this->request->all());
        $this->done();
    }
    public function update(int $id): void
    {
        if (!$this->categories->get($id)) {
            throw new HttpException(404, "Category not found.");
        }
        $this->categories->update($id, $this->request->all());
        $this->done();
    }
    public function delete(int $id): void
    {
        $this->categories->delete($id);
        $this->done();
    }
    private function done(): void
    {
        if (wants_json()) {
            $this->json([
                "success" => true,
                "message" => "Categories updated.",
                "redirect" => url_path("admin/categories"),
            ]);
        } else {
            $this->redirect("/admin/categories");
        }
    }
}
