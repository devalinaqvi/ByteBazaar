 <!-- Hero section -->
        <div class="relative">
            <!-- Background image and overlap -->
            <div aria-hidden="true" class="absolute inset-0 hidden sm:flex sm:flex-col">
                <div class="relative w-full flex-1 bg-gray-800">
                    <div class="absolute inset-0 overflow-hidden">
                        <img src="<?php echo returnBannerImage('default'); ?>" alt="" class="size-full object-cover" />
                    </div>
                    <div class="absolute inset-0 bg-gray-900 opacity-50"></div>
                </div>
                <div class="h-32 w-full bg-white md:h-40 lg:h-48"></div>
            </div>

            <div class="relative mx-auto max-w-3xl px-4 pb-96 text-center sm:px-6 sm:pb-0 lg:px-8">
                <!-- Background image and overlap -->
                <div aria-hidden="true" class="absolute inset-0 flex flex-col sm:hidden">
                    <div class="relative w-full flex-1 bg-gray-800">
                        <div class="absolute inset-0 overflow-hidden">
                            <img src="https://tailwindcss.com/plus-assets/img/ecommerce-images/home-page-04-hero-full-width.jpg" alt="" class="size-full object-cover" />
                        </div>
                        <div class="absolute inset-0 bg-gray-900 opacity-50"></div>
                    </div>
                    <div class="h-48 w-full bg-white"></div>
                </div>
                <div class="relative py-32">
                    <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl md:text-6xl">Computer Zone</h1>
                    <div class="mt-4 sm:mt-6">
                        <a href="<?php echo route('products') ?>" class="inline-block rounded-md border border-transparent bg-indigo-600 px-8 py-3 font-medium text-white hover:bg-indigo-700">Shop</a>
                    </div>
                </div>
            </div>

            <section aria-labelledby="collection-heading" class="relative -mt-96 sm:mt-0">
                <h2 id="collection-heading" class="sr-only">Collections</h2>
                <div class="mx-auto grid max-w-md grid-cols-1 gap-y-6 px-4 sm:max-w-7xl sm:grid-cols-3 sm:gap-x-6 sm:gap-y-0 sm:px-6 lg:gap-x-8 lg:px-8">
                    <?php if(isset($categories) && count($categories) > 0) : ?>
                    <?php foreach($categories as $index => $category) : ?>
                    <?php if($index >= 3) break; ?>
                    <div class="group relative h-96 rounded-lg bg-white shadow-xl sm:aspect-4/5 sm:h-auto">
                        <div aria-hidden="true" class="absolute inset-0 overflow-hidden rounded-lg">
                            <div class="absolute inset-0 overflow-hidden group-hover:opacity-75">
                                <img src="<?php echo mapCategoryImage($category->slug); ?>" alt="<?php echo $category->name; ?>" class="size-full object-cover" />
                            </div>
                            <div class="absolute inset-0 bg-linear-to-b from-transparent to-black opacity-50"></div>
                        </div>
                        <div class="absolute inset-0 flex items-end rounded-lg p-6">
                            <div>
                                <p aria-hidden="true" class="text-sm text-white">Shop</p>
                                <h3 class="mt-1 font-semibold text-white">
                                    <a href="<?php echo route('products', ['categories[]' => $category->id]) ?>">
                                        <span class="absolute inset-0"></span>
                                        <?php echo $category->name; ?>
                                    </a>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <section aria-labelledby="trending-heading">
            <div class="mx-auto max-w-7xl px-4 py-24 sm:px-6 sm:py-32 lg:px-8 lg:pt-32">
                <div class="md:flex md:items-center md:justify-between">
                    <h2 id="favorites-heading" class="text-2xl font-bold tracking-tight text-gray-900">Trending Products</h2>
                    <a href="<?php echo route('products') ?>" class="hidden text-sm font-medium text-indigo-600 hover:text-indigo-500 md:block">
                        Shop
                        <span aria-hidden="true"> &rarr;</span>
                    </a>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-x-4 gap-y-10 sm:gap-x-6 md:grid-cols-4 md:gap-y-0 lg:gap-x-8">
                    <?php if(isset($products) && count($products) > 0) : ?>
                    <?php foreach($products as $product) : ?>
                    <div class="group relative">
                        <div class="h-56 w-full overflow-hidden rounded-md group-hover:opacity-75 lg:h-72 xl:h-80">
                            <img src="<?php echo e($product->image_url); ?>" alt="<?php echo e($product->name); ?>" class="size-full object-cover" />
                        </div>
                        <h3 class="mt-4 text-sm text-gray-700">
                            <a href="<?php echo e(route('product/'.$product->slug)); ?>">
                                <span class="absolute inset-0"></span>
                                <?php echo e($product->name); ?>
                            </a>
                        </h3>
                        <p class="mt-1 text-sm font-medium text-gray-900">$<?php echo e(number_format($product->price, 2)); ?></p>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="mt-8 text-sm md:hidden">
                    <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Shop the collection
                        <span aria-hidden="true"> &rarr;</span>
                    </a>
                </div>
            </div>
        </section>

        <section aria-labelledby="perks-heading" class="border-t border-gray-200 bg-gray-50">
            <h2 id="perks-heading" class="sr-only">Our perks</h2>

            <div class="mx-auto max-w-7xl px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
                <div class="grid grid-cols-1 gap-y-12 sm:grid-cols-2 sm:gap-x-6 lg:grid-cols-4 lg:gap-x-8 lg:gap-y-0">
                    <div class="text-center md:flex md:items-start md:text-left lg:block lg:text-center">
                        <div class="md:shrink-0">
                            <div class="flow-root">
                                <img src="https://tailwindcss.com/plus-assets/img/ecommerce/icons/icon-returns-light.svg" alt="" class="mx-auto -my-1 h-24 w-auto" />
                            </div>
                        </div>
                        <div class="mt-6 md:mt-0 md:ml-4 lg:mt-6 lg:ml-0">
                            <h3 class="text-base font-medium text-gray-900">Free returns</h3>
                            <p class="mt-3 text-sm text-gray-500">Not what you expected? Place it back in the parcel and attach the pre-paid postage stamp.</p>
                        </div>
                    </div>
                    <div class="text-center md:flex md:items-start md:text-left lg:block lg:text-center">
                        <div class="md:shrink-0">
                            <div class="flow-root">
                                <img src="https://tailwindcss.com/plus-assets/img/ecommerce/icons/icon-calendar-light.svg" alt="" class="mx-auto -my-1 h-24 w-auto" />
                            </div>
                        </div>
                        <div class="mt-6 md:mt-0 md:ml-4 lg:mt-6 lg:ml-0">
                            <h3 class="text-base font-medium text-gray-900">Same day delivery</h3>
                            <p class="mt-3 text-sm text-gray-500">We offer a delivery service that has never been done before. Checkout today and receive your products within hours.</p>
                        </div>
                    </div>
                    <div class="text-center md:flex md:items-start md:text-left lg:block lg:text-center">
                        <div class="md:shrink-0">
                            <div class="flow-root">
                                <img src="https://tailwindcss.com/plus-assets/img/ecommerce/icons/icon-gift-card-light.svg" alt="" class="mx-auto -my-1 h-24 w-auto" />
                            </div>
                        </div>
                        <div class="mt-6 md:mt-0 md:ml-4 lg:mt-6 lg:ml-0">
                            <h3 class="text-base font-medium text-gray-900">All year discount</h3>
                            <p class="mt-3 text-sm text-gray-500">Looking for a deal? You can use the code &quot;ALLYEAR&quot; at checkout and get money off all year round.</p>
                        </div>
                    </div>
                    <div class="text-center md:flex md:items-start md:text-left lg:block lg:text-center">
                        <div class="md:shrink-0">
                            <div class="flow-root">
                                <img src="https://tailwindcss.com/plus-assets/img/ecommerce/icons/icon-planet-light.svg" alt="" class="mx-auto -my-1 h-24 w-auto" />
                            </div>
                        </div>
                        <div class="mt-6 md:mt-0 md:ml-4 lg:mt-6 lg:ml-0">
                            <h3 class="text-base font-medium text-gray-900">For the planet</h3>
                            <p class="mt-3 text-sm text-gray-500">We’ve pledged 1% of sales to the preservation and restoration of the natural environment.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
