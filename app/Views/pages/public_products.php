<!-- Include this script tag or install `@tailwindplus/elements` via npm: -->
<!-- <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script> -->
<div class="bg-white">
    <main class="mx-auto max-w-2xl px-4 lg:max-w-7xl lg:px-8">
        <div class="border-b border-gray-200 pt-24 pb-10">
            <h1 class="text-4xl font-bold tracking-tight text-gray-900">New</h1>
            <p class="mt-4 text-base text-gray-500">Checkout out the latest release of Basic Tees, new and improved with four openings!</p>
        </div>

        <div class="pt-12 pb-24 lg:grid lg:grid-cols-3 lg:gap-x-8 xl:grid-cols-4">
            <aside>
                <h2 class="sr-only">Filters</h2>

                <button type="button" command="show-modal" commandfor="mobile-filters" class="inline-flex items-center lg:hidden">
                    <span class="text-sm font-medium text-gray-700">Filters</span>
                    <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="ml-1 size-5 shrink-0 text-gray-400">
                        <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                    </svg>
                </button>

                <div class="flex">
                    <form method="GET" class="divide-y divide-gray-200">
                        <div class="py-10 first:pt-0 last:pb-0">
                            <fieldset>
                                <legend class="block text-sm font-medium text-gray-900">Category</legend>
                                <div class="space-y-3 pt-6">
                                    <?php if(isset($categories) && !empty($categories)) : ?>
                                        <?php foreach($categories as $category) : ?>
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-5 shrink-0 items-center">
                                                    <div class="group grid size-4 grid-cols-1">
                                                        <input
                                                                id="category-<?= $category->id ?>"
                                                                type="checkbox"
                                                                name="categories[]"
                                                                value="<?= $category->id ?>"
                                                                <?= in_array($category->id, $selected ?? []) ? 'checked' : '' ?>
                                                        />
                                                        <svg viewBox="0 0 14 14" fill="none" class="pointer-events-none col-start-1 size-3.5 stroke-white">
                                                            <path d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-checked:opacity-100" />
                                                        </svg>
                                                    </div>
                                                </div>

                                                <label for="category-<?= $category->id ?>" class="text-sm text-gray-600">
                                                    <?= $category->name ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </fieldset>
                        </div>
                        <div class="py-6 last:pb-0">
                            <button type="submit" class="w-full block text-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Apply Filters</button>
                        </div>
                    </form>
                </div>
            </aside>

            <section aria-labelledby="product-heading" class="mt-6 lg:col-span-2 lg:mt-0 xl:col-span-3">
                <h2 id="product-heading" class="sr-only">Products</h2>

                <div class="grid grid-cols-1 gap-y-4 sm:grid-cols-2 sm:gap-x-6 sm:gap-y-10 lg:gap-x-8 xl:grid-cols-3">
                    <?php if (!empty($products)) : ?>
                        <?php foreach ($products as $product) : ?>
                            <div class="group relative flex flex-col overflow-hidden rounded-lg border border-gray-200 bg-white">

                                <img src="<?= $product->image_url ?: '/assets/images/placeholder.png' ?>"
                                     alt="<?= htmlspecialchars($product->name) ?>"
                                     class="aspect-3/4 bg-gray-200 object-cover group-hover:opacity-75 sm:h-96" />

                                <div class="flex flex-1 flex-col space-y-2 p-4">

                                    <h3 class="text-sm font-medium text-gray-900">
                                        <a  href="/product/<?= htmlspecialchars($product->slug) ?>">
                                            <span aria-hidden="true" class="absolute inset-0"></span>
                                            <?= htmlspecialchars($product->name) ?>
                                        </a >
                                    </h3>

                                    <p class="text-sm text-gray-500">
                                        <?= htmlspecialchars(substr($product->description, 0, 80)) ?>...
                                    </p>

                                    <div class="flex flex-1 flex-col justify-end">
                                        <p class="text-base font-medium text-gray-900">
                                            $<?= number_format($product->price, 2) ?>
                                        </p>
                                    </div>

                                    <div class="relative z-40 border-t border-gray-200 pt-4">
                                            <input type="hidden" name="csrf_token" value="<?= csrf_hash() ?>" />
                                            <input type="hidden" name="quantity" value="1" />
                                            <input type="hidden" name="product_id" value="<?= $product->id ?>" />
                                            <button
                                                    type="button"
                                                    data-id="<?= $product->id ?>"
                                                    class="add-to-cart w-full rounded-full bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                                                Add to Cart
                                            </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
