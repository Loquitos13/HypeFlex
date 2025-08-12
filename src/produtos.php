<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Catalog</title>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

  <link rel="stylesheet" href="css/produtos.css">

  <?php //include 'includes/carrinho.php'; ?>
  <?php include 'includes/filtros.php'; ?>
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
  <?php include 'includes/header-mobile.php'; ?>

  <!-- Menu Desktop -->
  <?php include 'includes/header-desktop.php'; ?>

  <div id="containerTitulo">
    <div class="esqTitulo">
      <h1>Our Products</h1>
      <p>Choose from our wide range of customizable products</p>
    </div>
  </div>

  <div class="containerPrincipal">
    <hr>
    <!-- Filtros para desktop -->
    <div class="filtros">

      <!-- Categorias -->

      <div id="categorias-container">
        <h6>Categories</h6>
      </div>
      <hr>

      <div class="range-container">
        <h6 class="mb-3 fw-semibold">Price Range</h6>
        <div class="d-flex gap-2 align-items-center">
          <label for="price-min-input" class="form-label mb-0">Min:</label>
          <input type="number" class="form-control" id="price-min-input" value="0" min="0" step="1">
          <label for="price-max-input" class="form-label mb-0">Max:</label>
          <input type="number" class="form-control" id="price-max-input" value="100" min="0" step="1">
        </div>
      </div>
      <hr>

      <!-- Cores da aba filtros -->

      <div class="color-selector" id="color-selector-desktop">
        <h6>Colors</h6>
        <div id="idColorOptions" class="colorOptions d-flex gap-2">
        </div>
      </div>
      <hr>

      <!-- Tamanhos -->
      <div>
        <h6>Size</h6>

        <!-- Botões de tamanho -->
        <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups" id="size-selector-desktop">
        </div>
      </div>
      <hr>
      <!-- Botão de Aplicar -->
      <button id="apply-filters-desktop" class="btn btn-primary">Apply Filters</button>
      &nbsp;
      <!-- Botão de Limpar Filtros -->
      <button type="button" id="clear-filters-desktop" class="btn btn-outline-danger">Clear Filters</button>
    </div>

    <!-- Produtos -->
    <div id="produtos">
      <div class="containerOrdenar">
        <button data-bs-toggle="offcanvas" data-bs-target="#filtros_mob" aria-controls="filtros_mob"
          id="filters-toggle">Filters</button>
        <select class="form-select" style="width: auto;" aria-label="Sort options">
          <option selected>Popular (Best Seller)</option>
          <option value="1">Newest</option>
          <option value="2">Relevant</option>
          <option value="3">Sought-After</option>
        </select>
      </div>

      <!-- Todos os produtos -->
      <div class="container py-4">
        <!-- Todos os produtos serão exebidos aqui -->
        <div class="row" id="produtos-container">

        </div>

      </div>
      <!-- Paginação -->
      <div id="pagination-container"></div>
    </div>

  </div>
  <div class="shadow p-3 mb-5 bg-body rounded containerPrincipalinho ">
    <h2>Need Bulk Orders?</h2>
    <p>Get special pricing and dedicated support for large quantity orders</p>
    <button type="button" class="btn btn-primary mx-auto " style="color: white; background-color: #4F46E5;">Contact
      Sales Team</button>
  </div>
  <!-- Footer -->
  <?php include 'includes/footer.php'; ?>

  <script src="js/produtos.js"></script>
</body>

</html>