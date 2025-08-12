<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/produtosAdmin.css">
</head>

<body style="background-color: #E5E7EB;">

    <div class="d-flex">

        <div class="d-none d-md-block" style="background-color: white;">
            <!-- Menu lateral -->
            <?php include '../includes/header-desktop-admin.php'; ?>
        </div>
        <div class="flex-grow-1 d-block d-md-none">
            <!-- Menu mobile -->
            <?php include '../includes/header-mobile-admin.php'; ?>
        </div>


        <div class="container-fluid d-flex p-0 min-vh-100">
            <div class="flex-grow-1 p-4">
                <header class="dashboard-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="dashboard-title">Products</h1>
                            <p class="dashboard-subtitle">Manage your product catalog</p>
                        </div>
                        <div
                            class="col-12 col-md-4 d-flex flex-wrap justify-content-end align-items-center gap-2 mt-3 mt-md-0">
                            <button type="button" class="btn btn-outline-secondary" style="margin-right: 10px;">
                                <img src="exportButton.png" alt="">
                                Export
                            </button>
                            <button type="button" class="btn btn-primary bg-purple text-white" data-bs-toggle="modal"
                                data-bs-target="#addProductModal">+ Add Product</button>
                        </div>
                    </div>
                </header>

                <div class="container my-3">
                    <div class="row g-2">
                        <div class="col-12 col-md-4">
                            <input type="text" class="form-control" placeholder="Search products...">
                        </div>
                        <div class="col-6 col-md-2">
                            <select class="form-select category-options">
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <select class="form-select status-options">
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <select class="form-select">
                                <option>Sort By</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div style="max-height: 500px; overflow-y: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Product</th>
                                <th scope="col">Category</th>
                                <th scope="col">Price</th>
                                <th scope="col">Stock</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="productTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Popup para Adicionar Produto -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Formulário -->
                    <form id="productForm" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <!-- Área de upload Imagem -->
                            <div class="col-md-6 p-4 border border-dashed text-center rounded bg-light">
                                <div class="mb-2">
                                    <i class="bi bi-cloud-upload fs-1 text-muted"></i>
                                </div>
                                <p class="text-muted">Drag and drop your product image here<br><strong>or</strong></p>
                                <input type="file" class="form-control" name="imagem_principal" id="productImage"
                                    style="display:none;">
                                <button class="btn btn-outline-primary" id="browseImageButton">Browse Files</button>
                                <span id="fileNameImage" class="ms-2 text-muted"></span>
                            </div>
                            <!-- Área de upload Modelo 3D -->
                            <div class="col-md-6 p-4 border border-dashed text-center rounded bg-light">
                                <div class="mb-2">
                                    <i class="bi bi-cloud-upload fs-1 text-muted"></i>
                                </div>
                                <p class="text-muted">3D Model (.glb, ...)<br><strong>or</strong></p>
                                <input type="file" class="form-control" name="modelo3d_produto" id="productModel3D"
                                    style="display:none;">
                                <button type="button" class="btn btn-outline-primary" id="browseModelButton">Browse
                                    Model</button>
                                <span id="fileNameModel" class="ms-2 text-muted"></span>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6" id="productName">
                                <label class="form-label">Product Name</label>
                                <input type="text" class="form-control" name="titulo_produto">
                            </div>
                            <div class="col-md-6" id="keywords">
                                <label class="form-label">Keywords</label>
                                <input type="text" class="form-control" name="keywords_produto">
                            </div>

                            <div class="col-md-6" id="category">
                                <label class="form-label">Category</label>
                                <select class="form-select category-options" name="id_categoria">
                                </select>
                            </div>
                            <div class="col-md-6" id="price">
                                <label class="form-label">Price</label>
                                <input type="text" class="form-control" placeholder="$" name="preco_produto">
                            </div>

                            <div class="col-md-6" id="stock">
                                <label class="form-label">Stock Quantity</label>
                                <input type="text" class="form-control" name="stock_produto">
                            </div>

                            <div class="col-md-6" id="status">
                                <label class="form-label">Status</label>
                                <select class="form-select status-options" name="status_produto">
                                </select>
                            </div>

                            <div class="col-12" id="description">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" rows="3" name="descricao_produto"></textarea>
                            </div>

                            <div class="row g-2" id="variantsSection">
                                <div class="col-md-4">
                                    <label class="form-label">Nome da Cor</label>
                                    <input type="text" class="form-control nome-cor-input" name="nome_cor[]"
                                        placeholder="Nome da cor">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Cor</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color color-picker"
                                            name="cor_produto[]" value="#563d7c" title="Escolha a sua cor">
                                        <span class="colorValue fw-bold">#563d7c</span>
                                    </div>
                                </div>
                                <div class="col-12 mt-2">
                                    <button type="button" class="btn btn-outline-primary addColorBtn">Adicionar
                                        Cor</button>
                                    <div class="selectedColors"></div>
                                </div>
                            </div>

                            <div class="row g-2" id="dimensionsSection">
                                <div class="col-md-4">
                                    <label class="form-label">Tipo de Dimensão</label>
                                    <input type="text" class="form-control tipo-dimensao-input" name="tipo_dimensao[]"
                                        placeholder="Tipo de dimensão">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Dimensão</label>
                                    <input type="text" class="form-control" name="dimensao_produto[]"
                                        placeholder="Dimensão">
                                </div>
                                <div class="col-12 mt-2">
                                    <button type="button" class="btn btn-outline-primary addDimensionBtn">Adicionar
                                        Dimensão</button>
                                    <div class="selectedDimensions"></div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Popup para Editar Produto -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Formulário -->
                    <form id="editProductForm" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <input type="hidden" name="imagem_principal" id="editImagemPrincipal">
                            <!-- Área de upload Imagem -->
                            <div class="col-md-6 p-4 border border-dashed text-center rounded bg-light">
                                <div class="mb-2">
                                    <i class="bi bi-cloud-upload fs-1 text-muted"></i>
                                </div>
                                <p class="text-muted">Drag and drop your product image here<br><strong>or</strong></p>
                                <input type="file" class="form-control" name="product_image" id="editProductImage"
                                    style="display:none;">
                                <button class="btn btn-outline-primary" id="editBrowseButton">Browse Files</button>
                                <span id="editFileName" class="ms-2 text-muted"></span>
                            </div>
                            <!-- Área de upload Modelo 3D -->
                            <input type="hidden" name="modelo3d_produto" id="editModelo3dProduto">
                            <div class="col-md-6 p-4 border border-dashed text-center rounded bg-light">
                                <div class="mb-2">
                                    <i class="bi bi-cloud-upload fs-1 text-muted"></i>
                                </div>
                                <p class="text-muted">3D Model (.glb, ...)<br><strong>or</strong></p>
                                <input type="file" class="form-control" name="modelo3d_produto" id="editProductModel3D"
                                    style="display:none;">
                                <button type="button" class="btn btn-outline-primary" id="editModelButton">Browse
                                    Model</button>
                                <span id="editNameModel" class="ms-2 text-muted"></span>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6" id="editProductName">
                                <label class="form-label">Product Name</label>
                                <input type="text" class="form-control" name="titulo_produto">
                            </div>
                            <div class="col-md-6" id="editKeywords">
                                <label class="form-label">Keywords</label>
                                <input type="text" class="form-control" name="keywords_produto">
                            </div>

                            <div class="col-md-6" id="editCategory">
                                <label class="form-label">Category</label>
                                <select class="form-select category-options" name="id_categoria">
                                </select>
                            </div>
                            <div class="col-md-6" id="editPrice">
                                <label class="form-label">Price</label>
                                <input type="text" class="form-control" name="preco_produto" placeholder="$">
                            </div>

                            <div class="col-md-6" id="editStock">
                                <label class="form-label">Stock Quantity</label>
                                <input type="number" class="form-control" name="stock_produto">
                            </div>

                            <div class="col-md-6" id="editStatus">
                                <label class="form-label">Status</label>
                                <select class="form-select status-options" name="status_produto">
                                </select>
                            </div>

                            <div class="col-12" id="editDescription">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" rows="3" name="descricao_produto"></textarea>
                            </div>
                            <div class="row g-2" id="variantsSection">
                                <div class="col-md-4">
                                    <label class="form-label">Nome da Cor</label>
                                    <input type="text" class="form-control nome-cor-input" name="nome_cor[]"
                                        placeholder="Nome da cor">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Cor</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color color-picker"
                                            name="cor_produto[]" value="#563d7c" title="Escolha a sua cor">
                                        <span class="colorValue fw-bold">#563d7c</span>
                                    </div>
                                </div>
                                <div class="col-12 mt-2">
                                    <button type="button" class="btn btn-outline-primary addColorBtn">Adicionar
                                        Cor</button>
                                    <div class="selectedColors"></div>
                                </div>
                            </div>

                            <div class="row g-2" id="dimensionsSection">
                                <div class="col-md-4">
                                    <label class="form-label">Tipo de Dimensão</label>
                                    <input type="text" class="form-control tipo-dimensao-input" name="tipo_dimensao[]"
                                        placeholder="Tipo de dimensão">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Dimensão</label>
                                    <input type="text" class="form-control" name="dimensao_produto[]"
                                        placeholder="Dimensão">
                                </div>
                                <div class="col-12 mt-2">
                                    <button type="button" class="btn btn-outline-primary addDimensionBtn">Adicionar
                                        Dimensão</button>
                                    <div class="selectedDimensions"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Popup para Apagar Produto -->
    <div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModal">Confirm Deactivate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p>Are you sure you want to deactivate this product?</p>
                    <button class="btn btn-danger" id="confirmDelete">Yes</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>

            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/produtosAdmin.js"></script>
</body>

</html>