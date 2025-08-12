<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../SignIn.html');
    exit;
} else {

    $adminID = $_SESSION['admin_id'];

}

?>

<input type="hidden" id="adminID" value="<?php echo htmlspecialchars($adminID); ?>">

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="css/analytics.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="js/analytics.js"></script>
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
            display: none !important;
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

        #containerFooter {
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }
    }
</style>

<body>
    <div class="container-fluid d-flex p-0 min-vh-100">

        <!-- Menu lateral -->
        <?php include '../includes/header-desktop-admin.php'; ?>
        <!-- Menu mobile -->
        <?php include '../includes/header-mobile-admin.php'; ?>

        <div class="flex-grow-1 p-4">
            <header class="dashboard-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="dashboard-title">Analytics Dashboard</h1>
                        <p class="dashboard-subtitle">Monitor your business performance</p>
                    </div>
                    <div class="col-md-4 d-flex justify-content-end align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="../../imagens/admin.png" alt="" id="img-admin"
                            style="width:40px; height:40px; object-fit:cover; border-radius:50%;">
                            <h6 id="admin_nome" class="mb-0 ms-3">Nome Admin</h6>
                        </div>
                    </div>
                </div>
            </header>
            <div class="container py-4">
                <div class="row g-3">

                    <!-- Total de Pedidos -->
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div id="total_orders" class="metric-title">Total Orders</div>
                        </div>
                    </div>

                    <!-- Receita -->
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div id="revenue_metric_card" class="metric-title">Revenue</div>
                        </div>
                    </div>

                    <!-- Valor Médio do Pedido -->
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div id="avg_revenue" class="metric-title">Avg. Order Value</div>
                        </div>
                    </div>

                    <!-- Contas Criadas -->
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div id="created_accounts" class="metric-title">Created Accounts</div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="container py-4">
                <div class="row g-3">

                    <!-- Tendências -->
                    <div class="col-md-6">
                        <div class="metric-card-graph">
                            <div id="revenue_trends_div" class="metric-title">Revenue Trends</div>
                            <br>
                            <canvas id="revenue_trends_chart">Revenue Trends Placeholder</canvas>
                        </div>
                    </div>
                    <!-- Estatísticas -->
                    <div class="col-md-6">
                        <div class="metric-card-graph">
                            <div id="order_statistics_div" class="metric-title">Order Statistics</div>
                            <br>
                            <canvas id="order_statistics_chart">Order Statistics Placeholder</canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container py-4">
                <div class="row g-3">

                    <!-- Tendências -->
                    <div class="col-md-8">
                        <div class="metric-card">
                            <div id="top_products_div" class="metric-title">Top Products</div>
                            <br>
                            <table id="top_products" class="table">
                            </table>
                        </div>
                    </div>


                    <!-- Reviews -->
                    <div class="col-md-4">
                        <div id="reviews_div" class="metric-card-graph">
                            <div class="metric-title">Reviews</div>
                            <br>
                            <canvas id="reviews_chart">Reviews Placeholder</canvas>
                            <table id="reviews_table" class="table">
                                <tbody>
                                    <tr>
                                        <td scope="row">1 Star</td>
                                        <td id="one_star_perc">0%</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">2 Star</td>
                                        <td id="two_star_perc">0%</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">3 Star</td>
                                        <td id="three_star_perc">0%</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">4 Star</td>
                                        <td id="four_star_perc">0%</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">5 Star</td>
                                        <td id="five_star_perc">0%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
</body>

</html>