

<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Processar Encomenda - Print & Go</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="css/processItems.css">
  <script src="js/processItem.js"></script>
</head>

<body>
  <div id="container">
    <div class="header">
      <div class="header-l">
        <h2>Processar Encomenda</h2>
        <span>Número da Encomenda: #PG-<?php echo $orderId; ?></span>
      </div>
      <div class="header-r">
        <div>
          <p><?php echo $orderData ? $orderData['status_encomenda'] : 'Pendente'; ?></p>
        </div>
        <p>Origem: Print & Go</p>
      </div>
    </div>
    <hr>

    <!-- Formulário para processar encomenda -->
    <form id="process-order-form" method="POST">
      <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">

      <div class="order-details">
        <div class="order-detail-l">
          <h3>Detalhes da Encomenda</h3>
          <div>
            <label class="label">Cliente</label>
            <p class="nome-cliente"><?php echo $orderData ? htmlspecialchars($orderData['nome_cliente']) : ''; ?></p>
          </div>
          <?php if (!empty($orderItems) && isset($orderItems[0])): ?>
            <div>
              <label class="label">Produtos</label>
              <p class="nome-produto"><?php echo count($orderItems) . ' item(ns)'; ?></p>
            </div>
          <?php endif; ?>
        </div>
        <div class="order-detail-r">
          <div>
            <label class="label">Data da Encomenda</label>
            <p class="data-encomenda"><?php echo $orderData ? date('d/m/Y H:i', strtotime($orderData['data_criacao_encomenda'])) : ''; ?></p>
          </div>
          <div>
            <label class="label">Total</label>
            <p class="total-encomenda"><?php echo $orderData ? formatCurrency($orderData['preco_total_encomenda']) : ''; ?></p>
          </div>
        </div>
      </div>

      <div class="tracking-info">
        <h3>Informações de Rastreio</h3>
        <p>Adicionar um número de rastreio ajudará a melhorar a satisfação do cliente e reduzir consultas de suporte.</p>
        <div>
          <div class="tracking-number">
            <label for="tracking_number">Número de Rastreio</label>
            <input type="text" name="tracking_number" id="tracking_number" class="input" placeholder="Digite o número de rastreio" required>
          </div>
          <div class="carrier">
            <label for="carrier">Transportadora</label>
            <input type="text" name="carrier" id="carrier" class="input" placeholder="Digite o nome da transportadora" required>
          </div>
        </div>
      </div>

      <div class="shipping-address">
        <h3>Endereço de Envio</h3>
        <div class="container-envio">
          <h4 class="nome-envio"><?php echo $orderData ? htmlspecialchars($orderData['nome_cliente']) : ''; ?></h4>
          <p class="morada-envio"><?php echo $orderData ? nl2br(htmlspecialchars($orderData['morada_cliente'])) : ''; ?></p>
          <span class="span-shipping">Telefone: </span>
          <span class="telemovel"><?php echo $orderData ? htmlspecialchars($orderData['contacto_cliente']) : ''; ?></span>
          <br>
          <span class="span-shipping">Email: </span>
          <span class="email"><?php echo $orderData ? htmlspecialchars($orderData['email_cliente']) : ''; ?></span>
        </div>
      </div>

      <hr>

      <div class="footer-container">
        <div>
          <p>Processar a partir de: Print & Go</p>
          <span>Itens a processar: </span><span class="itens-processar"><?php echo count($orderItems); ?></span>
        </div>
        <div>
          <input type="checkbox" name="notify_customer" id="notify_customer" class="notification" value="1">
          <label for="notify_customer">Notificar Cliente</label>
        </div>
      </div>

      <div class="buttons-container">
        <button type="button" id="process-order-btn" class="process-order">Processar Encomenda</button>
        <button type="button" class="print">
          <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4.51562 0C3.4125 0 2.51562 0.896875 2.51562 2V5H4.51562V2H11.6L12.5156 2.91563V5H14.5156V2.91563C14.5156 2.38438 14.3062 1.875 13.9312 1.5L13.0156 0.584375C12.6406 0.209375 12.1313 0 11.6 0H4.51562ZM12.5156 11V12V14H4.51562V12V11.5V11H12.5156ZM14.5156 12H15.5156C16.0688 12 16.5156 11.5531 16.5156 11V8C16.5156 6.89687 15.6187 6 14.5156 6H2.51562C1.4125 6 0.515625 6.89687 0.515625 8V11C0.515625 11.5531 0.9625 12 1.51562 12H2.51562V14C2.51562 15.1031 3.4125 16 4.51562 16H12.5156C13.6187 16 14.5156 15.1031 14.5156 14V12ZM14.0156 7.75C14.2145 7.75 14.4053 7.82902 14.546 7.96967C14.6866 8.11032 14.7656 8.30109 14.7656 8.5C14.7656 8.69891 14.6866 8.88968 14.546 9.03033C14.4053 9.17098 14.2145 9.25 14.0156 9.25C13.8167 9.25 13.6259 9.17098 13.4853 9.03033C13.3446 8.88968 13.2656 8.69891 13.2656 8.5C13.2656 8.30109 13.3446 8.11032 13.4853 7.96967C13.6259 7.82902 13.8167 7.75 14.0156 7.75Z" fill="#1F2937" />
          </svg>
          Imprimir Etiqueta
        </button>
      </div>
    </form>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const processButton = document.getElementById('process-order-btn');
    
    processButton.addEventListener('click', function() {
      // Validação básica do formulário
      const trackingNumber = document.getElementById('tracking_number').value;
      const carrier = document.getElementById('carrier').value;
      
      if (!trackingNumber || !carrier) {
        alert('Por favor, preencha todos os campos obrigatórios.');
        return;
      }
      
      // Preparar dados para envio
      const formData = {
        order_id: <?php echo $orderId; ?>,
        tracking_number: trackingNumber,
        carrier: carrier,
        notify_customer: document.getElementById('notify_customer').checked ? 1 : 0
      };
      
      // Enviar dados para o engine
      fetch('../../admin/processItemsEngine.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Exibir mensagem de sucesso
          const container = document.getElementById('container');
          container.innerHTML = `
            <div class="alert alert-success">
              <h4><i class="bi bi-check-circle-fill"></i> Encomenda Processada com Sucesso!</h4>
              <p>A encomenda #PG-<?php echo $orderId; ?> foi processada e marcada como enviada.</p>
              <div class="mt-3">
                <a href="order_info.php?id=<?php echo $orderId; ?>" class="btn btn-primary">
                  <i class="bi bi-arrow-left"></i> Voltar à Encomenda
                </a>
              </div>
            </div>
          `;
        } else {
          alert('Erro ao processar encomenda: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Erro:', error);
        alert('Ocorreu um erro ao processar a requisição. Por favor, tente novamente.');
      });
    });
    
    // Botão para imprimir etiqueta
    document.querySelector('.print').addEventListener('click', function() {
      window.print();
    });
  });
</script>
</body>

</html>