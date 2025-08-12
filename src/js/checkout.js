let sum = 0;

const userIdInput = document.getElementById("userId");
const cartIdInput = document.getElementById("cartId");

const userId = userIdInput.value;
const cartId = cartIdInput.value;
let paymentMethodId;

document.addEventListener("DOMContentLoaded", async function () {

    const cartBackBtn = document.getElementById('cart-back');
    if (cartBackBtn) {
        cartBackBtn.addEventListener('click', function () {
            window.location.href = 'produtos.php';
        });
    }


    try {
        const cartResp = await fetch('../client/checkout.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ userId: userId })
        });
        const cartData = await cartResp.json();
        if (cartData.status === 'success') {
            renderCarrinhoItens(cartData.data);
        } else {
            console.error(cartData.message);
        }

        const clientResp = await fetch('../client/CheckoutClienteDados.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ userId: userId })
        });
        const clientData = await clientResp.json();
        if (clientData.status === 'success') {
            PreencherDadosCliente(clientData.data);
        } else {
            console.error(clientData.message);
        }

        const clientPaymentMethod = await fetch('../client/getClientPaymentMethod.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_cliente: userId })
        });
        const clientPaymentData = await clientPaymentMethod.json();

        if (clientPaymentData.status === 'success') {
            PreencherDadosClienteMetodoPagamento(clientPaymentData.data[0]);
            paymentMethodId = clientPaymentData.data[0].id_metodo_pagamento;
        } else {
            console.error(clientPaymentData.message);
        }
    } catch (err) {
        console.error("Erro ao carregar dados:", err);
    }

    const steps = [
        { // Step 0: Delivery
            container: document.querySelector('.container-delivery'),
            insignia: document.querySelectorAll('.Insignia')[0],
            barraProgresso: document.querySelectorAll('.BarraProgresso')[0],
            barraNomes: document.querySelectorAll('.BarraNomes')[0],
            buttons: cartBackBtn ? cartBackBtn.parentElement : null,
            nextButton: document.getElementById('payment-button')
        },
        { // Step 1: Payment
            container: document.querySelector('.container-payment'),
            insignia: document.querySelectorAll('.Insignia')[1],
            barraProgresso: document.querySelectorAll('.BarraProgresso')[1],
            barraNomes: document.querySelectorAll('.BarraNomes')[1],
            buttons: document.getElementById('delivery-back')?.parentElement,
            nextButton: document.getElementById('review-button')
        },
        { // Step 2: Confirm
            container: document.querySelector('.container-shipping'),
            insignia: document.querySelectorAll('.Insignia')[2],
            barraProgresso: document.querySelectorAll('.BarraProgresso')[2],
            barraNomes: document.querySelectorAll('.BarraNomes')[2],
            buttons: document.getElementById('payment-back')?.parentElement,
            nextButton: document.getElementById('confirm-button')
        }
    ];

    let currentStep = 0;

    function hideAllSteps() {
        document.querySelectorAll('.container-delivery, .container-payment, .container-shipping, .Insignia, .BarraProgresso, .BarraNomes').forEach(el => {
            el.classList.remove('active');
        });
        steps.forEach(step => step.buttons.style.display = 'none');
    }

    function showStep(stepIndex) {
        const step = steps[stepIndex];

        step.container.classList.add('active');
        step.insignia.classList.add('active');
        step.barraProgresso.classList.add('active');
        step.barraNomes.classList.add('active');

        step.buttons.style.display = 'flex';
    }

    steps.forEach((step, index) => {
        if (step.nextButton) {
            step.nextButton.addEventListener('click', function (e) {
                if (index === 0) {
                    if (!validateDeliveryFields()) {
                        e.preventDefault();
                        return;
                    }
                }
                if (index === 1) {
                    if (!validatePaymentFields()) {
                        e.preventDefault();
                        return;
                    }
                    PreencherDadosOrderReview();
                }
                if (index === 2) {
                    createOrder()
                    //botão de confirmar
                }
                e.preventDefault();
                if (currentStep < steps.length - 1) {
                    currentStep++;
                    hideAllSteps();
                    showStep(currentStep);
                }
            });
        }
    });

    steps.forEach((step, index) => {
        if (step.buttons) {
            const backButton = step.buttons.querySelector('.btn-secondary');
            if (backButton) {
                backButton.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (currentStep > 0) {
                        currentStep--;
                        hideAllSteps();
                        showStep(currentStep);
                    }
                });
            }
        }
    });

    hideAllSteps();
    showStep(currentStep);
});

function renderCarrinhoItens(itens) {
    const container = document.querySelector('.container-items');
    container.innerHTML = '';
    let html = '';
    let sum = 0;

    itens.forEach(item => {
        const preco = parseFloat(item.preco);
        sum += preco;

        html += `
            <div class='row w-100 mb-3'>
                <div class='col-6 d-flex align-items-center'>
                    <img src='${item.imagem_principal}' alt='${item.titulo_produto}' style='width: 80px; height: auto; margin-right: 15px; object-fit: contain;' />
                    <div class='d-flex flex-column justify-content-center'>
                        <h6 class='fw-bold'>${item.titulo_produto}</h6>
                        <p class='mb-1'>Quantidade: ${item.quantidade}</p>
                    </div>
                </div>
                <div class='col-6 d-flex align-items-center justify-content-end'>
                    <p class='fw-bold confirm-price'>${preco.toFixed(2)} €</p>
                </div>
            </div>
        `;

    });

    container.innerHTML = html;

    document.getElementById('subtotal').textContent = `${sum.toFixed(2)}€`;

}

function selectCountryCode(pais_cliente) {

    const countryNameToCode = {
        "Portugal": "+351",
        "Spain": "+34",
        "France": "+33",
        "Italy": "+39",
        "Germany": "+49",
        "United Kingdom": "+44",
        "Netherlands": "+31",
        "Belgium": "+32",
        "Austria": "+43",
        "Denmark": "+45"
    };

    const select = document.getElementById("country-code");
    if (!select) return;
    const code = countryNameToCode[pais_cliente] || "+351";
    select.value = code;
}

function PreencherDadosCliente(cliente) {
    if (!cliente) return;
    const nomePartes = (cliente.nome_cliente).trim().split(" ");
    const primeiroNome = nomePartes[0];
    const apelido = nomePartes.slice(1).join(" ");

    document.getElementById("firstName").value = primeiroNome;
    document.getElementById("lastName").value = apelido;
    document.getElementById("email").value = cliente.email_cliente;
    selectCountryCode(cliente.pais_cliente);
    document.getElementById("phoneNumber").value = cliente.contacto_cliente || '';
    document.getElementById("address").value = cliente.morada_cliente || '';
    document.getElementById("city").value = cliente.cidade_cliente || '';
    document.getElementById("postalCode").value = cliente.cod_postal_cliente || '';
    document.getElementById("taxId").value = cliente.nif_cliente || '';
}

function PreencherDadosClienteMetodoPagamento(paymentMethod) {
    if (!paymentMethod) return;

    document.getElementById('cardName').value = paymentMethod.nome_cartao;
    document.getElementById('cardNumber').value = paymentMethod.numero_cartao;
    document.getElementById('ExpirationDate').value = paymentMethod.validade_cartao;
    document.getElementById('CVC').value = paymentMethod.cvv_cartao;

}

function validateDeliveryFields() {
    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById("lastName").value.trim();
    const email = document.getElementById("email").value.trim();
    const phoneNumber = document.getElementById("phoneNumber").value.trim();
    const address = document.getElementById("address").value.trim();
    const city = document.getElementById("city").value.trim();
    const postalCode = document.getElementById("postalCode").value.trim();
    const taxId = document.getElementById("taxId").value.trim();

    if (!firstName) {
        alert("Please enter your first name.");
        document.getElementById('firstName').focus();
        return false;
    }

    if (!lastName) {
        alert("Please enter your last name.");
        document.getElementById('lastName').focus();
        return false;
    }

    if (!email) {
        alert("Please enter your email address.");
        document.getElementById('email').focus();
        return false;
    }

    if (!phoneNumber) {
        alert("Please enter your phone number.");
        document.getElementById('phoneNumber').focus();
        return false;
    }

    if (!address) {
        alert("Please enter your address.");
        document.getElementById('address').focus();
        return false;
    }

    if (!city) {
        alert("Please enter your city.");
        document.getElementById('city').focus();
        return false;
    }

    if (!postalCode) {
        alert("Please enter your postal code.");
        document.getElementById('postalCode').focus();
        return false;
    }
    if (!taxId) {
        alert("Please enter your tax ID.");
        document.getElementById('taxId').focus();
        return false;
    }

    return true;
}

function validatePaymentFields() {

    const cardName = document.getElementById('cardName').value.trim();
    const cardNumber = document.getElementById('cardNumber').value.trim();
    const ExpirationDate = document.getElementById('ExpirationDate').value.trim();
    const CVC = document.getElementById('CVC').value.trim();

    if (!cardName) {
        alert("Please enter your card name.");
        document.getElementById('cardName').focus();
        return false;
    }

    if (!cardNumber) {
        alert("Please enter your card name.");
        document.getElementById('cardNumber').focus();
        return false;
    }

    if (!ExpirationDate) {
        alert("Please enter your expiration date name.");
        document.getElementById('ExpirationDate').focus();
        return false;
    }

    if (!CVC) {
        alert("Please enter your CVC.");
        document.getElementById('CVC').focus();
        return false;
    }

    return true;
}

function PreencherDadosOrderReview() {
    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById("lastName").value.trim();
    const address = document.getElementById("address").value.trim();
    const city = document.getElementById("city").value.trim();
    const postalCode = document.getElementById("postalCode").value.trim();
    const cardNumber = document.getElementById('cardNumber').value.trim();
    const lastFour = cardNumber.slice(-4);

    if (getSelectedDeliveryOption() === 'Express') {

        document.getElementById("summaryDeliveryMethod").textContent = "Express Delivery (1-2 days)";
        document.getElementById("shipping").textContent = "12.99€";

    }

    document.getElementById("summaryDeliveryAddressName").textContent = firstName + " " + lastName;
    document.getElementById("summaryDeliveryAddressStreet").textContent = address;
    document.getElementById("summaryDeliveryAddressCity").textContent = city + ", " + postalCode;
    document.getElementById("summaryPaymentDetails").textContent = "Credit Card (**** " + lastFour + ")";

    const sum = parseFloat(document.getElementById('subtotal').textContent.replace('€', '')) || 0;
    const shipping = parseFloat(document.getElementById('shipping').textContent.replace('€', '')) || 0;
    const tax = parseFloat(document.getElementById('tax').textContent.replace('€', '')) || 0;

    const total = sum + shipping + tax;
    document.getElementById('total').textContent = `${total.toFixed(2)}€`;

}

function getSelectedDeliveryOption() {
    const selected = document.querySelector('input[name="deliveryOption"]:checked');

    return selected.value;
}

function getDate() {

    const now = new Date();

    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hour = String(now.getHours()).padStart(2, '0');
    const minute = String(now.getMinutes()).padStart(2, '0');
    const second = String(now.getSeconds()).padStart(2, '0');

    const formatted = `${year}-${month}-${day}-${hour}-${minute}-${second}`;

    return formatted;

}

async function createOrder() {

    const orderValues = {
        id_carrinho: cartId,
        preco_total_encomenda: parseFloat(document.getElementById('total').textContent.replace('€', '')),
        fatura: 'FAT-' + getDate(),
        status_encomenda: 'Pendente',
        notas_encomenda: document.getElementById('userNotes').value,
    }

    try {

        const orderResp = await fetch('../client/addOrder.php', {

            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(orderValues)

        });

        const orderData = await orderResp.json();

        if (orderData.status === 'success') {

            const cartItensResp = await fetch('../client/getCarrinhoItensBackup.php', {

                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_carrinho: cartId })

            });

            const cartItensData = await cartItensResp.json();

            if (cartItensData.status === 'success') {

                cartItensData.message.forEach(async (item, index) => {

                    item.Size = item.Size.replaceAll(' ', '%20');

                    const orderItensValues = {
                        id_encomenda: orderData.data.id.id_encomenda,
                        id_produto: item.ProductId,
                        quantidade: item.Quantity,
                        preco: item.Price,
                        nome_cor: item.Color,
                        tamanho: item.Size,
                        id_personalizacao: item.PersonalizacaoId === null ? null : item.PersonalizacaoId
                    }

                    const orderItensResp = await fetch('../client/addOrderItens.php', {

                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(orderItensValues)

                    });

                    const orderItensData = await orderItensResp.json();

                    if (orderItensData.status === 'success') {

                        const deleteOrderItensResp = await fetch('../client/deleteCartItens.php', {

                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id_carrinho: cartId })

                        });

                        await deleteOrderItensResp.json();

                    }
                });


                const checkPaymentCardResp = await fetch('../client/checkPaymentCard.php', {

                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ numero_cartao: document.getElementById('cardNumber').value })

                });

                const paymentData = await checkPaymentCardResp.json();


                if (paymentData.message.length === 0) {

                    const cardPaymentValues = {
                        numero_cartao: document.getElementById('cardNumber').value,
                        validade_cartao: document.getElementById('ExpirationDate').value,
                        cvv_cartao: document.getElementById('CVC').value,
                        nome_cartao: document.getElementById('cardName').value
                    }

                    const cardPaymentResp = await fetch('../client/addCardPayment.php', {

                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(cardPaymentValues)

                    });

                    const cardPaymentData = await cardPaymentResp.json();

                    if (cardPaymentData.status === 'success') {

                        const paymentMethodValues = {
                            id_cartao: cardPaymentData.data.id.id_cartao,
                            id_cliente: userId
                        }

                        const paymentMethodResp = await fetch('../client/addPaymentMethod.php', {

                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(paymentMethodValues)

                        });

                        const paymentMethodData = await paymentMethodResp.json();

                        paymentMethodId = paymentMethodData.data.id.id_metodo_pagamento

                    }
                }

                const paymentValues = {
                    id_encomenda: orderData.data.id.id_encomenda,
                    id_metodo_pagamento: paymentMethodId,
                    valor_pago: parseFloat(document.getElementById('total').textContent.replace('€', ''))
                }

                const pagamentoResp = await fetch('../client/addPayment.php', {

                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(paymentValues)

                });

                const pagamentoData = await pagamentoResp.json();

                if (pagamentoData.status === 'success') {

                    setTimeout(() => {
                        window.location.replace('../index.php');
                    }, 2000);

                }

            }


        } else {

            console.error(orderData.message);

        }

    } catch (err) {

        console.error("Erro ao carregar dados:", err);

    }

}
document.getElementById("payment-button").addEventListener("click", function () {
   window.scroll(0,0);
    });

document.getElementById("review-button").addEventListener("click", function () {
    window.scrollTo({
        top: 0,
        behavior: "smooth" // efeito suave
    });
});

