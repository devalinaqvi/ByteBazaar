<!-- Mobile menu -->
<el-dialog>
    <dialog id="mobile-menu" class="backdrop:bg-transparent lg:hidden">
        <el-dialog-backdrop class="fixed inset-0 bg-black/25 transition-opacity duration-300 ease-linear data-closed:opacity-0"></el-dialog-backdrop>
        <div tabindex="0" class="fixed inset-0 flex focus:outline-none">
            <el-dialog-panel class="relative flex w-full max-w-xs transform flex-col overflow-y-auto bg-white pb-12 shadow-xl transition duration-300 ease-in-out data-closed:-translate-x-full">
                <div class="flex px-4 pt-5 pb-2">
                    <button type="button" command="close" commandfor="mobile-menu" class="relative -m-2 inline-flex items-center justify-center rounded-md p-2 text-gray-400">
                        <span class="absolute -inset-0.5"></span>
                        <span class="sr-only">Close menu</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6">
                            <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>

                <!-- Links -->

                <div class="space-y-6 border-t border-gray-200 px-4 py-6">
                    <div class="flow-root">
                        <a href="<?php echo route('register') ?>" class="-m-2 block p-2 font-medium text-gray-900">Create an account</a>
                    </div>
                    <div class="flow-root">
                        <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) : ?>
                            <a href="<?php echo route('admin') ?>" class="-m-2 block p-2 font-medium text-gray-900">Dashboard</a>
                        <?php endif; ?>
                        <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 0) : ?>
                            <a href="<?php echo route('user/dashboard') ?>" class="-m-2 block p-2 font-medium text-gray-900">Dashboard</a>
                        <?php endif; ?>
                        <a href="<?php echo route('login') ?>" class="-m-2 block p-2 font-medium text-gray-900">Sign in</a>
                    </div>
                </div>

                <div class="space-y-6 border-t border-gray-200 px-4 py-6">
                    <!-- Currency selector -->
                    <form>
                        <div class="-ml-2 inline-grid grid-cols-1">
                            <select id="mobile-currency" name="currency" aria-label="Currency" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-0.5 pr-7 pl-2 text-base font-medium text-gray-700 group-hover:text-gray-800 focus:outline-2 sm:text-sm/6">
                                <option value="USD">USD</option>
                            </select>
                            <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="pointer-events-none col-start-1 row-start-1 mr-1 size-5 self-center justify-self-end fill-gray-500">
                                <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                            </svg>
                        </div>
                    </form>
                </div>
            </el-dialog-panel>
        </div>
    </dialog>
</el-dialog>

<header class="relative">
    <nav aria-label="Top">
        <!-- Top navigation -->
        <div class="bg-gray-900">
            <div class="mx-auto flex h-10 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <!-- Currency selector -->
                <form>
                    <div class="-ml-2 inline-grid grid-cols-1">
                        <select id="desktop-currency" name="currency" aria-label="Currency" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-gray-900 py-0.5 pr-7 pl-2 text-left text-base font-medium text-white focus:outline-2 focus:-outline-offset-1 focus:outline-white sm:text-sm/6">
                            <option value="USD">USD</option>
                        </select>
                        <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="pointer-events-none col-start-1 row-start-1 mr-1 size-5 self-center justify-self-end fill-gray-300">
                            <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                        </svg>
                    </div>
                </form>

                <div class="flex items-center space-x-6">
                    <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) : ?>
                        <a href="<?php echo route('admin') ?>" class="text-sm font-medium text-white hover:text-gray-100">Dashboard</a>
                    <?php endif; ?>
                    <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 0) : ?>
                        <a href="<?php echo route('user/dashboard') ?>" class="text-sm font-medium text-white hover:text-gray-100">Dashboard</a>
                    <?php endif; ?>
                    <?php if(isset($_SESSION['user_id'])) : ?>
                        <a href="<?php echo route('logout') ?>" class="text-sm font-medium text-white hover:text-gray-100">Sign out</a>
                    <?php endif; ?>
                    <?php if(!isset($_SESSION['user_id'])) : ?>
                        <a href="<?php echo route('login') ?>" class="text-sm font-medium text-white hover:text-gray-100">Sign in</a>
                        <a href="<?php echo route('register') ?>" class="text-sm font-medium text-white hover:text-gray-100">Create an account</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Secondary navigation -->
        <div class="bg-white shadow-xs">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <!-- Logo (lg+) -->
                    <div class="hidden lg:flex lg:flex-1 lg:items-center">
                        <a href="/">
                            <span class="sr-only">Your Company</span>
                            <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=600" alt="" class="h-8 w-auto" />
                        </a>
                    </div>

                    <!-- Mobile menu and search (lg-) -->
                    <div class="flex flex-1 items-center lg:hidden">
                        <button type="button" command="show-modal" commandfor="mobile-menu" class="-ml-2 rounded-md bg-white p-2 text-gray-400">
                            <span class="sr-only">Open menu</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6">
                                <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>

                    </div>

                    <!-- Logo (lg-) -->
                    <a href="/" class="lg:hidden">
                        <span class="sr-only">Your Company</span>
                        <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=600" alt="" class="h-8 w-auto" />
                    </a>

                    <div class="flex flex-1 items-center justify-end">
                        <a href="<?php echo route('/') ?>" class="hidden text-sm font-medium text-gray-700 hover:text-gray-800 lg:block">Home</a>

                        <div class="flex items-center lg:ml-8">
                            <a href="<?php echo route('products') ?>" class="hidden text-sm font-medium text-gray-700 hover:text-gray-800 lg:block">Shop</a>

                            <!-- Cart -->
                            <div class="ml-4 flow-root lg:ml-8">
                                <a href="<?php echo route('cart') ?>" class="group -m-2 flex items-center p-2">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0 text-gray-400 group-hover:text-gray-500">
                                        <path d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <span id="cart-count" class="ml-2 text-sm font-medium text-gray-700 group-hover:text-gray-800"><?php echo $_SESSION['cart_count'] ?? 0; ?></span>
                                    <span class="sr-only">items in cart, view bag</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>