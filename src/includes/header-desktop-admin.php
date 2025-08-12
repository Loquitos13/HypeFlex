<?php
session_start();

?>

<!-- Menu lateral -->
<div class="container d-flex flex-column flex-shrink-0 p-3 min-vh-100 header-desktop" id="div-menu" style="width: 280px;">
    <!-- Logo -->
    <a href="Dashboard.php"
        class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
        <svg class="bi pe-none me-2" width="40" height="32">
            <use xlink:href="#bootstrap"></use>
        </svg>
        <span class="fs-3 fw-bold text-custom-color">Print&Go</span>
    </a>
    <hr>
    <!-- Tabela com links -->
    <ul class="nav  flex-column mb-auto">
        <!-- Link ativo -->
        <li id="link-dashboard">
            <a href="Dashboard.php" class="nav-link link-body-emphasis" aria-current="page" id="">
                <svg xmlns="http://www.w3.org/2000/svg"
                    style="stroke:currentColor" width="16" height="16"
                    fill="currentColor" class="bi pe-none me-2 bi-graph-up" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M0 0h1v15h15v1H0zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07" />
                </svg>
                Dashboard
            </a>
        </li>
        <!-- Links para a pagina de produtos do admin-->
        <li id="link-products">
            <a href="produtosAdmin.php" class="nav-link link-body-emphasis">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi me-2 bi-box2-fill" viewBox="0 0 16 16">
                    <path
                        d="M3.75 0a1 1 0 0 0-.8.4L.1 4.2a.5.5 0 0 0-.1.3V15a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V4.5a.5.5 0 0 0-.1-.3L13.05.4a1 1 0 0 0-.8-.4zM15 4.667V5H1v-.333L1.5 4h6V1h1v3h6z" />
                </svg>
                Products
            </a>
        </li>
        <!-- Links para a pagina de pedidos do admin-->
        <li id="link-orders">
            <a href="orders.php" class="nav-link link-body-emphasis">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi me-2 bi-cart-fill" viewBox="0 0 16 16">
                    <path
                        d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                </svg>
                Orders
            </a>
        </li>
        <!-- Links para a pagina de analises do admin-->
        <li id="link-analytics">
            <a href="analytics.php" class="nav-link link-body-emphasis">
                <svg xmlns="http://www.w3.org/2000/svg" style="stroke:currentColor; stroke-width:1;" width="16"
                    height="16" fill="currentColor" class="bi pe-none me-2 bi-graph-up" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M0 0h1v15h15v1H0zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07" />
                </svg>
                Analytics
            </a>
        </li>
        <!-- Links para a pagina de descontos do admin-->
        <li>
            <a href="#" class="nav-link link-body-emphasis">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi me-2 bi-tag-fill" viewBox="0 0 16 16">
                    <path
                        d="M2 1a1 1 0 0 0-1 1v4.586a1 1 0 0 0 .293.707l7 7a1 1 0 0 0 1.414 0l4.586-4.586a1 1 0 0 0 0-1.414l-7-7A1 1 0 0 0 6.586 1zm4 3.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                </svg>
                Discounts
            </a>
        </li>
        <!-- Links para a pagina de configurações do admin-->
        <li>
            <a href="Settings.php" class="nav-link link-body-emphasis">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi me-2 bi-gear-fill" viewBox="0 0 16 16">
                    <path
                        d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z" />
                </svg>
                Settings
            </a>
        </li>
        <li class="nav-item my-5"></li>
        <li class="nav-item my-5"></li>
        <li class="nav-item my-5"></li>
        <li class="nav-item my-5"></li>
        <li class="nav-item my-5"></li>
        <li class="nav-item">
            <a href="../logout.php" class="nav-link">
                Logout
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#5985E1">
                    <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/>
                </svg>
            </a>
        </li>
    </ul>
</div>