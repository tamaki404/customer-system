<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/purchase-order-form.css') }}">
    <title>Purchase Order</title>
</head>
<body>
    <div class="form">
        <div class="header-1">
            <h2>Purchase Order</h2>
            <div class="header">
                <div class="sunny-info">
                    <img src="{{ asset('assets/sunnyLogo1.png') }}" alt="Sunny&Scramble Logo">
                    <div class="address">
                        <p><strong>Sunny & Scramble</strong></p>
                        <p>123 Sunny Street</p>
                        <p>Sunnyville, 1860</p>
                    </div>
                </div>
                <div class="order-info">
                    <p><strong>Order ID:</strong> PO-10234</p>
                    <p><strong>Date:</strong> 2025-08-15</p>
                    <p><strong>Status:</strong> Approved</p>
                </div>
            </div>
        </div>
        <div class="form-body" style="padding: 10px">
            <div class="shipping-info">
                <div class="vendor">
                    <h3>Vendor Information</h3>
                    <p>Sunny & Scramble</p>
                    <p>123 Sunny Street Sunnyville, 1860</p>
                    <p><strong>Sales person:</strong> Lisa Hanabishi</p>
                    <p><strong>Phone:</strong> (555) 987-6543</p>
                    <p><strong>Email:</strong> lisa.hanabishi@abcsupplies.com</p>
                </div>
                <div class="customer">
                    <h3>Customer Information</h3>
                    <p>Egglog Enterprises</p>
                    <p>456 Palm Avenue, Baytown, 90210</p>
                    <p><strong>Contact Person:</strong> Mark Doe</p>
                    <p><strong>Phone:</strong> (555) 123-4567</p>
                    <p><strong>Email:</strong> mark.doe@egglogenterprises.com</p>
                </div>

            </div>

            <div class="shipping-type">
                <h3>Shipping Type</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Requisitioner</th>
                            <th>Ship via</th>
                            <th>F.O.B</th>
                            <th>Shipping terms</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Jane Smith</td>
                            <td>FedEx</td>
                            <td>Sunnyville</td>
                            <td>Prepaid</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="items">
                <h3>Items</h3>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product no.</th>
                            <th>Product Name</th>
                            <th>Unit</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Discount</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>P-001</td>
                            <td>Office Chair</td>
                            <td>pcs</td>
                            <td>5</td>
                            <td>₱3,500.00</td>
                            <td>₱500.00</td>
                            <td>₱17,000.00</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>P-002</td>
                            <td>Desk Lamp</td>
                            <td>pcs</td>
                            <td>10</td>
                            <td>₱800.00</td>
                            <td>₱0.00</td>
                            <td>₱8,000.00</td>
                        </tr>
                    </tbody>
                </table>


            </div>
            <div class="totals">
                <table style="width: 300px; float: right; border-collapse: collapse; font-size: 14px;">
                    <tr>
                        <td style="padding: 6px; border: 1px solid #ccc;">Subtotal</td>
                        <td style="padding: 6px; border: 1px solid #ccc; text-align: right;">₱25,000.00</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px; border: 1px solid #ccc;">Tax</td>
                        <td style="padding: 6px; border: 1px solid #ccc; text-align: right;">₱3,000.00</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px; border: 1px solid #ccc;">Discount</td>
                        <td style="padding: 6px; border: 1px solid #ccc; text-align: right;">₱500.00</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px; border: 1px solid #ccc; font-weight: bold;">Total</td>
                        <td style="padding: 6px; border: 1px solid #ccc; text-align: right; font-weight: bold;">₱27,500.00</td>
                    </tr>
                </table>
            </div>

            <div class="notes-signatory">
                <div class="notes">
                    <h3>Additional Notes</h3>
                    <input type="text" name="notes" value="Delivery before 20th August">
                </div>
                <div class="signatory">
                    <h3>Authorized by</h3>
                    <input type="text" name="authorized_by" value="Jane Smith">
                    <p style="font-size: 12px; color: #666;">Name with signature of Authorized person</p>
                </div>
            </div>

        </div>


    </div>
</body>
</html>
