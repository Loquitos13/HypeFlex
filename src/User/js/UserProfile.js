const clientId = document.body.dataset.clientId; // obtém o ID do cliente a partir do atributo data-user-id no body
console.log('clientId:', clientId);
document.addEventListener('DOMContentLoaded', async function () {
    try {
        const dataOrders = await getRecentOrderItems();
        const dataClient = await getClientProfileInfo();
        const dataSummary = await getAcoountSummary();

        let totalOrders = 0;
        if (Array.isArray(dataSummary)) {
            totalOrders = dataSummary[0]?.total ?? 0;
        } else if (typeof dataSummary === 'object' && dataSummary !== null) {
            totalOrders = dataSummary.total || dataSummary.data?.total || 0;
        }

        renderRecentOrders(dataOrders); 
        renderUserProfile(dataClient);
        renderAccountSummary(totalOrders);
    } catch (error) {
        console.error('Erro ao inicializar perfil do usuário:', error);
    }
});

function renderRecentOrders(data) {
    const listGroup = document.getElementById('recent-orders-list');
    if (!listGroup) return;

    listGroup.innerHTML = '';

    if (!data || data.length === 0) {
        listGroup.innerHTML = '<div class="list-group-item">Nenhuma encomenda encontrada.</div>';
        return;
    }

    // Limitar a exibição às 5 encomendas mais recentes
    const recentOrders = data.slice(0, 5);

    recentOrders.forEach(encomenda => {
        let badgeClass = 'bg-secondary';
        let status = encomenda.status_encomenda || 'Pending';
        
        // Tradução e definição de cores para os status
        if (status.toLowerCase() === 'delivered' || status.toLowerCase() === 'entregue') {
            badgeClass = 'bg-success';
            status = 'Entregue';
        } else if (status.toLowerCase() === 'processing' || status.toLowerCase() === 'processando') {
            badgeClass = 'bg-info';
            status = 'Processando';
        } else if (status.toLowerCase() === 'cancelled' || status.toLowerCase() === 'cancelado') {
            badgeClass = 'bg-danger';
            status = 'Cancelado';
        } else if (status.toLowerCase() === 'shipping' || status.toLowerCase() === 'enviado') {
            badgeClass = 'bg-primary';
            status = 'Enviado';
        }

        const date = new Date(encomenda.data_criacao_encomenda);
        const formattedDate = date.toLocaleDateString('pt-PT', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        // Calcular valor total do pedido
        const total = encomenda.preco_total_encomenda || 0;
        const formattedTotal = total.toLocaleString('pt-PT', {
            style: 'currency',
            currency: 'EUR'
        });

        const item = document.createElement('div');
        item.className = 'list-group-item border rounded mb-2';
        item.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">Encomenda #${encomenda.id_encomenda}</h6>
                    <small class="text-muted">Realizada em ${formattedDate}</small>
                    <p class="mb-0 mt-1">Total: ${formattedTotal}</p>
                </div>
                <span class="badge ${badgeClass} rounded-pill">${status}</span>
            </div>
        `;
        
        // Adicionar evento de clique para ir para a página de detalhes da encomenda
        item.style.cursor = 'pointer';
        item.addEventListener('click', () => {
            window.location.href = `./order_details.php?id=${encomenda.id_encomenda}`;
        });
        
        listGroup.appendChild(item);
    });
}

async function getRecentOrderItems() {
    console.log("Buscando encomendas para cliente ID:", clientId);
    
    if (!clientId) {
        console.error("ID do cliente não disponível");
        return [];
    }
    
    try {
        const response = await fetch('../../client/getRecentOrdersByClient.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id_cliente: clientId })
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error(`Erro HTTP ${response.status}:`, errorText);
            throw new Error(`Erro HTTP: ${response.status}`);
        }
        
        const responseText = await response.text();
        console.log("Resposta bruta:", responseText);
        
        // Se a resposta estiver vazia, retornar array vazio
        if (!responseText.trim()) {
            console.log("Resposta vazia da API");
            return [];
        }
        
        // Tentar analisar a resposta como JSON
        try {
            const data = JSON.parse(responseText);
            console.log('Dados de encomendas recebidos:', data);
            
            // Verificação para garantir que temos um array de encomendas
            if (Array.isArray(data)) {
                return data;
            } else if (data && Array.isArray(data.data)) {
                return data.data;
            } else {
                console.log('Formato de dados inesperado, retornando array vazio');
                return [];
            }
        } catch (e) {
            console.error("Erro ao analisar resposta JSON:", e);
            return [];
        }
    } catch (error) {
        console.error('Erro ao buscar encomendas do cliente:', error);
        return [];
    }
}
function renderUserProfile(data) {
    console.log('A renderizar perfil', data);
    const profileDiv = document.getElementById('user-profile-card');
    if (!profileDiv || !data) return;

    const date = new Date(data.data_criacao_cliente);
    const formattedDate = date.toLocaleDateString('pt-PT', {
        year: 'numeric',
        month: 'long'
    });

    profileDiv.innerHTML = `
        <img src="${data.imagem_cliente}" 
             alt="${data.nome_cliente}" width="30%" height="30%" 
             class="rounded-circle img-thumbnail mb-3">
        <h4 class="mb-0">${data.nome_cliente}</h4>
        <p class="text-muted">Member since ${formattedDate}</p>
        <a href="./edit_profile.php">
            <button class="btn btn-primary px-4">Edit Profile</button>
        </a>
    `;
}

async function getClientProfileInfo() {
    try {
        const response = await fetch('../../client/getClientProfile.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id_cliente: clientId })
        });
        return await response.json();
    } catch (error) {
        console.error('Erro ao buscar informações do perfil do utilizador:', error);
        return null;
    }
}

function renderAccountSummary(totalOrders) {
    // Seleciona o elemento <span> dentro do Account Summary
    const accountSummary = document.querySelector('.card-body .list-group-item span');
    if (!accountSummary) return;

    accountSummary.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box text-primary me-2" viewBox="0 0 16 16">
            <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5 8 5.961 14.154 3.5 8.186 1.113zM15 4.239l-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z" />
        </svg>
        ${totalOrders} Orders
    `;
}

async function getAcoountSummary() {
    try {
        const response = await fetch('../../client/getNumberOfOrdersByClient.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id_cliente: clientId })
        });
        return await response.json();
    } catch (error) {
        console.error('Erro ao buscar resumo da conta:', error);
        return null;
    }
}