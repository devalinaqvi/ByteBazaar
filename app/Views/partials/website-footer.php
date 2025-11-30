<footer aria-labelledby="footer-heading" class="bg-gray-50">
    <h2 id="footer-heading" class="sr-only">Footer</h2>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="border-t border-gray-200 py-20">
            <div class="grid grid-cols-1 md:grid-flow-col md:auto-rows-min md:grid-cols-12 md:gap-x-8 md:gap-y-16">
                <!-- Image section -->
                <div class="col-span-1 md:col-span-2 lg:col-start-1 lg:row-start-1">
                    <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=600" alt="" class="h-8 w-auto" />
                </div>

                <!-- Sitemap sections -->
                <div class="col-span-6 mt-10 grid grid-cols-2 gap-8 sm:grid-cols-3 md:col-span-8 md:col-start-3 md:row-start-1 md:mt-0 lg:col-span-6 lg:col-start-2">
                    <div class="grid grid-cols-1 gap-y-12 sm:col-span-2 sm:grid-cols-2 sm:gap-x-8">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Links</h3>
                            <ul role="list" class="mt-6 space-y-6">
                                <li class="text-sm">
                                    <a href="<?php echo route('/'); ?>" class="text-gray-500 hover:text-gray-600">Home</a>
                                </li>
                                <li class="text-sm">
                                    <a href="<?php echo route('products'); ?>" class="text-gray-500 hover:text-gray-600">Shop</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100 py-10 text-center">
            <p class="text-sm text-gray-500">&copy; <?php echo date('Y'); ?> Byte Bazaar, Inc. All rights reserved.</p>
        </div>
    </div>
</footer>