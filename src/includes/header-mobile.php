<?php

session_start();

$userId = $_SESSION['user_id'];
$cartId = $_SESSION['user_cart_id'];

include 'carrinho.php';
?>

<div class="fixed-header">
  <button id="menu-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu"
    aria-controls="mobileMenu">
    ☰
  </button>
  <a href="index.php"
    class="d-flex align-items-center mb-md-0 me-md-auto link-body-emphasis text-decoration-none"
    id="a-logo-header-mobile">
    <img src="imagens/Print&Go.png" alt="" id="logo-header-mobile" href="/index.php">
  </a>
  <!-- Search Form -->
  <div id="search-bar" class="search-form-container" style="display: none;">
    <form class="d-flex position-relative" role="search" id="search-form-mobile">
      <input id="search-input-mobile" class="form-control me-2" type="search" placeholder="🔍 Search products..."
        aria-label="Search products" autocomplete="off">
      <button type="button" class="clear-search-btn" id="clear-search-btn-mobile" title="Clear search">×</button>
      <div id="search-results-mobile" class="search-results-container"></div>
    </form>
  </div>
  <!-- Search Icon -->
  <button id="search-toggle" type="button" aria-label="Search" class="btn btn-light">
    🔍
  </button>
</div>

<style>
  .fixed-header {
    display: flex;
    align-items: center;
    width: 100%;
    min-height: 60px; 
  }

  #search-bar {
    display: none;
    flex-grow: 1;
    margin-left: 10px;
    width: 100%;
    /* Make search bar use all available width */
  }

  #search-form-mobile {
    width: 100%;
    /* Make form use full width of container */
  }

  #search-toggle {
    margin-right: 10px;
  }

  #logo-header-mobile {
    transition: opacity 0.3s ease;
  }

  #search-bar.open~#logo-header-mobile {
    opacity: 0;
    pointer-events: none;
  }

  #search-input-mobile {
    width: 100%;
    height: 40px;
    /* Taller input field */
    font-size: 16px;
    /* Better text size for mobile */
  }

  #a-logo-header-mobile {
    min-width: 120px; /* Reserve space for the logo even when hidden */
    display: flex;
    align-items: center;
    justify-content: center;
  }

  #a-logo-header-mobile[style*="display: none"] {
    min-width: 120px; /* Keep space reserved when hidden */
    display: flex !important;
    visibility: hidden;
  }

  /* When search is active, ensure it gets maximum width */
  .fixed-header.search-active {
    padding: 0 10px;
    justify-content: space-between;
  }

  /* Improve search results container for better UX */
  .search-results-container {
    width: 100%;
  }

  /* Hide the entire fixed-header on desktop */
  @media (min-width: 1201px) {
    .fixed-header {
      display: none !important;
    }
  }
</style>

<script>
  document.getElementById('search-toggle').addEventListener('click', function () {
    const searchBar = document.getElementById('search-bar');
    const logoLink = document.getElementById('a-logo-header-mobile');
    const logoImg = document.getElementById('logo-header-mobile');
    const fixedHeader = document.querySelector('.fixed-header');

    if (searchBar.style.display === 'none') {
      searchBar.style.display = 'flex';
      logoLink.style.display = 'none';
      if (logoImg) logoImg.style.display = 'none';
      fixedHeader.classList.add('search-active');
      document.getElementById('search-input-mobile').focus();
    } else {
      searchBar.style.display = 'none';
      logoLink.style.display = '';
      if (logoImg) logoImg.style.display = '';
      fixedHeader.classList.remove('search-active');
    }
  });

  // Ensure mobile menu/search resets on desktop resize
  window.addEventListener('resize', function () {
    if (window.innerWidth > 1200) {
      // On desktop: Hide the entire header element
      const header = document.querySelector('.fixed-header');
      if (header) {
        header.style.display = 'none';
      }
    } else {
      // On mobile: Make sure the header is visible
      const header = document.querySelector('.fixed-header');
      if (header) {
        header.style.display = 'flex';
        // Reset the search state
        document.getElementById('search-bar').style.display = 'none';
        document.getElementById('a-logo-header-mobile').style.display = '';
        const logoImg = document.getElementById('logo-header-mobile');
        if (logoImg) logoImg.style.display = '';
        header.classList.remove('search-active');
      }
    }
  });

  // Run the resize handler once on page load to set initial state
  window.addEventListener('DOMContentLoaded', function() {
    if (window.innerWidth > 1200) {
      const header = document.querySelector('.fixed-header');
      if (header) {
        header.style.display = 'none';
      }
    }
  });

  document.getElementById('clear-search-btn-mobile').addEventListener('click', function () {
    const searchInput = document.getElementById('search-input-mobile');
    searchInput.value = '';
    document.getElementById('search-results-mobile').style.display = 'none';
  });
</script>

<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="mobileMenuLabel">Menu</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="d-flex flex-column gap-3">

      <a href="index.php" class="nav-link py-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-person me-2"
          viewBox="0 0 16 16">
          <path
            d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5" />
        </svg>
        Home
      </a>
      <a href="src/produtos.php" class="nav-link py-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-person me-2"
          viewBox="0 0 16 16">
          <path
            d="M3.75 0a1 1 0 0 0-.8.4L.1 4.2a.5.5 0 0 0-.1.3V15a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V4.5a.5.5 0 0 0-.1-.3L13.05.4a1 1 0 0 0-.8-.4zM15 4.667V5H1v-.333L1.5 4h6V1h1v3h6z" />
        </svg>
        Products
      </a>
      <a href="src/sobre.php" class="nav-link py-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-person me-2"
          viewBox="0 0 16 16">
          <path
            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
        </svg>
        About Us
      </a>

      <hr>

      <!-- Btn carrinho -->
      <a href="src/includes/carrinho.php" class="nav-link py-2 d-flex align-items-center"
        data-bs-toggle="offcanvas" data-bs-target="#carrinho" aria-controls="carrinho">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-person me-2"
          viewBox="0 0 16 16">
          <path
            d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
        </svg>
        Cart
      </a>

      <?php if (isset($_SESSION['user_email'])): ?>
        <a href="src/User/userProfile.php" class="nav-link py-2 d-flex align-items-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-person me-2"
            viewBox="0 0 16 16">
            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
          </svg>
          Profile
        </a>
        <a class="nav-link py-2 d-flex align-items-center" href="/src/logout.php">
          <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor"
            class="bi bi-box-arrow-right me-2" viewBox="0 0 16 16">
            <path fill-rule="evenodd"
              d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z" />
            <path fill-rule="evenodd"
              d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z" />
          </svg>
          Logout
        </a>
      <?php else: ?>
        <a href="src/SignIn.html" class="nav-link py-2 d-flex align-items-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-person me-2"
            viewBox="0 0 16 16">
            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
          </svg>
          Profile
        </a>
      <?php endif; ?>

    </div>
  </div>
</div>

<script>
  class ProductSearchMobile {
    constructor() {
      this.baseUrl = '';
      this.searchInput = document.getElementById('search-input-mobile');
      this.searchForm = document.getElementById('search-form-mobile');
      this.clearBtn = document.getElementById('clear-search-btn-mobile');
      this.searchResults = document.getElementById('search-results-mobile');

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
        <div class="search-item" onclick="productSearchMobile.selectProduct(${product.id_produto})" tabindex="0">
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
        <div class="search-item" onclick="productSearchMobile.viewAllResults('${this.escapeHtml(searchTerm)}')" style="background-color: #f8f9fa; font-weight: 500;" tabindex="0">
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

    escapeRegex(text) {
      return text.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
  }

  const productSearchMobile = new ProductSearchMobile();
</script>