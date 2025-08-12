/*
TODO:
- Connect to DB
- Admin check model with decal
- Limit image type, size and dimensions 
*/

// check for remove

const base_url = "/~ptaw-2025-gr4";

import * as THREE from "https://unpkg.com/three@0.174.0/build/three.module.js";

import WebGL from "https://unpkg.com/three@0.174.0/examples/jsm/capabilities/WebGL.js"; // import to see if device/browser supports WebGl 2

import { GLTFLoader } from "https://unpkg.com/three@0.174.0/examples/jsm/loaders/GLTFLoader.js"; // import own 3D models

import { OrbitControls } from "https://unpkg.com/three@0.174.0/examples/jsm/controls/OrbitControls.js"; // import to rotate the GLTF model using mouse click and drag

import { DecalGeometry } from "https://unpkg.com/three@0.174.0/examples/jsm/geometries/DecalGeometry.js"; // import to place decal on top of model

import { GLTFExporter } from "https://unpkg.com/three@0.174.0/examples/jsm/exporters/GLTFExporter.js"; // import to export the scene

import { renderCart } from './carrinho.js';

const userIdInput = document.getElementById("userId");
const cartIdInput = document.getElementById("cartId");
let userId = userIdInput.value;
let cartId = cartIdInput.value;

console.log("userId: " + userId);

console.log("cartId: " + cartId);

let loadingText, logo, decalPlaced = false;
let canvas, scene, camera, renderer, controls, light;
let loader, model, box, center, size;
let mouse, raycaster, helper, customDecal, decal, decalTexture, decalMaterial;

let logoSelected;

let product3DModel;

let tamanhoValue = "NoSize"
let corValue = "NoColor"
let productPriceValue;

// Obter o ID do produto da URL
const productID = window.location.search.split('=')[1];
let IDCategoriaProduto;


window.addEventListener('DOMContentLoaded', () => {
  // Chamada para API para obter produtos
  fetch('../restapi/PrintGoAPI.php/productByID/' + productID)
    .then(response => response.json())
    .then(data => {
      const produto = Array.isArray(data) ? data[0] : data;
      IDCategoriaProduto = produto.id_categoria;
      processarDadosCategoria(IDCategoriaProduto);

      // pega a div onde a imagem principal do produto sera exibida
      const bigImage = document.getElementById('bigImage');
      let img = document.createElement('img');
      img.src = produto.imagem_principal;
      bigImage.appendChild(img);

      // pega a div onde as imagens extras do produto sera exibida
      const thumbnailGallery = document.getElementById('thumbnailGallery');

      // Novo formato: imagens_extras é um array de objetos
      if (produto.imagens_extras && Array.isArray(produto.imagens_extras)) {
        produto.imagens_extras.forEach((imgObj, idx) => {

          if (produto.imagem_principal) {
            let img = document.createElement('img');
            img.src = produto.imagem_principal;
            img.classList.add('thumbnail');
            if (idx === 0) img.classList.add('active');
            img.alt = produto.titulo_produto;
            img.addEventListener("click", function () {
              const bigImage = document.getElementById("bigImage");
              const img = bigImage.querySelector("img");
              if (img) img.src = this.src;
              document.querySelectorAll(".thumbnail").forEach(img => img.classList.remove("active"));
              this.classList.add("active");
            });
            thumbnailGallery.appendChild(img);
          }

          // imagem_extra
          if (imgObj.imagem_extra) {
            let imgExtra = document.createElement('img');
            imgExtra.src = imgObj.imagem_extra;
            imgExtra.classList.add('thumbnail');
            imgExtra.alt = produto.titulo_produto;
            imgExtra.addEventListener("click", function () {
              // Troca a imagem principal
              const bigImage = document.getElementById("bigImage");
              const img = bigImage.querySelector("img");
              if (img) img.src = this.src;
              // Remove 'active' de todos
              document.querySelectorAll(".thumbnail").forEach(img => img.classList.remove("active"));
              this.classList.add("active");
            });
            thumbnailGallery.appendChild(imgExtra);
          }
          // imagem_extra_2
          if (imgObj.imagem_extra_2) {
            let imgExtra2 = document.createElement('img');
            imgExtra2.src = imgObj.imagem_extra_2;
            imgExtra2.classList.add('thumbnail');
            imgExtra2.alt = produto.titulo_produto;
            imgExtra2.addEventListener("click", function () {
              const bigImage = document.getElementById("bigImage");
              const img = bigImage.querySelector("img");
              if (img) img.src = this.src;
              document.querySelectorAll(".thumbnail").forEach(img => img.classList.remove("active"));
              this.classList.add("active");
            });
            thumbnailGallery.appendChild(imgExtra2);
          }
          // imagem_extra_3
          if (imgObj.imagem_extra_3) {
            let imgExtra3 = document.createElement('img');
            imgExtra3.src = imgObj.imagem_extra_3;
            imgExtra3.classList.add('thumbnail');
            imgExtra3.alt = produto.titulo_produto;
            imgExtra3.addEventListener("click", function () {
              const bigImage = document.getElementById("bigImage");
              const img = bigImage.querySelector("img");
              if (img) img.src = this.src;
              document.querySelectorAll(".thumbnail").forEach(img => img.classList.remove("active"));
              this.classList.add("active");
            });
            thumbnailGallery.appendChild(imgExtra3);
          }
        });
      }

      product3DModel = produto.modelo3d_produto;

      // pega o h1 onde o titulo do produto sera exibido e define o texto
      const productTitle = document.getElementById('productName');
      productTitle.textContent = produto.titulo_produto;

      // pega o h3 onde a descricao do produto sera exibida e define o texto
      const productPrice = document.getElementById('productPrice');
      productPriceValue = produto.preco_produto;
      productPrice.textContent = produto.preco_produto + "€";

      // se o produto tiver dimensão diferente de "unico", cria os botões de tamanhos
      if (produto.dimensoes && produto.dimensoes.length > 0 && produto.dimensoes[0].tamanho != "unico") {
        const sizeName = document.getElementById('productSize');
        sizeName.textContent = "Size";
        const producttSize = document.getElementById('idSizeOptions');

        let firstBtn = null;

        // Para cada dimensão, pode haver vários tamanhos separados por vírgula
        produto.dimensoes.forEach((dim) => {
          if (dim.tamanho && dim.tamanho !== "unico") {
            const tamanhosArr = dim.tamanho.split(',').map(t => t.trim());
            tamanhosArr.forEach((size) => {
              size = size.replaceAll('%20', ' ');
              const btn = document.createElement("button");
              btn.classList.add("sizeBtn");
              btn.textContent = size;
              btn.onclick = function () {
                document
                  .querySelectorAll(".sizeBtn")
                  .forEach((sizeBtn) => sizeBtn.classList.remove("activeSize"));
                this.classList.add("activeSize");
                tamanhoValue = this.innerText;
              };
              producttSize.appendChild(btn);
              if (!firstBtn) {
                firstBtn = btn;
              }
            });
          }
        });
        if (firstBtn) {
          firstBtn.classList.add("activeSize");
          tamanhoValue = firstBtn.innerText;
        }
      }

      const productColor = document.getElementById('idColorOptions');
      if (produto.cores && Array.isArray(produto.cores) && produto.cores.length > 0) {

        const colorName = document.getElementById('productColor');
        colorName.textContent = "Color";

        productColor.innerHTML = ""; // Limpa cores anteriores, se houver
        produto.cores.forEach((cor, idx) => {
          // Cria input radio
          const input = document.createElement("input");
          input.type = "radio";
          input.className = "btn-check";
          input.name = "color";
          input.id = `color-${cor.nome_cor}`;
          input.value = cor.hex_cor;
          input.autocomplete = "off";
          if (idx === 0) input.checked = true; // Seleciona a primeira cor por padrão

          // Cria label estilizado
          const label = document.createElement("label");
          label.className = "btnColor rounded-circle p-2";
          label.setAttribute("for", `color-${cor.nome_cor}`);
          label.style.backgroundColor = cor.hex_cor;
          label.style.border = "2px solid #ccc";
          label.title = cor.nome_cor;

          // Adiciona ao container
          productColor.appendChild(input);
          productColor.appendChild(label);

          input.addEventListener('change', function () {
            corValue = label.title;
          });

          if (idx === 0) {
            corValue = label.title;
          }

        });
      }

      const productDescription = document.getElementById('productDescriptionText');
      productDescription.textContent = produto.descricao_produto || "No description available.";
    })
    .catch(error => {
      console.error('Erro ao buscar produtos:', error);
    });


  fetch('../restapi/PrintGoAPI.php/feedbackAVGProduct/' + productID)
    .then(response => response.json())
    .then(data => {

      const productReviewData = Array.isArray(data) ? data[0] : data;

      const avg = Number(productReviewData.AverageClassification) || 0;

      const fullStars = Math.floor(avg);

      const hasHalfStar = avg % 1 >= 0.25 && avg % 1 < 0.75;

      const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

      let starsHTML = '';

      for (let i = 0; i < fullStars; i++) {
        starsHTML += `<svg width="30" height="30" viewBox="0 0 32 32"><use href="#star" fill="orange"/></svg>`;
      }

      if (hasHalfStar) {
        starsHTML += `<svg width="30" height="30" viewBox="0 0 32 32"><use href="#star" fill="url(#half-star)"/></svg>`;
      }

      for (let i = 0; i < emptyStars; i++) {
        starsHTML += `<svg width="30" height="30" viewBox="0 0 32 32"><use href="#star" fill="lightgray"/></svg>`;
      }

      document.getElementById('productReviews').innerHTML = starsHTML + ` (${Number(productReviewData.TotalCount) || 0} reviews)`;
    });
});

function processarDadosCategoria(idCategoria) {
  console.log("Categoria do produto: " + idCategoria);
  fetch(`../restapi/PrintGoAPI.php/getProductsBYCategory/${idCategoria}/${productID}`)
    .then(response => response.json())
    .then(data => {
      console.log("Produtos da categoria (excluindo o atual):", data);

      if (data.length != 0) {
        const Also_Like = document.getElementById('Also_Like');
        const Titulo_You_May = document.getElementById('Titulo_You_May');
        Also_Like.innerHTML = ''; // Limpa itens anteriores para evitar duplicatas
        Titulo_You_May.innerHTML = 'You may also like';
        data.forEach(produto => {
          Also_Like.innerHTML += `<a href="productscustom.php?id=${produto.id_produto}" class="boxItemLink">
            <div class="boxItem">
              <img src="${produto.imagem_principal}" alt="${produto.titulo_produto}" class="boxItemImg">
              <p class="boxItemTitle">${produto.titulo_produto}</p>
              <div class="boxItemFooter">
                <span class="boxItemPrice">${produto.preco_produto}€</span>
              </div>
            </div>
          </a>`;
        });
      }

    })
    .catch(error => {
      console.error('Erro ao buscar produtos da categoria:', error);
    });
}


document.getElementById("openModal").addEventListener("click", function () {
  document.getElementById("modal3D").style.display = "flex";
  document.getElementById("modalImage").style.display = "flex";
  document.body.classList.add("no-scroll");
});

document.getElementById("closeModal").addEventListener("click", function () {
  document.getElementById("modal3D").style.display = "none";
  document.getElementById("modalImage").style.display = "none";
  document.body.classList.remove("no-scroll");
});

document.addEventListener("DOMContentLoaded", function () {
  const label = document.getElementById("uploadImage");
  const fileInput = label.querySelector("input[type='file']");

  fileInput.addEventListener("change", function (event) {
    if (event.target.files && event.target.files[0]) {

      logoSelected = event.target.files[0];

      const reader = new FileReader();
      reader.onload = function (e) {
        reader.src = URL.createObjectURL(event.target.files[0]); // set src to blob url

        logo = reader.src;

        label.innerHTML = `<img id="chosenImg" class="chosenImg" src="${e.target.result}" alt="Uploaded Image" style="max-width: 50%; height: 50%;">`;

        checkSupport();
      };

      document.getElementById("warningsId").innerText =
        "Double Click to place decal!";

      reader.readAsDataURL(event.target.files[0]);
      removeClass("btnReplaceImage", "hideButton");
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const fileInput = document.getElementById("inputReplaceImage");

  fileInput.addEventListener("change", function (event) {
    if (event.target.files && event.target.files[0]) {

      logoSelected = event.target.files[0];

      const reader = new FileReader();

      reader.onload = function (e) {
        logo = e.target.result;

        document.getElementById("chosenImg").src = logo;

        checkSupport();
      };

      reader.readAsDataURL(event.target.files[0]);

      removeDecal();
    }
  });
});

function removeClass(id, className) {
  let element = document.getElementById(id);
  element.classList.remove(className);
}

function addClass(id, className) {
  let element = document.getElementById(id);
  element.classList.add(className);
}

function checkSupport() {
  if (WebGL.isWebGL2Available()) {
    initThreeJS();
  } else {
    const webGLError = WebGL.getWebGL2ErrorMessage();
    //document.getElementById('modal3D').appendChild(warning);

    loadingText = document.getElementById("loading");
    loadingText.style.display = webGLError;
  }
}

function initThreeJS() {
  canvas = document.getElementById("modelCanvas");
  loadingText = document.getElementById("loading");

  // Scene
  scene = new THREE.Scene();

  // Camera
  camera = new THREE.PerspectiveCamera(
    50,
    canvas.clientWidth / canvas.clientHeight,
    0.1,
    2000
  );

  // Renderer
  renderer = new THREE.WebGLRenderer({ canvas, antialias: true });
  renderer.setSize(canvas.clientWidth, canvas.clientHeight);
  renderer.setPixelRatio(canvas.devicePixelRatio);

  // Adding OrbitControls
  controls = new OrbitControls(camera, renderer.domElement);
  controls.enableDamping = true;
  controls.dampingFactor = 0.05;
  controls.rotateSpeed = 0.5;
  controls.zoomSpeed = 1.2;
  controls.minDistance = 1;
  controls.maxDistance = 1.5;
  controls.enablePan = true;

  // Light
  light = new THREE.AmbientLight(0xffffff, 1);
  scene.add(light);

  // Adding own model to the scene
  loader = new GLTFLoader();

  loader.load(
    product3DModel,
    function (gltf) {
      model = gltf.scene;

      scene.add(model);

      // Center model
      box = new THREE.Box3().setFromObject(model);
      center = box.getCenter(new THREE.Vector3());

      // Place the model in the center
      model.position.sub(center);

      // Move camera to fit model
      size = box.getSize(new THREE.Vector3()).length();
      camera.position.set(0, size * 0.5, size * 1.5);
      camera.lookAt(model.position);

      addCustomDecal();
    },
    undefined,
    function (error) {
      alert(`Error loading the model! \n Please try again. \n Error: ${error}`);
    }
  );

  // Hide loading text when model is ready
  loadingText.style.display = "none";

  animate();

  // Resize Handling
  canvas.addEventListener("resize", () => {
    camera.aspect = canvas.clientWidth / canvas.clientHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(canvas.clientWidth, canvas.clientHeight);
  });
}

// Animate the model
function animate() {
  requestAnimationFrame(animate);
  controls.update();
  renderer.render(scene, camera);
}

// Add decal to Model
function addCustomDecal() {
  // Decal
  mouse = new THREE.Vector2();
  raycaster = new THREE.Raycaster();
  helper = new THREE.Object3D();

  // Load the custom decal
  customDecal = new THREE.TextureLoader();
  decalTexture = customDecal.load(logo);

  document.addEventListener("dblclick", onClick);
}

function onClick(event) {
  event.preventDefault();

  mouse.x = (event.clientX / canvas.width) * 2 - 1;
  mouse.y = -(event.clientY / canvas.height) * 2 + 1;

  raycaster.setFromCamera(mouse, camera);

  let intersects = raycaster.intersectObject(model, true);

  if (intersects.length > 0) {
    let intersectedMesh = intersects[0].object;
    let position = intersects[0].point;
    let normal = intersects[0].face.normal.clone();
    normal.transformDirection(intersectedMesh.matrixWorld);
    normal.add(position);
    helper.position.copy(position);
    helper.lookAt(normal);

    let size = new THREE.Vector3(0.3, 0.3, 0.3);
    let decalGeometry = new DecalGeometry(
      intersectedMesh,
      position,
      helper.rotation,
      size
    );
    decalMaterial = new THREE.MeshStandardMaterial({
      map: decalTexture,
      transparent: true,
      depthTest: true,
      depthWrite: false,
      polygonOffset: true,
      polygonOffsetFactor: -4,
    });

    if (!decalPlaced) {
      decal = new THREE.Mesh(decalGeometry, decalMaterial);
      scene.add(decal);

      decalPlaced = true;

      document.getElementById("warningsId").innerText =
        "Limit to only one decal!";

      removeClass("rmvDecal", "disableButton");
      removeClass("btnAdd", "disableButton");
      document.removeEventListener("dblclick", onClick);

      addToCart();

      document.querySelectorAll(".removeDecal").forEach((button) => {
        button.addEventListener("click", function () {
          removeDecal();
        });
      });
    }
  }
}

function removeDecal() {
  scene.remove(decal);
  decalPlaced = false;

  document.getElementById("warningsId").innerText = "Double Click to place decal!";
  document.addEventListener("dblclick", onClick);
  addClass("rmvDecal", "disableButton");
  addClass("btnAdd", "disableButton");
}

function addToCart() {

  document.querySelectorAll(".buttonAdd").forEach((button) => {
    button.addEventListener("click", function () {

      if (!cartIdInput.value) {

        let alertMsg =
          `
      You need to be sign in to add products to the cart.
      Do you wish to proceed?
      `;

        if (confirm(alertMsg)) {

          window.location.href = "SignIn.html";

        }

      } else {

        const gltfExporter = new GLTFExporter();

        gltfExporter.parse(
          scene,
          function (result) {

            if (result instanceof ArrayBuffer) {
              saveArrayBuffer(result, getDate() + "_userId_" + userId + "_productId_" + productID + ".glb");
            } else {
              const output = JSON.stringify(result, null, 2);
              saveString(output, getDate() + "_userId_" + userId + "_productId_" + productID + ".gltf");
            }
          },
          { binary: true, embedImages: true },
          function (error) {
            console.log("An error happened during parsing", error);
          }
        );

      }
    });
  });
}

function uploadModel(blob, filename) {

  const formData = new FormData();

  const baseName = logoSelected.name.substring(0, logoSelected.name.lastIndexOf('.'));

  const extension = logoSelected.name.substring(logoSelected.name.lastIndexOf('.'));

  const newFileName = getDate() + "_userId_" + userId + "_productId_" + productID + "_" + baseName + extension;

  const renamedLogoFile = new File([logoSelected], newFileName, { type: logoSelected.type });

  formData.append('image', renamedLogoFile);

  formData.append("modelFile", blob, filename);

  fetch("../client/upload3DModel.php", {
    method: "POST",
    body: formData,
  })
    .then(response => response.text())
    .then(async text => {

      try {

        const result = JSON.parse(text);
        if (result.status === "success") {

          await uploadToDB(result.pathToFile, result.pathToImage);

          renderCart().then(() => {

            const cartOffcanvas = document.getElementById('carrinho');
            const bsOffcanvas = new bootstrap.Offcanvas(cartOffcanvas);
            bsOffcanvas.show();

          });

        } else {

          console.log("Error uploading to Server");

        }

      } catch (e) {

        console.error("Failed to parse response:", e);

      }

    })

    .catch(error => {

      console.error("Upload error:", error);

    });
}

function saveString(text, filename) {

  const blob = new Blob([text], { type: "text/plain" });
  uploadModel(blob, filename);

}

function saveArrayBuffer(buffer, filename) {

  const blob = new Blob([buffer], { type: "application/octet-stream" });
  uploadModel(blob, filename);

}

function getDate() {

  const now = new Date();

  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const day = String(now.getDate()).padStart(2, '0');
  const hour = String(now.getHours()).padStart(2, '0');
  const minute = String(now.getMinutes()).padStart(2, '0');
  const second = String(now.getSeconds()).padStart(2, '0');

  const formatted = `${year}_${month}_${day}_${hour}_${minute}_${second}`;

  return formatted;

}

async function uploadToDB(pathToFile, pathToImage) {

  tamanhoValue = tamanhoValue.replaceAll(' ', '%20');

  const personalizationData = {
    imagem_escolhida: pathToImage,
    modelo3d_personalizado: pathToFile,
    preco_personalizado: 0,
    mensagem_personalizada: ''
  };

  const resultAddPersonalization = await addPersonalization(personalizationData);

  console.log(resultAddPersonalization);

  const idPersonalizacao = resultAddPersonalization.data.id_personalizacao;

  const formData = {
    id_carrinho: cartId,
    id_produto: productID,
    tamanho: tamanhoValue,
    cor: corValue,
    id_personalizacao: idPersonalizacao,
  };

  const carrinhoItemId = await checkCarrinhoItem(formData);
  
  console.log(carrinhoItemId);

  if (carrinhoItemId.data.length === 0) {

    const valuesToAdd = {
      id_carrinho: cartId,
      id_produto: productID,
      tamanho: tamanhoValue,
      cor: corValue,
      quantidade: 1,
      preco: productPriceValue,
      id_personalizacao: idPersonalizacao,
    };

    await addCarrinhoItem(valuesToAdd);

  } else {

    let quantity = carrinhoItemId.data[0].quantidade + 1;
    let priceInCart = Number(carrinhoItemId.data[0].preco);
    let priceOfProduct = Number(productPriceValue);
    let price = (priceInCart + priceOfProduct);

    const updateCart = {
      id_carrinho_item: carrinhoItemId.data[0].id_carrinho_item,
      quantidade: quantity,
      preco: price.toFixed(2),
    }

    await updateCarrinhoItem(updateCart);

  }
}

async function addPersonalization(personalizationData) {

  try {
    const response = await fetch('../client/addPersonalization.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(personalizationData)
    })

    const data = await response.json();

    return data.data;

  } catch (error) {

    return null;

  }
}


document.querySelectorAll("#btnAddToCart").forEach((button) => {
  button.addEventListener("click", async function () {

    if (!cartIdInput.value) {

      let alertMsg =
        `
      You need to be sign in to add products to the cart.
      Do you wish to proceed?
      `;

      if (confirm(alertMsg)) {

        window.location.href = "SignIn.html";

      }

    } else {

      tamanhoValue = tamanhoValue.replaceAll(' ', '%20');

      const formData = {
        id_carrinho: cartIdInput.value,
        id_produto: productID,
        tamanho: tamanhoValue,
        cor: corValue,
        id_personalizacao: 1,
      };

      const carrinhoItemId = await checkCarrinhoItem(formData);

      if (carrinhoItemId.data.length === 0) {

        const valuesToAdd = {
          id_carrinho: cartIdInput.value,
          id_produto: productID,
          tamanho: tamanhoValue,
          cor: corValue,
          quantidade: 1,
          preco: productPriceValue,
          id_personalizacao: 1,
        };

        await addCarrinhoItem(valuesToAdd);

      } else {

        let quantity = carrinhoItemId.data[0].quantidade + 1;
        let priceInCart = Number(carrinhoItemId.data[0].preco);
        let priceOfProduct = Number(productPriceValue);
        let price = (priceInCart + priceOfProduct);

        const updateCart = {
          id_carrinho_item: carrinhoItemId.data[0].id_carrinho_item,
          quantidade: quantity,
          preco: price.toFixed(2),
        }

        await updateCarrinhoItem(updateCart);

      }
    }

    await renderCart();

    const cartOffcanvas = document.getElementById('carrinho');
    const bsOffcanvas = new bootstrap.Offcanvas(cartOffcanvas);
    bsOffcanvas.show();

  });
});

async function checkCarrinhoItem(formData) {

  try {
    const response = await fetch('../client/checkCarrinhoItem.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(formData)
    });

    const data = await response.json();

    return data;

  } catch (error) {

    return null;

  }
}

async function addCarrinhoItem(valuesToAdd) {

  try {
    const response = await fetch('../client/addToCart.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(valuesToAdd)
    })

    const data = await response.json();

    return data.data;

  } catch (error) {

    return null;

  }
}

async function updateCarrinhoItem(updateCart) {

  try {
    const response = await fetch('../client/updateItemFromCarrinho.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(updateCart)
    })

    const data = await response.json();

    return data.data;

  } catch (error) {

    return null;

  }
}

// estrutura de dados para feedbacks
let feedbacks = [];
let currentIndex = 0;

// função utilitária para remover acentos e converter para minúsculas
function normalizarTexto(texto) {
  if (!texto) return "";
  return texto
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "");
}

// obter feedbacks da api
async function getFeedbacks() {
  try {
    const response = await fetch(`../client/productReview.php`, {
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

document
  .getElementById("feedbackForm")
  .addEventListener("submit", async function (e) {
    e.preventDefault();

    // armazenar dados do formulario
    const comentario = document.querySelector('textarea[name="comments"]').value;
    const recommend = document.querySelector('input[name="recommend"]:checked')?.value === "yes" ? "2" : "1";
    const classificacao = document.querySelector('input[name="rating"]:checked')?.value || "5";
    //const id_categoria = document.querySelector('select[name="purchase"]').value;

    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const product = urlParams.get('id');

    console.log("asdadsd: " + product);

    const id_produto = product;


    const id_cliente = document.getElementById("userId").value;
    if (!id_cliente) {
      console.log("nao existe ID");
      alert("É necessário estar logado para enviar feedback.");
      return;
    } else {
      console.log("ID:" + id_cliente);
    }

    // validar campos "obrigatorios"
    if (!comentario || !classificacao) {
      alert("Preencha todos os campos.");
      return;
    }


    // construcao de array dos dados
    const data = {
      id_cliente: id_cliente,
      id_produto: id_produto,
      comentario: comentario,
      classificacao: classificacao,
      data_review: new Date().toISOString().split("T")[0],
      recommend: recommend,
    };
    if (recommend == 1) {
      alert("Lamentamos que não tenha gostado do produto. Por favor, deixe um comentário para que possamos melhorar.");
      data[5] = "0";
    }



    // enviar dados diretamente para a API
    try {
      const response = await fetch(`../restapi/PrintGoAPI.php/insertFeedback`,
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(data),
        }
      );

      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(`Erro HTTP: ${response.status}`);
      }

      const result = await response.json();
      console.log("Resposta da API:", result);

      if (result.success) {
        alert("Feedback enviado com sucesso!");
        document.getElementById("feedbackForm").reset();
      } else {
        alert(result.message || "Erro ao enviar feedback.");
      }

    } catch (err) {
      alert("Erro ao enviar feedback. Por favor, tente novamente.");
    }
  });