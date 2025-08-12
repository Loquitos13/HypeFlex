<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Print&Go</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="src/css/contact.css">
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
    <section class="hero-section">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">Get In Touch</h1>
                    <p class="lead text-muted">We're here to help you create amazing custom products. Get in touch with
                        us!</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card contact-card h-100 text-center p-4">
                        <div class="contact-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <h5 class="fw-bold">Address</h5>
                        <p class="text-muted mb-0">
                            123 Print Street<br>
                            Downtown, New York - NY<br>
                            ZIP: 10001
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card contact-card h-100 text-center p-4">
                        <div class="contact-icon">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <h5 class="fw-bold">Phone</h5>
                        <p class="text-muted mb-0">
                            <strong>Sales:</strong> (11) 9999-8888<br>
                            <strong>Support:</strong> (11) 9999-7777<br>
                            <strong>WhatsApp:</strong> (11) 9999-6666
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card contact-card h-100 text-center p-4">
                        <div class="contact-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <h5 class="fw-bold">Email</h5>
                        <p class="text-muted mb-0">
                            <strong>General:</strong> contato@printgo.com<br>
                            <strong>Sales:</strong> vendas@printgo.com<br>
                            <strong>Support:</strong> suporte@printgo.com
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form & Map -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-5">
                            <h3 class="fw-bold mb-4">Send Your Message</h3>
                            <form>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="firstName" class="form-label">First Name *</label>
                                        <input type="text" class="form-control" id="firstName" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lastName" class="form-label">Last Name *</label>
                                        <input type="text" class="form-control" id="lastName" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">E-mail *</label>
                                        <input type="email" class="form-control" id="email" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="tel" class="form-control" id="phone">
                                    </div>
                                    <div class="col-12">
                                        <label for="subject" class="form-label">Subject *</label>
                                        <select class="form-select" id="subject" required>
                                            <option value="">Select a subject</option>
                                            <option value="vendas">Sales and Quotes</option>
                                            <option value="suporte">Technical Support</option>
                                            <option value="parceria">Partnerships</option>
                                            <option value="outros">Others</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="message" class="form-label">Message *</label>
                                        <textarea class="form-control" id="message" rows="5"
                                            placeholder="Describe how we can help you..." required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="bi bi-send me-2"></i>
                                            Send Message
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3">Business Hours</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Monday to Friday:</span>
                                <span class="fw-medium">8h às 18h</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Saturday:</span>
                                <span class="fw-medium">8h às 14h</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Sunday:</span>
                                <span class="text-muted">Closed</span>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3">Location</h5>
                            <div class="map-container" style="height: 250px;">
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3004.735511796001!2d-8.6062530845819!3d41.14951997928689!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd2464e1187a7e6b%3A0x3057c63e1d1c777d!2sR.%20de%20Santa%20Catarina%2C%20Porto%2C%20Portugal!5e0!3m2!1sen!2spt!4v1716555555555&maptypecontrol=0"
                                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'src/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="src/js/carrinho.js"></script>
    <script src="<?= $base_url ?>/src/js/carrinho.js"></script>
</body>

</html>