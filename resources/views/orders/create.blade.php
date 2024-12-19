<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 30px;
            color: #333;
        }

        .container {
            width: 70%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-size: 16px;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .product {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .remove-product {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .remove-product:hover {
            background-color: #ff1a1a;
        }

        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
            width: 100%;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        .cancel-button {
            background-color: #ccc;
            color: #333;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            text-align: center;
            display: inline-block;
            width: 100%;
        }

        .cancel-button:hover {
            background-color: #bbb;
        }

        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .add-product {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .add-product:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Create Order</h1>

        <form action="{{ route('orders.store') }}" method="POST">
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <!-- User Selection -->
            <div class="form-group">
                <label for="user_id">User:</label>
                <select name="user_id" id="user_id" >
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Products Section -->
            <div id="products">
                <div class="product" id="product-0">
                    <label for="name">Product Name:</label>
                    <input type="text" name="products[0][name]" >
                    
                    <label for="qty">Quantity:</label>
                    <input type="number" name="products[0][qty]" class="product-qty" min="1">
                    
                    <label for="amount">Amount:</label>
                    <input type="number" name="products[0][amount]" class="product-amount" step="0.01" min="1" >
                    
                    <label for="total">Total:</label>
                    <input type="number" name="products[0][total]" class="product-total" readonly>
                    
                </div>
            </div>

            <!-- Add More Products Button -->
            <button type="button" class="add-product" id="add-product-btn">Add More Products</button>

            <!-- Grand Total -->
            <div class="form-group">
                <label for="grand_total">Grand Total:</label>
                <input type="number" id="grand_total" name="grand_total" readonly>
            </div>

            <!-- Submit Button -->
            <button type="submit">Save Order</button>

            <!-- Cancel Button -->
            <div class="form-footer">
                <a href="{{ route('orders.index') }}" class="cancel-button">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let productIndex = 1;
            const productsContainer = document.getElementById('products');
            const addProductBtn = document.getElementById('add-product-btn');
            const grandTotalInput = document.getElementById('grand_total');

            // Function to calculate the total of a product
            function calculateProductTotal(productDiv) {
                const qty = productDiv.querySelector('.product-qty').value;
                const amount = productDiv.querySelector('.product-amount').value;
                const totalInput = productDiv.querySelector('.product-total');

                if (qty && amount) {
                    const total = (parseFloat(qty) * parseFloat(amount)).toFixed(2);
                    totalInput.value = total;
                    updateGrandTotal();  // Update grand total whenever a product's total is updated
                } else {
                    totalInput.value = '';  // If qty or amount is empty, set total to empty
                }
            }

            // Function to update the grand total
            function updateGrandTotal() {
                let grandTotal = 0;

                // Loop through each product to sum their totals
                const productTotals = document.querySelectorAll('.product-total');
                productTotals.forEach(function (totalInput) {
                    if (totalInput.value) {
                        grandTotal += parseFloat(totalInput.value);
                    }
                });

                grandTotalInput.value = grandTotal.toFixed(2);  // Display grand total with two decimals
            }

            // Add Product Fields
            function addProduct() {
                const newProductDiv = document.createElement('div');
                newProductDiv.classList.add('product');
                newProductDiv.id = `product-${productIndex}`;
                newProductDiv.innerHTML = `
                    <label for="name">Product Name:</label>
                    <input type="text" name="products[${productIndex}][name]" >
                    
                    <label for="qty">Quantity:</label>
                    <input type="number" name="products[${productIndex}][qty]" class="product-qty" >
                    
                    <label for="amount">Amount:</label>
                    <input type="number" name="products[${productIndex}][amount]" class="product-amount" step="0.01" >
                    
                    <label for="total">Total:</label>
                    <input type="number" name="products[${productIndex}][total]" class="product-total" readonly>
                    
                    <button type="button" class="remove-product" data-index="${productIndex}">Remove</button>
                `;
                productsContainer.appendChild(newProductDiv);
                productIndex++;

                // Add event listeners to the new product fields
                const newQtyInput = newProductDiv.querySelector('.product-qty');
                const newAmountInput = newProductDiv.querySelector('.product-amount');
                newQtyInput.addEventListener('input', () => calculateProductTotal(newProductDiv));
                newAmountInput.addEventListener('input', () => calculateProductTotal(newProductDiv));
            }

            // Remove Product Fields
            productsContainer.addEventListener('click', function (e) {
                if (e.target && e.target.classList.contains('remove-product')) {
                    const index = e.target.getAttribute('data-index');
                    const productDiv = document.getElementById(`product-${index}`);
                    if (productDiv) {
                        productDiv.remove();
                        updateGrandTotal();  // Update grand total after removing a product
                    }
                }
            });

            // Add Product Button Event
            addProductBtn.addEventListener('click', addProduct);

            // Add event listeners to the existing product fields to calculate the total
            const qtyInputs = document.querySelectorAll('.product-qty');
            const amountInputs = document.querySelectorAll('.product-amount');
            qtyInputs.forEach((input) => {
                input.addEventListener('input', function () {
                    const productDiv = input.closest('.product');
                    calculateProductTotal(productDiv);
                });
            });
            amountInputs.forEach((input) => {
                input.addEventListener('input', function () {
                    const productDiv = input.closest('.product');
                    calculateProductTotal(productDiv);
                });
            });
        });
    </script>
</body>

</html>
