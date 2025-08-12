document.getElementById("productForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    let titulo_produto = document.getElementById("productName").querySelector("input").value;
    let sku = document.getElementById("sku").querySelector("input").value;
    let id_categoria = document.getElementById("category").querySelector("select").value;
    let preco_produto = parseFloat(document.getElementById("price").querySelector("input").value);
    let stock_produto = document.getElementById("stock").querySelector("input").value;
    let status_produto = document.getElementById("status").querySelector("select").value === "Active" ? 1 : 0;
    let descricao_produto = document.getElementById("description").querySelector("textarea").value;
    /*let keywords_produto = document.getElementById("keywords").value;
    let imagem_principal = document.getElementById("image").files[0];
    let modelo3d_produto = document.getElementById("model3d").value; */

    console.log("Título:", titulo_produto);
    console.log("SKU:", sku);
    console.log("Categoria:", id_categoria);
    console.log("Preço:", preco_produto);
    console.log("Stock:", stock_produto);
    console.log("Status:", status_produto);
    console.log("Descrição:", descricao_produto);

    if (!titulo_produto || !sku || !id_categoria || !preco_produto || !stock_produto || !status_produto || !descricao_produto) {
        alert("Por favor, preencha todos os campos obrigatórios.");
        return;

    } else {

        const formData = {
            titulo_produto: titulo_produto,
            sku: sku,
            id_categoria: id_categoria,
            preco_produto: preco_produto,
            stock_produto: stock_produto,
            status_produto: status_produto,
            descricao_produto: descricao_produto,
        };

        const productResult = await productInsertion(formData);

        if (productResult.status === 'success') {

            window.location.href = "/PTAW/src/Admin/Dashboard.php";

        } 
    }

    async function productInsertion(formData) {

        const response = await fetch("/PTAW/admin/insertProducts.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(formData),
        });
        return await response.json();
    }

})
/*
async function fetchAndDisplayProducts() {
    try {
        const response = await fetch("../admin/getProducts.php");
        const data = await response.json();

        const tbody = document.getElementById("productTableBody");
        tbody.innerHTML = ""; // limpa a tabela antes de atualizar

        if (Array.isArray(data)) {
            data.forEach(product => {
                const tr = document.createElement("tr");

                tr.innerHTML = `
                    <th scope="row">${product.titulo_produto}</th>
                    <td>${product.nome_categoria}</td>
                    <td>${parseFloat(product.preco_produto).toFixed(2)}€</td>
                    <td>${product.stock_produto}</td>
                    <td>${product.stock_produto > 0 ? "In Stock" : "Out of Stock"}</td>
                    <td>
                        <button type="button" data-id="${product.id}" data-bs-toggle="modal" data-bs-target="#editProductModal">
                            <img src="../imagens/editButton.png" alt="Edit">
                        </button>
                        <button type="button" data-id="${product.id}" data-bs-toggle="modal" data-bs-target="#deleteProductModal">
                            <img src="../imagens/deleteButton.png" alt="Delete">
                        </button>
                    </td>
                `;

                tbody.appendChild(tr);
            });
        } else {
            console.error("Dados inesperados:", data);
        }
    } catch (err) {
        console.error("Erro ao buscar produtos:", err);
    }
}
*/

// Atualiza os produtos de 30 em 30 segundos
//setInterval(fetchAndDisplayProducts, 30000);

// FUNÇÕES DE UPDATE E DELETE (Ainda é preciso criar a estrutura))
/*
async function productUpdate(productId, formData) {
        const response = await fetch(`../admin/updateProducts.php`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(formData),
        });

        return await response.json();
    }


    async function productDeletion(productId) {
        const response = await fetch(`../admin/deleteProducts.php`, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
            }
        });

        return await response.json();
    }
        */