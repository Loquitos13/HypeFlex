<?php
session_start();

$userId = $_SESSION['user_id'];
$cartId = $_SESSION['user_cart_id'];
?>

<input type="hidden" id="userId" value="<?php echo htmlspecialchars($userId); ?>">
<input type="hidden" id="cartId" value="<?php echo htmlspecialchars($cartId); ?>">

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/checkout.css">
    <title>Checkout</title>
</head>

<body>
    <header>

    </header>

    <main>
        <div class="main-container">
            <div class="Insignia" id="delivery-insignia">
                <span class="badge rounded-pill text-bg-primary bg-blueviolet First">1</span>

                <span class="badge rounded-pill text-bg-primary bg-grey Second">2</span>

                <span class="badge rounded-pill text-bg-primary bg-grey Third">3</span>
            </div>
            <div class="BarraProgresso" id="delivert-barra">
                <div class="progress custom-progress" role="progressbar" aria-label="Basic example" aria-valuenow="100"
                    aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar bg-blueviolet" style="width: 100%"></div>
                </div>
                <div class="progress custom-progress" role="progressbar" aria-label="Basic example" aria-valuenow="0"
                    aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar" style="width: 0%"></div>
                </div>
                <div class="progress custom-progress" role="progressbar" aria-label="Basic example" aria-valuenow="0"
                    aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar" style="width: 0%"></div>
                </div>
            </div>
            <div class="BarraNomes" id="delivery-nomes">
                <span>Delivery</span>
                <span>Payment</span>
                <span>Confirm</span>
            </div>
            <div class="container-delivery ">
                <div class="container-details">
                    <h1>Delivery Details</h1>
                    <h4>Shipping Method</h4>

                    <div class="container-format-row">
                        <div class="container-standard-delivery">
                            <input class="form-check-input" type="radio" name="deliveryOption" id="standardDelivery" value="Standard" checked>
                            <label class="form-check-label" for="standardDelivery">Standard Delivery</label>
                            <div class="businessDays">3-5 business days</div>
                            <div class="delivery-price">5.99€</div>
                        </div>

                        <div class="container-express-delivery">
                            <input class="form-check-input" type="radio" name="deliveryOption" id="expressDelivery" value="Express">
                            <label class="form-check-label" for="expressDelivery">Express Delivery</label> <br>
                            <div class="businessDays">1-2 business days</div>
                            <div class="delivery-price">12.99€</div>
                        </div>
                    </div>

                    <div class="container-format-row">
                        <div class="container-format-column">
                            First Name
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="firstName" aria-label="Username"
                                    aria-describedby="basic-addon1">
                            </div>
                        </div>
                        <div class="container-format-column">
                            Last Name
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="lastName" aria-label="Username"
                                    aria-describedby="basic-addon1">
                            </div>
                        </div>
                    </div>
                    <!-- Linha 1: Email -->
    <div class="container-format-column">
        Email
        <div class="input-group mb-3">
            <input type="text" class="form-control" id="email" aria-label="Email" aria-describedby="email-addon">
        </div>
    </div>

    <!-- Linha 2: Country Code + Phone Number lado a lado -->
    <div style="display: flex; gap: 1rem;">
         <div class="container-format-column" style="flex: 1;">
          <label for="country-code">Country Code</label>
        <div class="input-group mb-3">
            <select class="form-control" id="country-code" aria-label="Country Code" aria-describedby="country-addon">
                <option value="+351">🇵🇹 Portugal (+351)</option>
                <option value="+34">🇪🇸 Spain (+34)</option>
                <option value="+33">🇫🇷 France (+33)</option>
                <option value="+39">🇮🇹 Italy (+39)</option>
                <option value="+49">🇩🇪 Germany (+49)</option>
                <option value="+44">🇬🇧 United Kingdom (+44)</option>
                <option value="+31">🇳🇱 Netherlands (+31)</option>
                <option value="+32">🇧🇪 Belgium (+32)</option>
                <option value="+43">🇦🇹 Austria (+43)</option>
                <option value="+45">🇩🇰 Denmark (+45)</option>
            </select>
        </div>
        </div>
        <div class="container-format-column" style="flex: 2;">
            Phone Number
            <div class="input-group mb-3">
                <input type="text" class="form-control" id="phoneNumber" aria-label="Phone Number" aria-describedby="phone-addon">
            </div>
        </div>
    </div>
</div>

                    <div class="container-format-row">
                        <div class="container-format-column">
                            Adress
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="address" aria-label="Username"
                                    aria-describedby="basic-addon1">
                            </div>
                        </div>
                    </div>
                    <div class="container-format-row">
                        <div class="container-format-column">
                            City
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="city" aria-label="Username"
                                    aria-describedby="basic-addon1">
                            </div>
                        </div>
                        <div class="container-format-column">
                            Postal Code
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="postalCode" aria-label="Username"
                                    aria-describedby="basic-addon1">
                            </div>
                        </div>
                    </div>
                    <div class="container-format-row">
                        <div class="container-format-column">
                            Tax ID Number
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="taxId" aria-label="Username"
                                    aria-describedby="basic-addon1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-format-row format-buttons">
                <button type="button" id="cart-back" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Shopping
                </button>
                <button type="button" id="payment-button" class="btn btn-primary bg-blueviolet">
                    Continue to Payment <i class="bi bi-arrow-right"></i>
                </button>
            </div>
            <div class="space"></div>
            <div class="Insignia" id="payment-insignia">
            <span class="badge rounded-pill text-bg-primary bg-blueviolet First">1</span>
            <span class="badge rounded-pill text-bg-primary bg-blueviolet Fourth">2</span>
            <span class="badge rounded-pill text-bg-primary bg-grey Third">3</span>
        </div>
        <div class="BarraProgresso" id="payment-barra">
            <div class="progress custom-progress" role="progressbar" aria-label="Basic example" aria-valuenow="100"
                aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar bg-blueviolet" style="width: 100%"></div>
            </div>
            <div class="progress custom-progress" role="progressbar" aria-label="Basic example" aria-valuenow="100"
                aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar bg-blueviolet" style="width: 100%"></div>
            </div>
            <div class="progress custom-progress" role="progressbar" aria-label="Basic example" aria-valuenow="0"
                aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar" style="width: 0%"></div>
            </div>
        </div>
        <div class="BarraNomes" id="payment-nomes">
            <span>Delivery</span>
            <span>Payment</span>
            <span>Confirm</span>
        </div>
        <div class="container-payment">
            <div class="container-details">
                <h1>Payment Method</h1>
                <div class="container-format-row">
                    <div class="container-standard">
                        <div class="container-format-column">
                            <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefaultCard" checked>
                            <label class="form-check-label" for="flexRadioDefaultCard">
                                <svg width="23" height="20" viewBox="0 0 23 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_11_1057)">
                                        <g clip-path="url(#clip1_11_1057)">
                                            <path
                                                d="M20 3.125C20.3438 3.125 20.625 3.40625 20.625 3.75V5H1.875V3.75C1.875 3.40625 2.15625 3.125 2.5 3.125H20ZM20.625 8.75V16.25C20.625 16.5938 20.3438 16.875 20 16.875H2.5C2.15625 16.875 1.875 16.5938 1.875 16.25V8.75H20.625ZM2.5 1.25C1.12109 1.25 0 2.37109 0 3.75V16.25C0 17.6289 1.12109 18.75 2.5 18.75H20C21.3789 18.75 22.5 17.6289 22.5 16.25V3.75C22.5 2.37109 21.3789 1.25 20 1.25H2.5ZM4.6875 13.125C4.16797 13.125 3.75 13.543 3.75 14.0625C3.75 14.582 4.16797 15 4.6875 15H6.5625C7.08203 15 7.5 14.582 7.5 14.0625C7.5 13.543 7.08203 13.125 6.5625 13.125H4.6875ZM9.6875 13.125C9.16797 13.125 8.75 13.543 8.75 14.0625C8.75 14.582 9.16797 15 9.6875 15H14.0625C14.582 15 15 14.582 15 14.0625C15 13.543 14.582 13.125 14.0625 13.125H9.6875Z"
                                                fill="#1F2937" />
                                        </g>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_11_1057">
                                            <rect width="22.5" height="20" fill="white" />
                                        </clipPath>
                                        <clipPath id="clip1_11_1057">
                                            <path d="M0 0H22.5V20H0V0Z" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                Credit/Debit Card
                            </label>
                            <div class="input-group mb-3 card-input-container">
                                <input type="text" id="cardName" class="form-control" placeholder="Card Name"
                                    aria-label="Card Name" aria-describedby="basic-addon1">
                            </div>
                            <div class="input-group mb-3 card-input-container">
                                <input type="text" id="cardNumber" class="form-control" placeholder="Card Number"
                                    aria-label="Card Number" aria-describedby="basic-addon1">
                            </div>
                            <div class="container-format-row">
                                <div class="input-group mb-3 card-container">
                                    <input type="text" id="ExpirationDate" class="form-control" placeholder="MM/YY"
                                        aria-label="Expiration Date" aria-describedby="basic-addon1">
                                </div>
                                <div class="input-group mb-3">
                                    <input type="text" id="CVC" class="form-control" placeholder="CVC" aria-label="CVC"
                                        aria-describedby="basic-addon1">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <h3>Promo Code</h3>
                <div class="input-group mb-3 card-container">
                    <input type="text" class="form-control" placeholder="Enter promo code" aria-label="Promo Code"
                        aria-describedby="basic-addon1">
                    <button type="button" class="btn btn-dark" style="margin-left: 1vw;">Apply</button>
                </div>
                <h3>Additional Comments</h3>
                <div class="input-group">
                    <textarea id="userNotes" class="form-control" rows="5" aria-label="With textarea"
                        placeholder="Any special instructions?"></textarea>
                </div>
            </div>
        </div>
        <div class="container-format-row format-buttons">
            <button type="button" id="delivery-back" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Delivery
            </button>
            <button type="button" id="review-button" class="btn btn-primary bg-blueviolet">
                Review Order <i class="bi bi-arrow-right"></i>
            </button>
        </div>
        <div class="space"></div>
            <div class="Insignia" id="confirm-insignia">
                <span class="badge rounded-pill text-bg-primary bg-blueviolet First">1</span>

                <span class="badge rounded-pill text-bg-primary bg-blueviolet Fourth">2</span>

                <span class="badge rounded-pill text-bg-primary bg-blueviolet First">3</span>
            </div>
            <div class="BarraProgresso" id="confirm-barra">
                <div class="progress custom-progress" role="progressbar" aria-label="Basic example" aria-valuenow="100"
                    aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar bg-blueviolet" style="width: 100%"></div>
                </div>
                <div class="progress custom-progress" role="progressbar" aria-label="Basic example" aria-valuenow="100"
                    aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar bg-blueviolet" style="width: 100%"></div>
                </div>
                <div class="progress custom-progress" role="progressbar" aria-label="Basic example" aria-valuenow="100"
                    aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar bg-blueviolet" style="width: 100%"></div>
                </div>
            </div>
            <div class="BarraNomes" id="confirm-nomes">
                <span>Delivery</span>
                <span>Payment</span>
                <span>Confirm</span>
            </div>
           
            <div class="container-shipping">
                <div class="container-details">
                    <h1>Order Review</h1>
                    <h4>Items</h4>
                    <div class="container-items">
                        <div class="row w-100">
                            <div class="col-6 d-flex flex-column justify-content-center">
                                <h6 class="fw-bold">Premium Cotton T-shirt</h6>
                                <p class="mb-1">Size: L | Color: White</p>
                                <p class="mb-0">Quantity: 1</p>
                            </div>
                            <div class="col-6 d-flex align-items-center justify-content-end">
                                <p class="fw-bold confirm-price">$29.99</p>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h4>Delivery Details</h4>
                    <div class="row w-100 align-items-start">
                        <div class="col-6 d-flex flex-column justify-content-center">
                            <p class="mb-1">Delivery Method:</p>
                            <h6 id="summaryDeliveryMethod" class="fw-bold">Standard-Delivery (3-5 days)</h6>
                        </div>
                        <div class="col-6 d-flex flex-column justify-content-end">
                            <p class="mb-1">Delivery Address:</p>
                            <h6 id="summaryDeliveryAddressName" class="fw-bold">John Doe</h6>
                            <h6 id="summaryDeliveryAddressStreet" class="fw-bold">123 Main St</h6>
                            <h6 id="summaryDeliveryAddressCity" class="fw-bold">New York, NY 10001</h6>
                        </div>
                    </div>
                    <hr>
                    <h4>Payment Details</h4>
                    <div class="row w-100 align-items-start">
                        <div class="col-6 d-flex flex-column justify-content-center">
                            <p class="mb-1">Payment Method:</p>
                            <h6 id="summaryPaymentDetails" class="fw-bold">Credit Card (**** 1234)</h6>
                        </div>
                        <div class="col-6 d-flex flex-column justify-content-end">
                            <p class="mb-1">Billing Address:</p>
                            <h6 class="fw-bold">Same as delivery address</h6>
                        </div>
                    </div>
                    <hr>
                    <div class="container-format-row">
                        <div class="container-format-column">
                            <p>Subtotal</p>
                            <p>Shipping</p>
                            <p>Tax</p>
                        </div>
                        <div class="container-format-column right">
                            <p id="subtotal">29.99€</p>
                            <p id="shipping">5.99€</p>
                            <p id="tax">3.60€</p>
                        </div>
                    </div>
                    <hr>
                    <div class="container-format-row-right delivery-price">
                        <div class="container-format-column">
                            <p>Total</p>
                        </div>
                        <div class="container-format-column right">
                            <p id="total">39.58€</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-format-row format-buttons">
                <button type="button" id="payment-back" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Payment
                </button>
                <button type="button" id="confirm-button" class="btn btn-primary bg-blueviolet">
                    Confirm Order <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>
    </main>

    <footer>

    </footer>
    <script src="js/checkout.js"></script>

</body>

</html>