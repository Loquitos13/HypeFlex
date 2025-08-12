-- =====================================================================
-- HypeFlex - Script completo de criação do esquema relacional (MySQL 8)
-- =====================================================================

-- 0) Preparação
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1) Criar base de dados e selecionar como schema por defeito
CREATE DATABASE IF NOT EXISTS `HypeFlex`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE `HypeFlex`;

-- 2) (Opcional) Limpeza idempotente - DROP em ordem reversa de dependências
DROP TABLE IF EXISTS Reviews;
DROP TABLE IF EXISTS Pagamento;
DROP TABLE IF EXISTS MetodoPagamento;
DROP TABLE IF EXISTS PagamentoPaypal;
DROP TABLE IF EXISTS PagamentoMbway;
DROP TABLE IF EXISTS PagamentoCartao;
DROP TABLE IF EXISTS EncomendaItens;
DROP TABLE IF EXISTS Encomendas;
DROP TABLE IF EXISTS Personalizacao;
DROP TABLE IF EXISTS CarrinhoItens;
DROP TABLE IF EXISTS Carrinhos;
DROP TABLE IF EXISTS ProdutosVariantes;
DROP TABLE IF EXISTS ImagemProdutos;
DROP TABLE IF EXISTS Dimensoes;
DROP TABLE IF EXISTS Produtos;
DROP TABLE IF EXISTS Categorias;
DROP TABLE IF EXISTS Cores;
DROP TABLE IF EXISTS Admins;
DROP TABLE IF EXISTS Clientes;

SET FOREIGN_KEY_CHECKS = 1;

-- 3) Criação das tabelas base/folha

-- 3.1) Clientes
CREATE TABLE Clientes (
  id_cliente           INT AUTO_INCREMENT PRIMARY KEY,
  nome_cliente         VARCHAR(255)      NOT NULL,
  email_cliente        VARCHAR(255)      NOT NULL UNIQUE,
  pass_cliente         VARCHAR(255)      NOT NULL,
  contacto_cliente     VARCHAR(15),
  morada_cliente       VARCHAR(255),
  cidade_cliente       VARCHAR(255),
  state_cliente        VARCHAR(255),
  cod_postal_cliente   VARCHAR(255),
  pais_cliente         VARCHAR(255),
  nif_cliente          VARCHAR(9),
  ip_cliente           VARCHAR(45)       NOT NULL UNIQUE,
  imagem_cliente       VARCHAR(255),
  id_gift              INT,
  id_favoritos         INT,
  id_boletim           INT,
  id_google            VARCHAR(255) UNIQUE,
  id_facebook          VARCHAR(255) UNIQUE,
  data_criacao_cliente DATETIME          NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3.2) Admins
CREATE TABLE Admins (
  id_admin            INT AUTO_INCREMENT PRIMARY KEY,
  nome_admin          VARCHAR(255) NOT NULL,
  email_admin         VARCHAR(255) NOT NULL UNIQUE,
  pass_admin          VARCHAR(255) NOT NULL,
  contacto_admin      VARCHAR(255),
  funcao_admin        VARCHAR(255) NOT NULL,
  data_criacao_admin  DATE         NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3.3) Cores
CREATE TABLE Cores (
  id_cor    INT AUTO_INCREMENT PRIMARY KEY,
  nome_cor  VARCHAR(255) NOT NULL,
  hex_cor   VARCHAR(7)   NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3.4) Categorias
CREATE TABLE Categorias (
  id_categoria        INT AUTO_INCREMENT PRIMARY KEY,
  titulo_categoria    VARCHAR(255) NOT NULL,
  descricao_categoria TEXT         NOT NULL,
  status_categoria    BOOLEAN      DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3.5) Produtos
CREATE TABLE Produtos (
  id_produto            INT AUTO_INCREMENT PRIMARY KEY,
  id_categoria          INT           NOT NULL,
  data_criacao_produto  DATETIME      NOT NULL,
  titulo_produto        VARCHAR(255)  NOT NULL,
  modelo3d_produto      VARCHAR(255),
  descricao_produto     TEXT          NOT NULL,
  imagem_principal      VARCHAR(255)  NOT NULL,
  preco_produto         DECIMAL(10,2) NOT NULL,
  stock_produto         INT           NOT NULL,
  keywords_produto      VARCHAR(255)  NOT NULL,
  status_produto        BOOLEAN       DEFAULT 0,
  KEY idx_produtos_categoria (id_categoria),
  CONSTRAINT fk_produtos_categoria
    FOREIGN KEY (id_categoria)
    REFERENCES Categorias(id_categoria)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3.6) Dimensoes (tamanhos/variações dimensionais por produto)
CREATE TABLE Dimensoes (
  id_dimensao   INT AUTO_INCREMENT PRIMARY KEY,
  dimensao_tipo VARCHAR(255) NOT NULL,
  tamanho       VARCHAR(255) NOT NULL,
  id_produto    INT          NOT NULL,
  KEY idx_dim_produto (id_produto),
  CONSTRAINT fk_dim_produto
    FOREIGN KEY (id_produto)
    REFERENCES Produtos(id_produto)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3.7) Imagens extra por produto
CREATE TABLE ImagemProdutos (
  id_imagem_extra INT AUTO_INCREMENT PRIMARY KEY,
  id_produto      INT          NOT NULL,
  imagem_extra    VARCHAR(255) NOT NULL,
  imagem_extra_2  VARCHAR(255),
  imagem_extra_3  VARCHAR(255),
  KEY idx_img_produto (id_produto),
  CONSTRAINT fk_img_produto
    FOREIGN KEY (id_produto)
    REFERENCES Produtos(id_produto)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3.8) Variantes de produtos (cor/promo)
CREATE TABLE ProdutosVariantes (
  id_produto_variante INT AUTO_INCREMENT PRIMARY KEY,
  id_produto          INT          NOT NULL,
  id_cor              INT          NOT NULL,
  promocao            DECIMAL(5,2) DEFAULT 0,
  KEY idx_var_produto (id_produto),
  KEY idx_var_cor (id_cor),
  CONSTRAINT fk_var_produto
    FOREIGN KEY (id_produto)
    REFERENCES Produtos(id_produto)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_var_cor
    FOREIGN KEY (id_cor)
    REFERENCES Cores(id_cor)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3.9) Carrinhos
CREATE TABLE Carrinhos (
  id_carrinho INT AUTO_INCREMENT PRIMARY KEY,
  id_cliente  INT         NOT NULL,
  ip_cliente  VARCHAR(45),
  KEY idx_carr_cliente (id_cliente),
  CONSTRAINT fk_carr_cliente
    FOREIGN KEY (id_cliente)
    REFERENCES Clientes(id_cliente)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3.10) Itens do carrinho
CREATE TABLE CarrinhoItens (
  id_carrinho_item INT AUTO_INCREMENT PRIMARY KEY,
  id_carrinho      INT          NOT NULL,
  id_produto       INT          NOT NULL,
  quantidade       INT          NOT NULL,
  tamanho          VARCHAR(255) NOT NULL,
  cor              VARCHAR(50)  NOT NULL,
  preco            DECIMAL(10,2) NOT NULL,
  KEY idx_ci_carrinho (id_carrinho),
  KEY idx_ci_produto (id_produto),
  CONSTRAINT fk_ci_carrinho
    FOREIGN KEY (id_carrinho)
    REFERENCES Carrinhos(id_carrinho)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ci_produto
    FOREIGN KEY (id_produto)
    REFERENCES Produtos(id_produto)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3.11) Personalização (ligada a itens do carrinho)
CREATE TABLE Personalizacao (
  id_personalizacao      INT AUTO_INCREMENT PRIMARY KEY,
  id_carrinho_item       INT          NOT NULL,
  imagem_escolhida       VARCHAR(255) NOT NULL,
  modelo3d_personalizado VARCHAR(255) NOT NULL,
  mensagem_personalizada VARCHAR(255),
  preco_personalizacao   DECIMAL(10,2) NOT NULL,
  KEY idx_pers_carrinho_item (id_carrinho_item),
  CONSTRAINT fk_pers_ci
    FOREIGN KEY (id_carrinho_item)
    REFERENCES CarrinhoItens(id_carrinho_item)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3.12) Encomendas (agora com colunas adicionais usadas nos INSERTs)
CREATE TABLE Encomendas (
  id_encomenda               INT AUTO_INCREMENT PRIMARY KEY,
  id_carrinho                INT          NOT NULL,
  preco_total_encomenda      DECIMAL(10,2) NOT NULL,
  fatura                     VARCHAR(255) NOT NULL,
  status_encomenda           VARCHAR(50)  NOT NULL,
  data_criacao_encomenda     DATETIME     NOT NULL,
  data_atualizacao_encomenda DATETIME,
  numero_seguimento          VARCHAR(255),
  transportadora             VARCHAR(255),
  notas_encomenda            TEXT,
  data_rececao_encomenda     DATETIME,
  KEY idx_enc_carrinho (id_carrinho),
  CONSTRAINT fk_enc_carrinho
    FOREIGN KEY (id_carrinho)
    REFERENCES Carrinhos(id_carrinho)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3.13) Itens da encomenda
CREATE TABLE EncomendaItens (
  id_encomenda_item  INT AUTO_INCREMENT PRIMARY KEY,
  id_encomenda       INT          NOT NULL,
  id_produto         INT          NOT NULL,
  quantidade         INT          NOT NULL,
  preco              DECIMAL(10,2) NOT NULL,
  nome_cor           VARCHAR(50)  NOT NULL,
  tamanho            VARCHAR(255) NOT NULL,
  id_personalizacao  INT,
  KEY idx_ei_encomenda (id_encomenda),
  KEY idx_ei_produto (id_produto),
  KEY idx_ei_personalizacao (id_personalizacao),
  CONSTRAINT fk_ei_encomenda
    FOREIGN KEY (id_encomenda)
    REFERENCES Encomendas(id_encomenda)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ei_produto
    FOREIGN KEY (id_produto)
    REFERENCES Produtos(id_produto)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT fk_ei_personalizacao
    FOREIGN KEY (id_personalizacao)
    REFERENCES Personalizacao(id_personalizacao)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3.14) Métodos de pagamento (tabelas específicas)
CREATE TABLE PagamentoCartao (
  id_cartao       INT AUTO_INCREMENT PRIMARY KEY,
  numero_cartao   VARCHAR(255) NOT NULL,
  validade_cartao VARCHAR(7)   NOT NULL,
  cvv_cartao      VARCHAR(4)   NOT NULL,
  nome_cartao     VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE PagamentoMbway (
  id_mbway        INT AUTO_INCREMENT PRIMARY KEY,
  telemovel_mbway VARCHAR(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE PagamentoPaypal (
  id_paypal    INT AUTO_INCREMENT PRIMARY KEY,
  email_paypal VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3.15) Método de pagamento (agregador)
CREATE TABLE MetodoPagamento (
  id_metodo_pagamento INT AUTO_INCREMENT PRIMARY KEY,
  id_cliente          INT NOT NULL,
  id_cartao           INT,
  id_mbway            INT,
  id_paypal           INT,
  KEY idx_mp_cliente (id_cliente),
  KEY idx_mp_cartao (id_cartao),
  KEY idx_mp_mbway (id_mbway),
  KEY idx_mp_paypal (id_paypal),
  CONSTRAINT fk_mp_cliente
    FOREIGN KEY (id_cliente)
    REFERENCES Clientes(id_cliente)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_mp_cartao
    FOREIGN KEY (id_cartao)
    REFERENCES PagamentoCartao(id_cartao)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_mp_mbway
    FOREIGN KEY (id_mbway)
    REFERENCES PagamentoMbway(id_mbway)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_mp_paypal
    FOREIGN KEY (id_paypal)
    REFERENCES PagamentoPaypal(id_paypal)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3.16) Pagamento (ligado à encomenda + método)
CREATE TABLE Pagamento (
  id_pagamento        INT AUTO_INCREMENT PRIMARY KEY,
  id_encomenda        INT           NOT NULL,
  id_metodo_pagamento INT           NOT NULL,
  valor_pago          DECIMAL(10,2) NOT NULL,
  data_pagamento      DATETIME      NOT NULL,
  KEY idx_pag_encomenda (id_encomenda),
  KEY idx_pag_metodo (id_metodo_pagamento),
  CONSTRAINT fk_pag_encomenda
    FOREIGN KEY (id_encomenda)
    REFERENCES Encomendas(id_encomenda)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT fk_pag_metodo
    FOREIGN KEY (id_metodo_pagamento)
    REFERENCES MetodoPagamento(id_metodo_pagamento)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3.17) Reviews
CREATE TABLE Reviews (
  id_review     INT AUTO_INCREMENT PRIMARY KEY,
  id_cliente    INT      NOT NULL,
  nome_cliente  VARCHAR(200) NOT NULL,
  id_produto    INT      NOT NULL,
  comentario    TEXT     NOT NULL,
  classificacao INT      NOT NULL,
  data_review   DATETIME NOT NULL,
  recommend     BOOLEAN  DEFAULT 0,
  KEY idx_rev_cliente (id_cliente),
  KEY idx_rev_produto (id_produto),
  CONSTRAINT fk_rev_cliente
    FOREIGN KEY (id_cliente)
    REFERENCES Clientes(id_cliente)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_rev_produto
    FOREIGN KEY (id_produto)
    REFERENCES Produtos(id_produto)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4) Segurança final
SET FOREIGN_KEY_CHECKS = 1;


