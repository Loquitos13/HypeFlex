document.addEventListener("DOMContentLoaded", async function () {
  try {
    // Obter ID do cliente do atributo data-client-id
    const clientId = document.body.getAttribute("data-client-id");

    // Carregar dados do cliente
    const response = await fetch(
      `../../client/showClientInfo.php?id_cliente=${clientId}`,
      {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      }
    );

    if (!response.ok) {
      throw new Error(`Erro HTTP: ${response.status}`);
    }

    const userData = await response.json();
    console.log("Dados do usuário:", userData);

    // Preencher o formulário com os dados do usuário
    populateUserForm(userData);

    try {
      const cardResponse = await fetch(
        `../../client/showClientCardInfo.php?id_cliente=${clientId}`,
        {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
          },
        }
      );

      if (cardResponse.ok) {
        const cardData = await cardResponse.json();
        console.log("Resposta de dados do cartão:", cardData);

        const processedCardData =
          cardData.data && cardData.data.success ? cardData.data : cardData;

        if (processedCardData.success && processedCardData.data) {
          // Cliente possui cartão - preencher formulário
          populateCardForm(processedCardData);

          // Mostrar interface de edição de cartão
          const cardInfoSection = document.getElementById("cardInfoSection");
          if (cardInfoSection) {
            cardInfoSection.style.display = "block";
          }

          // Ocultar seção de adicionar novo cartão (se existir)
          const noCardSection = document.getElementById("noCardSection");
          if (noCardSection) {
            noCardSection.style.display = "none";
          }

          // Atualizar texto com últimos 4 dígitos diretamente aqui também
          const cardLast4 = document.getElementById("cardLast4");
          if (cardLast4 && processedCardData.data.numero_cartao) {
            const last4 = processedCardData.data.numero_cartao.slice(-4);
            cardLast4.textContent = `Visa •••• ${last4}`;
          }
        } else {
          // Cliente não possui cartão - mostrar interface para adicionar
          console.log("Cliente não possui cartão cadastrado");

          // Ocultar seção de edição de cartão
          const cardInfoSection = document.getElementById("cardInfoSection");
          if (cardInfoSection) {
            cardInfoSection.style.display = "none";
          }

          // Mostrar seção de adicionar novo cartão
          const noCardSection = document.getElementById("noCardSection");
          if (noCardSection) {
            noCardSection.style.display = "block";
          }

          // Atualizar texto informativo
          const cardLast4 = document.getElementById("cardLast4");
          if (cardLast4) {
            cardLast4.textContent = "Nenhum cartão cadastrado";
          }
        }
      }
    } catch (cardError) {
      console.error("Erro ao buscar informações de cartão:", cardError);
    }

    // Configurar handlers para os botões
    setupEventListeners(clientId);
  } catch (error) {
    console.error("Erro ao inicializar perfil:", error);
  }
});

function populateUserForm(userData) {
  // Verificar se userData e userData.data existem
  if (!userData || !userData.data) {
    console.error("Dados do usuário inválidos:", userData);
    return;
  }

  const data = userData.data;

  const profileImage = document.getElementById("profileImage");
  if (profileImage) {
    // Verificar se imagem_cliente já é uma URL completa
    if (data.imagem_cliente && data.imagem_cliente !== "null") {
      // Verificar se a imagem já começa com http ou https
      if (data.imagem_cliente.startsWith("http")) {
        profileImage.src = data.imagem_cliente;
      } else {
        // Apenas adicionar o path relativo
        profileImage.src = "../../imagens/" + data.imagem_cliente;
      }
    } else {
      // Imagem padrão com caminho relativo
      profileImage.src = "../../imagens/admin.png";
    }

    // Definir tamanho da imagem para 100x100 pixels
    profileImage.width = 100;
    profileImage.height = 100;
    profileImage.style.objectFit = "cover"; // Mantém a proporção e corta se necessário

    profileImage.alt = data.nome_cliente || "Profile Image";
  }
  // Informações pessoais
  setInputValue("fullName", data.nome_cliente);
  setInputValue("email", data.email_cliente);
  setInputValue("phone", data.contacto_cliente);

  // Endereço
  setInputValue("street", data.morada_cliente);
  setInputValue("city", data.cidade_cliente);
  setInputValue("state", data.state_cliente);
  setInputValue("zip", data.cod_postal_cliente);
  setInputValue("country", data.pais_cliente);
}

function populateCardForm(cardData) {
  // Verificar se cardData e cardData.data existem
  if (!cardData || !cardData.success || !cardData.data) {
    console.error("Dados do cartão inválidos:", cardData);
    return;
  }
  
  const data = cardData.data;
  console.log("Preenchendo formulário de cartão com:", data);
  
  // Atualizar texto com últimos 4 dígitos
  const cardLast4 = document.getElementById("cardLast4");
  if (cardLast4 && data.numero_cartao) {
    const last4 = data.numero_cartao.slice(-4);
    cardLast4.textContent = `Visa •••• ${last4}`;
  }

  // Preencher formulário do modal de edição de cartão
  setInputValue("cardNumber", data.numero_cartao);
  setInputValue("expiryDate", data.validade_cartao);
  setInputValue("cvv", data.cvv_cartao);
  setInputValue("cardholderName", data.nome_cartao);
}

// Função auxiliar para definir o valor de um input
function setInputValue(id, value) {
  const element = document.getElementById(id);
  if (element && value) {
    element.value = value;
  }
}

// Configurar os event listeners para os botões
function setupEventListeners(clientId) {
  // Botão de salvar alterações
  const saveButton = document.getElementById("updateInfoButton");
  if (saveButton) {
    saveButton.addEventListener("click", function () {
      saveUserInfo(clientId);
    });
  }

  // Botão de salvar cartão (modal de edição)
  const paymentButton = document.getElementById("paymentButton");
  if (paymentButton) {
    paymentButton.addEventListener("click", function () {
      saveCardInfo(clientId);
    });
  }

  // Botão de adicionar novo cartão
  const addCardButton = document.getElementById("addCardButton");
  if (addCardButton) {
    addCardButton.addEventListener("click", function () {
      addNewCard(clientId);
    });
  }
}

// Função para salvar informações do usuário
async function saveUserInfo(clientId) {
  try {
    // Mostrar spinner no botão
    const saveButton = document.getElementById("updateInfoButton");
    const originalText = saveButton.innerHTML;
    saveButton.innerHTML =
      '<span class="spinner-border spinner-border-sm me-2"></span>Salvando...';
    saveButton.disabled = true;

    // Obter valores do formulário
    const userData = {
      id_cliente: clientId,
      nome_cliente: document.getElementById("fullName").value,
      email_cliente: document.getElementById("email").value,
      contacto_cliente: document.getElementById("phone").value,
      morada_cliente: document.getElementById("street").value,
      cidade_cliente: document.getElementById("city").value,
      state_cliente: document.getElementById("state").value,
      cod_postal_cliente: document.getElementById("zip").value,
      pais_cliente: document.getElementById("country").value,
    };

    // Verificar se há nova senha
    const newPassword = document.getElementById("newPassword").value;
    const currentPassword = document.getElementById("currentPassword").value;

    if (newPassword) {
      if (!currentPassword) {
        showAlert(
          "Por favor, insira sua senha atual para confirmar a alteração de senha.",
          "warning"
        );
        saveButton.innerHTML = originalText;
        saveButton.disabled = false;
        return;
      }
      userData.pass_cliente = newPassword;
      userData.current_password = currentPassword;
    }

    // Enviar dados para o servidor
    const response = await fetch("../../client/updateClientInfo.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(userData),
    });

    if (!response.ok) {
      throw new Error(`Erro HTTP: ${response.status}`);
    }

    const result = await response.json();

    if (result.success) {
      showAlert("Informações atualizadas com sucesso!", "success");
    } else {
      showAlert(result.message || "Erro ao atualizar informações.", "danger");
    }

    // Restaurar botão
    saveButton.innerHTML = originalText;
    saveButton.disabled = false;
  } catch (error) {
    console.error("Erro ao atualizar perfil:", error);
    showAlert(`Erro ao atualizar perfil: ${error.message}`, "danger");

    const saveButton = document.getElementById("updateInfoButton");
    if (saveButton) {
      saveButton.innerHTML = "Save Changes";
      saveButton.disabled = false;
    }
  }
}

// Função para salvar informações do cartão
async function saveCardInfo(clientId) {
  try {
    // Mostrar spinner no botão
    const saveButton = document.getElementById("paymentButton");
    const originalText = saveButton.innerHTML;
    saveButton.innerHTML =
      '<span class="spinner-border spinner-border-sm me-2"></span>Salvando...';
    saveButton.disabled = true;

    // Obter valores do formulário
    const cardData = {
      id_cliente: clientId,
      numero_cartao: document.getElementById("cardNumber").value,
      validade_cartao: document.getElementById("expiryDate").value,
      cvv_cartao: document.getElementById("cvv").value,
      nome_cartao: document.getElementById("cardholderName").value,
    };

    // Enviar dados para o servidor
    const response = await fetch("../../client/updateClientCardInfo.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(cardData),
    });

    if (!response.ok) {
      throw new Error(`Erro HTTP: ${response.status}`);
    }

    const result = await response.json();

    if (result.success) {
      // Atualizar o texto do cartão na página
      const cardLast4 = document.getElementById("cardLast4");
      if (cardLast4) {
        const last4 = cardData.numero_cartao.slice(-4);
        cardLast4.textContent = `Visa •••• ${last4}`;
      }

      // Fechar o modal
      const modal = bootstrap.Modal.getInstance(
        document.getElementById("paymentModal")
      );
      if (modal) {
        modal.hide();
      }

      showAlert("Informações do cartão atualizadas com sucesso!", "success");
    } else {
      showAlert(result.message || "Erro ao atualizar cartão.", "danger");
    }

    // Restaurar botão
    saveButton.innerHTML = originalText;
    saveButton.disabled = false;
  } catch (error) {
    console.error("Erro ao atualizar cartão:", error);
    showAlert(`Erro ao atualizar cartão: ${error.message}`, "danger");

    const saveButton = document.getElementById("paymentButton");
    if (saveButton) {
      saveButton.innerHTML = "Save Card";
      saveButton.disabled = false;
    }
  }
}

// Função para adicionar novo cartão
async function addNewCard(clientId) {
  try {
    // Mostrar spinner no botão
    const addButton = document.getElementById("addCardButton");
    const originalText = addButton.innerHTML;
    addButton.innerHTML =
      '<span class="spinner-border spinner-border-sm me-2"></span>Adicionando...';
    addButton.disabled = true;

    // Obter valores do formulário
    const cardData = {
      id_cliente: clientId,
      numero_cartao: document.getElementById("newCardNumber").value,
      validade_cartao: document.getElementById("newExpiryDate").value,
      cvv_cartao: document.getElementById("newCvv").value,
      nome_cartao: document.getElementById("newCardholderName").value,
    };

    // Enviar dados para o servidor
    const response = await fetch("../../client/insertClientCard.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(cardData),
    });

    if (!response.ok) {
      throw new Error(`Erro HTTP: ${response.status}`);
    }

    const result = await response.json();

    if (result.success) {
      // Atualizar o texto do cartão na página
      const cardLast4 = document.getElementById("cardLast4");
      if (cardLast4) {
        const last4 = cardData.numero_cartao.slice(-4);
        cardLast4.textContent = `Visa •••• ${last4}`;
      }

      // Fechar o modal
      const modal = bootstrap.Modal.getInstance(
        document.getElementById("addCardModal")
      );
      if (modal) {
        modal.hide();
      }

      showAlert("Cartão adicionado com sucesso!", "success");
    } else {
      showAlert(result.message || "Erro ao adicionar cartão.", "danger");
    }

    // Restaurar botão
    addButton.innerHTML = originalText;
    addButton.disabled = false;
  } catch (error) {
    console.error("Erro ao adicionar cartão:", error);
    showAlert(`Erro ao adicionar cartão: ${error.message}`, "danger");

    const addButton = document.getElementById("addCardButton");
    if (addButton) {
      addButton.innerHTML = "Save Card";
      addButton.disabled = false;
    }
  }
}

// Função para mostrar alerta
function showAlert(message, type = "info") {
  // Criar div de alerta se não existir
  let alertContainer = document.querySelector(".alert-container");
  if (!alertContainer) {
    alertContainer = document.createElement("div");
    alertContainer.className = "alert-container position-fixed top-0 end-0 p-3";
    alertContainer.style.zIndex = "1050";
    document.body.appendChild(alertContainer);
  }

  // Criar alerta
  const alertDiv = document.createElement("div");
  alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
  alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

  // Adicionar alerta ao container
  alertContainer.appendChild(alertDiv);

  // Remover alerta após 5 segundos
  setTimeout(() => {
    alertDiv.classList.remove("show");
    setTimeout(() => alertDiv.remove(), 150);
  }, 5000);
}
