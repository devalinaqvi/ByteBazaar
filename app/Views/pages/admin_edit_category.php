<div class="mx-auto container px-4 py-6 sm:px-6 lg:px-8">
    <div class="divide-y divide-gray-900/10">
        <div class="grid grid-cols-1 gap-x-8 gap-y-8 py-10 md:grid-cols-3">
            <div class="px-4 sm:px-0">
                <h2 class="text-base/7 font-semibold text-gray-900">Edit Category</h2>
                <p class="mt-1 text-sm/6 text-gray-600">Update the category information.</p>
            </div>

            <form method="POST" action="/admin/categories/<?= $category->id ?>" class="bg-white shadow-xs outline outline-gray-900/5 sm:rounded-xl md:col-span-2">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <div class="px-4 py-6 sm:p-8">
                    <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                        <div class="sm:col-span-4">
                            <label for="name" class="block text-sm/6 font-medium text-gray-900">Category Name</label>
                            <div class="mt-2">
                                <input id="name" type="text" name="name" value="<?= htmlspecialchars($category->name) ?>" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-x-6 border-t border-gray-900/10 px-4 py-4 sm:px-8">
                    <a href="/admin/categories" class="text-sm/6 font-semibold text-gray-900">Cancel</a>
                    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
