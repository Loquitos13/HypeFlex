<?php

session_start();

$userId = $_SESSION['user_id'];
$cartId = $_SESSION['user_cart_id'];

include 'carrinho.php';
?>

<input type="hidden" id="userId" value="<?php echo htmlspecialchars($userId); ?>">
<input type="hidden" id="cartId" value="<?php echo htmlspecialchars($cartId); ?>">

<style>
    .search-results-container {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        max-height: 400px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    }

    /* Force dropdown to be closed initially */
    .dropdown-menu {
        display: none !important;
    }

    .dropdown-menu.show {
        display: block !important;
    }

    /* Ensure dropdown toggle doesn't have active state initially */
    .dropdown-toggle {
        pointer-events: auto !important;
    }

    .dropdown-toggle[aria-expanded="true"]+.dropdown-menu {
        display: block !important;
    }

    .search-item {
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .search-item:hover {
        background-color: #f8f9fa;
    }

    .search-item:last-child {
        border-bottom: none;
    }

    .search-item-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
    }

    .search-item-title {
        font-weight: 500;
        color: #333;
        margin-bottom: 4px;
    }

    .search-item-price {
        color: #4F46E5;
        font-weight: 600;
    }

    .search-loading {
        padding: 20px;
        text-align: center;
        color: #666;
    }

    .search-no-results {
        padding: 20px;
        text-align: center;
        color: #999;
    }

    .search-form-container {
        position: relative;
    }

    .loading-spinner {
        width: 20px;
        height: 20px;
        border: 2px solid #f3f3f3;
        border-radius: 50%;
        border-top: 2px solid #4F46E5;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .clear-search-btn {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #999;
        cursor: pointer;
        padding: 0;
        width: 20px;
        height: 20px;
        display: none;
        z-index: 10;
    }

    .clear-search-btn:hover {
        color: #666;
    }

    .header-desktop .left {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-grow: 1;
    }

    .header-desktop .right {
        margin-left: auto;
    }

    .search-form-container {
        flex-grow: 1;
        max-width: 400px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .header-desktop .left {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .search-form-container {
            width: 100%;
            max-width: none;
        }
    }
</style>

<div class="container header-desktop">
    <header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
        <div class="left">
            <a href="index.php"
                class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
                <img src="imagens/Print&Go.png" alt="Print&Go Logo" style="width: 100px;">
            </a>


            <div class="search-form-container">
                <form class="d-flex position-relative" role="search" id="search-form">
                    <input id="search-input" class="form-control me-2" type="search" placeholder="🔍 Search products..."
                        aria-label="Search products" autocomplete="off">
                    <button type="button" class="clear-search-btn" id="clear-search-btn" title="Clear search">
                        ×
                    </button>


                    <div id="search-results" class="search-results-container"></div>
                </form>
            </div>
        </div>

        <div class="right">
            <ul class="nav nav-pills">
                <li class="nav-item" id="link-index">
                    <a href="index.php" class="nav-link" style="color: #4F46E5;">Home</a>
                </li>
                <li class="nav-item" id="link-produtos">
                    <a href="src/produtos.php" class="nav-link" style="color: #4F46E5;">Products</a>
                </li>
                <li class="nav-item" id="link-sobre">
                    <a href="src/sobre.php" class="nav-link" style="color: #4F46E5;">About</a>
                </li>
                <li class="nav-item dropdown">
                    <?php if (isset($_SESSION['user_email'])): ?>
                        <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" id="userDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false" title="User Menu">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                                fill="#0d6efd">
                                <path
                                    d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T560-640q0-33-23.5-56.5T480-720q-33 0-56.5 23.5T400-640q0 33 23.5 56.5T480-560Zm0-80Zm0 400Z" />
                            </svg>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="src/User/userProfile.php">
                                    Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="src/logout.php">
                                    Logout
                                </a>
                            </li>
                        </ul>
                    <?php else: ?>
                        <a href="src/SignIn.html" class="nav-link" title="Sign In">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                                fill="#0d6efd">
                                <path
                                    d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T560-640q0-33-23.5-56.5T480-720q-33 0-56.5 23.5T400-640q0 33 23.5 56.5T480-560Zm0-80Zm0 400Z" />
                            </svg>
                        </a>
                    <?php endif; ?>
                </li>
                <!-- Shopping Cart Button -->
                <li class="nav-item" id="mostrarCarrinho">
                    <a href="src/includes/carrinho.php" class="nav-link" data-bs-toggle="offcanvas"
                        data-bs-target="#carrinho" aria-controls="carrinho" title="Shopping Cart">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                            fill="#0d6efd">
                            <path
                                d="M280-80q-33 0-56.5-23.5T200-160q0-33 23.5-56.5T280-240q33 0 56.5 23.5T360-160q0 33-23.5 56.5T280-80Zm400 0q-33 0-56.5-23.5T600-160q0-33 23.5-56.5T680-240q33 0 56.5 23.5T760-160q0 33-23.5 56.5T680-80ZM246-720l96 200h280l110-200H246Zm-38-80h590q23 0 35 20.5t1 41.5L692-482q-11 20-29.5 31T622-440H324l-44 80h480v80H280q-45 0-68-39.5t-2-78.5l54-98-144-304H40v-80h130l38 80Zm134 280h280-280Z" />
                        </svg>
                    </a>
                </li>
            </ul>
        </div>
    </header>
</div>


<script>
    class ProductSearch {
        constructor() {
            this.baseUrl = '';
            this.searchInput = document.getElementById('search-input');
            this.searchForm = document.getElementById('search-form');
            this.clearBtn = document.getElementById('clear-search-btn');
            this.searchResults = document.getElementById('search-results');

            this.searchTimeout = null;
            this.currentSearchTerm = '';
            this.isLoading = false;

            this.init();
        }

        init() {
            this.bindEvents();
            this.setupClickOutside();
        }

        bindEvents() {

            this.searchInput.addEventListener('input', (e) => {
                this.handleSearchInput(e.target.value);
            });


            this.searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmit();
            });


            this.clearBtn.addEventListener('click', () => {
                this.clearSearch();
            });


            this.searchInput.addEventListener('focus', () => {
                if (this.currentSearchTerm) {
                    this.searchResults.style.display = 'block';
                }
            });


            this.searchInput.addEventListener('keydown', (e) => {
                this.handleKeyboardNavigation(e);
            });
        }

        setupClickOutside() {
            document.addEventListener('click', (e) => {
                if (!this.searchForm.contains(e.target)) {
                    this.hideResults();
                }
            });
        }

        handleSearchInput(searchTerm) {
            searchTerm = searchTerm.trim();


            this.clearBtn.style.display = searchTerm ? 'block' : 'none';


            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }


            if (!searchTerm) {
                this.hideResults();
                this.currentSearchTerm = '';
                return;
            }


            this.searchTimeout = setTimeout(() => {
                if (searchTerm !== this.currentSearchTerm && searchTerm.length >= 2) {
                    this.performSearch(searchTerm);
                }
            }, 300);
        }

        handleFormSubmit() {
            const searchTerm = this.searchInput.value.trim();
            if (searchTerm) {

                window.location.href = `${this.baseUrl}/src/produtos.php?search=${encodeURIComponent(searchTerm)}`;
            }
        }

        handleKeyboardNavigation(e) {
            const items = this.searchResults.querySelectorAll('.search-item');
            if (items.length === 0) return;

            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    this.highlightNextItem(items);
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    this.highlightPreviousItem(items);
                    break;
                case 'Enter':
                    e.preventDefault();
                    const highlighted = this.searchResults.querySelector('.search-item.highlighted');
                    if (highlighted) {
                        highlighted.click();
                    } else {
                        this.handleFormSubmit();
                    }
                    break;
                case 'Escape':
                    this.hideResults();
                    break;
            }
        }

        highlightNextItem(items) {
            const currentHighlighted = this.searchResults.querySelector('.search-item.highlighted');
            let nextIndex = 0;

            if (currentHighlighted) {
                currentHighlighted.classList.remove('highlighted');
                const currentIndex = Array.from(items).indexOf(currentHighlighted);
                nextIndex = (currentIndex + 1) % items.length;
            }

            items[nextIndex].classList.add('highlighted');
        }

        highlightPreviousItem(items) {
            const currentHighlighted = this.searchResults.querySelector('.search-item.highlighted');
            let prevIndex = items.length - 1;

            if (currentHighlighted) {
                currentHighlighted.classList.remove('highlighted');
                const currentIndex = Array.from(items).indexOf(currentHighlighted);
                prevIndex = currentIndex === 0 ? items.length - 1 : currentIndex - 1;
            }

            items[prevIndex].classList.add('highlighted');
        }

        async performSearch(searchTerm) {
            if (this.isLoading) return;

            this.currentSearchTerm = searchTerm;
            this.setLoading(true);

            try {
                this.showLoadingDropdown();

                const encodedSearchTerm = encodeURIComponent(searchTerm);
                const response = await fetch(`${this.baseUrl}/restapi/PrintGoAPI.php/searchProductsByTitle/${encodedSearchTerm}`);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const products = await response.json();
                this.displayDropdownResults(products, searchTerm);

            } catch (error) {
                console.error('Search error:', error);
                this.handleSearchError(error);
            } finally {
                this.setLoading(false);
            }
        }

        setLoading(loading) {
            this.isLoading = loading;

        }

        showLoadingDropdown() {
            this.searchResults.innerHTML = `
            <div class="search-loading">
                <div class="loading-spinner mx-auto mb-2"></div>
                <div>Searching products...</div>
            </div>
        `;
            this.searchResults.style.display = 'block';
        }

        displayDropdownResults(products, searchTerm) {
            if (!Array.isArray(products) || products.length === 0) {
                this.searchResults.innerHTML = `
                <div class="search-no-results">
                    <div>No products found for "${this.escapeHtml(searchTerm)}"</div>
                    <small>Try different keywords or check spelling</small>
                </div>
            `;
                this.searchResults.style.display = 'block';
                return;
            }

            const maxResults = 5;
            const limitedProducts = products.slice(0, maxResults);

            let html = '';
            limitedProducts.forEach(product => {
                const imagePath = product.imagem_principal ?
                    `${this.baseUrl}/imagens/produtos/${product.imagem_principal}` :
                    'https://via.placeholder.com/50x50/f0f0f0/999999?text=No+Image';

                const price = parseFloat(product.preco_produto || 0);

                html += `
                <div class="search-item" onclick="productSearch.selectProduct(${product.id_produto})" tabindex="0">
                    <div class="d-flex align-items-center">
                        <img src="${imagePath}" 
                             alt="${this.escapeHtml(product.titulo_produto)}" 
                             class="search-item-image me-3"
                             onerror="this.src='https://via.placeholder.com/50x50/f0f0f0/999999?text=No+Image'">
                        <div class="flex-grow-1">
                            <div class="search-item-title">${this.highlightSearchTerm(product.titulo_produto, searchTerm)}</div>
                            <div class="search-item-price">€${price.toFixed(2)}</div>
                        </div>
                    </div>
                </div>
            `;
            });


            if (products.length > maxResults) {
                html += `
                <div class="search-item" onclick="productSearch.viewAllResults('${this.escapeHtml(searchTerm)}')" style="background-color: #f8f9fa; font-weight: 500;" tabindex="0">
                    <div class="text-center text-primary">
                        View all ${products.length} results for "${this.escapeHtml(searchTerm)}"
                    </div>
                </div>
            `;
            }

            this.searchResults.innerHTML = html;
            this.searchResults.style.display = 'block';
        }

        selectProduct(productId) {

            window.location.href = `${this.baseUrl}/src/productscustom.php?id=${productId}`;
        }

        viewAllResults(searchTerm) {

            window.location.href = `${this.baseUrl}/src/produtos.php?search=${encodeURIComponent(searchTerm)}`;
        }

        handleSearchError(error) {
            const errorMessage = `
            <div class="search-no-results">
                <div class="text-danger">
                    <strong>Search Error</strong><br>
                    <small>${error.message || 'Unable to perform search. Please try again.'}</small>
                </div>
            </div>
        `;

            this.searchResults.innerHTML = errorMessage;
            this.searchResults.style.display = 'block';
        }

        clearSearch() {
            this.searchInput.value = '';
            this.currentSearchTerm = '';
            this.clearBtn.style.display = 'none';
            this.hideResults();
            this.searchInput.focus();
        }

        hideResults() {
            this.searchResults.style.display = 'none';

            const highlighted = this.searchResults.querySelector('.search-item.highlighted');
            if (highlighted) {
                highlighted.classList.remove('highlighted');
            }
        }

        highlightSearchTerm(text, searchTerm) {
            if (!searchTerm || !text) return this.escapeHtml(text);

            const regex = new RegExp(`(${this.escapeRegex(searchTerm)})`, 'gi');
            return this.escapeHtml(text).replace(regex, '<mark>$1</mark>');
        }

        escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        escapeRegex(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }
    }


    let productSearch;
    document.addEventListener('DOMContentLoaded', function () {
        productSearch = new ProductSearch();
    });
</script>


<style>
    .search-item.highlighted {
        background-color: #e3f2fd !important;
    }

    .search-item mark {
        background-color: #ffeb3b;
        padding: 0 2px;
        border-radius: 2px;
    }
</style>