<div class="bg-white">
    <div class="mx-auto max-w-2xl px-4 pt-16 pb-24 sm:px-6 lg:max-w-7xl lg:px-8">
        <h2 class="sr-only">Checkout</h2>

        <form id="checkout-form" method="POST" action="/checkout" class="lg:grid lg:grid-cols-2 lg:gap-x-12 xl:gap-x-16">
            <input type="hidden" name="csrf_token" value="<?= csrf_hash() ?>" />

            <div>
                <div>
                    <h2 class="text-lg font-medium text-gray-900">Contact information</h2>

                    <div class="mt-4">
                        <label for="email-address" class="block text-sm/6 font-medium text-gray-700">Email address</label>
                        <div class="mt-2">
                            <input
                                    id="email-address"
                                    type="email"
                                    name="customer_email"
                                    required
                                    autocomplete="email"
                                    placeholder="Enter your email address"
                                    class="block w-full rounded-md bg-white px-3 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                        </div>
                    </div>
                </div>

                <div class="mt-10 border-t border-gray-200 pt-10">
                    <h2 class="text-lg font-medium text-gray-900">Shipping information</h2>

                    <div class="mt-4 grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                        <div class="sm:col-span-2">
                            <label for="customer-name" class="block text-sm/6 font-medium text-gray-700">Full name</label>
                            <div class="mt-2">
                                <input
                                        id="customer-name"
                                        type="text"
                                        name="customer_name"
                                        required
                                        placeholder="Enter your full name"
                                        autocomplete="name"
                                        class="block w-full rounded-md bg-white px-3 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="address" class="block text-sm/6 font-medium text-gray-700">Address</label>
                            <div class="mt-2">
                                <input
                                        id="address"
                                        type="text"
                                        name="shipping_address"
                                        required
                                        placeholder="Enter your address"
                                        autocomplete="street-address"
                                        class="block w-full rounded-md bg-white px-3 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                            </div>
                        </div>

                        <div>
                            <label for="city" class="block text-sm/6 font-medium text-gray-700">City</label>
                            <div class="mt-2">
                                <input
                                        id="city"
                                        type="text"
                                        name="shipping_city"
                                        required
                                        placeholder="Enter your city"
                                        autocomplete="address-level2"
                                        class="block w-full rounded-md bg-white px-3 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                            </div>
                        </div>

                        <div>
                            <label for="country" class="block text-sm/6 font-medium text-gray-700">Country</label>
                            <div class="mt-2 grid grid-cols-1">
                                <select
                                        id="country"
                                        name="shipping_country"
                                        required
                                        autocomplete="country-name"
                                        class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-2 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    <option value="">Select Country</option>
                                    <option value="US">United States</option>
                                    <option value="CA">Canada</option>
                                    <option value="MX">Mexico</option>
                                </select>
                                <svg viewBox="0 0 16 16" fill="currentColor" data-slot="icon" aria-hidden="true" class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-500 sm:size-4">
                                    <path d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                                </svg>
                            </div>
                        </div>

                        <div class="col-span-2">
                            <label for="postal-code" class="block text-sm/6 font-medium text-gray-700">Postal code</label>
                            <div class="mt-2">
                                <input
                                        id="postal-code"
                                        type="text"
                                        name="shipping_postal_code"
                                        required
                                        placeholder="Enter your postal code"
                                        autocomplete="postal-code"
                                        class="block w-full rounded-md bg-white px-3 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="phone" class="block text-sm/6 font-medium text-gray-700">Phone</label>
                            <div class="mt-2">
                                <input
                                        id="phone"
                                        type="tel"
                                        name="phone"
                                        required
                                        placeholder="Enter your phone number"
                                        autocomplete="tel"
                                        class="block w-full rounded-md bg-white px-3 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order summary -->
            <div class="mt-10 lg:mt-0">
                <h2 class="text-lg font-medium text-gray-900">Order summary</h2>

                <div class="mt-4 rounded-lg border border-gray-200 bg-white shadow-xs">
                    <h3 class="sr-only">Items in your cart</h3>
                    <ul role="list" class="divide-y divide-gray-200" id="cart-items">
                        <!-- JavaScript will populate Cart items -->
                    </ul>
                    <dl class="space-y-6 border-t border-gray-200 px-4 py-6 sm:px-6">
                        <div class="flex items-center justify-between">
                            <dt class="text-sm">Subtotal</dt>
                            <dd class="text-sm font-medium text-gray-900" id="subtotal">$0.00</dd>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-200 pt-6">
                            <dt class="text-base font-medium">Total</dt>
                            <dd class="text-base font-medium text-gray-900" id="total">$0.00</dd>
                        </div>
                    </dl>

                    <div class="border-t border-gray-200 px-4 py-6 sm:px-6">
                        <button
                                type="submit"
                                class="w-full rounded-md border border-transparent bg-indigo-600 px-4 py-3 text-base font-medium text-white shadow-xs hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-50 focus:outline-hidden disabled:opacity-50 disabled:cursor-not-allowed"
                                id="submit-btn">
                            Confirm order
                        </button>
                    </div>
                </div>
                <div class="mt-10 border-t border-gray-200 pt-10">
                    <fieldset>
                        <legend class="text-lg font-medium text-gray-900">Payment method</legend>
                        <div class="mt-4 grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                            <label aria-label="Cash on Delivery" class="group relative flex rounded-lg border border-gray-300 bg-white p-4 has-checked:outline-2 has-checked:-outline-offset-2 has-checked:outline-indigo-600 has-focus-visible:outline-3 has-focus-visible:-outline-offset-1 has-disabled:border-gray-400 has-disabled:bg-gray-200 has-disabled:opacity-25">
                                <input
                                        type="radio"
                                        name="payment_method"
                                        value="cod"
                                        checked
                                        class="absolute inset-0 appearance-none focus:outline-none" />
                                <div class="flex-1">
                                    <span class="block text-sm font-medium text-gray-900">Cash on Delivery (COD)</span>
                                    <span class="mt-1 block text-sm text-gray-500">Pay when you receive your order</span>
                                </div>
                                <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="invisible size-5 text-indigo-600 group-has-checked:visible">
                                    <path d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" fill-rule="evenodd" />
                                </svg>
                            </label>
                        </div>
                    </fieldset>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Fetch and display cart items
    async function loadCartItems() {
        try {
            const cartResponse = await fetch('api/cart', {
                headers: { 'Accept': 'application/json' }
            });

            console.log(cartResponse.ok);

            if (!cartResponse.ok) return;

            const cartData = await cartResponse.json();
            const items = cartData.items || [];

            console.log(items);

            let subtotal = 0;
            let cartItemsHtml = '';

            items.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;

                cartItemsHtml += `
                <li class="flex px-4 py-6 sm:px-6">
                    <div class="shrink-0">
                        <img src="${item.image || '/assets/images/placeholder.png'}" alt="${item.product_name}" class="w-20 rounded-md" />
                    </div>
                    <div class="ml-6 flex flex-1 flex-col">
                        <div class="flex">
                            <div class="min-w-0 flex-1">
                                <h4 class="text-sm">
                                    <a href="/product/${item.product_slug}" class="font-medium text-gray-700 hover:text-gray-800">${item.product_name}</a>
                                </h4>
                            </div>
                        </div>
                        <div class="flex flex-1 items-end justify-between pt-2">
                            <p class="text-sm font-medium text-gray-900">$${item.price.toFixed(2)}</p>
                            <p class="text-sm text-gray-500">Qty: ${item.quantity}</p>
                        </div>
                    </div>
                </li>
            `;
            });

            document.getElementById('cart-items').innerHTML = cartItemsHtml;

            // Calculate totals
            const shipping = 0.00;
            const taxRate = 0.00;
            const taxes = (subtotal + shipping) * taxRate;
            const total = subtotal;

            document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
            // document.getElementById('taxes').textContent = `$${taxes.toFixed(2)}`;
            document.getElementById('total').textContent = `$${total.toFixed(2)}`;

        } catch (error) {
            console.error('Error loading cart:', error);
        }
    }

    // Handle form submission
    document.getElementById('checkout-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;

        const formData = new FormData(e.target);

        try {
            const response = await fetch('/checkout', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showToast('Order placed successfully!', 'success');
                setTimeout(() => {
                    window.location.href = `/`;
                }, 1500);
            } else {
                showToast(data.message || 'Failed to place order', 'error');
                submitBtn.disabled = false;
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Failed to place order', 'error');
            submitBtn.disabled = false;
        }
    });

    // Load cart on page load
    loadCartItems();

    function showToast(message, type = 'info') {
        console.log(`[${type.toUpperCase()}] ${message}`);
        // Implement your toast library here
        alert(`[${type.toUpperCase()}] ${message}`);
    }
</script>