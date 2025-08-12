<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print&Go - Edit Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: auto;
            align-items: center;
        }

        #menu-mobile {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            width: 80%;
            height: 100%;
            background-color: white;
            transform: translateX(-10%);
            transition: transform 2000ms ease;
            overflow: hidden;
            z-index: 999;
            flex-direction: column-reverse;
            margin-top: 0;
            justify-content: flex-end;
            gap: 20px;
            align-items: center;
            padding-top: 100px;
        }

        #menu-mobile.open {
            display: flex;
            transform: translateX(0);
        }

        #menu-mobile a {
            color: #4F46E5;
            text-decoration: none;
            font-size: 1.5rem;
            margin: 15px 0;
        }

        #menu-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1000;
            font-size: 2rem;
            background: none;
            border: none;
            color: black;
            cursor: pointer;
        }

        #logo-header-mobile {
            display: none;
            background: none;
            border: none;
        }

        /* Ajustes para telas menores */
        @media (max-width: 1200px) {
            body {
                width: 100%;
            }

            .header-desktop {
                display: none;
            }

            #a-logo-header-mobile {
                display: flex;
                width: max-content;
                justify-content: center;
                padding-top: 20px;
                padding-bottom: 20px;
                width: 100%;

            }

            #logo-header-mobile {
                display: block;
                width: 100px;
                height: auto;
                margin: 0 auto;
            }

            #menu-toggle {
                display: block;
                top: 0;
            }

            #menu-mobile {
                position: fixed;
                height: 100%;
                overflow: hidden;
            }

            #menu-mobile li {
                list-style: none;
            }

            #menu-mobile .social {
                display: flex;
                height: 100%;
                width: 100%;
                align-items: center;
                align-self: flex-end;
                justify-content: center;
            }

            .container1 {
                display: none;
            }

            .left,
            .right {
                width: 100%;
                justify-content: center;
            }

            .left a {
                width: auto;
            }

            #containerHeroe {
                flex-direction: column;
                height: auto;
                padding: 20px 20px;
                width: 100%;
            }

            .esqHeroe,
            .dirHeroe {
                width: 100%;
                text-align: center;
            }

            .dirHeroe img {
                position: static;
                height: auto;
                width: 80%;
            }

            #prodDestaques {
                flex-direction: column;
                align-items: center;
                justify-content: center;

            }

            .containerDestaques {
                flex-direction: column;
                align-items: center;
                justify-content: center;
                width: 100%;
                gap: 20px;
            }

            .feedback-carousel-container {
                max-width: 90%;
                max-height: 400px;
            }

            #featureSection {
                padding: 5vh;
            }

            #containerFeatures {
                flex-direction: column;
            }

            .featureBox p {
                width: 90%;
            }

            #cta h3 {
                text-align: center;
                font-size: 100%;
            }

            #cta p {
                text-align: center;
                font-size: 90%;
            }

            footer {
                text-align: center;
            }

            #containerFooter {
                flex-direction: column;
                align-items: center;
                gap: 20px;
            }
        }
    </style>

<body>
    <?php
    // Dados fictícios do produto
    $product = [
        'name' => 'Premium Cotton T-shirt',
        'size' => 'L',
        'color' => 'White',
        'qty' => 1,
        'price' => 29.99,
        'image' => 'https://via.placeholder.com/80'
    ];

    // Custos adicionais
    $shipping = 5.00;
    $tax = 2.50;
    $subtotal = $product['price'] * $product['qty'];
    $total = $subtotal + $shipping + $tax;
    ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 bg-white p-4 rounded shadow-sm">
                <div class="row">
                    <!-- 🛒 Carrinho de Compras -->
                    <div class="col-md-8">
                        <h4 class="mb-4">Shopping Cart</h4>
                        <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                            <img src="<?php echo $product['image']; ?>" class="me-3" alt="Product" style="width:80px;">
                            <div class="flex-grow-1">
                                <h6 class="mb-1"><?php echo $product['name']; ?></h6>
                                <small>Size: <?php echo $product['size']; ?> | Color: <?php echo $product['color']; ?></small>
                                <div class="mt-2">Qty: <?php echo $product['qty']; ?></div>
                            </div>
                            <div>
                                <strong>$<?php echo number_format($product['price'], 2); ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- 📦 Resumo da Encomenda -->
                    <div class="col-md-4">
                        <h5>Order Summary</h5>
                        <ul class="list-group mb-3">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Subtotal</span>
                                <strong>$<?php echo number_format($subtotal, 2); ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Shipping</span>
                                <strong>$<?php echo number_format($shipping, 2); ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Tax</span>
                                <strong>$<?php echo number_format($tax, 2); ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Total</span>
                                <strong>$<?php echo number_format($total, 2); ?></strong>
                            </li>
                        </ul>
                        <button class="btn btn-primary w-100">Proceed to Checkout</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <header>Shopping Cart</header>

</body>