<div class="mx-auto container px-4 py-6 sm:px-6 lg:px-8">
    <!-- Your content -->
    <?php
    if(empty($orders)) {
        ?>
        <div class="text-center">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true" class="mx-auto size-12 text-gray-400">
                <path d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" stroke-width="2" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <h3 class="mt-2 text-sm font-semibold text-gray-900">No orders</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new order.</p>
            <div class="mt-6">
                <button type="button" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 cursor-pointer">
                    <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="mr-1.5 -ml-0.5 size-5">
                        <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                    </svg>
                    New Order
                </button>
            </div>
        </div>

        <?php
    } else {
    ?>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-base font-semibold text-gray-900">Orders</h1>
                <p class="mt-2 text-sm text-gray-700">A list of all the orders in your account including their details, status, and customer information.</p>
            </div>
        </div>
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-hidden shadow-sm outline-1 outline-black/5 sm:rounded-lg">
                        <table class="relative min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6">Order ID</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Customer Email</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Phone</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total</th>
                                <th scope="col" class="py-3.5 pr-4 pl-3 sm:pr-6">
                                    <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6"><?= $order->id ?></td>
                                <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500"><?= $order->customer_email ?></td>
                                <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500"><?= $order->phone ?></td>
                                <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500"><?= $order->total_price ?></td>
                                <td class="py-4 pr-4 pl-3 text-right sm:pr-6">
                                    <a href="<?= base_url('admin/order/'.$order->id) ?>" class="text-indigo-600 hover:text-indigo-900">View</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
