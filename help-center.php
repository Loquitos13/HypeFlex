<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center - Print&Go</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="src/css/help-center.css">
    <?php
    $base_url = "/~ptaw-2025-gr4";
    include 'src/includes/carrinho.php'; ?>
</head>

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

    .fixed-header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        background-color: white;
        z-index: 999;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
            padding-top: 70px;
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

        .right {
            width: 100%;
            justify-content: center;
        }

        .left {
            width: 50%;
            display: flex;
            align-items: center;
        }

        .left a {
            width: 20%;
        }

        .left form {
            flex: 1;
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

        #carrinho {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #search-toggle {
            display: block;
        }
    }

    @media (min-width: 1201px) {
        #search-toggle {
            display: none;
        }
    }
</style>

<body>

    <!-- Menu Mobile -->
    <?php include 'src/includes/header-mobile.php'; ?>

    <!-- Menu Desktop -->
    <?php include 'src/includes/header-desktop.php'; ?>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1 class="fw-bold">Help Center</h1>
            <p class="text-muted">Find answers, guides, and policies for all your Print&Go needs</p>
        </div>
    </div>

    <!-- Help Center Content -->
    <div class="help-section">
        <div class="container">
            <!-- Search Bar -->
            <div class="search-help">
                <span class="search-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-search" viewBox="0 0 16 16">
                        <path
                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                    </svg>
                </span>
                <input type="text" class="form-control" id="searchHelp" placeholder="Search for help...">
            </div>

            <div class="row">
                <!-- Sidebar Navigation -->
                <div class="col-lg-3">
                    <div class="sidebar">
                        <nav class="sidebar-nav" id="sidebar-nav">
                            <div class="mb-3">
                                <a class="nav-link active" href="#faq">Frequently Asked Questions</a>
                                <div class="sub-nav">
                                    <a class="nav-link" href="#general">General Questions</a>
                                    <a class="nav-link" href="#products">Products & Customization</a>
                                    <a class="nav-link" href="#orders">Orders & Shipping</a>
                                    <a class="nav-link" href="#returns-faq">Returns & Refunds FAQ</a>
                                    <a class="nav-link" href="#account">Account & Payment</a>
                                </div>
                            </div>

                            <div class="mb-3">
                                <a class="nav-link" href="#terms">Terms of Service</a>
                                <div class="sub-nav">
                                    <a class="nav-link" href="#terms-acceptance">Acceptance of Terms</a>
                                    <a class="nav-link" href="#terms-service">Service Description</a>
                                    <a class="nav-link" href="#terms-account">User Account</a>
                                    <a class="nav-link" href="#terms-intellectual">Intellectual Property</a>
                                    <a class="nav-link" href="#terms-prohibited">Prohibited Content</a>
                                    <a class="nav-link" href="#terms-payment">Payments & Refunds</a>
                                    <a class="nav-link" href="#terms-liability">Limitation of Liability</a>
                                    <a class="nav-link" href="#terms-changes">Changes to Terms</a>
                                    <a class="nav-link" href="#terms-law">Applicable Law</a>
                                </div>
                            </div>

                            <div class="mb-3">
                                <a class="nav-link" href="#privacy">Privacy Policy</a>
                                <div class="sub-nav">
                                    <a class="nav-link" href="#privacy-collect">Information Collection</a>
                                    <a class="nav-link" href="#privacy-use">Information Usage</a>
                                    <a class="nav-link" href="#privacy-share">Information Sharing</a>
                                    <a class="nav-link" href="#privacy-security">Data Security</a>
                                    <a class="nav-link" href="#privacy-rights">Your Rights</a>
                                    <a class="nav-link" href="#privacy-retention">Data Retention</a>
                                    <a class="nav-link" href="#privacy-changes">Policy Changes</a>
                                </div>
                            </div>

                            <div class="mb-3">
                                <a class="nav-link" href="#shipping">Shipping Information</a>
                                <div class="sub-nav">
                                    <a class="nav-link" href="#shipping-times">Delivery Times</a>
                                    <a class="nav-link" href="#shipping-costs">Shipping Costs</a>
                                    <a class="nav-link" href="#shipping-international">International Shipping</a>
                                    <a class="nav-link" href="#shipping-tracking">Order Tracking</a>
                                </div>
                            </div>

                            <div class="mb-3">
                                <a class="nav-link" href="#returns">Returns & Refunds</a>
                                <div class="sub-nav">
                                    <a class="nav-link" href="#returns-policy">Return Policy</a>
                                    <a class="nav-link" href="#returns-process">Return Process</a>
                                    <a class="nav-link" href="#returns-refunds">Refund Processing</a>
                                    <a class="nav-link" href="#returns-defective">Defective Products</a>
                                </div>
                            </div>

                            <div class="mb-3">
                                <a class="nav-link" href="#size-guide">Size Guide</a>
                                <div class="sub-nav">
                                    <a class="nav-link" href="#size-tshirts">T-Shirts & Tops</a>
                                    <a class="nav-link" href="#size-hoodies">Hoodies & Sweatshirts</a>
                                    <a class="nav-link" href="#size-accessories">Accessories</a>
                                    <a class="nav-link" href="#size-measuring">How to Measure</a>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-lg-9">
                    <!-- FAQ Section -->
                    <div id="faq" class="section-content">
                        <h2 class="category-title">Frequently Asked Questions</h2>

                        <!-- General Questions -->
                        <div id="general">
                            <h3 class="subcategory-title">General Questions</h3>
                            <div class="accordion" id="accordionGeneral">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseOne" aria-expanded="true"
                                            aria-controls="collapseOne">
                                            What is Print&Go?
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show"
                                        aria-labelledby="headingOne" data-bs-parent="#accordionGeneral">
                                        <div class="accordion-body">
                                            <p>Print&Go is a custom printing service that allows you to create
                                                personalized products with your own designs. We offer a wide range of
                                                customizable products including apparel, accessories, home & living
                                                items, and stationery. Our mission is to bring creative ideas to life
                                                with high-quality custom printed products that exceed your expectations.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingTwo">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                            aria-expanded="false" aria-controls="collapseTwo">
                                            How does the customization process work?
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse"
                                        aria-labelledby="headingTwo" data-bs-parent="#accordionGeneral">
                                        <div class="accordion-body">
                                            <p>Our customization process is simple:</p>
                                            <ol>
                                                <li>Select the product you want to customize</li>
                                                <li>Upload your design or create one using our online design tool</li>
                                                <li>Preview your customized product</li>
                                                <li>Add to cart and checkout</li>
                                            </ol>
                                            <p>Once your order is confirmed, our team will review your design, produce
                                                your custom product, and ship it to your address.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingThree">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                            aria-expanded="false" aria-controls="collapseThree">
                                            Where is Print&Go located?
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse"
                                        aria-labelledby="headingThree" data-bs-parent="#accordionGeneral">
                                        <div class="accordion-body">
                                            <p>Print&Go is headquartered in Lisbon, Portugal, where we have our main
                                                production facility. We ship our products worldwide and have
                                                distribution centers in several European countries to ensure fast
                                                delivery.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingFour">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseFour"
                                            aria-expanded="false" aria-controls="collapseFour">
                                            How can I contact customer support?
                                        </button>
                                    </h2>
                                    <div id="collapseFour" class="accordion-collapse collapse"
                                        aria-labelledby="headingFour" data-bs-parent="#accordionGeneral">
                                        <div class="accordion-body">
                                            <p>You can contact our customer support team through several channels:</p>
                                            <ul>
                                                <li>Email: support@printandgo.com</li>
                                                <li>Phone: +351 123 456 789 (Monday to Friday, 9 AM - 6 PM CET)</li>
                                                <li>Live Chat: Available on our website during business hours</li>
                                                <li>Contact Form: Available on our Contact Us page</li>
                                            </ul>
                                            <p>We aim to respond to all inquiries within 24 hours during business days.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Products & Customization -->
                        <div id="products">
                            <h3 class="subcategory-title">Products & Customization</h3>
                            <div class="accordion" id="accordionProducts">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingFive">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseFive"
                                            aria-expanded="false" aria-controls="collapseFive">
                                            What products can I customize?
                                        </button>
                                    </h2>
                                    <div id="collapseFive" class="accordion-collapse collapse"
                                        aria-labelledby="headingFive" data-bs-parent="#accordionProducts">
                                        <div class="accordion-body">
                                            <p>We offer a wide range of customizable products, including:</p>
                                            <ul>
                                                <li><strong>Apparel:</strong> T-shirts, hoodies, sweatshirts, tank tops,
                                                    polo shirts, and more</li>
                                                <li><strong>Accessories:</strong> Bags, hats, phone cases, masks, and
                                                    jewelry</li>
                                                <li><strong>Home & Living:</strong> Mugs, pillows, blankets, canvas
                                                    prints, and wall art</li>
                                                <li><strong>Stationery:</strong> Notebooks, calendars, stickers, and
                                                    business cards</li>
                                            </ul>
                                            <p>Our product catalog is constantly expanding, so check our website
                                                regularly for new items.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingSix">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseSix"
                                            aria-expanded="false" aria-controls="collapseSix">
                                            What file formats do you accept for designs?
                                        </button>
                                    </h2>
                                    <div id="collapseSix" class="accordion-collapse collapse"
                                        aria-labelledby="headingSix" data-bs-parent="#accordionProducts">
                                        <div class="accordion-body">
                                            <p>We accept the following file formats for your custom designs:</p>
                                            <ul>
                                                <li>PNG (preferred for designs with transparency)</li>
                                                <li>JPG/JPEG</li>
                                                <li>SVG</li>
                                                <li>PDF</li>
                                                <li>AI (Adobe Illustrator)</li>
                                                <li>PSD (Photoshop)</li>
                                            </ul>
                                            <p>For best results, we recommend using high-resolution images (at least 300
                                                DPI) and vector files when possible.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingSeven">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseSeven"
                                            aria-expanded="false" aria-controls="collapseSeven">
                                            What if I don't have my own design?
                                        </button>
                                    </h2>
                                    <div id="collapseSeven" class="accordion-collapse collapse"
                                        aria-labelledby="headingSeven" data-bs-parent="#accordionProducts">
                                        <div class="accordion-body">
                                            <p>If you don't have your own design, we offer several options:</p>
                                            <ol>
                                                <li><strong>Design Library:</strong> Browse our collection of pre-made
                                                    designs that you can use or modify</li>
                                                <li><strong>Online Design Tool:</strong> Create your own design using
                                                    our user-friendly design tool with various templates, fonts, and
                                                    graphics</li>
                                                <li><strong>Design Service:</strong> For an additional fee, our
                                                    professional designers can create a custom design based on your
                                                    ideas and requirements</li>
                                            </ol>
                                            <p>Contact our customer support for more information about our design
                                                services.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Orders & Shipping -->
                        <div id="orders">
                            <h3 class="subcategory-title">Orders & Shipping</h3>
                            <div class="accordion" id="accordionOrders">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingNine">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseNine"
                                            aria-expanded="false" aria-controls="collapseNine">
                                            How long does it take to process my order?
                                        </button>
                                    </h2>
                                    <div id="collapseNine" class="accordion-collapse collapse"
                                        aria-labelledby="headingNine" data-bs-parent="#accordionOrders">
                                        <div class="accordion-body">
                                            <p>Order processing times vary depending on the product and customization:
                                            </p>
                                            <ul>
                                                <li>Standard products: 1-2 business days</li>
                                                <li>Custom printed apparel: 2-3 business days</li>
                                                <li>Complex customizations: 3-5 business days</li>
                                                <li>Bulk orders: 5-7 business days</li>
                                            </ul>
                                            <p>Processing time begins after your design has been approved. You'll
                                                receive an email notification when your order ships with tracking
                                                information.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingTen">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseTen"
                                            aria-expanded="false" aria-controls="collapseTen">
                                            What shipping options are available?
                                        </button>
                                    </h2>
                                    <div id="collapseTen" class="accordion-collapse collapse"
                                        aria-labelledby="headingTen" data-bs-parent="#accordionOrders">
                                        <div class="accordion-body">
                                            <p>We offer several shipping options:</p>
                                            <ul>
                                                <li><strong>Standard Shipping:</strong> 5-7 business days</li>
                                                <li><strong>Express Shipping:</strong> 2-3 business days</li>
                                                <li><strong>Next Day Delivery:</strong> Available for select locations
                                                    (order must be placed before 12 PM)</li>
                                            </ul>
                                            <p>Shipping times are estimates and begin after the order processing is
                                                complete. International shipping may take longer due to customs
                                                clearance.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Returns & Refunds FAQ -->
                        <div id="returns-faq">
                            <h3 class="subcategory-title">Returns & Refunds FAQ</h3>
                            <div class="accordion" id="accordionReturns">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingThirteen">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseThirteen"
                                            aria-expanded="false" aria-controls="collapseThirteen">
                                            What is your return policy?
                                        </button>
                                    </h2>
                                    <div id="collapseThirteen" class="accordion-collapse collapse"
                                        aria-labelledby="headingThirteen" data-bs-parent="#accordionReturns">
                                        <div class="accordion-body">
                                            <p>We accept returns within 14 days of delivery for products that are in
                                                their original condition, unworn/unused, and with all tags and packaging
                                                intact.</p>
                                            <p><strong>Please note:</strong> Due to the personalized nature of
                                                custom-printed products, we can only accept returns for the following
                                                reasons:</p>
                                            <ul>
                                                <li>Manufacturing defects</li>
                                                <li>Printing errors</li>
                                                <li>Damaged during shipping</li>
                                                <li>Wrong item shipped</li>
                                                <li>Size issues (for apparel only)</li>
                                            </ul>
                                            <p>Returns for personal preference or design mistakes are not accepted for
                                                custom products.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingFourteen">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseFourteen"
                                            aria-expanded="false" aria-controls="collapseFourteen">
                                            How do I initiate a return?
                                        </button>
                                    </h2>
                                    <div id="collapseFourteen" class="accordion-collapse collapse"
                                        aria-labelledby="headingFourteen" data-bs-parent="#accordionReturns">
                                        <div class="accordion-body">
                                            <p>To initiate a return, please follow these steps:</p>
                                            <ol>
                                                <li>Log in to your account and go to your order history</li>
                                                <li>Select the order containing the item you wish to return</li>
                                                <li>Click on "Return Item" and follow the instructions</li>
                                                <li>Alternatively, contact our customer support at
                                                    returns@printandgo.com with your order number and return reason</li>
                                            </ol>
                                            <p>Once your return request is approved, you'll receive a return
                                                authorization and shipping instructions.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account & Payment -->
                        <div id="account">
                            <h3 class="subcategory-title">Account & Payment</h3>
                            <div class="accordion" id="accordionAccount">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingSeventeen">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseSeventeen"
                                            aria-expanded="false" aria-controls="collapseSeventeen">
                                            Do I need to create an account to place an order?
                                        </button>
                                    </h2>
                                    <div id="collapseSeventeen" class="accordion-collapse collapse"
                                        aria-labelledby="headingSeventeen" data-bs-parent="#accordionAccount">
                                        <div class="accordion-body">
                                            <p>No, you can place an order as a guest without creating an account.
                                                However, creating an account offers several benefits:</p>
                                            <ul>
                                                <li>Faster checkout for future orders</li>
                                                <li>Order history and tracking</li>
                                                <li>Saved shipping addresses and payment methods</li>
                                                <li>Saved designs for future use</li>
                                                <li>Access to exclusive promotions and discounts</li>
                                            </ul>
                                            <p>Creating an account is free and only takes a minute.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingEighteen">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseEighteen"
                                            aria-expanded="false" aria-controls="collapseEighteen">
                                            What payment methods do you accept?
                                        </button>
                                    </h2>
                                    <div id="collapseEighteen" class="accordion-collapse collapse"
                                        aria-labelledby="headingEighteen" data-bs-parent="#accordionAccount">
                                        <div class="accordion-body">
                                            <p>We accept the following payment methods:</p>
                                            <ul>
                                                <li>Credit/Debit Cards (Visa, Mastercard, American Express)</li>
                                                <li>PayPal</li>
                                                <li>Apple Pay</li>
                                                <li>Google Pay</li>
                                                <li>Bank Transfer (for bulk orders only)</li>
                                            </ul>
                                            <p>All payments are processed securely through encrypted connections. We do
                                                not store your full credit card details on our servers.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms of Service -->
                    <div id="terms" class="section-content">
                        <h2 class="category-title">Terms of Service</h2>
                        <p class="mb-4">Last updated: May 22, 2025</p>

                        <div id="terms-acceptance">
                            <h3 class="subcategory-title">1. Acceptance of Terms</h3>
                            <p>By accessing and using the services of Print&Go, you agree to comply with and be bound by
                                the following terms and conditions. If you do not agree with any part of these terms,
                                you may not access or use our services.</p>
                        </div>

                        <div id="terms-service">
                            <h3 class="subcategory-title">2. Service Description</h3>
                            <p>Print&Go provides high-quality custom printing services for various products, including
                                apparel, accessories, home & living items, and stationery. Our services allow customers
                                to submit their own designs for printing or choose from designs available on our
                                platform.</p>
                        </div>

                        <div id="terms-account">
                            <h3 class="subcategory-title">3. User Account</h3>
                            <p>To use certain features of our service, you may need to create an account. You are
                                responsible for maintaining the confidentiality of your account credentials and for all
                                activities that occur under your account. You agree to notify us immediately of any
                                unauthorized use of your account.</p>
                        </div>

                        <div id="terms-intellectual">
                            <h3 class="subcategory-title">4. Intellectual Property</h3>
                            <p>By submitting designs for printing, you declare and warrant that you own all necessary
                                rights to the submitted content or have permission to use it. You retain ownership of
                                your designs, but grant Print&Go a non-exclusive license to use, reproduce, and modify
                                the content as necessary to provide the requested services.</p>
                        </div>

                        <div id="terms-prohibited">
                            <h3 class="subcategory-title">5. Prohibited Content</h3>
                            <p>You agree not to submit content that is illegal, offensive, defamatory, obscene,
                                threatening, pornographic, that violates intellectual property rights, or that is
                                harmful in any way. Print&Go reserves the right to refuse printing any content it deems
                                inappropriate.</p>
                        </div>

                        <div id="terms-payment">
                            <h3 class="subcategory-title">6. Payments and Refunds</h3>
                            <p>Product and service prices are subject to change without notice. Print&Go reserves the
                                right to refuse or cancel orders at its discretion. Refunds will be processed according
                                to our Return Policy.</p>
                        </div>

                        <div id="terms-liability">
                            <h3 class="subcategory-title">7. Limitation of Liability</h3>
                            <p>Print&Go will not be liable for any direct, indirect, incidental, special, or
                                consequential damages resulting from the use or inability to use our services.</p>
                        </div>

                        <div id="terms-changes">
                            <h3 class="subcategory-title">8. Changes to Terms</h3>
                            <p>Print&Go reserves the right to modify these terms at any time. Changes will take effect
                                immediately upon posting the updated terms on our website. Continued use of our services
                                after such changes constitutes your acceptance of the new terms.</p>
                        </div>

                        <div id="terms-law">
                            <h3 class="subcategory-title">9. Applicable Law</h3>
                            <p>These terms will be governed and interpreted according to the laws of Portugal, without
                                regard to its conflict of law provisions.</p>
                        </div>
                    </div>

                    <!-- Privacy Policy -->
                    <div id="privacy" class="section-content">
                        <h2 class="category-title">Privacy Policy</h2>
                        <p class="mb-4">Last updated: May 22, 2025</p>

                        <div id="privacy-collect">
                            <h3 class="subcategory-title">1. Information We Collect</h3>
                            <p>Print&Go collects personal information such as name, email address, postal address, phone
                                number, and payment information when you create an account or place an order. We also
                                collect information about how you use our website, including IP address, browser type,
                                pages visited, and time spent on the site.</p>
                        </div>

                        <div id="privacy-use">
                            <h3 class="subcategory-title">2. How We Use Your Information</h3>
                            <p>We use your personal information to process orders, provide customer support, send
                                communications about your orders, and improve our services. We may also use your
                                information to send marketing communications, but you can opt out of receiving these at
                                any time.</p>
                        </div>

                        <div id="privacy-share">
                            <h3 class="subcategory-title">3. Information Sharing</h3>
                            <p>We do not sell your personal information to third parties. We may share your information
                                with service providers who help us operate our business, such as payment processors and
                                logistics companies. These service providers have limited access to your information and
                                are required to protect it.</p>
                        </div>

                        <div id="privacy-security">
                            <h3 class="subcategory-title">4. Data Security</h3>
                            <p>We implement technical and organizational security measures to protect your personal
                                information against unauthorized access, misuse, or disclosure. However, no method of
                                transmission over the Internet or method of electronic storage is 100% secure.</p>
                        </div>

                        <div id="privacy-rights">
                            <h3 class="subcategory-title">5. Your Rights</h3>
                            <p>You have the right to access, correct, update, or request deletion of your personal
                                information. You also have the right to object to processing of your personal
                                information, request restriction of processing, or request portability of your
                                information.</p>
                        </div>

                        <div id="privacy-retention">
                            <h3 class="subcategory-title">6. Data Retention</h3>
                            <p>We retain your personal information for as long as necessary to fulfill the purposes
                                outlined in this Privacy Policy, unless a longer retention period is required or
                                permitted by law.</p>
                        </div>

                        <div id="privacy-changes">
                            <h3 class="subcategory-title">7. Changes to Privacy Policy</h3>
                            <p>We may update this Privacy Policy periodically. We will notify you of any significant
                                changes by posting the new Privacy Policy on our website and, when appropriate, sending
                                an email.</p>
                        </div>
                    </div>

                    <!-- Shipping Information -->
                    <div id="shipping" class="section-content">
                        <h2 class="category-title">Shipping Information</h2>
                        <p>Print&Go strives to deliver your products within the estimated timeframe. Delivery times vary
                            depending on the product, customization, and destination.</p>

                        <div id="shipping-times">
                            <h3 class="subcategory-title">Delivery Times</h3>
                            <p>Estimated delivery times are:</p>
                            <ul>
                                <li><strong>Standard Products:</strong> 3-5 business days after order confirmation</li>
                                <li><strong>Custom Products:</strong> 5-7 business days after design approval</li>
                                <li><strong>International Deliveries:</strong> 7-14 business days</li>
                            </ul>
                            <p>Please note that these are estimates and actual delivery times may vary based on factors
                                outside our control, such as weather conditions or customs processing for international
                                shipments.</p>
                        </div>

                        <div id="shipping-costs">
                            <h3 class="subcategory-title">Shipping Costs</h3>
                            <p>Shipping costs are calculated based on the weight, dimensions, and destination of your
                                order. The exact costs will be displayed during the checkout process.</p>
                            <p>We offer free shipping on orders over €100 within Portugal and €150 for international
                                orders.</p>
                            <table class="table table-bordered mt-3">
                                <thead>
                                    <tr>
                                        <th>Destination</th>
                                        <th>Standard Shipping</th>
                                        <th>Express Shipping</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Portugal</td>
                                        <td>€4.99</td>
                                        <td>€9.99</td>
                                    </tr>
                                    <tr>
                                        <td>European Union</td>
                                        <td>€9.99</td>
                                        <td>€14.99</td>
                                    </tr>
                                    <tr>
                                        <td>Rest of Europe</td>
                                        <td>€12.99</td>
                                        <td>€19.99</td>
                                    </tr>
                                    <tr>
                                        <td>North America</td>
                                        <td>€14.99</td>
                                        <td>€24.99</td>
                                    </tr>
                                    <tr>
                                        <td>Rest of World</td>
                                        <td>€19.99</td>
                                        <td>€29.99</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div id="shipping-international">
                            <h3 class="subcategory-title">International Shipping</h3>
                            <p>We ship to most countries worldwide. Please note that international orders may be subject
                                to import duties, taxes, and customs clearance fees, which are the responsibility of the
                                recipient. These charges vary by country and are not included in our shipping fees.</p>
                            <p>Some countries have restrictions on certain products or materials. Please check your
                                local customs regulations before placing an order.</p>
                        </div>

                        <div id="shipping-tracking">
                            <h3 class="subcategory-title">Order Tracking</h3>
                            <p>Once your order ships, you will receive a confirmation email with tracking information.
                                You can track your order in several ways:</p>
                            <ul>
                                <li>Through your account dashboard on our website</li>
                                <li>Via the tracking link in your shipping confirmation email</li>
                                <li>By contacting our customer support with your order number</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Returns & Refunds -->
                    <div id="returns" class="section-content">
                        <h2 class="category-title">Returns & Refunds</h2>

                        <div id="returns-policy">
                            <h3 class="subcategory-title">Return Policy</h3>
                            <p>Print&Go accepts returns of products within 14 days of delivery, provided they are in
                                perfect condition, unused, and in the original packaging.</p>
                            <p><strong>Custom Products:</strong> Due to the personalized nature of custom products,
                                returns are only accepted for the following reasons:</p>
                            <ul>
                                <li>Manufacturing defects</li>
                                <li>Printing errors</li>
                                <li>Damage during shipping</li>
                                <li>Wrong item shipped</li>
                                <li>Size issues (for apparel only)</li>
                            </ul>
                            <p>Returns for personal preference or design mistakes are not accepted for custom products.
                            </p>
                        </div>

                        <div id="returns-process">
                            <h3 class="subcategory-title">Return Process</h3>
                            <p>To initiate a return, please contact our customer service through email at
                                returns@printandgo.com or by phone. You will receive instructions on how to proceed with
                                the return.</p>
                            <p>The return process involves the following steps:</p>
                            <ol>
                                <li>Contact customer service within 14 days of receiving your order</li>
                                <li>Provide your order number and reason for return</li>
                                <li>Receive a return authorization and shipping instructions</li>
                                <li>Package the item(s) securely in the original packaging if possible</li>
                                <li>Ship the item(s) to the address provided</li>
                                <li>Provide tracking information for the return shipment</li>
                            </ol>
                        </div>

                        <div id="returns-refunds">
                            <h3 class="subcategory-title">Refund Processing</h3>
                            <p>Refunds will be processed within 14 days after we receive and inspect the returned items.
                                The refund will be issued to the same payment method used for the original purchase.</p>
                            <p>Please note:</p>
                            <ul>
                                <li>Shipping costs are non-refundable unless the return is due to our error</li>
                                <li>Depending on your payment provider, it may take an additional 5-10 business days for
                                    the refund to appear in your account</li>
                                <li>For returns due to size issues, you can choose between a refund or an exchange for
                                    the correct size</li>
                            </ul>
                        </div>

                        <div id="returns-defective">
                            <h3 class="subcategory-title">Defective Products</h3>
                            <p>If you receive a defective product, please contact us within 7 days of delivery with the
                                following information:</p>
                            <ul>
                                <li>Your order number</li>
                                <li>Description of the defect</li>
                                <li>Photos clearly showing the issue</li>
                            </ul>
                            <p>For defective items, we offer the following solutions:</p>
                            <ul>
                                <li>Replacement of the same item</li>
                                <li>Full refund including shipping costs</li>
                                <li>Store credit with an additional 10% bonus</li>
                            </ul>
                            <p>We may request that you return the defective item for inspection, in which case we'll
                                provide a prepaid return shipping label.</p>
                        </div>
                    </div>

                    <!-- Size Guide -->
                    <div id="size-guide" class="section-content">
                        <h2 class="category-title">Size Guide</h2>
                        <p>Finding the right size is essential for your satisfaction with our products. Please use the
                            following size charts to determine the best fit for you.</p>

                        <div id="size-tshirts">
                            <h3 class="subcategory-title">T-Shirts & Tops</h3>
                            <p>Our t-shirts are available in unisex, men's, and women's fits. Measurements are in
                                centimeters.</p>

                            <h4 class="mt-4 mb-3">Unisex T-Shirts</h4>
                            <table class="size-table">
                                <thead>
                                    <tr>
                                        <th>Size</th>
                                        <th>Chest (cm)</th>
                                        <th>Length (cm)</th>
                                        <th>Sleeve (cm)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>S</td>
                                        <td>96-101</td>
                                        <td>71</td>
                                        <td>20</td>
                                    </tr>
                                    <tr>
                                        <td>M</td>
                                        <td>101-106</td>
                                        <td>74</td>
                                        <td>21</td>
                                    </tr>
                                    <tr>
                                        <td>L</td>
                                        <td>106-111</td>
                                        <td>76</td>
                                        <td>22</td>
                                    </tr>
                                    <tr>
                                        <td>XL</td>
                                        <td>111-116</td>
                                        <td>79</td>
                                        <td>23</td>
                                    </tr>
                                    <tr>
                                        <td>2XL</td>
                                        <td>116-121</td>
                                        <td>81</td>
                                        <td>24</td>
                                    </tr>
                                </tbody>
                            </table>

                            <h4 class="mt-4 mb-3">Women's T-Shirts</h4>
                            <table class="size-table">
                                <thead>
                                    <tr>
                                        <th>Size</th>
                                        <th>Chest (cm)</th>
                                        <th>Length (cm)</th>
                                        <th>Sleeve (cm)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>S</td>
                                        <td>86-91</td>
                                        <td>64</td>
                                        <td>15</td>
                                    </tr>
                                    <tr>
                                        <td>M</td>
                                        <td>91-96</td>
                                        <td>66</td>
                                        <td>16</td>
                                    </tr>
                                    <tr>
                                        <td>L</td>
                                        <td>96-101</td>
                                        <td>69</td>
                                        <td>17</td>
                                    </tr>
                                    <tr>
                                        <td>XL</td>
                                        <td>101-106</td>
                                        <td>71</td>
                                        <td>18</td>
                                    </tr>
                                    <tr>
                                        <td>2XL</td>
                                        <td>106-111</td>
                                        <td>74</td>
                                        <td>19</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div id="size-hoodies">
                            <h3 class="subcategory-title">Hoodies & Sweatshirts</h3>
                            <p>Our hoodies and sweatshirts are designed for a comfortable fit. Measurements are in
                                centimeters.</p>

                            <table class="size-table">
                                <thead>
                                    <tr>
                                        <th>Size</th>
                                        <th>Chest (cm)</th>
                                        <th>Length (cm)</th>
                                        <th>Sleeve (cm)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>S</td>
                                        <td>106-111</td>
                                        <td>69</td>
                                        <td>64</td>
                                    </tr>
                                    <tr>
                                        <td>M</td>
                                        <td>111-116</td>
                                        <td>71</td>
                                        <td>66</td>
                                    </tr>
                                    <tr>
                                        <td>L</td>
                                        <td>116-121</td>
                                        <td>74</td>
                                        <td>67</td>
                                    </tr>
                                    <tr>
                                        <td>XL</td>
                                        <td>121-126</td>
                                        <td>76</td>
                                        <td>69</td>
                                    </tr>
                                    <tr>
                                        <td>2XL</td>
                                        <td>126-131</td>
                                        <td>79</td>
                                        <td>70</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div id="size-accessories">
                            <h3 class="subcategory-title">Accessories</h3>

                            <h4 class="mt-4 mb-3">Hats & Caps</h4>
                            <table class="size-table">
                                <thead>
                                    <tr>
                                        <th>Size</th>
                                        <th>Head Circumference (cm)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>S/M</td>
                                        <td>54-58</td>
                                    </tr>
                                    <tr>
                                        <td>L/XL</td>
                                        <td>58-62</td>
                                    </tr>
                                </tbody>
                            </table>

                            <h4 class="mt-4 mb-3">Phone Cases</h4>
                            <p>Please select your phone model from the dropdown menu when ordering. We offer cases for
                                most popular smartphone models.</p>
                        </div>

                        <div id="size-measuring">
                            <h3 class="subcategory-title">How to Measure</h3>
                            <p>To ensure the best fit, please follow these measuring guidelines:</p>

                            <h4 class="mt-4 mb-2">Chest</h4>
                            <p>Measure around the fullest part of your chest, keeping the measuring tape horizontal.</p>

                            <h4 class="mt-4 mb-2">Length</h4>
                            <p>For tops, measure from the highest point of the shoulder to the bottom hem.</p>

                            <h4 class="mt-4 mb-2">Sleeve</h4>
                            <p>Measure from the shoulder seam to the end of the sleeve.</p>

                            <h4 class="mt-4 mb-2">Head Circumference</h4>
                            <p>Measure around your head, just above the ears and across the forehead.</p>

                            <div class="alert alert-info mt-4">
                                <strong>Tip:</strong> If you're between sizes, we recommend choosing the larger size for
                                a more comfortable fit.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Section -->
            <div class="text-center mt-5 pt-4 border-top">
                <h4>Still have questions?</h4>
                <p class="text-muted mb-4">Our customer support team is here to help you</p>
                <a href="#" class="btn btn-primary px-4 py-2">Contact Us</a>
            </div>
        </div>
    </div>

    <!-- Back to Top Button -->
    <div class="back-to-top" id="backToTop">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up"
            viewBox="0 0 16 16">
            <path fill-rule="evenodd"
                d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z" />
        </svg>
    </div>

    <!-- Footer -->
    <?php include 'src/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="src/js/help-center.js"></script>
    <script src="<?= $base_url ?>/src/js/carrinho.js"></script>
</body>

</html>