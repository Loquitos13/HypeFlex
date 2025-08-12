const base_url = "/~ptaw-2025-gr4";

// função para adicionar o link ativado ao header
document.addEventListener("DOMContentLoaded", function () {
  document.querySelector("#link-index").innerHTML = `<li class="nav-item"><a href="<?= $base_url ?>/index.php" class="nav-link active" style="background-color: #4F46E5;">Home</a></li>`;
});

document.addEventListener("DOMContentLoaded", function () {
  const menuToggle = document.getElementById("menu-toggle");
  const menuMobile = document.getElementById("menu-mobile");

  if (menuToggle && menuMobile) {
    menuToggle.addEventListener("click", function () {
      if (menuMobile.style.display === "flex") {
        menuMobile.style.display = "none";
      } else {
        menuMobile.style.display = "flex";
      }
    });

    // Optionally close the menu when clicking a link
    const links = menuMobile.querySelectorAll("a");
    links.forEach((link) => {
      link.addEventListener("click", () => {
        menuMobile.style.display = "none";
      });
    });
  }

  const userIdInput = document.getElementById("userId");
  if (userIdInput) {
    console.log("User ID:", userIdInput.value);
  } else {
    console.log("Elemento #userId não encontrado.");
  }

  /*const cartIdInput = document.getElementById("cartId");
  if (cartIdInput) {
    console.log("Cart ID:", cartIdInput.value);
  } else {
    console.log("Elemento #cartId não encontrado.");
  }*/

    
});

// estrutura de dados para feedbacks
let feedbacks = [];
let currentIndex = 0;


async function getTopProducts() {
  try {
    // Try calling your API directly
    const response = await fetch('client/getTopProductsIndex.php', {
      method: "GET",
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    });

    if (!response.ok) {
      throw new Error(`Erro ao apanhar produtos: ${response.status} - ${response.statusText}`);
    }

    const result = await response.json();
    
    // Log para debug
    console.log('API Response:', result);
    
    // Verificar se há erro na resposta
    if (result.error) {
      throw new Error(result.message || 'Erro desconhecido da API');
    }
    
    // Retornar dados - ajustar conforme estrutura da API
    return Array.isArray(result) ? result : result.data || [];
    
  } catch (err) {
    console.error('Erro ao buscar produtos:', err);
  
  }
}

async function populateTopProducts() {
  try {
    const products = await getTopProducts();
    
    const container = document.querySelector(".containerDestaques");
    
    if (!container) {
      console.error('Container dos produtos não encontrado');
      return;
    }

    // Mostrar loading enquanto carrega
    container.innerHTML = "<p>Carregando produtos...</p>";

    if (!Array.isArray(products) || products.length === 0) {
      container.innerHTML = "<p>Nenhum produto disponível.</p>";
      return;
    }

    // Limpar container
    container.innerHTML = "";

    // Limitar a 4 produtos conforme a API
    const limitedProducts = products.slice(0, 4);
    
    limitedProducts.forEach((product) => {
      const productElement = createProductItem(product);
      container.appendChild(productElement);
    });

  } catch (error) {
    console.error('Erro ao popular produtos:', error);
    const container = document.querySelector(".containerDestaques");
    if (container) {
      container.innerHTML = "<p>Erro ao carregar produtos. Tente novamente mais tarde.</p>";
    }
  }
}

function createProductItem(product) {
  const productLink = document.createElement('a');
  productLink.href = `src/productscustom.php?id=${product.id_produto}`; 
  productLink.className = 'boxItemLink';

  const productDiv = document.createElement('div');
  productDiv.className = 'boxItem';

  // Imagem do produto
  const productImg = document.createElement('img');
  productImg.src = product.imagem_produto || 'imagens/produtos varios hero.png';
  productImg.alt = product.nome_produto || 'Produto';
  productImg.className = 'boxItemImg';
  
  // Adicionar evento de erro para imagem
  productImg.onerror = function() {
    this.src = 'imagens/produtos varios hero.png';
  };

  // Título do produto
  const productTitle = document.createElement('p');
  productTitle.className = 'boxItemTitle';
  productTitle.textContent = normalizarTexto(product.nome_produto);

  // Descrição do produto (truncar se muito longa)
  const productDescription = document.createElement('p');
  productDescription.className = 'boxItemDescription';
  const description = normalizarTexto(product.descricao_produto);
  productDescription.textContent = description.length > 100 ? 
    description.substring(0, 100) + '...' : description;

  // Footer com preço e botão
  const productFooter = document.createElement('div');
  productFooter.className = 'boxItemFooter';

  const productPrice = document.createElement('span');
  productPrice.className = 'boxItemPrice';
  const price = parseFloat(product.preco_produto) || 0;
  productPrice.textContent = `${price.toFixed(2)}€`;

  const customizeButton = document.createElement('button');
  customizeButton.type = 'button';
  customizeButton.className = 'btn btn-primary';
  customizeButton.style.backgroundColor = '#4F46E5';
  customizeButton.style.border = '0';
  customizeButton.textContent = 'Customize';
  
  // Adicionar funcionalidade ao botão
  customizeButton.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    // Redirecionar para página de customização
    window.location.href = `src/productscustom.php?id=${product.id_produto}`;
  });

  // Montagem da estrutura
  productFooter.appendChild(productPrice);
  productFooter.appendChild(customizeButton);

  productDiv.appendChild(productImg);
  productDiv.appendChild(productTitle);
  productDiv.appendChild(productDescription);
  productDiv.appendChild(productFooter);

  productLink.appendChild(productDiv);

  return productLink;
}

// Função para normalizar texto
function normalizarTexto(texto) {
  if (!texto) return '';
  return texto.toString().trim();
}

// Inicializar quando DOM estiver pronto
document.addEventListener('DOMContentLoaded', function(){
  populateTopProducts();
});

// Opcional: Adicionar refresh automático ou manual
function refreshProducts() {
  populateTopProducts();
}

// Expor função globalmente se necessário
window.refreshProducts = refreshProducts;

// obter feedbacks da api
async function getFeedbacks() {
  try {
    const response = await fetch(`client/productReview.php`, {
      method: "GET",
    });

    if (!response.ok) {
      throw new Error("erro na requisição: " + response.status);
    }

    const result = await response.json();

    if (result.status !== "success") {
      throw new Error("erro no retorno da api: " + (result.message || ""));
    }

    return result.data;
  } catch (err) {
    return [];
  }
}

// função para converter e exibir feedbacks
async function convertFeedbacks() {
  try {
    feedbacks = await getFeedbacks();

    const container = document.querySelector("#carouselItems");
    const indicatorContainer = document.querySelector("#indicator");
    const carouselContainer = document.querySelector('.feedback-carousel-container');

    if (!container) {
      return;
    }

    let createdIndicator = false;
    if (!indicatorContainer) {

      const newIndicator = document.createElement('div');
      newIndicator.id = 'indicator';
      newIndicator.className = 'carousel-indicator';
      newIndicator.style.display = 'flex';
      newIndicator.style.justifyContent = 'center';
      newIndicator.style.gap = '8px';
      newIndicator.style.margin = '15px 0';

      if (container.parentNode) {
        container.parentNode.insertBefore(newIndicator, container.nextSibling);
      } else if (carouselContainer) {
        carouselContainer.appendChild(newIndicator);
      }

      createdIndicator = true;
    }

    const updatedIndicatorContainer = document.querySelector("#indicator");

    if (!updatedIndicatorContainer) {
      return;
    }

    container.innerHTML = "";
    updatedIndicatorContainer.innerHTML = "";

    if (!Array.isArray(feedbacks) || feedbacks.length === 0) {
      container.innerHTML = "<p>nenhum feedback disponível.</p>";
      return;
    }

    feedbacks.forEach((feedback, i) => {
      const feedbackAdaptado = {
        id: feedback.nome_cliente,
        name: normalizarTexto(`cliente ${feedback.nome_cliente}`),
        role: normalizarTexto(`produto ${feedback.titulo_produto}`),
        content: normalizarTexto(feedback.comentario),
        rating: parseInt(feedback.classificacao) || 0,
        date: normalizarTexto(feedback.data_review),
        tags: [],
      };

      const feedbackItem = createFeedbackItem(feedbackAdaptado);
      container.appendChild(feedbackItem);

      const dot = document.createElement("span");
      dot.classList.add("carousel-dot");
      dot.style.width = "10px";
      dot.style.height = "10px";
      dot.style.borderRadius = "50%";
      dot.style.backgroundColor = "#ccc";
      dot.style.display = "inline-block";
      dot.style.cursor = "pointer";

      if (i === 0) {
        dot.classList.add("active-dot");
        dot.style.backgroundColor = "#333";
      }

      dot.addEventListener("click", () => {
        goToSlide(i);
      });

      updatedIndicatorContainer.appendChild(dot);
    });

    createNavigationButtonsIfNeeded(carouselContainer);


    const allItems = document.querySelectorAll(".carousel-item-container");

    if (createdIndicator) {
      const style = document.createElement('style');
      style.textContent = `
        .carousel-dot {
          width: 10px;
          height: 10px;
          border-radius: 50%;
          background-color: #ccc;
          margin: 0 5px;
          cursor: pointer;
          display: inline-block;
          transition: background-color 0.3s ease;
        }
        .carousel-dot.active-dot {
          background-color: #333;
        }
      `;
      document.head.appendChild(style);
    }

    updateCarouselState(0);
    checkResponsiveness();
    // startAutoSlide();
  } catch (error) {
    const carouselItems = document.querySelector("#carouselItems");
    if (carouselItems) {
      carouselItems.innerHTML = "<p>erro ao carregar feedbacks.</p>";
    }
  }
}


// actualizar o estado do carrossel
function updateCarouselState(index) {
  const items = document.querySelectorAll(".carousel-item-container");
  const total = items.length;

  if (total === 0) return;

  if (index < 0) index = total - 1;
  if (index >= total) index = 0;

  items.forEach((item, i) => {

    if (i === index) {
      item.classList.remove("hidden-item");
      item.classList.add("active");
    } else {
      item.classList.add("hidden-item");
      item.classList.remove("active");
    }
  });

  const dots = document.querySelectorAll(".carousel-dot");

  if (dots.length > 0) {

    dots.forEach((dot, i) => {
      if (i === index) {
        dot.classList.add("active-dot");
        dot.style.backgroundColor = "#333";
      } else {
        dot.classList.remove("active-dot");
        dot.style.backgroundColor = "#ccc";
      }
    });
  }

  currentIndex = index;
}

// criar botões de navegação se necessário
function createNavigationButtonsIfNeeded(carouselContainer) {
  if (!carouselContainer) {
    carouselContainer = document.querySelector('.carousel-container') ||
      document.querySelector('#carouselContainer') ||
      document.querySelector('.feedback-carousel-container') ||
      document.querySelector('.carousel');

    if (!carouselContainer) {
      return;
    }
  }

  const existingPrevButton = document.querySelector('.carousel-prev, .prev-button, #prevBtn, [data-action="prev"], #prevButton');
  const existingNextButton = document.querySelector('.carousel-next, .next-button, #nextBtn, [data-action="next"], #nextButton');


  if (existingPrevButton && existingNextButton) {

    existingPrevButton.addEventListener("click", () => {
      goToPrevSlide();
    });

    existingNextButton.addEventListener("click", () => {
      goToNextSlide();
    });

    return;
  }

  const navContainer = document.createElement('div');
  navContainer.className = 'carousel-navigation';
  navContainer.style.display = 'flex';
  navContainer.style.justifyContent = 'space-between';
  navContainer.style.margin = '10px 0';
  navContainer.style.position = 'relative';
  navContainer.style.width = '100%';

  const prevButton = document.createElement('button');
  prevButton.className = 'carousel-prev';
  prevButton.innerHTML = '&lt;';
  prevButton.setAttribute('aria-label', 'slide anterior');
  prevButton.style.cursor = 'pointer';
  prevButton.style.padding = '5px 10px';
  prevButton.style.backgroundColor = '#f0f0f0';
  prevButton.style.border = 'none';
  prevButton.style.borderRadius = '3px';

  const nextButton = document.createElement('button');
  nextButton.className = 'carousel-next';
  nextButton.innerHTML = '&gt;';
  nextButton.setAttribute('aria-label', 'slide seguinte');
  nextButton.style.cursor = 'pointer';
  nextButton.style.padding = '5px 10px';
  nextButton.style.backgroundColor = '#f0f0f0';
  nextButton.style.border = 'none';
  nextButton.style.borderRadius = '3px';

  navContainer.appendChild(prevButton);
  navContainer.appendChild(nextButton);

  carouselContainer.parentNode.insertBefore(navContainer, carouselContainer.nextSibling);

  prevButton.addEventListener('click', () => {
    goToPrevSlide();
  });

  nextButton.addEventListener('click', () => {
    goToNextSlide();
  });

}

function createStars(rating) {
  let stars = "";
  for (let i = 0; i < 5; i++) {
    stars += `<span class="star">${i < rating ? "★" : "☆"}</span>`;
  }
  return stars;
}

function createFeedbackItem(feedback) {
  if (!feedback || !feedback.id) {
    return null;
  }

  const item = document.createElement("div");
  item.className = "carousel-item-container hidden-item";
  item.setAttribute("data-id", feedback.id);

  const tags = feedback.tags && Array.isArray(feedback.tags)
    ? feedback.tags.map((tag) => `<span class="feedback-tag">${normalizarTexto(tag)}</span>`).join("")
    : "";

  item.innerHTML = `
    <div class="feedback-item">
      <div class="feedback-header"> 
        <div class="feedback-author">
          <h3>${feedback.name || 'anónimo'}</h3>
          <p>${feedback.role || ''}</p>
        </div>
      </div>
      <div class="feedback-content">
        "${feedback.content || ''}"
      </div>
      <div class="feedback-rating">
        ${createStars(feedback.rating)}
      </div>
      <div class="feedback-metadata">
        <div class="feedback-tags">
          ${tags}
        </div>
        <div class="feedback-date">${feedback.date || ''}</div>
      </div>
    </div>
  `;

  return item;
}

function checkResponsiveness() {
  const isMobile = window.innerWidth <= 576;
  const isTablet = window.innerWidth <= 768 && window.innerWidth > 576;

  const carouselContent = document.querySelector(".carousel-content");
  const dataFeedbacks = document.querySelectorAll(".feedback-date");

  if (carouselContent) {
    if (isMobile) {
      carouselContent.style.minHeight = "350px";
      dataFeedbacks.forEach((item) => {
        item.style.display = "none";
      });
    } else if (isTablet) {
      carouselContent.style.minHeight = "320px";
      dataFeedbacks.forEach((item) => {
        item.style.display = "none";
      });
    } else {
      carouselContent.style.minHeight = "300px";
      dataFeedbacks.forEach((item) => {
        item.style.display = "block";
      });
    }
  }
}

// navegação automática (opcional)
function startAutoSlide() {
  setInterval(() => {
    goToNextSlide();
  }, 5000);
}

function goToPrevSlide() {
  updateCarouselState(currentIndex - 1);
}

function goToNextSlide() {
  updateCarouselState(currentIndex + 1);
}

function goToSlide(index) {
  updateCarouselState(index);
}

document.addEventListener("DOMContentLoaded", () => {
  convertFeedbacks();

  document.addEventListener("keydown", (event) => {
    if (event.key === "ArrowLeft") {
      goToPrevSlide();
    } else if (event.key === "ArrowRight") {
      goToNextSlide();
    }
  });

  window.addEventListener("resize", checkResponsiveness);
});
