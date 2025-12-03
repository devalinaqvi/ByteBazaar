<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Request;
use App\Services\CategoryService;

class CategoryController extends BaseController
{
    private Request $request;
    public function __construct(private readonly CategoryService $categories, Request $request)
    {
        $this->request = $request;
        parent::__construct();
    }

    public function index(): void
    {
        $list = $this->categories->list();

        $this->render('pages/admin_categories', [
            'title' => 'Categories',
            'body_class' => 'h-full',
            'html_class' => 'h-full bg-gray-100',
            'header' => true,
            'is_admin' => true,
            'admin_nav_active' => 'active',
            'admin_footer_active' => 'active',
            'categories' => $list,
        ]);
    }

    public function create(): void
    {
        $this->render('pages/admin_add_category', [
            'title' => 'Add Category',
            'body_class' => 'h-full',
            'html_class' => 'h-full bg-gray-100',
            'header' => true,
            'is_admin' => true,
            'admin_nav_active' => 'active',
            'admin_footer_active' => 'active',
        ]);
    }

    public function store(): void
    {
        $data = $this->request->all();
        $this->categories->create($data);
        $this->redirect('/admin/categories');
    }

    public function edit(int $id): void
    {
        $category = $this->categories->get($id);

        $this->render('pages/admin_edit_category', [
            'title' => 'Edit Category',
            'body_class' => 'h-full',
            'html_class' => 'h-full bg-gray-100',
            'header' => true,
            'is_admin' => true,
            'admin_nav_active' => 'active',
            'admin_footer_active' => 'active',
            'category' => $category
        ]);
    }

    public function update(int $id): void
    {
        $data = $this->request->all();
        $this->categories->update($id, $data);
        $this->redirect('/admin/categories');
    }

    public function delete(int $id): void
    {
        $this->categories->delete($id);
        $this->redirect('/admin/categories');
    }
}
