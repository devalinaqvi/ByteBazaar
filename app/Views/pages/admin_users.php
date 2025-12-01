<div class="mx-auto container px-4 py-6 sm:px-6 lg:px-8">
    <!-- Your content -->
    <?php
    if(!empty($users)) {
        ?>
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-base font-semibold text-gray-900">Users</h1>
                    <p class="mt-2 text-sm text-gray-700">A list of all the users.</p>
                </div>
            </div>
            <div class="mt-8 flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <div class="overflow-hidden shadow-sm outline-1 outline-black/5 sm:rounded-lg">
                            <table class="relative min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6">User ID</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Name</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Role</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6"><?= $user->id ?></td>
                                        <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500"><?= $user->name ?></td>
                                        <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500"><?= $user->email ?></td>
                                        <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500"><?php echo $user->is_admin ? 'Admin' : 'User'; ?></td>
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
