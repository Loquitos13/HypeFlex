const clientId = document.body.dataset.userId;
let allOrders = [];
console.log("clientId:", clientId);

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('orders-container');

    fetch('../../client/getUserOrders.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_cliente: clientId })
    })
        .then(res => {
            if (!res.ok) throw new Error("Erro ao obter dados");
            return res.json();
        })
        .then(async data => {
            if (!Array.isArray(data.data)) throw new Error("Resposta inesperada");

            const orders = data.data;

            for (const order of orders) {
                await (async () => {
                    const itens = await fetch('../../client/getOrderItems.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id_encomenda: order.id_encomenda })
                    })
                        .then(res => res.json())
                        .then(resData => resData.status === 'success' ? resData.data : [])
                        .catch(() => []);

                    let produtosHtml = '';
                    if (itens.length > 0) {
                        produtosHtml = itens.map(item => `<p>${item.titulo_produto} — Quantidade: ${item.quantidade}</p>`).join('');
                    } else {
                        produtosHtml = '<p>Sem produtos</p>';
                    }

                    // Armazena a encomenda com HTML já gerado para fácil filtragem
                    order.produtosHtml = produtosHtml;
                    allOrders.push(order);
                })();
            }

            renderOrders(allOrders); // Mostra todas no início
        })
        .catch(err => {
            console.error(err);
            container.innerHTML += `<p class="text-danger">Erro ao carregar encomendas.</p>`;
        });
});

// Renderiza as encomendas na página
function renderOrders(filteredOrders) {
    const container = document.getElementById('orders-container');;
    container.innerHTML = '';

    filteredOrders.forEach(order => {
        const card = document.createElement('div');
        card.className = 'order-card';
        card.dataset.status = order.status_encomenda.toLowerCase();

        const statusClass = getStatusClass(order.status_encomenda);

        const html = `
            <div class="d-flex justify-content-between">
                <div>
                    <h6>Order #${order.id_encomenda}</h6>
                    <p class="text-muted">Placed on ${formatDate(order.data_criacao_encomenda)}</p>
                    <strong>Produtos:</strong>
                    ${order.produtosHtml}
                    <p><strong>Total:</strong> €${parseFloat(order.preco_total_encomenda).toFixed(2)}</p>
                </div>
                <div>
                    <span class="order-status ${statusClass}">${capitalize(order.status_encomenda)}</span>
                    <div class="mt-2">
                        <a href="#" class="text-primary">Track Order</a> |
                        <a href="#" class="text-primary">View Details</a>
                    </div>
                </div>
            </div>
        `;

        card.innerHTML = html;
        container.appendChild(card);
    });
}

// Filtro por estado ao clicar nos botões
document.getElementById('filter-buttons').addEventListener('click', (e) => {
    const button = e.target.closest('button');
    if (!button) return;

    const status = button.dataset.status;

    // Muda estilo visual dos botões
    document.querySelectorAll('#filter-buttons button').forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-light');
    });
    button.classList.add('btn-primary');

    if (status === 'all') {
        renderOrders(allOrders);
    } else {
        const mapped = mapStatus(status);
        const filtered = allOrders.filter(order =>
            order.status_encomenda.toLowerCase() === mapped.toLowerCase()
        );
        renderOrders(filtered);
    }
});

function getStatusClass(status_encomenda) {
    switch (status_encomenda.toLowerCase()) {
        case 'processing': return 'processing';
        case 'shipped': return 'shipped';
        case 'delivered': return 'delivered';
        default: return '';
    }
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' });
}

function capitalize(text) {
    return text.charAt(0).toUpperCase() + text.slice(1);
}

document.querySelector('.search-bar').addEventListener('keyup', (e) => {
    const searchTerm = e.target.value.toLowerCase().trim();

    const filtered = allOrders.filter(order => {
        const idMatch = order.id_encomenda.toString().includes(searchTerm);
        const produtosMatch = order.produtosHtml.toLowerCase().includes(searchTerm);
        return idMatch || produtosMatch;
    });

    renderOrders(filtered);
});

function mapStatus(statusKey) {
    switch (statusKey) {
        case 'processing': return 'Pendente';
        case 'shipped': return 'Enviado';
        case 'delivered': return 'Entregue';
        default: return '';
    }
}