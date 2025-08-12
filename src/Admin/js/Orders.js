document.addEventListener("DOMContentLoaded", function () {
  const ordersLink = document.querySelector("#link-orders");
  if (ordersLink) {
    ordersLink.innerHTML = `<li>
            <a href="#" class="nav-link active" aria-current="page" id="">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi me-2 bi-cart-fill" viewBox="0 0 16 16">
                    <path
                        d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                </svg>
                Orders
            </a>
        </li>`;
  }


    const adminID = document.getElementById("adminID");
    
    getAdminInfo(adminID);

    async function getAdminInfo(adminID) {

        const resultAdminInfo = await adminInfoByID(adminID.value);

        if (resultAdminInfo.status == 'success') {

            const adminName = resultAdminInfo.data["nome_admin"];
            const adminImage = resultAdminInfo.data["imagem_admin"];

            document.getElementById("admin_nome").textContent = adminName;
            document.getElementById("img-admin").src = adminImage;

        }
    }


    async function adminInfoByID(id_admin) {

        const response = await fetch('../../admin/getAdmin.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({id_admin: id_admin})
        });


        return await response.json();

    }



  loadOrders();
});

function loadOrders() {
    // Fetch all orders using the API
    fetch('../../admin/orderEngine.php', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        // Handle the response based on your API structure
        let orders;
        if (data.status === 'success' && data.data) {
            orders = data.data;
        } else if (Array.isArray(data)) {
            orders = data;
        } else {
            throw new Error('Invalid response format');
        }

        const tbody = document.getElementById('orders-table-body');
        if (!tbody) {
            console.error('Table body element not found');
            return;
        }
        
        tbody.innerHTML = '';
        
        if (!Array.isArray(orders) || orders.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-inbox"></i> Nenhuma encomenda encontrada
                    </td>
                </tr>
            `;
            return;
        }
        
        orders.forEach(order => {
            const row = document.createElement('tr');
            row.setAttribute('data-order-id', order.id_encomenda || order.id);
            row.style.cursor = 'pointer';
            row.classList.add('order-row');
            
            row.innerHTML = `
                <td><strong>#PG-${order.id_encomenda || order.id}</strong></td>
                <td>${order.nome_cliente || order.customer_name || 'N/A'}</td>
                <td>€${parseFloat(order.preco_total_encomenda || order.total_price || 0).toFixed(2)}</td>
                <td><span class="badge ${getStatusClass(order.status_encomenda || order.status)}">${order.status_encomenda || order.status}</span></td>
                <td>${formatDate(order.data_criacao_encomenda || order.created_at)}</td>
                <td>
                    <button class="btn btn-sm btn-primary view-order-btn" data-order-id="${order.id_encomenda || order.id}">
                        <i class="bi bi-eye"></i> Ver
                    </button>
                </td>
            `;
            
            tbody.appendChild(row);
        });
        
        // Add event listeners for clickable rows
        addOrderClickListeners();
    })
    .catch(error => {
        console.error('Erro ao carregar encomendas:', error);
        
        const tbody = document.getElementById('orders-table-body');
        if (tbody) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger py-4">
                        <i class="bi bi-exclamation-triangle"></i> 
                        Erro ao carregar encomendas: ${error.message}
                        <br>
                        <button class="btn btn-sm btn-outline-primary mt-2" onclick="loadOrders()">
                            <i class="bi bi-arrow-clockwise"></i> Tentar novamente
                        </button>
                    </td>
                </tr>
            `;
        }
    });
}

function addOrderClickListeners() {
    // Listener for table rows
    const orderRows = document.querySelectorAll('.order-row');
    orderRows.forEach(row => {
        row.addEventListener('click', function(e) {
            // Avoid double click when clicking on button
            if (e.target.tagName !== 'BUTTON' && !e.target.closest('button')) {
                const orderId = this.getAttribute('data-order-id');
                if (orderId) {
                    console.log('Row click - Order ID:', orderId);
                    redirectToOrderInfo(orderId);
                }
            }
        });
    });   
    
    // Listener for specific buttons
    const viewButtons = document.querySelectorAll('.view-order-btn');
    viewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent event propagation
            const orderId = this.getAttribute('data-order-id');
            if (orderId) {
                console.log('Button click - Order ID:', orderId);
                redirectToOrderInfo(orderId);
            }
        });
    });
}

function getStatusClass(status) {
    if (!status) return 'bg-secondary';
    
    switch(status.toLowerCase()) {
        case 'pendente':
            return 'bg-warning text-dark';
        case 'processando':
            return 'bg-info';
        case 'enviado':
            return 'bg-primary';
        case 'entregue':
            return 'bg-success';
        case 'cancelado':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) {
            return 'Data inválida';
        }
        return date.toLocaleDateString('pt-PT') + ' ' + date.toLocaleTimeString('pt-PT', {hour: '2-digit', minute: '2-digit'});
    } catch (error) {
        console.error('Error formatting date:', error);
        return 'Data inválida';
    }
}

function redirectToOrderInfo(orderId) {
    if (!orderId) {
        console.error('Order ID is required');
        return;
    }
    
    // Store the order ID for the next page if needed
    sessionStorage.setItem('selectedOrderId', orderId);
    
    const targetUrl = `order_info.php?id=${orderId}`;
    console.log('Redirecting to:', targetUrl);
    window.location.href = targetUrl;
}

// Function to get order details (called when row is clicked)
function getOrderId(orderId) {
    if (!orderId) return null;
    
    // This function can be used to fetch additional order details if needed
    // For now, we'll just return the orderId
    fetch('../../admin/orderInfoEngine.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: orderId })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Order details fetched:', data);
    })
    .catch(error => {
        console.error('Error fetching order details:', error);
    });
    
    return orderId;
}

// Debug function to check current page path
function debugPagePath() {
    console.log('Current path:', window.location.pathname);
    console.log('Full URL:', window.location.href);
}

// Utility function to refresh orders list
function refreshOrders() {
    console.log('Refreshing orders list...');
    loadOrders();
}