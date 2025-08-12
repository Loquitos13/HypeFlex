<?php 
session_start(); 
$orderId=isset($_GET['id']); 
function formatCurrency($amount) { return '€' .
  number_format((float) $amount, 2, ',' , '.' ); } ?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <title>Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/Orders.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="js/OrderInfo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
      </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
  </head>
  <style>
    body {
      margin: 0;
      padding: 0;
      overflow: auto;
      align-items: center;
      background-color: #f8f9fa;
    }

    body {
      margin: 0;
      padding: 0;
      overflow: auto;
      align-items: center;
    }

    #menu-mobile {
      display: none;
      position: absolute;
      top: 0;
      left: 0;
      width: 80%;
      height: 100%;
      background-color: white;
      transform: translateX(-10%);
      transition: transform 2000ms ease;
      overflow: hidden;
      z-index: 999;
      flex-direction: column-reverse;
      margin-top: 0;
      justify-content: flex-end;
      gap: 20px;
      align-items: center;
      padding-top: 100px;
    }

    .fixed-header {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      background-color: white;
      z-index: 999;
      display: flex;
      align-items: center;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    #menu-mobile.open {
      display: flex;
      transform: translateX(0);
    }

    #menu-mobile a {
      color: #4F46E5;
      text-decoration: none;
      font-size: 1.5rem;
      margin: 15px 0;
    }

    #menu-toggle {
      display: none;
      position: fixed;
      top: 15px;
      left: 15px;
      z-index: 1000;
      font-size: 2rem;
      background: none;
      border: none;
      color: black;
      cursor: pointer;
    }

    #logo-header-mobile {
      display: none;
      background: none;
      border: none;
    }

    .order-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 2rem 0;
      margin-bottom: 2rem;
    }

    .card {
      border: none;
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
      transition: box-shadow 0.15s ease-in-out;
    }

    .card:hover {
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 9999;
      padding-top: 60px;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(5px);
    }

    .modal-content {
      background-color: #fff;
      margin: auto;
      padding: 20px;
      border-radius: 12px;
      width: 90%;
      max-width: 800px;
      position: relative;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .close-button {
      color: #aaa;
      position: absolute;
      top: 15px;
      right: 25px;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
      transition: color 0.3s;
    }

    .close-button:hover {
      color: #000;
    }

    .fixed-header {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      background-color: white;
      z-index: 999;
      display: flex;
      align-items: center;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .product-image {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 8px;
    }

    .status-timeline {
      display: flex;
      justify-content: space-between;
      margin: 1rem 0;
    }

    .timeline-step {
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
      flex: 1;
    }

    .timeline-step:not(:last-child)::after {
      content: '';
      position: absolute;
      top: 15px;
      left: 50%;
      width: 100%;
      height: 2px;
      background-color: #e9ecef;
      z-index: -1;
    }

    .timeline-step.active::after {
      background-color: #28a745;
    }

    .timeline-icon {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background-color: #e9ecef;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 0.5rem;
    }

    .timeline-step.active .timeline-icon {
      background-color: #28a745;
      color: white;
    }

    @media (max-width: 1200px) {
      body {
        width: 100%;
        padding-top: 70px;
      }

      .fixed-header {
        display: flex;
        align-items: center;
      }

      #div-menu.container.header-desktop {
        display: none !important;
      }

      #a-logo-header-mobile {
        display: flex;
        width: max-content;
        justify-content: center;
        padding-top: 20px;
        padding-bottom: 20px;
        width: 100%;

      }

      #logo-header-mobile {
        display: block;
        width: 100px;
        height: auto;
        margin: 0 auto;
      }

      #menu-toggle {
        display: block;
        align-self: center;
      }


    }

    @media (max-width: 768px) {
      .order-header {
        padding: 1rem 0;
      }

      .status-timeline {
        flex-wrap: wrap;
      }

      .timeline-step {
        flex-basis: 50%;
        margin-bottom: 1rem;
      }
    }

    .loading-spinner {
      display: inline-block;
      width: 20px;
      height: 20px;
      border: 3px solid #f3f3f3;
      border-top: 3px solid #3498db;
      border-radius: 50%;
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
  </style>

  <body>
    <div class="d-flex">
      <?php include '../includes/header-desktop-admin.php'; ?>
      <?php include '../includes/header-mobile-admin.php'; ?>

      <div class="flex-grow-1" id="main-content">
        <div class="order-header">
          <div class="container">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h2 class="mb-1">Encomenda #PG-<?php echo $_GET['id']; ?></h2>
                <p class="mb-0 opacity-75" data-creation-date>

                </p>
              </div>
              <div class="text-end" data-order-status>
                <div class="mt-2">
                  <a href="orders.php" class="btn btn-light">
                    <i class="bi bi-arrow-left"></i> Voltar às Encomendas
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="container mb-5">
          <div class="card mb-4">
            <div class="card-body">
              <h6 class="card-title">Estado da Encomenda</h6>
              <div class="status-timeline">
                <div class="timeline-step ">
                  <div class="timeline-icon"><i class="bi bi-clock"></i></div>
                  <small>Pendente</small>
                </div>
                <div
                  class="timeline-step <?php echo in_array(strtolower($order['status_encomenda']), ['processando', 'enviado', 'entregue']) ? 'active' : ''; ?>">
                  <div class="timeline-icon"><i class="bi bi-gear"></i></div>
                  <small>Processando</small>
                </div>
                <div
                  class="timeline-step <?php echo in_array(strtolower($order['status_encomenda']), ['enviado', 'entregue']) ? 'active' : ''; ?>">
                  <div class="timeline-icon"><i class="bi bi-truck"></i></div>
                  <small>Enviado</small>
                </div>
                <div
                  class="timeline-step <?php echo strtolower($order['status_encomenda']) === 'entregue' ? 'active' : ''; ?>">
                  <div class="timeline-icon"><i class="bi bi-check2"></i></div>
                  <small>Entregue</small>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-8">
              <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h6 class="mb-0">Informações da Encomenda</h6>
                  <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printOrder()">
                      <i class="bi bi-printer"></i> Imprimir
                    </button>
                  </div>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <p data-order-status></p>
                      <p data-creation-date-card></p>
                      <p data-update-date></p>
                    </div>
                    <div class="col-md-6">
                      <p><strong>Origem da Compra:</strong> <span class="badge bg-info">Loja Online</span></p>
                      <div>
                        <strong>Transportadora: </strong><span data-carrier></span><br>
                        <strong>Rastreio: </strong><span class="mt-2" data-tracking></span>
                      </div>

                      <p><strong>Total: </strong> <span class="h5 text-success" data-order-total></span>
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h6 class="mb-0">Itens da Encomenda</h6>
                  <button class="btn btn-sm btn-primary" id="button-process">
                    <i class="bi bi-gear"></i> Processar Itens
                  </button>
                </div>
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-hover mb-0">
                      <thead class="table-light">
                        <tr>
                          <th>Produto</th>
                          <th>Tamanho</th>
                          <th>Cor</th>
                          <th>Qtd</th>
                          <th class="text-end">Preço</th>
                        </tr>
                      </thead>
                      <tbody class="table-tbody">

                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="card">
                <div class="card-header">
                  <h6 class="mb-0">Resumo Financeiro</h6>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <table class="table table-sm">
                        <tr>
                          <td><strong>Subtotal:</strong></td>
                          <td class="text-end" data-subtotal></td>
                        </tr>
                        <tr>
                          <td><strong>Envio:</strong></td>
                          <td class="text-end">
                            <?php echo $shippingCost > 0 ? formatCurrency($shippingCost) : '<span class="text-success">Gratuito</span>'; ?>
                          </td>
                        </tr>
                        <tr class="table-active">
                          <td><strong>Total:</strong></td>
                          <td class="text-end">
                            <strong data-financial-total></strong>
                          </td>
                        </tr>
                      </table>
                    </div>

                    <div class="col-md-6">
                      <h6>Informações de Pagamento</h6>
                      <div>
                        <i class="me-2" data-payment-method></i>
                        <p></p>
                      </div>
                      <p><strong>Status:</strong>
                        <span
                          class="badge <?php echo strtolower($payment['status_pagamento']) === 'pago' ? 'bg-success' : 'bg-warning'; ?>">
                          <?php echo ucfirst($payment['status_pagamento']); ?>
                        </span>
                      </p>
                      <p data-payment-reference>
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-4">
              <div class="card mb-4">
                <div class="card-header">
                  <h6 class="mb-0"><i class="bi bi-person"></i> Cliente</h6>
                </div>
                <div class="card-body">
                  <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                      style="width: 50px; height: 50px;">
                      <i class="bi bi-person text-white"></i>
                    </div>
                    <div>
                      <h6 class="mb-1" data-customer-name></h6>
                      <small class="text-muted" data-member-since>
                      </small>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h6 class="mb-0"><i class="bi bi-geo-alt"></i> Contacto e Morada</h6>
                  <button class="btn btn-sm btn-outline-secondary" onclick="editCustomerInfo()">
                    <i class="bi bi-pencil"></i>
                  </button>
                </div>
                <div class="card-body">
                  <p><strong>Email:</strong><br>
                    <a href="mailto: data-customer-email" class="text-decoration-none" data-customer-email>
                    </a>
                  </p>

                  <p><strong>Telefone:</strong><br>
                    <a href="tel: data-customer-phone" class="text-decoration-none" data-customer-phone>
                    </a>
                  </p>
                  <p><strong>Morada de Envio:</strong>
                  <address class="mb-0" data-customer-address>
                  </address>
                  </p>
                  <strong>NIF: </strong><span data-customer-nif></span>

                  <p><small class="text-muted"><i class="bi bi-info-circle"></i> Faturação igual à morada de
                      envio</small>
                  </p>
                </div>
              </div>

              <div class="card">
                <div class="card-header">
                  <h6 class="mb-0"><i class="bi bi-chat-text"></i> Notas</h6>
                </div>
                <div class="card-body">
                  <div class="alert alert-info">
                    <p class="text-muted mb-0" data-order-notes></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div id="processModal" class="modal">
      <div class="modal-content">
        <span class="close-button">&times;</span>
        <div id="modal-body-content">
          <div class="text-center">
            <div class="loading-spinner"></div>
            <p class="mt-2">A carregar...</p>
          </div>
        </div>
      </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('processModal');
        const closeBtn = document.querySelector('.close-button');
        const modalContent = document.getElementById('modal-body-content');

        document.getElementById('button-process').addEventListener('click', function () {
          modal.style.display = 'block';

          const urlParams = new URLSearchParams(window.location.search);
          const orderId = urlParams.get('id');

          if (!orderId) {
            modalContent.innerHTML = `
        <div class="alert alert-danger">
          <h4>Erro</h4>
          <p>ID da encomenda não encontrado.</p>
        </div>
      `;
            return;
          }

          modalContent.innerHTML = createProcessForm(orderId);

          attachFormEventListeners(orderId);
        });

        closeBtn.addEventListener('click', function () {
          modal.style.display = 'none';
        });

        window.addEventListener('click', function (event) {
          if (event.target === modal) {
            modal.style.display = 'none';
          }
        });

        document.addEventListener('keydown', function (event) {
          if (event.key === 'Escape' && modal.style.display === 'block') {
            modal.style.display = 'none';
          }
        });
      });

      function createProcessForm(orderId) {
        return `
    <div style="max-height: 70vh; overflow-y: auto;">
      <div class="header mb-4">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h3>Processar Encomenda</h3>
            <span class="text-muted">Número da Encomenda: #PG-${orderId}</span>
          </div>
          <div class="text-end">
            <span class="badge bg-warning">Processando</span>
            <p class="small text-muted mb-0">Origem: Print & Go</p>
          </div>
        </div>
      </div>
      
      <hr>

      <form id="process-order-form">
        <input type="hidden" name="order_id" value="${orderId}">

        <div class="mb-4">
          <h5>Informações de Rastreio</h5>
          <p class="text-muted">Adicionar um número de rastreio ajudará a melhorar a satisfação do cliente e reduzir consultas de suporte.</p>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="tracking_number" class="form-label">Número de Rastreio <span class="text-danger">*</span></label>
              <input type="text" name="tracking_number" id="tracking_number" class="form-control" 
                     placeholder="Digite o número de rastreio" required>
              <small class="form-text text-muted">Ex: AB123456789PT</small>
            </div>
            <div class="col-md-6 mb-3">
              <label for="carrier" class="form-label">Transportadora <span class="text-danger">*</span></label>
              <select name="carrier" id="carrier" class="form-control" required>
                <option value="">Selecione uma transportadora</option>
                <option value="CTT">CTT Correios</option>
                <option value="DPD">DPD</option>
                <option value="UPS">UPS</option>
                <option value="FedEx">FedEx</option>
                <option value="DHL">DHL</option>
                <option value="GLS">GLS</option>
                <option value="Chronopost">Chronopost</option>
                <option value="Outra">Outra</option>
              </select>
              <input type="text" id="carrier_custom" class="form-control mt-2" 
                     placeholder="Digite o nome da transportadora" style="display: none;">
            </div>
          </div>
        </div>

        <div class="mb-4">
          <h5>Opções de Notificação</h5>
          <div class="form-check">
            <input type="checkbox" name="notify_customer" id="notify_customer" class="form-check-input" value="1" checked>
            <label for="notify_customer" class="form-check-label">
              Notificar Cliente por Email
            </label>
          </div>
        </div>

        <hr>

        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <p class="mb-0">Processar a partir de: <strong>Print & Go</strong></p>
            <small class="text-muted">Esta ação marcará a encomenda como "Enviada"</small>
          </div>
        </div>

        <div class="d-flex gap-2 justify-content-end">
          <button type="button" class="btn btn-secondary" onclick="document.getElementById('processModal').style.display='none'">
            Cancelar
          </button>
          <button type="button" id="process-order-btn" class="btn btn-primary">
            <i class="bi bi-gear"></i> Processar Encomenda
          </button>
          <button type="button" class="btn btn-outline-secondary print-label-btn">
            <i class="bi bi-printer"></i> Imprimir Etiqueta
          </button>
        </div>
      </form>
      
      <div class="mt-3">
        <div class="alert alert-info">
          <small><strong>Debug Info:</strong> Order ID = ${orderId}</small>
        </div>
      </div>
    </div>
  `;
      }

      function attachFormEventListeners(orderId) {
        const processButton = document.getElementById('process-order-btn');
        const carrierSelect = document.getElementById('carrier');
        const carrierCustom = document.getElementById('carrier_custom');

        if (carrierSelect && carrierCustom) {
          carrierSelect.addEventListener('change', function () {
            if (this.value === 'Outra') {
              carrierCustom.style.display = 'block';
              carrierCustom.required = true;
            } else {
              carrierCustom.style.display = 'none';
              carrierCustom.required = false;
              carrierCustom.value = '';
            }
          });
        }

        if (processButton) {
          processButton.addEventListener('click', function () {

            const originalText = processButton.innerHTML;
            processButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processando...';
            processButton.disabled = true;


            const trackingNumber = document.getElementById('tracking_number').value.trim();
            let carrier = document.getElementById('carrier').value.trim();


            if (carrier === 'Outra') {
              carrier = document.getElementById('carrier_custom').value.trim();
            }

            if (!trackingNumber || !carrier) {
              showAlert('Por favor, preencha todos os campos obrigatórios.', 'warning');

              processButton.innerHTML = originalText;
              processButton.disabled = false;
              return;
            }


            if (trackingNumber.length < 6) {
              showAlert('O número de rastreio deve ter pelo menos 6 caracteres.', 'warning');
              processButton.innerHTML = originalText;
              processButton.disabled = false;
              return;
            }

            const requestData = {
              order_id: parseInt(orderId),
              numero_seguimento: trackingNumber,
              transportadora: carrier,
              notify_customer: document.getElementById('notify_customer').checked ? 1 : 0
            };

            console.log('Sending data to processItemsEngine.php:', requestData);


            showProcessingStatus('Enviando dados para o processador...');


            const processorUrl = '../../admin/processItemsEngine.php';

            fetch(processorUrl, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
              },
              body: JSON.stringify(requestData)
            })
              .then(response => {
                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);


                return response.text().then(text => {
                  console.log('Raw response:', text);

                  try {
                    const jsonData = JSON.parse(text);
                    return {
                      json: jsonData,
                      status: response.status,
                      ok: response.ok
                    };
                  } catch (e) {
                    console.error('Failed to parse JSON:', e);
                    return {
                      text: text,
                      status: response.status,
                      ok: response.ok
                    };
                  }
                });
              })
              .then(result => {
                if (result.json) {
                  console.log('Parsed JSON response:', result.json);

                  if (result.json.success) {
                    showSuccessMessage(orderId, result.json);
                  } else {
                    showErrorMessage(result.json.message || 'Erro desconhecido do processador');
                    processButton.innerHTML = originalText;
                    processButton.disabled = false;
                  }
                } else {
                  console.error('Non-JSON response:', result.text);

                  if (result.status === 403) {
                    showErrorMessage('Erro 403: Sem permissão para acessar o processador. Verifique as configurações do servidor.');
                  } else if (result.status === 404) {
                    showErrorMessage('Erro 404: Processador não encontrado. Verifique se processItemsEngine.php existe.');
                  } else {
                    showErrorMessage(`Erro do processador (HTTP ${result.status}): ${result.text.substring(0, 200)}`);
                  }

                  processButton.innerHTML = originalText;
                  processButton.disabled = false;
                }
              })
              .catch(error => {
                console.error('Network or other error:', error);
                showErrorMessage('Erro de rede: ' + error.message);

                processButton.innerHTML = originalText;
                processButton.disabled = false;
              });
          });

        }


        const printButton = document.querySelector('.print-label-btn');
        if (printButton) {
          printButton.addEventListener('click', function () {
            window.print();
          });
        }
      }

      function showAlert(message, type = 'info') {
        const alertClass = type === 'warning' ? 'alert-warning' :
          type === 'danger' ? 'alert-danger' : 'alert-info';


        const alertDiv = document.createElement('div');
        alertDiv.className = `alert ${alertClass} alert-dismissible fade show`;
        alertDiv.innerHTML = `
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  `;


        const modalContent = document.getElementById('modal-body-content');
        modalContent.insertBefore(alertDiv, modalContent.firstChild);
      }

      function showProcessingStatus(message) {
        const statusDiv = document.createElement('div');
        statusDiv.id = 'processing-status';
        statusDiv.className = 'alert alert-info';
        statusDiv.innerHTML = `
    <div class="d-flex align-items-center">
      <div class="spinner-border spinner-border-sm me-2"></div>
      <span>${message}</span>
    </div>
  `;

        const existing = document.getElementById('processing-status');
        if (existing) existing.remove();

        const modalContent = document.getElementById('modal-body-content');
        modalContent.insertBefore(statusDiv, modalContent.firstChild);
      }

      function showSuccessMessage(orderId, responseData) {
        const modalContent = document.getElementById('modal-body-content');
        modalContent.innerHTML = `
    <div class="text-center">
      <div class="alert alert-success">
        <i class="bi bi-check-circle-fill fs-1 text-success"></i>
        <h4 class="mt-3">Encomenda Processada com Sucesso!</h4>
        <p>A encomenda #PG-${orderId} foi processada e marcada como enviada.</p>
        ${responseData.data ? `
          <div class="mt-3">
            <small class="text-muted">
              <strong>Número de Rastreio:</strong> ${responseData.data.numero_seguimento}<br>
              <strong>Transportadora:</strong> ${responseData.data.transportadora}<br>
              <strong>Método:</strong> ${responseData.data.method || responseData.data.method_used || 'Processamento automático'}
            </small>
          </div>
        ` : ''}
        <div class="mt-4">
          <button class="btn btn-primary me-2" onclick="location.reload()">
            <i class="bi bi-arrow-clockwise"></i> Recarregar Página
          </button>
          <button class="btn btn-secondary" onclick="document.getElementById('processModal').style.display='none'">
            Fechar
          </button>
        </div>
      </div>
    </div>
  `;
      }

      function showErrorMessage(message) {
        const modalContent = document.getElementById('modal-body-content');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger';
        errorDiv.innerHTML = `
    <h5><i class="bi bi-exclamation-triangle"></i> Erro ao Processar</h5>
    <p>${message}</p>
    <div class="mt-3">
      <button class="btn btn-outline-danger" onclick="console.log('Debug info logged')">
        Ver Detalhes no Console
      </button>
    </div>
  `;

        const statusDiv = document.getElementById('processing-status');
        if (statusDiv) statusDiv.remove();

        modalContent.insertBefore(errorDiv, modalContent.firstChild);
      }

      function printOrder() {
        const { jsPDF } = window.jspdf;

        const doc = new jsPDF('l', 'mm', 'a4');

        const orderNumber = '#PG-' + new URLSearchParams(window.location.search).get('id');
        const orderNumberRaw = new URLSearchParams(window.location.search).get('id');
        const customerName = document.querySelector('[data-customer-name]').textContent.trim();
        const customerAddress = document.querySelector('[data-customer-address]').textContent.trim();
        const carrier = document.querySelector('[data-carrier]').textContent.trim() || 'Não especificada';

        doc.setFontSize(22);
        doc.setFont('helvetica', 'bold');
        doc.text('ETIQUETA DE ENVIO', 150, 20, { align: 'center' });

        doc.setFontSize(12);
        doc.setFont('helvetica', 'bold');
        doc.text('REMETENTE:', 20, 35);

        doc.setFont('helvetica', 'normal');
        doc.text('PrintAndGo', 20, 42);
        doc.text('R. de Santa Catarina 999', 20, 49);

        doc.setLineWidth(0.3);
        doc.line(100, 30, 100, 90);

        doc.setFontSize(14);
        doc.setFont('helvetica', 'bold');
        doc.text('Encomenda: ' + orderNumber, 150, 35, { align: 'center' });

        doc.text('Transportadora: ', 110, 50);
        doc.setFont('helvetica', 'normal');
        doc.text(carrier, 150, 50);

        doc.setLineWidth(0.5);
        doc.line(20, 95, 280, 95);

        doc.setFontSize(16);
        doc.setFont('helvetica', 'bold');
        doc.text('DESTINATÁRIO:', 150, 110, { align: 'center' });

        doc.setFontSize(14);
        doc.setFont('helvetica', 'bold');
        doc.text(customerName, 150, 125, { align: 'center' });

        doc.setFont('helvetica', 'normal');
        const addressLines = doc.splitTextToSize(customerAddress, 200);
        doc.text(addressLines, 150, 135, { align: 'center' });

        const today = new Date();
        const dateStr = today.toLocaleDateString('pt-PT');
        doc.setFontSize(9);
        doc.setTextColor(100, 100, 100);
        doc.text('Impresso em: ' + dateStr, 20, 190);

        const canvas = document.createElement('canvas');


        const trackingElement = document.querySelector('[data-tracking]');
        const trackingNumber = trackingElement && trackingElement.textContent.trim()
          ? trackingElement.textContent.trim()
          : 'Sem Rastreio';

        JsBarcode(canvas, "PG" + orderNumberRaw, {
          format: "CODE128",
          width: 2,
          height: 50,
          displayValue: true,
          fontSize: 14,
          textMargin: 4,
          text: trackingNumber
        });

        const barcodeData = canvas.toDataURL('image/png');
        doc.addImage(barcodeData, 'PNG', 20, 155, 100, 30);

        doc.setFontSize(8);
        doc.text('Print & Go - Gráfica e Muito Mais, LDA', 150, 200, { align: 'center' });

        const pdfBlob = doc.output('blob');
        const blobUrl = URL.createObjectURL(pdfBlob);

        const printWindow = window.open(blobUrl, '_blank');

        if (printWindow) {
          printWindow.addEventListener('load', function () {
            printWindow.print();
          });
        } else {
          alert('Por favor, permita pop-ups para imprimir a etiqueta.');
          const downloadLink = document.createElement('a');
          downloadLink.href = blobUrl;
          downloadLink.download = `etiqueta-PG-${orderNumberRaw}.pdf`;
          downloadLink.click();
        }
      }
      function editCustomerInfo() {
        // Obter os dados atuais do cliente
        const customerName = document.querySelector('[data-customer-name]').textContent.trim();
        const customerEmail = document.querySelector('[data-customer-email]').textContent.trim();
        const customerPhone = document.querySelector('[data-customer-phone]').textContent.trim();
        const customerAddress = document.querySelector('[data-customer-address]').innerHTML.replace(/<br>/g, '\n').trim();
        const customerNIF = document.querySelector('[data-customer-nif]').textContent.trim();
        const orderId = new URLSearchParams(window.location.search).get('id');

        // Reutilizar o modal existente para edição
        const modal = document.getElementById('processModal');
        const modalContent = document.getElementById('modal-body-content');

        // Criar formulário de edição
        modalContent.innerHTML = `
    <div class="header mb-4">
      <h3>Editar Informações do Cliente</h3>
      <p class="text-muted">Encomenda #PG-${orderId}</p>
    </div>
    
    <form id="edit-customer-form">
      <div class="row mb-3">
        <div class="col-md-12">
          <label for="customer_name" class="form-label">Nome Completo</label>
          <input type="text" class="form-control" id="customer_name" value="${customerName}" required>
        </div>
      </div>
      
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="customer_email" class="form-label">Email</label>
          <input type="email" class="form-control" id="customer_email" value="${customerEmail}" required>
        </div>
        <div class="col-md-6">
          <label for="customer_phone" class="form-label">Telefone</label>
          <input type="tel" class="form-control" id="customer_phone" value="${customerPhone}">
        </div>
      </div>
      
      <div class="row mb-3">
        <div class="col-md-12">
          <label for="customer_address" class="form-label">Morada de Envio</label>
          <textarea class="form-control" id="customer_address" rows="3" required>${customerAddress}</textarea>
        </div>
      </div>
      
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="customer_nif" class="form-label">NIF</label>
          <input type="text" class="form-control" id="customer_nif" value="${customerNIF}">
        </div>
      </div>
      
      <div class="d-flex justify-content-between mt-4">
        <div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="update_shipping" checked>
            <label class="form-check-label" for="update_shipping">
              Atualizar morada de faturação (igual à de envio)
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="notify_customer">
            <label class="form-check-label" for="notify_customer">
              Notificar cliente sobre as alterações
            </label>
          </div>
        </div>
        <div>
          <button type="button" class="btn btn-secondary me-2" onclick="document.getElementById('processModal').style.display='none'">
            Cancelar
          </button>
          <button type="button" id="save-customer-info" class="btn btn-primary">
            <i class="bi bi-save"></i> Guardar Alterações
          </button>
        </div>
      </div>
    </form>
    
    <div id="edit-status-message" class="mt-3"></div>
  `;

        // Mostrar o modal
        modal.style.display = 'block';

        // Adicionar event listener ao botão de salvar
        document.getElementById('save-customer-info').addEventListener('click', function () {
          saveCustomerInfo(orderId);
        });
      }

      function saveCustomerInfo(orderId) {
        // Obter valores do formulário
        const customerName = document.getElementById('customer_name').value.trim();
        const customerEmail = document.getElementById('customer_email').value.trim();
        const customerPhone = document.getElementById('customer_phone').value.trim();
        const customerAddress = document.getElementById('customer_address').value.trim();
        const customerNIF = document.getElementById('customer_nif').value.trim();
        const updateShipping = document.getElementById('update_shipping').checked;
        const notifyCustomer = document.getElementById('notify_customer').checked;


        if (!customerName || !customerEmail || !customerAddress) {
          showEditStatusMessage('Por favor, preencha todos os campos obrigatórios.', 'warning');
          return;
        }


        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(customerEmail)) {
          showEditStatusMessage('Por favor, insira um endereço de email válido.', 'warning');
          return;
        }

        const saveButton = document.getElementById('save-customer-info');
        const originalButtonText = saveButton.innerHTML;
        saveButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>A guardar...';
        saveButton.disabled = true;


        const requestData = {
          order_id: parseInt(orderId),
          customer_info: {
            nome: customerName,
            email: customerEmail,
            telefone: customerPhone,
            morada: customerAddress,
            nif: customerNIF
          },
          update_shipping: updateShipping,
          notify_customer: notifyCustomer
        };
        fetch('../../admin/updateCustomerInfoEngine.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(requestData)
        })
          .then(response => {
            if (!response.ok) {
              throw new Error(`Erro HTTP: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            if (data.success) {
                showEditStatusMessage('Informações do cliente atualizadas com sucesso!', 'success');
                document.querySelector('[data-customer-name]').textContent = customerName;
                document.querySelector('[data-customer-email]').textContent = customerEmail;
                document.querySelector('[data-customer-email]').href = `mailto:${customerEmail}`;
                document.querySelector('[data-customer-phone]').textContent = customerPhone;
                document.querySelector('[data-customer-phone]').href = `tel:${customerPhone}`;
                document.querySelector('[data-customer-address]').innerHTML = customerAddress.replace(/\n/g, '<br>');
                document.querySelector('[data-customer-nif]').textContent = customerNIF;
                setTimeout(() => {
                    document.getElementById('processModal').style.display = 'none';
                }, 5000);
            } else {
                showEditStatusMessage(data.message || 'Erro ao atualizar informações.', 'danger');
                saveButton.innerHTML = originalButtonText;
                saveButton.disabled = false;
            }
        })
        .catch(error => {
            console.error('Erro ao atualizar informações do cliente:', error);
            showEditStatusMessage(`Erro de conexão: ${error.message}`, 'danger');
            saveButton.innerHTML = originalButtonText;
            saveButton.disabled = false;
        });
      }

      function showEditStatusMessage(message, type = 'info') {
        const statusContainer = document.getElementById('edit-status-message');
        const alertClass = type === 'success' ? 'alert-success' :
          type === 'warning' ? 'alert-warning' :
            type === 'danger' ? 'alert-danger' : 'alert-info';

        statusContainer.innerHTML = `
    <div class="alert ${alertClass} alert-dismissible fade show">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  `;
      }

    </script>
  </body>

  </html>