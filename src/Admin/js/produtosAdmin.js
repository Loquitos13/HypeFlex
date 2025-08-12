let allProductsAdmin = [];
let selectedColors = [];
let selectedDimensions = [];
let allCores = [];
let allDimensions = [];
let currentEditProductId = null;
let currentDeleteProductId = null;
let currentCategoryFilter = "all";
let currentStatusFilter = "all";

document.addEventListener('DOMContentLoaded', async function () {
    await carregarProdutosAdmin();
    await fetchCores();
    await fetchDimensions();
    await fetchAndDisplayProducts("all", "all");

    setupCategoryFilterEvents();
    setupStatusFilterEvents();
    setupProductFormSubmit();
    setupEditProductFormSubmit();
    setupDeleteProductEvent();
    setupFileInputs();
    setupColorPickerInput();
    setupDeleteButtonEvent();
    setupAddColorButtons();
    setupAddDimensionButtons();

    // Limpa os arrays quando abre o formulário de inserção
    document.getElementById('addProductModal').addEventListener('show.bs.modal', function () {
        selectedColors = [];
        selectedDimensions = [];
        const form = document.getElementById('productForm');
        showColors(form);
        showDimensions(form);
    });

    // Armazena o ID do produto que está a ser editado
    document.addEventListener('click', async function (e) {
        const form = document.getElementById('editProductForm');
        const editButton = e.target.closest('button[data-bs-target="#editProductModal"]');

        if (editButton) {
            currentEditProductId = editButton.getAttribute('data-id');
            const produto = allProductsAdmin.find(p => p.id_produto == currentEditProductId);

            if (produto) {
                // Limpa os arrays temporários
                selectedColors = [];
                selectedDimensions = [];

                try {
                    // 1. Busca as cores associadas ao produto (via variantes)
                    const coresDoProduto = await fetchCoresDoProduto(currentEditProductId);
                    if (coresDoProduto && coresDoProduto.length > 0) {
                        selectedColors = coresDoProduto.map(cor => ({
                            id_cor: cor.id_cor,
                            nome_cor: cor.nome_cor,
                            hex_cor: cor.hex_cor
                        }));
                    }

                    // 2. Busca as dimensões do produto
                    const dimensoesDoProduto = await fetchDimensoesDoProduto(currentEditProductId);
                    if (dimensoesDoProduto && dimensoesDoProduto.length > 0) {
                        selectedDimensions = dimensoesDoProduto.map(dim => ({
                            dimensao_tipo: dim.dimensao_tipo,
                            tamanho: dim.tamanho
                        }));
                    }

                    // Preenche o formulário
                    preencherModalEdicao(produto);
                    showColors(form);
                    showDimensions(form);

                } catch (error) {
                    console.error("Erro ao carregar dados do produto:", error);
                    alert("Não foi possível carregar todos os dados do produto");
                }
            }
        }
    });
});

// Função para buscar cores
async function fetchCores() {
    const response = await fetch("../../admin/getColors.php");
    allCores = await response.json();
}

// Função para buscar dimensões
async function fetchDimensions() {
    const response = await fetch("../../admin/getDimensions.php");
    allDimensions = await response.json();
}

// Função para buscar categorias
async function fetchCategories() {
    try {
        const response = await fetch("../../admin/getCategories.php");
        const data = await response.json();
        if (Array.isArray(data)) {
            return data;
        } else {
            console.error("Dados inesperados:", data);
            return null;
        }
    } catch (err) {
        console.error("Erro ao buscar categorias:", err);
        return null;
    }
}

// Função para buscar produtos
async function fetchProducts() {
    try {
        const response = await fetch("../../admin/getProducts.php");
        const data = await response.json();
        if (Array.isArray(data)) {
            return data;
        } else {
            console.error("Dados inesperados:", data);
            return null;
        }
    } catch (err) {
        console.error("Erro ao buscar produtos:", err);
        return null;
    }
}

// Função para carregar produtos admin
async function carregarProdutosAdmin() {
    const response = await fetch("../../admin/getProductsAdmin.php");
    allProductsAdmin = await response.json();
}

// Função para buscar e mostrar produtos
async function fetchAndDisplayProducts(selectedCategory = null, selectedStatus = null) {
    try {
        const response = await fetch("../../admin/getProductsAdmin.php");
        const data = await response.json();

        allProductsAdmin = data;

        const tbody = document.getElementById("productTableBody");
        tbody.innerHTML = "";

        let filteredData = data;
        if (selectedCategory && selectedCategory !== "all") {
            filteredData = data.filter(product => product.id_categoria == selectedCategory);
        }

        if (selectedStatus && selectedStatus !== "all") {
            filteredData = filteredData.filter(product => product.status_produto == selectedStatus);
        }

        if (Array.isArray(filteredData)) {
            filteredData.forEach(product => {
                const tr = document.createElement("tr");

                tr.innerHTML = `
                    <th scope="row">${product.titulo_produto}</th>
                    <td>${product.id_categoria}</td>
                    <td>${parseFloat(product.preco_produto).toFixed(2)}€</td>
                    <td>${product.stock_produto}</td>
                    <td>${product.status_produto == 1 ? "Active" : "Inactive"}</td>
                    <td>
                        <button type="button" class="btn btn-sm" data-id="${product.id_produto}" data-bs-toggle="modal" data-bs-target="#editProductModal">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button type="button" class="btn btn-sm" data-id="${product.id_produto}" data-bs-toggle="modal" data-bs-target="#deleteProductModal">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            console.error("Dados inesperados:", data);
        }
    } catch (err) {
        console.error("Erro ao buscar produtos:", err);
        console.log("Resposta do servidor:", await response.text());
    }
}

// Função para configurar eventos do filtro de categoria
function setupCategoryFilterEvents() {
    fetchCategories().then(categories => {
        document.querySelectorAll('.category-options').forEach(categoryScroll => {
            let optionAll = document.createElement('option');
            optionAll.value = "all";
            optionAll.innerHTML = "All Categories";
            optionAll.selected = true;
            categoryScroll.appendChild(optionAll);

            for (let i = 0; i < categories.length; i++) {
                let option = document.createElement('option');
                option.value = categories[i].id_categoria;
                option.innerHTML = categories[i].titulo_categoria;
                categoryScroll.appendChild(option);
            }
        });
    });

    document.querySelectorAll('.category-options').forEach(categoryScroll => {
        categoryScroll.addEventListener("change", async function () {
            currentCategoryFilter = this.value;
            if (currentCategoryFilter.innerHTML === "All Categories") {
                currentCategoryFilter = "all";
            }
            await fetchAndDisplayProducts(currentCategoryFilter, currentStatusFilter);
        });
    });
}

// Função para configurar eventos do filtro de status
function setupStatusFilterEvents() {
    fetchProducts().then(products => {
        const statusAdicionados = new Set();
        const opcoesStatus = [];

        for (let i = 0; i < products.length; i++) {
            const status = products[i].status_produto;
            if (!statusAdicionados.has(status)) {
                statusAdicionados.add(status);
                opcoesStatus.push(status);
            }
        }

        document.querySelectorAll('.status-options').forEach(statusScroll => {
            statusScroll.innerHTML = "";
            let optionAll = document.createElement('option');
            optionAll.value = "all";
            optionAll.innerHTML = "All Status";
            optionAll.selected = true;
            statusScroll.appendChild(optionAll);

            opcoesStatus.forEach(status => {
                let option = document.createElement('option');
                option.value = status;
                option.innerHTML = status == 1 ? "Active" : "Inactive";
                statusScroll.appendChild(option);
            });
        });
    });

    document.querySelectorAll('.status-options').forEach(statusScroll => {
        statusScroll.addEventListener("change", async function () {
            currentStatusFilter = this.value;
            if (currentStatusFilter.innerHTML === "All Status") {
                currentStatusFilter = "all";
            }
            await fetchAndDisplayProducts(currentCategoryFilter, currentStatusFilter);
        });
    });
}

// Função para configurar evento do formulário de inserção
function setupProductFormSubmit() {
    document.getElementById("productForm").addEventListener("submit", async function (e) {
        e.preventDefault();

        // Enviar tudo (produto, cor, dimensao, variantes) para o backend
        await inserirProdutoCompleto();
    });
}

// Função para o fetch do produto
async function insertProductFetch(formData) {
    try {
        const response = await fetch("../../admin/insertProducts.php", {
            method: "POST",
            body: formData
        });

        const responseText = await response.text();

        let productResult;
        try {
            productResult = JSON.parse(responseText);
        } catch (e) {
            alert('Erro: Resposta inválida do servidor');
            return {};
        }

        return productResult;
    } catch (error) {
        alert('Erro ao inserir produto: ' + error.message);
        return {};
    }
}

// Função para configurar evento do formulário de edição
function setupEditProductFormSubmit() {
    document.getElementById('editProductForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        // Edita tudo (produto, cor, dimensoes, variantes)
        await editarProdutoCompleto();
    });
}

// Função para configurar evento de eliminação
function setupDeleteProductEvent() {
    document.getElementById("confirmDelete").addEventListener("click", async function (e) {
        e.preventDefault();
        await productDeletion(currentDeleteProductId);
        await carregarProdutosAdmin();
        await fetchAndDisplayProducts(currentCategoryFilter, currentStatusFilter);
        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteProductModal'));
        if (modal) modal.hide();
        alert('Product deactivated successfully');
    });
}

// Função para configurar inputs de ficheiro
function setupFileInputs() {
    //Função para associar botões e inputs aos labels
    function setupFileInput(buttonId, inputId, labelId) {
        const button = document.getElementById(buttonId);
        const input = document.getElementById(inputId);
        const label = document.getElementById(labelId);

        if (button && input) {
            button.addEventListener("click", function (e) {
                e.preventDefault();
                input.click();
            });

            input.addEventListener("change", function () {
                if (input.files.length > 0) {
                    if (label) label.textContent = input.files[0].name;
                } else {
                    if (label) label.textContent = "";
                }
            });
        }
    }

    setupFileInput("browseImageButton", "productImage", "fileNameImage");
    setupFileInput("browseModelButton", "productModel3D", "fileNameModel");
    setupFileInput("editBrowseButton", "editProductImage", "editFileName");
    setupFileInput("editModelButton", "editProductModel3D", "editNameModel");
}

// Função para atualizar valor do color picker em tempo real
function setupColorPickerInput() {
    document.addEventListener('input', function (e) {
        if (e.target.matches('input[type="color"].color-picker')) {
            const span = e.target.parentElement.querySelector('.colorValue');
            if (span) span.textContent = e.target.value;
        }
    });
}

// Função para guardar o ID do produto a eliminar
function setupDeleteButtonEvent() {
    document.addEventListener('click', function (e) {
        const deleteButton = e.target.closest('button[data-bs-target="#deleteProductModal"]');
        if (deleteButton) {
            currentDeleteProductId = deleteButton.getAttribute('data-id');
            console.log("ID armazenado:", currentDeleteProductId);
        }
    });
}

function preencherModalEdicao(produto) {
    const form = document.getElementById('editProductForm');

    // Campos básicos do produto
    form.querySelector('input[name="titulo_produto"]').value = produto.titulo_produto || '';
    form.querySelector('input[name="keywords_produto"]').value = produto.keywords_produto || '';
    form.querySelector('input[name="preco_produto"]').value = produto.preco_produto || '';
    form.querySelector('input[name="stock_produto"]').value = produto.stock_produto || '';
    form.querySelector('textarea[name="descricao_produto"]').value = produto.descricao_produto || '';

    // Categoria e status
    const categoriaSelect = form.querySelector('select[name="id_categoria"]');
    if (categoriaSelect) categoriaSelect.value = produto.id_categoria || '';

    const statusSelect = form.querySelector('select[name="status_produto"]');
    if (statusSelect) statusSelect.value = produto.status_produto || 'active';

    // Imagens
    if (produto.imagem_principal) {
        document.getElementById('editImagemPrincipal').value = produto.imagem_principal;
        document.getElementById('editFileName').textContent = produto.imagem_principal;
    }

    if (produto.modelo3d) {
        document.getElementById('editModelo3dProduto').value = produto.modelo3d;
        document.getElementById('editNameModel').textContent = produto.modelo3d;
    }
}

// Função para configurar botões de adicionar cor ao DOM
function setupAddColorButtons() {
    document.querySelectorAll('.addColorBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const form = btn.closest('form');
            const nomeCorInput = form.querySelector('.nome-cor-input');
            const colorPicker = form.querySelector('.color-picker');

            const nomeCor = nomeCorInput.value.trim();
            const hexCor = colorPicker.value.toUpperCase();


            if (!nomeCor) {
                alert('Por favor, insira um nome para a cor');
                return;
            }

            // Verifica se a cor já foi adicionada
            const corJaExiste = selectedColors.some(cor =>
                cor.nome_cor.toLowerCase() === nomeCor.toLowerCase() ||
                cor.hex_cor === hexCor
            );

            if (corJaExiste) {
                alert('Esta cor já foi adicionada');
                return;
            }

            // Adiciona ao array e atualiza o DOM
            selectedColors.push({
                nome_cor: nomeCor,
                hex_cor: hexCor
            });
            console.log(selectedColors);
            showColors(form);
        });
    });
}

// Função para configurar botões de adicionar dimensão ao DOM
function setupAddDimensionButtons() {
    document.querySelectorAll('.addDimensionBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const form = btn.closest('form');
            const tipoInput = form.querySelector('.tipo-dimensao-input');
            const dimensaoInput = form.querySelector('input[name="dimensao_produto[]"]');

            const tipoDimensao = tipoInput.value.trim();
            const dimensao = dimensaoInput.value.trim();

            if (!tipoDimensao || !dimensao) {
                alert('Por favor, preencha ambos os campos de dimensão');
                if (!tipoDimensao) tipoInput.focus();
                else dimensaoInput.focus();
                return;
            }

            const dimensaoJaExiste = selectedDimensions.some(dim =>
                dim.dimensao_tipo.toLowerCase() === tipoDimensao.toLowerCase() &&
                dim.tamanho.toLowerCase() === dimensao.toLowerCase()
            );

            if (dimensaoJaExiste) {
                alert('Esta dimensão já foi adicionada');
                return;
            }

            selectedDimensions.push({
                dimensao_tipo: tipoDimensao,
                tamanho: dimensao
            });
            console.log(selectedDimensions);
            showDimensions(form);
        });
    });
}

async function productUpdate(formElement) {
    const formData = new FormData(formElement);
    formData.set('id_produto', currentEditProductId);

    if (!formData.get("imagem_principal") && !formData.get("product_image")) {
        alert("Por favor, selecione uma imagem ou mantenha a atual.");
        return;
    }

    try {
        const response = await fetch("../../admin/editProducts.php", {
            method: "POST",
            body: formData
        });

        const responseText = await response.text();

        let productResult;
        try {
            productResult = JSON.parse(responseText);
        } catch (e) {
            alert('Erro: Resposta inválida do servidor');
            return {};
        }

        return productResult;
    } catch (error) {
        alert('Erro ao inserir produto: ' + error.message);
        return {};
    }
}

// Função para apagar produto
async function productDeletion(productId) {
    const response = await fetch(`../../admin/deleteProducts.php`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ id_produto: productId }),
    });

    return await response.json();
}

// Função para mostrar as cores no formulário
function showColors(form = document) {
    const container = form.querySelector('.selectedColors');
    if (!container) return;

    container.innerHTML = '';
    if (selectedColors.length === 0) {
        container.innerHTML = '<span class="text-muted">Nenhuma cor selecionada.</span>';
        return;
    }

    selectedColors.forEach((cor, idx) => {
        const colorDiv = document.createElement('div');
        colorDiv.className = 'color-item position-relative mb-1';

        colorDiv.innerHTML = `
            <span class="color-badge" style="background:${cor.hex_cor};"></span>
            <span class="color-name">${cor.nome_cor}</span>
            <span class="color-hex">${cor.hex_cor}</span>
            <button type="button" class="remove-variant-btn" data-index="${idx}" title="Remover cor">&times;</button>
        `;
        container.appendChild(colorDiv);
    });

    container.querySelectorAll('.remove-variant-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const idx = parseInt(this.getAttribute('data-index'));
            selectedColors.splice(idx, 1);
            showColors(form);
        });
    });
}

// Função para mostrar as dimensões no formulário
function showDimensions(form = document) {
    const container = form.querySelector('.selectedDimensions');
    if (!container) return;

    container.innerHTML = '';
    if (selectedDimensions.length === 0) {
        container.innerHTML = '<span class="text-muted">Nenhuma dimensão selecionada.</span>';
        return;
    }

    selectedDimensions.forEach((dim, idx) => {
        const dimDiv = document.createElement('div');
        dimDiv.className = 'dimension-item position-relative mb-1';

        dimDiv.innerHTML = `
            <span class="dimension-type">${dim.dimensao_tipo}</span>
            <span class="dimension-size">${dim.tamanho}</span>
            <button type="button" class="remove-variant-btn" data-index="${idx}" title="Remover dimensão">&times;</button>
        `;
        container.appendChild(dimDiv);
    });

    container.querySelectorAll('.remove-variant-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const idx = parseInt(this.getAttribute('data-index'));
            selectedDimensions.splice(idx, 1);
            showDimensions(form);
        });
    });
}

// Função para saber que formulário está aberto
function getActiveProductForm() {
    const insertForm = document.getElementById('productForm');
    const editForm = document.getElementById('editProductForm');

    if (insertForm && insertForm.closest('.modal.show')) return insertForm;
    if (editForm && editForm.closest('.modal.show')) return editForm;
    return null;
}

async function fetchCoresDoProduto(idProduto) {
    try {
        const response = await fetch(`../../admin/getColorsByProduct.php?id_produto=${idProduto}`);
        const data = await response.json();

        if (data.cores) {
            return data.cores;
        } else if (data.error) {
            console.error("Erro na API:", data.error);
            return [];
        } else {
            return [];
        }
    } catch (error) {
        console.error("Erro ao buscar cores:", error);
        return [];
    }
}

async function fetchDimensoesDoProduto(idProduto) {
    try {
        const response = await fetch(`../../admin/getDimensionsByProduct.php?id_produto=${idProduto}`);
        const data = await response.json();

        if (data.dimensoes) {
            return data.dimensoes;
        } else if (data.error) {
            console.error("Erro na API:", data.error);
            return [];
        } else {
            return [];
        }
    } catch (error) {
        console.error("Erro ao buscar dimensões:", error);
        return [];
    }
}

// Função para atualizar Dimensão
async function updateDimension(id_dimensao, novoTipo, novoTamanho) {
    if (!id_dimensao || !novoTipo || !novoTamanho) {
        console.error("Dados em falta para atualização da dimensão.");
        return;
    }

    const data = {
        id_dimensao: id_dimensao,
        dimensao_tipo: novoTipo,
        tamanho: novoTamanho
    };

    const resultado = await updateDimensionFetch(data);

    if (resultado.success) {
        alert("Dimensão atualizada com sucesso!");
    } else {
        alert("Erro ao atualizar dimensão: " + resultado.message);
    }
}

// Função para atualizar variantes de produto
async function updateProductVariant(id_variante, novoIdCor, novaPromocao = 0) {
    if (!id_variante || !novoIdCor) {
        console.error("Dados em falta para atualização da variante.");
        return;
    }

    const data = {
        id_produto_variante: id_variante,
        id_cor: novoIdCor,
        promocao: novaPromocao
    };

    const resultado = await updateProductVariantFetch(data);

    if (resultado.success) {
        alert("Variante atualizada com sucesso!");
    } else {
        alert("Erro ao atualizar variante: " + resultado.message);
    }
}

// Função para o inserir a cor
async function insertColorFetch(data) {
    try {
        const response = await fetch("../../admin/insertColors.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        return result;
    } catch (error) {
        console.error("Erro ao inserir cor:", error);
        return { success: false, message: error.message };
    }
}

// Função para inserir a dimensão
async function insertDimensionFetch(data) {
    try {
        const response = await fetch("../../admin/insertDimension.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        return result;
    } catch (error) {
        console.error("Erro ao inserir dimensão:", error);
        return { success: false, message: error.message };
    }
}

// Função para inserir a variante do produto
async function insertProductVariantFetch(data) {
    try {
        const response = await fetch("../../admin/insertProductVariant.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        return result;
    } catch (error) {
        console.error("Erro ao inserir variante do produto:", error);
        return { success: false, message: error.message };
    }
}

// Função para atualizar a dimensão 
async function updateDimensionFetch(data) {
    try {
        const response = await fetch("../../admin/updateDimensions.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        return result;
    } catch (error) {
        console.error("Erro ao atualizar dimensão:", error);
        return { success: false, message: error.message };
    }
}

// Função para atualizar a variante do produto
async function updateProductVariantFetch(data) {
    try {
        const response = await fetch("../../admin/updateProductVariant.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        return result;
    } catch (error) {
        console.error("Erro ao atualizar variante de produto:", error);
        return { success: false, message: error.message };
    }
}

// Função para inserir tudo (Produto, Dimensão, Cor, Variante de Produto)
async function inserirProdutoCompleto() {
    const form = document.getElementById("productForm");
    const formData = new FormData(form);

    const id_categoria = form.querySelector('select[name="id_categoria"]').value;
    const titulo_produto = form.querySelector('input[name="titulo_produto"]').value;
    const modelo3d_produto = form.querySelector('#productModel3D').files[0] || null;
    const descricao_produto = form.querySelector('textarea[name="descricao_produto"]').value;
    const imagem_principal = form.querySelector('#productImage').files[0];
    const preco_produto = form.querySelector('input[name="preco_produto"]').value;
    const stock_produto = form.querySelector('input[name="stock_produto"]').value;
    const keywords_produto = form.querySelector('input[name="keywords_produto"]').value;
    const status_produto = form.querySelector('select[name="status_produto"]').value;

    // Validação dos campos obrigatórios
    if (!imagem_principal || !titulo_produto || !keywords_produto ||
        !id_categoria || !preco_produto || !stock_produto ||
        !status_produto || !descricao_produto) {
        alert("Por favor, preencha todos os campos obrigatórios.");
        return;
    }

    formData.append('id_categoria', id_categoria);
    formData.append('titulo_produto', titulo_produto);
    formData.append('descricao_produto', descricao_produto);
    formData.append('imagem_principal', imagem_principal);
    if (modelo3d_produto) {
        formData.append('modelo3d_produto', modelo3d_produto);
    }
    formData.append('preco_produto', preco_produto);
    formData.append('stock_produto', stock_produto);
    formData.append('keywords_produto', keywords_produto);
    formData.append('status_produto', status_produto);

    // 1. Insere o produto
    const productResult = await insertProductFetch(formData);
    if (!productResult.status || productResult.status !== 'success') {
        alert(productResult.message || "Erro ao inserir produto.");
        return;
    }
    const id_produto = productResult.data.id_produto;

    console.log(productResult);
    console.log("Dimensões a inserir:", selectedDimensions);

    // 2. Insere todas as dimensões (mesmo que iguais), associadas a este produto
    let dimensoesIds = [];
    for (const dim of selectedDimensions) {

        const payload = {
            dimensao_tipo: dim.dimensao_tipo,
            tamanho: dim.tamanho.replace(' ', /%20/g),
            id_produto: id_produto
        };

        console.log("Payload para inserção:", payload);

        const dimResult = await insertDimensionFetch(payload);
        if (dimResult.success) {
            dimensoesIds.push(dimResult.id_dimensao);
        } else {
            alert(dimResult.message || "Erro ao inserir dimensão.");
        }
    }

    // 3. Cores: verifica se já existem, senão insere nova
    let coresIds = [];
    for (const cor of selectedColors) {
        let id_cor = cor.id_cor;
        if (!id_cor) {
            const corResult = await insertColorFetch({
                nome_cor: cor.nome_cor,
                hex_cor: cor.hex_cor
            });
            if (corResult.success) {
                id_cor = corResult.id_cor;
            } else if (corResult.id_cor) {
                id_cor = corResult.id_cor;
            } else {
                alert(corResult.message || "Erro ao inserir cor.");
            }
        }
        coresIds.push(id_cor);
    }

    console.log("Cores selecionadas:", selectedColors);
    console.log("IDs de cores a processar:", coresIds);

    // 4. Insere variantes (uma para cada cor, associada ao produto)
    for (const id_cor of coresIds) {
        console.log("Criando variante para cor ID:", id_cor);
        const varianteResult = await insertProductVariantFetch({
            id_produto: id_produto,
            id_cor: id_cor,
            promocao: 0
        });
        console.log("Resultado da variante:", varianteResult);
        if (!varianteResult.success) {
            alert(varianteResult.message || "Erro ao inserir variante.");
        }
    }

    alert("Product inserted successfully!");
    await carregarProdutosAdmin();
    await fetchAndDisplayProducts(currentCategoryFilter, currentStatusFilter);

    const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
    if (modal) modal.hide();
}

// Função para editar tudo 
async function editarProdutoCompleto() {
    const form = document.getElementById("editProductForm");

    // 1. Atualiza o produto principal
    const productResult = await productUpdate(form);
    if (!productResult.status || productResult.status !== 'success') {
        alert(productResult.message || "Erro ao atualizar produto.");
        return;
    }

    const id_produto = currentEditProductId;

    // 2. Insere novas dimensões associadas ao produto
    for (const dim of selectedDimensions) {
        const dimResult = await insertDimensionFetch({
            dimensao_tipo: dim.dimensao_tipo,
            tamanho: dim.tamanho,
            id_produto: id_produto
        });
        if (!dimResult.success) {
            alert(dimResult.message || "Erro ao inserir dimensão.");
        }
    }

    // 3. Atualiza variantes (cor)
    const produto = allProductsAdmin.find(p => p.id_produto == id_produto);
    if (!produto || !produto.variantes) {
        alert("Não foi possível encontrar as variantes do produto.");
        return;
    }

    // 4. Insere novas cores
    for (const cor of selectedColors) {
        let id_cor = cor.id_cor;

        if (!id_cor) {
            const corResult = await insertColorFetch({
                nome_cor: cor.nome_cor,
                hex_cor: cor.hex_cor
            });
            if (corResult.success) {
                id_cor = corResult.id_cor;
            } else if (corResult.id_cor) {
                id_cor = corResult.id_cor;
            } else {
                alert(corResult.message || "Erro ao inserir nova cor.");
                continue;
            }
        }

        const variante = produto.variantes.find(v => v.id_cor == id_cor);

        if (variante && variante.id_produto_variante) {
            await updateProductVariant(variante.id_produto_variante, id_cor, variante.promocao || 0);
        } else {
            await insertProductVariantFetch({
                id_produto: id_produto,
                id_cor: id_cor,
                promocao: 0
            });
        }
    }

    alert("Produto atualizado com sucesso!");
    await carregarProdutosAdmin();
    await fetchAndDisplayProducts(currentCategoryFilter, currentStatusFilter);
    const modal = bootstrap.Modal.getInstance(document.getElementById('editProductModal'));
    if (modal) modal.hide();
}