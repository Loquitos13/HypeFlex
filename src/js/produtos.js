// função para adicionar o link ativado ao header
document.addEventListener("DOMContentLoaded", function () {
    document.querySelector("#link-produtos").innerHTML = `<li class="nav-item"><a href="<?= $base_url ?>/index.php" class="nav-link active" style="background-color: #4F46E5;">Products</a></li>`;
});

document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.getElementById('menu-toggle');
    const menuMobile = document.getElementById('menu-mobile');

    menuToggle.addEventListener('click', function () {
        if (menuMobile.style.display === 'flex') {
            menuMobile.style.display = 'none';
        } else {
            menuMobile.style.display = 'flex';
        }
    });

    // Opcionalmente fecha o menu ao clicar em um link
    const links = menuMobile.querySelectorAll('a');
    links.forEach(link => {
        link.addEventListener('click', () => {
            menuMobile.style.display = 'none';
        });
    });
});

// Verificar responsividade
function checkResponsiveness() {
    const isMobile = window.innerWidth <= 576;
    const isTablet = window.innerWidth <= 768 && window.innerWidth > 576;

    // Ajustar altura do carrossel com base no tamanho da tela
    const carouselContent = document.querySelector('.carousel-content');
    const dataFeedbacks = document.querySelectorAll('.feedback-date');
    if (carouselContent) {
        if (isMobile) {
            carouselContent.style.minHeight = '350px';
            dataFeedbacks.forEach(item => {
                item.style.display = 'none';
            });
        } else if (isTablet) {
            carouselContent.style.minHeight = '320px';
            dataFeedbacks.forEach(item => {
                item.style.display = 'none';
            });
        } else {
            carouselContent.style.minHeight = '300px';
            dataFeedbacks.forEach(item => {
                item.style.display = 'block';
            });
        }
    }
}


document.addEventListener("DOMContentLoaded", () => {
    checkResponsiveness();
});

document.addEventListener('DOMContentLoaded', function () {
    const filtersSidebar = document.getElementById('filters-sidebar');
    const filtersOverlay = document.getElementById('filters-overlay');
    const applyFilters = document.getElementById('apply-filters');

    // Fechar filtros
    function closeFiltersSidebar() {
        filtersSidebar.classList.remove('open');
        filtersOverlay.classList.remove('active');
    }

    if (filtersOverlay) {
        filtersOverlay.addEventListener('click', closeFiltersSidebar);
    }

    // Aplicar filtros
    if (applyFilters) {
        applyFilters.addEventListener('click', function () {
            // Sua lógica de filtragem aqui
            console.log('Filtros aplicados!');
            closeFiltersSidebar();
        });
    }
});


// --- SUPORTE MOBILE E DESKTOP PARA FILTROS ---

// Função utilitária para obter prefixos de IDs para desktop/mobile
function getFiltroPrefix(isMobile) {
    return isMobile ? '-mobile' : '';
}

// --- FILTROS DESKTOP E MOBILE: LÓGICA UNIFICADA E DINÂMICA ---

// Utilitário para obter os elementos de filtro por contexto
function getFiltroElements(context) {
    return {
        categoriasContainer: document.getElementById(context === 'mobile' ? 'categorias-container-mobile' : 'categorias-container'),
        colorContainer: document.getElementById(context === 'mobile' ? 'idColorOptions-mobile' : 'idColorOptions'),
        sizeContainer: document.getElementById(context === 'mobile' ? 'size-selector-mobile' : 'size-selector-desktop'),
        precoMinInput: document.getElementById(context === 'mobile' ? 'price-min-input-mobile' : 'price-min-input'),
        precoMaxInput: document.getElementById(context === 'mobile' ? 'price-max-input-mobile' : 'price-max-input'),
        applyBtn: document.getElementById(context === 'mobile' ? 'apply-filters' : 'apply-filters-desktop'),
        clearBtn: document.getElementById(context === 'mobile' ? 'clear-filters-mobile' : 'clear-filters-desktop'),
        categoriaPrefix: context === 'mobile' ? 'defaultCategoryMobile' : 'defaultCategory',
        colorRadioName: context === 'mobile' ? 'color-mobile' : 'color',
        sizeRadioName: context === 'mobile' ? 'size-mobile' : 'size-desktop',
    };
}

// Popular categorias (desktop e mobile)
function popularCategoriasAmbos() {
    fetch('../restapi/PrintGoAPI.php/getCategoriesByID')
        .then(response => response.json())
        .then(data => {
            ['desktop', 'mobile'].forEach(context => {
                const els = getFiltroElements(context);
                if (els.categoriasContainer) {
                    els.categoriasContainer.innerHTML = '';
                    data.forEach((categoria, i) => {
                        const divCategoria = document.createElement('div');
                        divCategoria.classList.add('form-check');
                        const inputCategoria = document.createElement('input');
                        inputCategoria.classList.add('form-check-input');
                        inputCategoria.type = 'checkbox';
                        inputCategoria.value = categoria.id_categoria;
                        inputCategoria.id = `${els.categoriaPrefix}${i}`;
                        const labelCategoria = document.createElement('label');
                        labelCategoria.classList.add('form-check-label');
                        labelCategoria.setAttribute('for', `${els.categoriaPrefix}${i}`);
                        labelCategoria.textContent = categoria.titulo_categoria;
                        divCategoria.appendChild(inputCategoria);
                        divCategoria.appendChild(labelCategoria);
                        els.categoriasContainer.appendChild(divCategoria);
                    });
                }
            });
        })
        .catch(error => {
            console.error('Erro ao buscar categorias:', error);
        });
}

// Atualizar cores por categoria (desktop e mobile)
async function atualizarCoresPorCategoriaAmbos(context) {
    const els = getFiltroElements(context);
    if (!els.colorContainer) return;
    els.colorContainer.innerHTML = '';
    const categoriasSelecionadas = [...document.querySelectorAll(`input[type=checkbox][id^="${els.categoriaPrefix}"]:checked`)].map(cb => cb.value);
    if (categoriasSelecionadas.length === 0) {
        els.colorContainer.innerHTML = '<p>Selecione uma categoria para ver as cores disponíveis.</p>';
        return;
    }
    try {
        const categoriasStr = categoriasSelecionadas.join(',');
        const response = await fetch(`../restapi/PrintGoAPI.php/getColorsByCategories/${categoriasStr}`);
        const data = await response.json();
        const cores = data.cores || (Array.isArray(data) ? data : []);
        if (cores.length === 0) {
            els.colorContainer.innerHTML = '<p>Nenhuma cor disponível para esta categoria.</p>';
            return;
        }
        cores.forEach(cor => {
            const input = document.createElement('input');
            input.type = 'radio';
            input.className = 'btn-check';
            input.name = els.colorRadioName;
            input.id = `color-${cor.nome_cor}${context === 'mobile' ? '-mobile' : ''}`;
            input.value = cor.hex_cor;
            input.setAttribute('data-color-name', cor.nome_cor);
            input.autocomplete = 'off';
            const label = document.createElement('label');
            label.className = 'btnColor rounded-circle p-2';
            label.setAttribute('for', input.id);
            label.style.backgroundColor = cor.hex_cor;
            label.style.border = '2px solid #ccc';
            label.title = cor.nome_cor;
            els.colorContainer.appendChild(input);
            els.colorContainer.appendChild(label);
        });
    } catch (error) {
        els.colorContainer.innerHTML = '<p>Ocorreu um erro ao carregar as cores.</p>';
    }
}

// Atualizar tamanhos por categoria (desktop e mobile)
async function atualizarSizePorCategoriaAmbos(context) {
    const els = getFiltroElements(context);
    if (!els.sizeContainer) return;
    els.sizeContainer.innerHTML = '';
    const categoriasSelecionadas = [...document.querySelectorAll(`input[type=checkbox][id^="${els.categoriaPrefix}"]:checked`)].map(cb => cb.value);
    if (categoriasSelecionadas.length === 0) {
        els.sizeContainer.innerHTML = '<p>Selecione uma categoria para ver os tamanhos disponíveis.</p>';
        return;
    }
    try {
        const categoriasStr = categoriasSelecionadas.join(',');
        const response = await fetch(`../restapi/PrintGoAPI.php/getSizesByCategories/${categoriasStr}`);
        const data = await response.json();
        if (!Array.isArray(data)) {
            els.sizeContainer.innerHTML = '<p>Erro ao carregar tamanhos (formato inválido).</p>';
            return;
        }
        const todosTamanhos = data.flatMap(item => {
            if (typeof item.tamanho === 'string' && item.tamanho.includes(',')) {
                return item.tamanho.split(',').map(t => t.trim());
            }
            return item.tamanho;
        });
        const tamanhosSemDuplicatas = [...new Set(todosTamanhos)].filter(t => t);
        if (tamanhosSemDuplicatas.length === 0) {
            els.sizeContainer.innerHTML = '<p>Nenhum tamanho disponível para esta categoria.</p>';
            return;
        }
        tamanhosSemDuplicatas.forEach(tamanho => {
            const btnGroup = document.createElement('div');
            btnGroup.classList.add('btn-group', 'me-2');
            btnGroup.setAttribute('role', 'group');
            btnGroup.setAttribute('aria-label', 'Size option');
            const input = document.createElement('input');
            input.type = 'radio';
            input.classList.add('btn-check');
            input.name = els.sizeRadioName;
            input.value = tamanho;
            input.id = `btnradio-${tamanho}${context === 'mobile' ? '-mobile' : '-desktop'}`;
            input.autocomplete = 'off';
            const label = document.createElement('label');
            label.classList.add('btn', 'btn-outline-primary');
            label.setAttribute('for', input.id);
            label.textContent = tamanho.replace(/%20/g, ' ');
            btnGroup.appendChild(input);
            btnGroup.appendChild(label);
            els.sizeContainer.appendChild(btnGroup);
        });
    } catch (error) {
        els.sizeContainer.innerHTML = '<p>Ocorreu um erro ao carregar os tamanhos.</p>';
    }
}

// Listeners para atualizar filtros dinâmicos (categorias, cores, tamanhos) para desktop e mobile
function setupFiltroListenersAmbos() {
    ['desktop', 'mobile'].forEach(context => {
        const els = getFiltroElements(context);
        if (els.categoriasContainer) {
            els.categoriasContainer.addEventListener('change', function (e) {
                if (e.target && e.target.matches(`input[type=checkbox][id^="${els.categoriaPrefix}"]`)) {
                    atualizarCoresPorCategoriaAmbos(context);
                    atualizarSizePorCategoriaAmbos(context);
                }
            });
        }
    });
}

// Aplicar filtros (desktop e mobile)
function setupApplyClearBtnsAmbos() {
    ['desktop', 'mobile'].forEach(context => {
        const els = getFiltroElements(context);
        if (els.applyBtn) {
            els.applyBtn.addEventListener('click', function () {
                const filtros = {
                    categorias: [...document.querySelectorAll(`input[type=checkbox][id^="${els.categoriaPrefix}"]:checked`)].map(cb => cb.value),
                    precoMin: els.precoMinInput ? els.precoMinInput.value : '0',
                    precoMax: els.precoMaxInput ? els.precoMaxInput.value : '100',
                    cores: [...document.querySelectorAll(`input[name="${els.colorRadioName}"]:checked`)].map(cb => cb.getAttribute('data-color-name')),
                    tamanhos: [...document.querySelectorAll(`input[name="${els.sizeRadioName}"]:checked`)].map(cb => cb.value),
                };
                buscarProdutos(filtros);
                if (context === 'mobile') closeMobileOffcanvas();
            });
        }
        if (els.clearBtn) {
            els.clearBtn.addEventListener('click', function () {
                document.querySelectorAll(`input[type=checkbox][id^="${els.categoriaPrefix}"]`).forEach(checkbox => {
                    checkbox.checked = false;
                });
                const checkedColor = document.querySelector(`input[name="${els.colorRadioName}"]:checked`);
                if (checkedColor) checkedColor.checked = false;
                const checkedSize = document.querySelector(`input[name="${els.sizeRadioName}"]:checked`);
                if (checkedSize) checkedSize.checked = false;
                if (els.precoMinInput && els.precoMaxInput) {
                    els.precoMinInput.value = '0';
                    els.precoMaxInput.value = '100';
                }
                atualizarCoresPorCategoriaAmbos(context);
                atualizarSizePorCategoriaAmbos(context);
                buscarProdutos();
                if (context === 'mobile') closeMobileOffcanvas();
            });
        }
    });
}

// Inicialização dos filtros dinâmicos para ambos contextos

document.addEventListener('DOMContentLoaded', function () {
    popularCategoriasAmbos();
    setupFiltroListenersAmbos();
    setupApplyClearBtnsAmbos();
    setTimeout(() => {
        atualizarCoresPorCategoriaAmbos('desktop');
        atualizarSizePorCategoriaAmbos('desktop');
        atualizarCoresPorCategoriaAmbos('mobile');
        atualizarSizePorCategoriaAmbos('mobile');
    }, 500);
});

// Variável para controlar o índice atual
let currentIndex = 0;

let todosOsProdutos = []; // Store all fetched products
let paginaAtual = 1;
const produtosPorPagina = 12;


// Faz uma requisição inicial para obter os filtros (categorias) disponíveis.
fetch('../restapi/PrintGoAPI.php/getCategoriesByID')
    .then(response => response.json())
    .then(data => {

        const categoriasContainer = document.getElementById('categorias-container');

        // Agora data é um array de categorias
        data.forEach((categoria, i) => {

            const divCategoria = document.createElement('div');
            divCategoria.classList.add('form-check');

            const inputCategoria = document.createElement('input');
            inputCategoria.classList.add('form-check-input');
            inputCategoria.type = 'checkbox';
            inputCategoria.value = categoria.id_categoria;
            inputCategoria.id = `defaultCategory${i}`; // ID único agora

            const labelCategoria = document.createElement('label');
            labelCategoria.classList.add('form-check-label');
            labelCategoria.setAttribute('for', `defaultCategory${i}`);
            labelCategoria.textContent = categoria.titulo_categoria;

            divCategoria.appendChild(inputCategoria);
            divCategoria.appendChild(labelCategoria);
            categoriasContainer.appendChild(divCategoria);
        });
    })
    .catch(error => {
        console.error('Erro ao buscar produtos:', error);
    });



// Função para buscar produtos (com ou sem filtros)
function buscarProdutos(filtros = {}) {
    // Check if filters are empty
    const isEmptyFilters = Object.keys(filtros).length === 0


    let url = '';

    if (isEmptyFilters) {
        // Se não houver filtros, obtém todos os produtos
        url = '../restapi/PrintGoAPI.php/products';
    } else {
        // Formata os parâmetros para o endpoint da API
        const categoria = filtros.categorias && filtros.categorias.length > 0 ? filtros.categorias.join(',') : '_';
        const precoMinimo = filtros.precoMin || '0';
        const precoMaximo = filtros.precoMax || '100';
        const cor = filtros.cores && filtros.cores.length > 0 ? filtros.cores.join(',') : '_';
        const tamanho = filtros.tamanhos && filtros.tamanhos.length > 0 ? filtros.tamanhos.join(',') : '_';

        // Chama o endpoint da API com parâmetros de caminho
        url = `../restapi/PrintGoAPI.php/filterProducts/${categoria}/${precoMinimo}/${precoMaximo}/${cor}/${tamanho}`;
    }

    console.log('URL final para fetch:', url);

    fetch(url)
        .then(response => {
            if (!response.ok) {
                console.error("API Error Response Status:", response.status);
                return response.text().then(text => { throw new Error("API Error: " + text) });
            }
            return response.json();
        })
        .then(data => {
            todosOsProdutos = Array.isArray(data) ? data : data.products || [];
            paginaAtual = 1; // Reset to first page on new search/filter
            exibirProdutosDaPagina(paginaAtual);
            renderizarPaginacao();
        })
        .catch(error => {
            console.error('Erro ao buscar produtos ou processar resposta:', error);
            const produtosLista = document.getElementById('produtos-container');
            produtosLista.innerHTML = "<p>Ocorreu um erro ao carregar os produtos.</p>";
            todosOsProdutos = []; // Clear products on error
            renderizarPaginacao(); // Clear pagination as well
        });
}

function exibirProdutosDaPagina(pagina) {
    const produtosLista = document.getElementById('produtos-container');
    produtosLista.innerHTML = ""; // Limpa produtos existentes

    if (todosOsProdutos.length === 0) {
        produtosLista.innerHTML = "<p>Nenhum produto encontrado com os filtros selecionados.</p>";
        return;
    }

    const inicio = (pagina - 1) * produtosPorPagina;
    const fim = inicio + produtosPorPagina;
    const produtosDaPagina = todosOsProdutos.slice(inicio, fim);

    produtosDaPagina.forEach(produto => {
        //cria o container principal para ficar responsivo
        let Containner = document.createElement('div');
        Containner.classList.add('col-lg-3', 'col-md-4', 'col-sm-6', 'mb-4');
        // cria o card do produto
        let card = document.createElement('div');
        card.classList.add("card", "border-0", "shadow-sm");
        // Cria a div que vai conter a imagem do produto
        let divImg = document.createElement('div');
        divImg.classList.add("position-relative");
        divImg.style.width = "100%";
        divImg.style.height = "240px"; // Altura fixa para a imagem
        divImg.style.display = "flex";
        divImg.style.alignItems = "center";
        divImg.style.justifyContent = "center";
        divImg.style.overflow = "hidden"; // Garante que a imagem não ultrapasse os limites

        // Imagem do produto
        let img = document.createElement('img');
        img.src = produto.imagem_principal; // Use a imagem principal do produto
        img.classList.add("card-img-top", "bg-light"); // Mantém classes Bootstrap
        img.alt = produto.titulo_produto;
        // Ajustes para centralização perfeita e para não distorcer
        img.style.maxWidth = "80%"; // Limita a largura máxima da imagem
        img.style.maxHeight = "80%"; // Limita a altura máxima da imagem
        img.style.width = "auto"; // Permite que a largura se ajuste automaticamente
        img.style.height = "auto"; // Permite que a altura se ajuste automaticamente
        img.style.objectFit = "contain"; // Garante que a imagem inteira seja visível sem cortar ou esticar
        img.style.position = "absolute"; // Necessário para centralização com transform
        img.style.top = "50%";
        img.style.left = "50%";
        img.style.transform = "translate(-50%, -50%)"; // Centraliza a imagem

        // cria uma div para conter as informações do produto
        let divInfo = document.createElement("div");
        divInfo.classList.add("card-body", "px-3", "pb-3");
        // Titulo do produto
        let tituloProduto = document.createElement("h5");
        tituloProduto.classList.add("card-title", "fw-bold", "mb-1");
        tituloProduto.textContent = produto.titulo_produto;
        // Cria uma div para conter o preço e o botão
        let divPrecoBtn = document.createElement("div");
        divPrecoBtn.classList.add("d-flex", "justify-content-between", "align-items-center");
        // Preço do produto
        let precoProduto = document.createElement("span");
        precoProduto.classList.add("fw-bold");
        precoProduto.style.color = "#4F46E5";
        precoProduto.textContent = produto.preco_produto + "€";
        // Botão de comprar
        let btnComprar = document.createElement("button");
        btnComprar.type = "button";
        btnComprar.classList.add("btn", "btn-primary");
        btnComprar.style = "background-color: #4F46E5; border: 0;"
        btnComprar.textContent = "Shop Now";
        // Adiciona o evento de clique para redirecionar para a página do produto
        btnComprar.addEventListener('click', function () {
            window.location.href = "productscustom.php?id=" + produto.id_produto;
        });

        card.appendChild(divImg);
        card.appendChild(divInfo);

        divImg.appendChild(img);

        divInfo.appendChild(tituloProduto);
        divInfo.appendChild(divPrecoBtn);

        divPrecoBtn.appendChild(precoProduto);
        divPrecoBtn.appendChild(btnComprar);

        Containner.appendChild(card);

        // Adiciona o card ao container principal
        produtosLista.appendChild(Containner);
    });
}

function renderizarPaginacao() {
    const paginationContainer = document.getElementById('pagination-container');
    paginationContainer.innerHTML = ""; // Limpa paginação existente

    const totalPaginas = Math.ceil(todosOsProdutos.length / produtosPorPagina);

    if (totalPaginas <= 1) {
        return; // Não renderiza paginação se houver apenas uma página ou nenhuma
    }

    const nav = document.createElement('nav');
    nav.setAttribute('aria-label', 'Page navigation');

    const ul = document.createElement('ul');
    ul.classList.add('pagination', 'justify-content-center'); // Adicionado justify-content-center para centralizar

    // Botão "Previous"
    const liPrevious = document.createElement('li');
    liPrevious.classList.add('page-item');
    if (paginaAtual === 1) {
        liPrevious.classList.add('disabled');
    }
    const aPrevious = document.createElement('a');
    aPrevious.classList.add('page-link');
    aPrevious.href = '#';
    aPrevious.textContent = 'Previous';
    aPrevious.addEventListener('click', (e) => {
        e.preventDefault();
        if (paginaAtual > 1) {
            paginaAtual--;
            exibirProdutosDaPagina(paginaAtual);
            renderizarPaginacao(); // Re-renderiza para atualizar o estado dos botões
        }
    });
    liPrevious.appendChild(aPrevious);
    ul.appendChild(liPrevious);

    // Botões de página
    for (let i = 1; i <= totalPaginas; i++) {
        const li = document.createElement('li');
        li.classList.add('page-item');
        if (i === paginaAtual) {
            li.classList.add('active');
        }
        const a = document.createElement('a');
        a.classList.add('page-link');
        a.href = '#';
        a.textContent = i;
        a.addEventListener('click', (e) => {
            e.preventDefault();
            paginaAtual = i;
            exibirProdutosDaPagina(paginaAtual);
            renderizarPaginacao(); // Re-renderiza para atualizar o estado dos botões
        });
        li.appendChild(a);
        ul.appendChild(li);
    }

    // Botão "Next"
    const liNext = document.createElement('li');
    liNext.classList.add('page-item');
    if (paginaAtual === totalPaginas) {
        liNext.classList.add('disabled');
    }
    const aNext = document.createElement('a');
    aNext.classList.add('page-link');
    aNext.href = '#';
    aNext.textContent = 'Next';
    aNext.addEventListener('click', (e) => {
        e.preventDefault();
        if (paginaAtual < totalPaginas) {
            paginaAtual++;
            exibirProdutosDaPagina(paginaAtual);
            renderizarPaginacao(); // Re-renderiza para atualizar o estado dos botões
        }
    });
    liNext.appendChild(aNext);
    ul.appendChild(liNext);

    nav.appendChild(ul);
    paginationContainer.appendChild(nav);
}


// Ao carregar a página, buscar todos os produtos (sem filtros)
document.addEventListener('DOMContentLoaded', function () {
    buscarProdutos(); // Isso agora vai buscar, exibir a primeira página e renderizar a paginação
});

const applyFiltersDesktop = document.getElementById('apply-filters-desktop');
if (applyFiltersDesktop) {
    applyFiltersDesktop.addEventListener('click', function () {
        const filtros = {
            categorias: [...document.querySelectorAll('input[type=checkbox][id^="defaultCategory"]:checked')].map(cb => cb.value),
            precoMin: document.getElementById('price-min-input').value, // Altere aqui
            precoMax: document.getElementById('price-max-input').value, // Altere aqui
            cores: [...document.querySelectorAll('input[name="color"]:checked')].map(cb => cb.getAttribute('data-color-name')),
            tamanhos: [...document.querySelectorAll('input[name="size-desktop"]:checked')].map(cb => cb.value),
        };
        buscarProdutos(filtros);
    });
}

const clearFiltersDesktop = document.getElementById('clear-filters-desktop');
if (clearFiltersDesktop) {
    clearFiltersDesktop.addEventListener('click', function () {
        // 1. Desmarca todas as caixas de seleção de categoria
        document.querySelectorAll('input[type=checkbox][id^="defaultCategory"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        // 2. Desmarca qualquer botão de opção de cor selecionado
        const checkedColor = document.querySelector('input[name="color"]:checked');
        if (checkedColor) {
            checkedColor.checked = false;
        }
        // 3. Desmarca qualquer botão de opção de tamanho selecionado (desktop)
        const checkedSizeDesktop = document.querySelector('input[name="size-desktop"]:checked');
        if (checkedSizeDesktop) {
            checkedSizeDesktop.checked = false;
        }
        // 4. Reseta os campos de entrada de faixa de preço (desktop)
        const priceMinInput = document.getElementById('price-min-input');
        const priceMaxInput = document.getElementById('price-max-input');
        if (priceMinInput && priceMaxInput) {
            priceMinInput.value = "0";
            priceMaxInput.value = "100";
        }
        // Essas funções agora vão limpar seus respectivos containers e mostrar uma mensagem de placeholder
        atualizarCoresPorCategoria();
        atualizarSizePorCategoria();
        // 6. Busca e exibe todos os produtos (sem filtros)
        buscarProdutos();
    });
}

// Fecha o offcanvas de filtros no mobile
function closeMobileOffcanvas() {
    const offcanvasEl = document.getElementById('filtros_mob');
    if (offcanvasEl && typeof bootstrap !== 'undefined' && bootstrap.Offcanvas) {
        const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
        offcanvas.hide();
    } else if (offcanvasEl) {
        // fallback: esconde manualmente
        offcanvasEl.classList.remove('show');
        offcanvasEl.style.display = 'none';
        document.body.classList.remove('offcanvas-backdrop', 'show');
    }
}