<?php
session_start();


// Array simulando itens do carrinho
/*$cart_items = [
    [
        'name' => 'Premium Cotton T-shirt',
        'size' => 'L',
        'color' => 'White',
        'price' => 29.99,
        'quantity' => 1,
        'image' => "{$base_url}/imagens/produtos varios hero.png"
    ],
    [
        'name' => 'Premium Cotton T-shirt',
        'size' => 'L',
        'color' => 'White',
        'price' => 29.99,
        'quantity' => 1,
        'image' => "{$base_url}/imagens/produtos varios hero.png"
    ],
    [
        'name' => 'Premium Cotton T-shirt',
        'size' => 'L',
        'color' => 'White',
        'price' => 29.99,
        'quantity' => 1,
        'image' => "{$base_url}/imagens/produtos varios hero.png"
    ]
];*/


//fazer todo no JS menos o canva

/*
<!--
<div class="offcanvas offcanvas-end" tabindex="-1" id="carrinho" aria-labelledby="offcanvasRightLabel" style="display:flex; flex-direction:column; height: fit-content; padding-bottom: 2vh;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasRightLabel">Shopping Cart (<?php echo count($cart_items); ?>)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="container">
            <?php foreach ($cart_items as $item): ?>
                <div class="row align-items-start">
                    <div class="col-4">
                        <img src="<?php echo $item['image']; ?>" class="img-small" alt="produtos">
                    </div>
                    <div class="col-8 d-flex justify-content-between align-items-start cart-item">
                        <div>
                            <p class="product-info mb-1"><?php echo $item['name']; ?></p>
                            <span class="product-info">Size: <?php echo $item['size']; ?></span>
                            <span class="product-info"> | </span>
                            <span class="product-info">Color: <?php echo $item['color']; ?></span>
                            Quantidade e Preço
                            <div class="d-flex align-items-center mt-2">
                                <button type="button" class="btn btn-outline-secondary me-2">-</button>
                                <span class="me-2">1</span>
                                <button type="button" class="btn btn-outline-secondary me-3">+</button>
                                <span class="price"><?php echo number_format($item['price'], 2); ?>€</span>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-link p-0 delete-cart-btn">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <hr>
            <?php endforeach; ?>
        </div>

    </div>

    <div style="align-self: center;  height:100%;  display: flex; flex-direction: column; justify-items: space-between; gap: 5%;">
        <button type="button" class="btn btn-primary" style="margin-bottom:5%;" id="concluir-compra">Concluir Compra</button>
        Modifique o botão para:
        <button type="button" class="btn btn-outline-success" style="" id="continuar-compra" data-bs-dismiss="offcanvas">Continuar a Comprar</button>
    </div>
</div>
        -->
*/

?>



<div class="offcanvas offcanvas-end" tabindex="-1" id="carrinho" aria-labelledby="offcanvasRightLabel" style="display:flex; flex-direction:column; padding-bottom: 2vh;">
    
    <div class="offcanvas-header">

        <h5 class="offcanvas-title" id="offcanvasRightLabel">Shopping Cart (0)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>

    </div>

    <div class="offcanvas-body">

        <div class="container" id="cartProducts">
        </div>

    </div>

    <div style="align-self: center; display: flex; flex-direction: column; gap: 5%;">

        <button type="button" class="btn btn-primary" style="margin-bottom:5%;" id="concluir-compra">Concluir Compra</button>
        <button type="button" class="btn btn-outline-success" style="" id="continuar-compra" data-bs-dismiss="offcanvas">Continuar a Comprar</button>

    </div>

</div>

<script src="src/js/carrinho.js" type="module"></script>