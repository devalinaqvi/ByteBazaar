<?php
class ProductServiceTest extends StoreTestCase
{
    public function testUniqueSlugsAndImagePreserved(): void
    {
        $r = new App\Repositories\ProductRepository($this->db);
        $s = new App\Services\ProductService(
            $r,
            new App\Services\FileUploadService(),
            new App\Services\CategoryService(
                new App\Repositories\CategoryRepository($this->db),
            ),
        );
        $data = [
            "name" => "Laptop",
            "description" => "Example",
            "price" => "199.99",
            "stock" => 5,
            "category_id" => 1,
        ];
        $id = $s->create($data);
        $this->assertSame("laptop-2", $r->find($id)->slug);
        $this->db->exec(
            "UPDATE products SET image_url='/assets/example.png' WHERE id=$id",
        );
        $s->update($id, $data);
        $this->assertSame("/assets/example.png", $r->find($id)->image_url);
    }
    public function testDisguisedScriptIsNotAnImage(): void
    {
        $file = tempnam(sys_get_temp_dir(), "upload-test");
        file_put_contents($file, '<?php echo "bad";');
        try {
            App\Services\FileUploadService::imageExtension($file);
            $this->fail("Accepted script");
        } catch (App\Core\HttpException $e) {
            $this->assertSame(422, $e->status);
        } finally {
            unlink($file);
        }
    }
}
