    /**
     * Reusable Form Handler for CRUD Operations
     * Usage: new FormHandler('#formId', options).init();
     */
    class FormHandler {
        constructor(formSelector, options = {}) {
            this.form = $(formSelector);
            this.options = {
                method: 'POST',
                submitUrl: null,
                redirectUrl: null,
                successMessage: 'Operation completed successfully!',
                errorMessage: 'An error occurred',
                onSuccess: null,
                onError: null,
                ...options
            };
            this.isSubmitting = false;
        }

        init() {
            if (!this.form.length) {
                console.error('Form not found:', this.form.selector);
                return;
            }

            this.form.on('submit', (e) => this.handleSubmit(e));
            this.setupFileInputs();
            this.setupDragDrop();

            return this;
        }

        handleSubmit(e) {
            e.preventDefault();

            // Prevent double submission
            if (this.isSubmitting) {
                return;
            }
            this.isSubmitting = true;

            const formData = new FormData(this.form[0]);
            const $submitBtn = this.form.find('button[type="submit"]');
            const $loading = $submitBtn.find('.loading');
            const $btnText = $submitBtn.find('.btn-text');

            // Show loading state
            $submitBtn.prop('disabled', true);
            $loading.addClass('show');
            $btnText.text('Processing...');

            // Get submit URL
            const submitUrl = this.options.submitUrl || this.form.attr('action');

            if (!submitUrl) {
                console.error('No submit URL provided');
                this.resetForm($submitBtn, $loading, $btnText);
                return;
            }

            $.ajax({
                type: this.options.method,
                url: submitUrl,
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: (response) => this.handleSuccess(response, $submitBtn, $loading, $btnText),
                error: (xhr) => this.handleError(xhr, $submitBtn, $loading, $btnText),
                complete: () => this.completeRequest($submitBtn, $loading, $btnText)
            });
        }

        handleSuccess(response, $submitBtn, $loading, $btnText) {
            if (response.success) {
                const message = response.message || this.options.successMessage;
                showToast(message, 'success');

                // Execute custom callback if provided
                if (typeof this.options.onSuccess === 'function') {
                    this.options.onSuccess(response);
                    return;
                }

                // Default redirect or reload
                if (this.options.redirectUrl) {
                    setTimeout(() => {
                        location.href = this.options.redirectUrl;
                    }, 1500);
                } else {
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
            } else {
                showToast(response.message || this.options.errorMessage, 'error');
                this.isSubmitting = false;
            }
        }

        handleError(xhr, $submitBtn, $loading, $btnText) {
            let errorMessage = this.options.errorMessage;

            try {
                const response = JSON.parse(xhr.responseText);
                errorMessage = response.message || errorMessage;
            } catch (e) {
                errorMessage = xhr.statusText || errorMessage;
            }

            showToast(errorMessage, 'error');

            // Execute custom error callback if provided
            if (typeof this.options.onError === 'function') {
                this.options.onError(xhr);
            }

            this.isSubmitting = false;
        }

        completeRequest($submitBtn, $loading, $btnText) {
            this.resetForm($submitBtn, $loading, $btnText);
        }

        resetForm($submitBtn, $loading, $btnText) {
            $submitBtn.prop('disabled', false);
            $loading.removeClass('show');
            $btnText.text($btnText.data('original-text') || 'Submit');
        }

        setupFileInputs() {
            this.form.find('input[type="file"]').on('change', function() {
                const fileName = this.files[0]?.name;
                if (fileName) {
                    showToast('File selected: ' + fileName, 'success');
                }
            });
        }

        setupDragDrop() {
            const fileUploadAreas = this.form.find('.file-upload');

            fileUploadAreas.on('dragover', function(e) {
                e.preventDefault();
                $(this).css('border-color', '#4f46e5');
            });

            fileUploadAreas.on('dragleave', function(e) {
                $(this).css('border-color', '#d1d5db');
            });

            fileUploadAreas.on('drop', (e) => {
                e.preventDefault();
                const fileUpload = $(e.target).closest('.file-upload');
                fileUpload.css('border-color', '#d1d5db');

                const files = e.originalEvent.dataTransfer.files;
                if (files.length) {
                    const fileInput = this.form.find('input[type="file"]').first();
                    fileInput[0].files = files;
                    showToast('File selected: ' + files[0].name, 'success');
                }
            });
        }
    }

    /**
     * Toast Notification Function
     */
    function showToast(message, type = 'success') {
        const toastContainer = $('#toastContainer');
        const icon = type === 'success' ? '✓' : '✕';

        const toast = $(`
        <div class="toast ${type}">
            <div class="icon">${icon}</div>
            <div class="message">${escapeHtml(message)}</div>
            <div class="close">&times;</div>
        </div>
    `);

        toastContainer.append(toast);

        toast.find('.close').click(function() {
            toast.fadeOut(300, function() {
                $(this).remove();
            });
        });

        setTimeout(function() {
            toast.fadeOut(300, function() {
                $(this).remove();
            });
        }, 4000);
    }

    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    /**
     * Initialize Forms
     * Document Ready
     */
    $(document).ready(function() {
        // Create Product Form
        if ($('#productForm').length) {
            new FormHandler('#productForm', {
                submitUrl: '/admin/products/create',
                redirectUrl: '/admin/products',
                successMessage: 'Product created successfully!',
                errorMessage: 'Failed to create product'
            }).init();
        }

        // Edit Product Form
        if ($('#editProductForm').length) {
            const productId = $('input[name="product_id"]').val();
            new FormHandler('#editProductForm', {
                submitUrl: `/admin/products/${productId}`,
                redirectUrl: '/admin/products',
                successMessage: 'Product updated successfully!',
                errorMessage: 'Failed to update product'
            }).init();
        }

        // Add to Cart Form
        document.querySelectorAll('form[id^="addToCartForm-"]').forEach(form => {
            const productId = form.querySelector('input[name="product_id"]').value;

            new FormHandler(`#addToCartForm-${productId}`, {
                submitUrl: `/cart/add/${productId}`,
                successMessage: 'Product added to cart successfully!',
                errorMessage: 'Failed to add product to cart'
            }).init();
        });

        // Delete Product (Example for other CRUD operations)
        $('.delete-product-btn').on('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this product?')) {
                const productId = $(this).data('product-id');
                $.ajax({
                    type: 'POST',
                    url: `/admin/products/${productId}/delete`,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showToast('Product deleted successfully!', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showToast(response.message || 'Failed to delete product', 'error');
                        }
                    },
                    error: function() {
                        showToast('An error occurred', 'error');
                    }
                });
            }
        });

        // Add Category Form (Example)
        if ($('#categoryForm').length) {
            new FormHandler('#categoryForm', {
                submitUrl: '/admin/categories/create',
                redirectUrl: '/admin/categories',
                successMessage: 'Category created successfully!',
                errorMessage: 'Failed to create category'
            }).init();
        }

        // Edit Category Form (Example)
        if ($('#editCategoryForm').length) {
            const categoryId = $('input[name="category_id"]').val();
            new FormHandler('#editCategoryForm', {
                submitUrl: `/admin/categories/${categoryId}`,
                redirectUrl: '/admin/categories',
                successMessage: 'Category updated successfully!',
                errorMessage: 'Failed to update category'
            }).init();
        }
    });

    // File input label update
    $('#image').on('change', function() {
        const fileName = this.files[0]?.name;
        if (fileName) {
            showToast('File selected: ' + fileName, 'success');
        }
    });

    // Drag and drop
    const fileUpload = $('.file-upload');
    fileUpload.on('dragover', function(e) {
        e.preventDefault();
        $(this).css('border-color', '#4f46e5');
    });

    fileUpload.on('dragleave', function(e) {
        $(this).css('border-color', '#d1d5db');
    });

    fileUpload.on('drop', function(e) {
        e.preventDefault();
        $(this).css('border-color', '#d1d5db');
        const files = e.originalEvent.dataTransfer.files;
        if (files.length) {
            $('#image')[0].files = files;
            showToast('File selected: ' + files[0].name, 'success');
        }
    });

    $(document).on('click', '.add-to-cart', function () {
        const productId = $(this).data('id');

        $.post('/cart/add/' + productId, {
            qty: 1,
            product_id: productId
        }, function (res) {
            const data = JSON.parse(res);
            showToast(data.message, 'success');
        });
    });

    $(document).on('change', '.qty', function () {
        $.post('/cart/update', {
            id: $(this).data('id'),
            qty: $(this).val()
        }, function (res) {
            console.log("Quantity updated.");
        });
    });

    $(document).on('click', '.remove', function () {
        const itemId = $(this).data('id');

        $.post('/cart/remove/' + itemId, {}, function () {
            location.reload();
        });
    });

