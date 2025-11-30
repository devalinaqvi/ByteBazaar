<?php

use App\Services\ProductService;
use App\Repositories\ProductRepository;

class ProductServiceTest extends TestCase
{
    public function testSlugGenerated()
    {
        $repo = $this->createMock(ProductRepository::class);

        $service = new ProductService($repo);

        $method = new ReflectionMethod(ProductService::class, 'slugify');
        $method->setAccessible(true);

        $slug = $method->invokeArgs($service, ['My Product Name']);

        $this->assertEquals('my-product-name', $slug);
    }
}
