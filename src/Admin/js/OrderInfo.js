document.addEventListener("DOMContentLoaded", function () {
  const dashboardLink = document.querySelector("#link-orders");
  if (dashboardLink) {
    dashboardLink.innerHTML = `<li>
            <a href="orders.php" class="nav-link active" aria-current="page" id="">
                <svg xmlns="http://www.w3.org/2000/svg"
                    style="stroke:currentColor; stroke-width:1; color: #4F46E5;" width="16" height="16"
                    fill="currentColor" class="bi pe-none me-2 bi-graph-up" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M0 0h1v15h15v1H0zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07" />
                </svg>
                Orders
            </a>
        </li>`;
  }

  const urlParams = new URLSearchParams(window.location.search);
  const orderId = urlParams.get("id");

  if (orderId) {
    getOrderInfo(orderId);
    getOrderItems(orderId);
  }

});

function getOrderInfo(orderId) {
  fetch("../../admin/orderInfoEngine.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      id: orderId,
    }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      if (data.status === "success") {
        displayOrderInfo(data.message);
      } else {
        throw new Error(data.message || "Failed to fetch order data");
      }
    })
    .catch((error) => {
      console.error("Error fetching order info:", error);
      showErrorMessage(
        "Erro ao carregar informações da encomenda: " + error.message
      );
    });
}

function getOrderItems(orderId) {
  fetch("../../admin/orderItemsEngine.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      id: orderId,
    }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      if (data.status === "success") {
        updateOrderItems(data.message.items || []);
      } else {
        throw new Error(data.message || "Failed to fetch order items");
      }
    })
    .catch((error) => {
      console.error("Error fetching order items:", error);
      showErrorMessage("Erro ao carregar itens da encomenda: " + error.message);
    });
}

function displayOrderInfo(orderData) {
  try {

    if (!orderData) {
        console.error("No order data received");
        document.querySelector('.order-header').innerHTML += `
            <div class="alert alert-warning">
                Dados da encomenda incompletos. Alguns detalhes podem não estar disponíveis.
            </div>
        `;
        return;
    }
    const orderDate = document.querySelector("#order-date");
    if (orderDate && orderData.data_criacao_encomenda) {
      orderDate.textContent = formatDate(orderData.data_criacao_encomenda);
    }
    
    updateTrackingInfo(orderData);
    updateStatusTimeline(orderData.status_encomenda);
    updateOrderInfoSection(orderData);
    updateCustomerInfo(orderData);
    updateFinancialSummary(orderData);
    updateOrderNotes(orderData.notas_encomenda);
  } catch (error) {
    console.error("Error displaying order info:", error);
    showErrorMessage("Erro ao exibir informações da encomenda");
  }
}

function updateStatusTimeline(status) {
  const statusSteps = document.querySelectorAll(".timeline-step");
  const statusLower = status ? status.toLowerCase() : "";

  statusSteps.forEach((step, index) => {
    const stepText = step.querySelector("small").textContent.toLowerCase();

    if (stepText === "pendente") {
      step.classList.add("active");
    } else if (
      stepText === "processando" &&
      ["processando", "enviado", "entregue"].includes(statusLower)
    ) {
      step.classList.add("active");
    } else if (
      stepText === "enviado" &&
      ["enviado", "entregue"].includes(statusLower)
    ) {
      step.classList.add("active");
    } else if (stepText === "entregue" && statusLower === "entregue") {
      step.classList.add("active");
    }
  });
}

function updateTrackingInfo(orderData) {
    const trackingElement = document.querySelector('[data-tracking]');
    const carrierElement = document.querySelector('[data-carrier]');
    
    if (trackingElement) {
        trackingElement.textContent = orderData && orderData.numero_seguimento ? 
            orderData.numero_seguimento : 'Pendente';
    }
    
    if (carrierElement) {
        carrierElement.textContent = orderData && orderData.transportadora ? 
            orderData.transportadora : 'Pendente';
    }
}

function updateOrderInfoSection(orderData) {
  const statusElements = document.querySelectorAll("[data-order-status]");
  statusElements.forEach((el) => {
    el.innerHTML =
      "Estado da Encomenda: " + getStatusBadge(orderData.status_encomenda);
  });

  const creationDateEl = document.querySelector("[data-creation-date]");

  if (creationDateEl && orderData.data_criacao_encomenda) {
    creationDateEl.textContent =
      "Encomenda Criada a " + formatDate(orderData.data_criacao_encomenda);
  }
  const creationDateCard = document.querySelector("[data-creation-date-card]");
  if (creationDateCard && orderData.data_criacao_encomenda) {
    creationDateCard.textContent =
      "Encomenda criada a " + formatDate(orderData.data_criacao_encomenda);
  }

  const updateDateEl = document.querySelector("[data-update-date]");
  if (updateDateEl && orderData.data_atualizacao_encomenda) {
    updateDateEl.textContent =
      "Última atualização: " + formatDate(orderData.data_atualizacao_encomenda);
  }

  const carrierEl = document.querySelector("[data-carrier]");
  if (carrierEl && orderData.transportadora) {
    carrierEl.textContent = orderData.transportadora;
  }

  const totalEl = document.querySelector("[data-order-total]");
  if (totalEl && orderData.preco_total_encomenda) {
    totalEl.textContent = formatCurrency(orderData.preco_total_encomenda);
  }
}

function updateCustomerInfo(orderData) {
  const customerNameEl = document.querySelector("[data-customer-name]");
  if (customerNameEl && orderData.nome_cliente) {
    customerNameEl.textContent = orderData.nome_cliente;
  }

  const emailEl = document.querySelector("[data-customer-email]");
  if (emailEl && orderData.email_cliente) {
    emailEl.textContent = orderData.email_cliente;
    emailEl.href = `mailto:${orderData.email_cliente}`;
  }

  const phoneEl = document.querySelector("[data-customer-phone]");
  if (phoneEl && orderData.contacto_cliente) {
    phoneEl.textContent = orderData.contacto_cliente;
    phoneEl.href = `tel:${orderData.contacto_cliente}`;
  }

  const addressEl = document.querySelector("[data-customer-address]");
  if (addressEl && orderData.morada_cliente) {
    addressEl.innerHTML = orderData.morada_cliente.replace(/\n/g, "<br>");
  }

  const nifEl = document.querySelector("[data-customer-nif]");
  if (nifEl && orderData.nif_cliente) {
    nifEl.textContent = orderData.nif_cliente;
  }

  const memberSinceEl = document.querySelector("[data-member-since]");
  if (memberSinceEl && orderData.data_criacao_cliente) {
    const date = new Date(orderData.data_criacao_cliente);
    memberSinceEl.textContent =
      "Cliente Desde: " +
      date.toLocaleDateString("pt-PT", {
        month: "short",
        year: "numeric",
      });
  }
}

function updatePaymentInfo(orderData) {
  if (orderData.payment) {
    const paymentMethodEl = document.querySelector("[data-payment-method]");
    if (paymentMethodEl) {
      paymentMethodEl.innerHTML = `
                <i class="${getPaymentIcon(
                  orderData.payment.id_metodo_pagamento
                )} me-2"></i>
                <strong>${orderData.payment.id_metodo_pagamento}</strong>
            `;
    }

    const paymentStatusEl = document.querySelector("[data-payment-status]");
    if (paymentStatusEl) {
      const isPaid =
        orderData.payment.status_pagamento.toLowerCase() === "pago";
      paymentStatusEl.innerHTML = `
                <span class="badge ${isPaid ? "bg-success" : "bg-warning"}">
                    ${orderData.payment.status_pagamento}
                </span>
            `;
    }

    const paymentRefEl = document.querySelector("[data-payment-reference]");
    if (paymentRefEl && orderData.payment.id_pagamento) {
      paymentRefEl.innerHTML = `<code>${orderData.payment.id_pagamento}</code>`;
    }
  }
}

function updateOrderItems(items) {
  const itemsTableBody = document.querySelector(".table-tbody");
  if (!itemsTableBody) {
    console.error("Table body element not found for order items");
    return;
  }

  const parentTable = itemsTableBody.closest("table");
  if (parentTable) {
    parentTable.classList.add("table-responsive-sm");
    parentTable.style.tableLayout = "fixed";
    parentTable.style.width = "100%";
  }

  itemsTableBody.innerHTML = "";
  console.log("Processing Order Items:", items);

  if (!items.length) {
    const emptyRow = document.createElement("tr");
    emptyRow.innerHTML = `
            <td colspan="5" class="text-center py-4">
                <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                <p class="text-muted mt-2">Nenhum item encontrado para esta encomenda</p>
            </td>
        `;
    itemsTableBody.appendChild(emptyRow);
    return;
  }

  items.forEach((item) => {
    const row = document.createElement("tr");
    const tituloProduto = (item.titulo_produto || "Produto sem título").replace(
      /%/g,
      " "
    );
    const personalizacao = item.personalizacao
      ? item.personalizacao.replace(/%/g, " ")
      : "";
    const tamanho = item.tamanho ? item.tamanho.replace(/%/g, " ") : "";
    const nomeCor = item.nome_cor ? item.nome_cor.replace(/%/g, " ") : "";

    row.innerHTML = `
            <td style="min-width: 180px; max-width: 40%;">
            <div class="d-flex align-items-start flex-wrap">
                ${
                  item.imagem_principal
                    ? `<img src="${item.imagem_principal}" alt="${tituloProduto}" class="product-image me-2" style="max-width: 60px; min-width: 60px;">`
                    : `<div class="product-image me-2 bg-light d-flex align-items-center justify-content-center" style="max-width: 60px; min-width: 60px;">
                    <i class="bi bi-image text-muted"></i>
                </div>`
                }
                <div style="word-wrap: break-word; overflow-wrap: break-word; width: calc(100% - 70px);">
                <strong class="text-break">${tituloProduto}</strong>
                ${
                  personalizacao
                    ? `<br><small class="text-primary text-break"><i class="bi bi-star"></i> ${personalizacao}</small>`
                    : ""
                }
                </div>
            </div>
            </td>
            <td style="width: 15%;" class="text-wrap">
            ${
              tamanho
                ? `<span class="badge bg-secondary text-wrap">${tamanho}</span>`
                : `<span class="text-muted">N/A</span>`
            }
            </td>
            <td style="width: 15%;" class="text-wrap">
            ${
              nomeCor
                ? `<span class="badge bg-secondary text-wrap">${nomeCor}</span>`
                : `<span class="text-muted">N/A</span>`
            }
            </td>
            <td style="width: 10%;" class="text-center"><span class="badge bg-light text-dark">${
              item.quantidade || 1
            }</span></td>
            <td style="width: 20%;" class="text-end">${formatCurrency(
              item.preco || 0
            )}</td>
        `;
    itemsTableBody.appendChild(row);
  });

  if (!document.getElementById("order-items-styles")) {
    const styleEl = document.createElement("style");
    styleEl.id = "order-items-styles";
    styleEl.textContent = `
            .table-tbody td {
                vertical-align: top;
                padding: 0.75rem;
                word-break: break-word;
                hyphens: auto;
            }
            .text-break {
                word-break: break-word !important;
                overflow-wrap: break-word !important;
            }
            .text-wrap {
                white-space: normal !important;
            }
            .product-image {
                height: 60px;
                width: 60px;
                object-fit: cover;
                border-radius: 4px;
            }
        `;
    document.head.appendChild(styleEl);
  }
}

function updateFinancialSummary(orderData) {
  const subtotalEl = document.querySelector("[data-subtotal]");
  if (subtotalEl && orderData.subtotal) {
    subtotalEl.textContent = formatCurrency(orderData.subtotal);
  }

  const shippingEl = document.querySelector("[data-shipping]");
  if (shippingEl) {
    const shippingCost = orderData.shipping_cost || 0;
    shippingEl.innerHTML =
      shippingCost > 0
        ? formatCurrency(shippingCost)
        : '<span class="text-success">Gratuito</span>';
  }

  const totalEl = document.querySelector("[data-financial-total]");
  if (totalEl && orderData.preco_total_encomenda) {
    totalEl.innerHTML = `<strong>${formatCurrency(
      orderData.preco_total_encomenda
    )}</strong>`;
  }
}

function updateOrderNotes(notes) {
  const notesContainer = document.querySelector("[data-order-notes]");
  if (!notesContainer) return;

  if (notes && notes.trim()) {
    notesContainer.innerHTML = `
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                ${notes.replace(/\n/g, "<br>")}
            </div>
        `;
  } else {
    notesContainer.innerHTML = `
            <p class="text-muted mb-0">
                <i class="bi bi-chat-text me-2"></i>Sem notas do cliente
            </p>
        `;
  }
}

function formatDate(dateString) {
  if (!dateString) return "";
  const date = new Date(dateString);
  return date.toLocaleDateString("pt-PT", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

function formatCurrency(amount) {
  if (!amount && amount !== 0) return "";
  return new Intl.NumberFormat("pt-PT", {
    style: "currency",
    currency: "EUR",
  }).format(amount);
}

function getStatusBadge(status) {
  if (!status) return "";

  const statusClasses = {
    pendente: "bg-warning",
    processando: "bg-info",
    enviado: "bg-primary",
    entregue: "bg-success",
    cancelado: "bg-danger",
  };

  const badgeClass = statusClasses[status.toLowerCase()] || "bg-secondary";
  return `<span class="badge ${badgeClass}">${status}</span>`;
}

function getPaymentIcon(method) {
  const icons = {
    "cartão de crédito": "bi-credit-card",
    "cartão de débito": "bi-credit-card-2-front",
    paypal: "bi-paypal",
    mbway: "bi-phone",
    "transferência bancária": "bi-bank",
    multibanco: "bi-credit-card-fill",
  };

  return icons[method?.toLowerCase()] || "bi-credit-card";
}

function showErrorMessage(message) {
  let errorContainer = document.querySelector("#error-message");
  if (!errorContainer) {
    errorContainer = document.createElement("div");
    errorContainer.id = "error-message";
    errorContainer.className = "alert alert-danger alert-dismissible fade show";
    errorContainer.style.position = "fixed";
    errorContainer.style.top = "20px";
    errorContainer.style.right = "20px";
    errorContainer.style.zIndex = "9999";
    document.body.appendChild(errorContainer);
  }

  errorContainer.innerHTML = `
        <i class="bi bi-exclamation-triangle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

  setTimeout(() => {
    if (errorContainer && errorContainer.parentNode) {
      errorContainer.remove();
    }
  }, 5000);
}
