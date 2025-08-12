<?php

require_once 'QueryBuilder.php';

class ApiController
{

    protected QueryBuilder $queryBuilder;

    public function __construct()
    {

        date_default_timezone_set("Europe/Lisbon");

        $this->queryBuilder = new QueryBuilder();
    }

    public function getServerTimeStamp(): string
    {

        return $this->queryBuilder->getServerTimeStamp();

    }

    public function getUsers(): array
    {

        return $this->queryBuilder->table('Clientes')
            ->select(['id_cliente', 'nome_cliente', 'email_cliente'])
            ->order('id_cliente', 'DESC')
            ->get();
    }

    public function getFiltersProducts($categoria, $precoMinimo, $precoMaximo, $cor, $tamanho): array
    {
        $categoria = ($categoria === '_') ? [] : explode(',', $categoria);
        $cor = ($cor === '_') ? [] : explode(',', $cor);
        $tamanho = ($tamanho === '_') ? [] : explode(',', $tamanho);

        $qb = $this->queryBuilder->table('Produtos')
            ->select([
                'Produtos.*',
                'GROUP_CONCAT(DISTINCT Dimensoes.tamanho) AS tamanhos',
                'GROUP_CONCAT(DISTINCT Cores.nome_cor) AS cores'
            ])
            ->join('Categorias', 'Produtos.id_categoria', '=', 'Categorias.id_categoria') // Manter INNER JOIN se uma categoria for sempre obrigatória para um produto ser listadoAdd commentMore actionsMore actions
            ->leftJoin('Dimensoes', 'Produtos.id_produto', '=', 'Dimensoes.id_produto')
            ->leftJoin('ProdutosVariantes', 'Produtos.id_produto', '=', 'ProdutosVariantes.id_produto')
            ->leftJoin('Cores', 'ProdutosVariantes.id_cor', '=', 'Cores.id_cor'); // Este JOIN depende de ProdutosVariantes

        if (!empty($categoria)) {
            $qb->where('Produtos.id_categoria', 'IN', $categoria);
        }

        if (is_numeric($precoMinimo)) {
            $qb->where('Produtos.preco_produto', '>=', (float) $precoMinimo);
        }

        if (is_numeric($precoMaximo)) {
            $qb->where('Produtos.preco_produto', '<=', (float) $precoMaximo);
        }

        if (!empty($cor)) {
            $qb->where('Cores.nome_cor', 'IN', $cor);
        }

        if (!empty($tamanho)) {
            $primeiro = true;
            foreach ($tamanho as $t) {
                if ($primeiro) {
                    $qb->where('Dimensoes.tamanho', 'LIKE', "%$t%");
                    $primeiro = false;
                } else {
                    $qb->orWhere('Dimensoes.tamanho', 'LIKE', "%$t%");
                }
            }
        }

        $qb->groupBy('Produtos.id_produto');
        $qb->order('Produtos.id_produto', 'DESC');
        return $qb->get();
    }

    public function getCategories(): array
    {

        return $this->queryBuilder->table('Categorias')
            ->select(['*'])
            ->get();

    }

    public function getProductByID(int $productID): array
    {
        $rows = $this->queryBuilder->table('Produtos')
            ->select([
                'Produtos.id_produto AS id_produto',
                'Produtos.id_categoria',
                'Produtos.data_criacao_produto',
                'Produtos.titulo_produto',
                'Produtos.modelo3d_produto',
                'Produtos.descricao_produto',
                'Produtos.imagem_principal',
                'Produtos.preco_produto',
                'Produtos.stock_produto',
                'Produtos.keywords_produto',
                'Produtos.status_produto',
                'Cores.id_cor',
                'Cores.hex_cor',
                'Cores.nome_cor',
                'Dimensoes.dimensao_tipo',
                'Dimensoes.tamanho',
                'ImagemProdutos.id_imagem_extra',
                'ImagemProdutos.imagem_extra',
                'ImagemProdutos.imagem_extra_2',
                'ImagemProdutos.imagem_extra_3'
            ])
            ->leftJoin('ProdutosVariantes', 'Produtos.id_produto', '=', 'ProdutosVariantes.id_produto')
            ->leftJoin('Cores', 'ProdutosVariantes.id_cor', '=', 'Cores.id_cor')
            ->leftJoin('Dimensoes', 'Produtos.id_produto', '=', 'Dimensoes.id_produto')
            ->leftJoin('ImagemProdutos', 'Produtos.id_produto', '=', 'ImagemProdutos.id_produto')
            ->where('Produtos.id_produto', '=', $productID)
            ->get();

        if (empty($rows)) {
            return [];
        }

        $produto = [
            'id_produto' => $rows[0]['id_produto'],
            'id_categoria' => $rows[0]['id_categoria'],
            'data_criacao_produto' => $rows[0]['data_criacao_produto'],
            'titulo_produto' => $rows[0]['titulo_produto'],
            'modelo3d_produto' => $rows[0]['modelo3d_produto'],
            'descricao_produto' => $rows[0]['descricao_produto'],
            'imagem_principal' => $rows[0]['imagem_principal'],
            'preco_produto' => $rows[0]['preco_produto'],
            'stock_produto' => $rows[0]['stock_produto'],
            'keywords_produto' => $rows[0]['keywords_produto'],
            'status_produto' => $rows[0]['status_produto'],
            'cores' => [],
            'dimensoes' => [],
            'imagens_extras' => []
        ];

        foreach ($rows as $row) {
            if ($row['id_cor'] && !in_array($row['id_cor'], array_column($produto['cores'], 'id_cor'))) {
                $produto['cores'][] = [
                    'id_cor' => $row['id_cor'],
                    'hex_cor' => $row['hex_cor'],
                    'nome_cor' => $row['nome_cor']
                ];
            }
            if ($row['tamanho'] && !in_array($row['tamanho'], array_column($produto['dimensoes'], 'tamanho'))) {
                $produto['dimensoes'][] = [
                    'dimensao_tipo' => $row['dimensao_tipo'],
                    'tamanho' => $row['tamanho']
                ];
            }
            if ($row['id_imagem_extra'] && !in_array($row['id_imagem_extra'], array_column($produto['imagens_extras'], 'id_imagem_extra'))) {
                $produto['imagens_extras'][] = [
                    'id_imagem_extra' => $row['id_imagem_extra'],
                    'imagem_extra' => $row['imagem_extra'],
                    'imagem_extra_2' => $row['imagem_extra_2'],
                    'imagem_extra_3' => $row['imagem_extra_3']
                ];
            }
        }

        return $produto;
    }

    public function getProducts(): array
    {   
        return $this->queryBuilder->table('Produtos')
            ->select(['*'])
            ->where('status_produto', '=', 1)
            ->order('id_produto', 'DESC')
            ->get();
    }

    public function getColors(): array
    {

        return $this->queryBuilder->table('Cores')
            ->select(['*'])
            ->order('id_cor', 'DESC')
            ->get();

    }

    public function getDimensions(): array
    {
        return $this->queryBuilder->table('Dimensoes')
            ->select(['*'])
            ->order('id_dimensao', 'DESC')
            ->get();
    }

    public function getProductVariants(): array
    {
        return $this->queryBuilder->table('ProdutosVariantes')
            ->select(['*'])
            ->order('id_produto_variante', 'DESC')
            ->get();
    }

    public function getProductsAdmin(): array
    {
        try {
            $rows = $this->queryBuilder->table('Produtos')
                ->select([
                    'Produtos.id_produto AS id_produto',
                    'Produtos.id_categoria',
                    'Produtos.data_criacao_produto',
                    'Produtos.titulo_produto',
                    'Produtos.modelo3d_produto',
                    'Produtos.descricao_produto',
                    'Produtos.imagem_principal',
                    'Produtos.preco_produto',
                    'Produtos.stock_produto',
                    'Produtos.keywords_produto',
                    'Produtos.status_produto',
                    'Cores.id_cor',
                    'Cores.hex_cor',
                    'Cores.nome_cor',
                    'Dimensoes.dimensao_tipo',
                    'Dimensoes.tamanho',
                    'ImagemProdutos.id_imagem_extra',
                    'ImagemProdutos.imagem_extra',
                    'ImagemProdutos.imagem_extra_2',
                    'ImagemProdutos.imagem_extra_3'
                ])
                ->leftJoin('ProdutosVariantes', 'Produtos.id_produto', '=', 'ProdutosVariantes.id_produto')
                ->leftJoin('Cores', 'ProdutosVariantes.id_cor', '=', 'Cores.id_cor')
                ->leftJoin('Dimensoes', 'Produtos.id_produto', '=', 'Dimensoes.id_produto')
                ->leftJoin('ImagemProdutos', 'Produtos.id_produto', '=', 'ImagemProdutos.id_produto')
                ->order('Produtos.id_produto', 'DESC')
                ->get();
        } catch (Exception $e) {
            error_log("ERRO SQL: " . $e->getMessage());
            return ['error' => 'Erro SQL', 'message' => $e->getMessage()];
        }
                
        if (empty($rows)) {
            return [];
        }

        $produtos = [];
        foreach ($rows as $row) {
            $id = $row['id_produto'];
            if (!isset($produtos[$id])) {
                $produtos[$id] = [
                    'id_produto' => $row['id_produto'],
                    'id_categoria' => $row['id_categoria'],
                    'data_criacao_produto' => $row['data_criacao_produto'],
                    'titulo_produto' => $row['titulo_produto'],
                    'modelo3d_produto' => $row['modelo3d_produto'],
                    'descricao_produto' => $row['descricao_produto'],
                    'imagem_principal' => $row['imagem_principal'],
                    'preco_produto' => $row['preco_produto'],
                    'stock_produto' => $row['stock_produto'],
                    'keywords_produto' => $row['keywords_produto'],
                    'status_produto' => $row['status_produto'],
                    'cores' => [],
                    'dimensoes' => [],
                    'imagens_extras' => []
                ];
            }

            if ($row['id_cor'] && !in_array($row['id_cor'], array_column($produtos[$id]['cores'], 'id_cor'))) {
                $produtos[$id]['cores'][] = [
                    'id_cor' => $row['id_cor'],
                    'hex_cor' => $row['hex_cor'],
                    'nome_cor' => $row['nome_cor']
                ];
            }

            if ($row['tamanho'] && !in_array($row['tamanho'], array_column($produtos[$id]['dimensoes'], 'tamanho'))) {
                $produtos[$id]['dimensoes'][] = [
                    'dimensao_tipo' => $row['dimensao_tipo'],
                    'tamanho' => $row['tamanho']
                ];
            }

            if ($row['id_imagem_extra'] && !in_array($row['id_imagem_extra'], array_column($produtos[$id]['imagens_extras'], 'id_imagem_extra'))) {
                $produtos[$id]['imagens_extras'][] = [
                    'id_imagem_extra' => $row['id_imagem_extra'],
                    'imagem_extra' => $row['imagem_extra'],
                    'imagem_extra_2' => $row['imagem_extra_2'],
                    'imagem_extra_3' => $row['imagem_extra_3']
                ];
            }
        }

        return array_values($produtos);
    }

    public function getProductsBYCategory(int $productCategory, int $currentProductID): array
    {

        return $this->queryBuilder->table('Produtos')
            ->select(['*'])
            ->where('id_categoria', '=', $productCategory)
            ->where('id_produto', '!=', $currentProductID) // Adiciona esta linha para excluir o produto atual
            ->limit(4)
            ->get();

    }

    public function searchProductsByTitle($searchTerm): array
    {
        try {

            $searchTerm = urldecode($searchTerm);
            $searchTerm = trim($searchTerm);

            if (empty($searchTerm)) {
                return [];
            }

            return $this->queryBuilder->table('Produtos')
                ->select([
                    'Produtos.*',
                    'GROUP_CONCAT(DISTINCT Dimensoes.tamanho) AS tamanhos',
                    'GROUP_CONCAT(DISTINCT Cores.nome_cor) AS cores'
                ])
                ->leftJoin('Dimensoes', 'Produtos.id_produto', '=', 'Dimensoes.id_produto')
                ->leftJoin('ProdutosVariantes', 'Produtos.id_produto', '=', 'ProdutosVariantes.id_produto')
                ->leftJoin('Cores', 'ProdutosVariantes.id_cor', '=', 'Cores.id_cor')
                ->where('Produtos.titulo_produto', 'LIKE', "%$searchTerm%")
                ->where('Produtos.status_produto', '=', 1)
                ->groupBy('Produtos.id_produto')
                ->order('Produtos.id_produto', 'DESC')
                ->get();

        } catch (PDOException $e) {
            error_log("Database error in searchProductsByTitle: " . $e->getMessage());
            return [];
        } catch (Exception $e) {
            error_log("General error in searchProductsByTitle: " . $e->getMessage());
            return [];
        }
    }
    public function getCategoriesByID(): array
    {
        return $this->queryBuilder->table('Categorias')
            ->select(['*'])
            ->order('id_categoria', 'ASC')
            ->get();
    }

    public function getColorsByCategories($categorias): array
    {
        if (is_string($categorias)) {
            $categorias = explode(',', $categorias);
        }

        return $this->queryBuilder->table('Produtos')
            ->select(['DISTINCT Cores.id_cor', 'Cores.nome_cor', 'Cores.hex_cor'])
            ->join('ProdutosVariantes', 'Produtos.id_produto', '=', 'ProdutosVariantes.id_produto')
            ->join('Cores', 'ProdutosVariantes.id_cor', '=', 'Cores.id_cor')
            ->where('Produtos.id_categoria', 'IN', $categorias)
            ->get();
    }

    public function getSizesByCategories($categorias): array
    {
        if (is_string($categorias)) {
            $categorias = explode(',', $categorias);
        }

        return $this->queryBuilder->table('Dimensoes')
            ->select(['tamanho'])
            ->join('Produtos', 'Produtos.id_produto', '=', 'Dimensoes.id_produto')
            ->join('Categorias', 'Categorias.id_categoria', '=', 'Produtos.id_categoria')
            ->where('Categorias.id_categoria', 'IN', $categorias)
            ->get();
    }

    public function getUserInfoByID(int $userID): ?array
    {

        $result = $this->queryBuilder->table('Clientes')
            ->select(['*'])
            ->where('id_cliente', '=', $userID)
            ->get();

        return $result[0] ?? null;
    }

    public function getColorsByProduct($idProduto): ?array
    {
        try {
            $query = $this->QueryBuilder->table('ProdutosVariantes')
                ->select(['Cores.id_cor', 'Cores.nome_cor', 'Cores.hex_cor'])
                ->join('Cores', 'ProdutosVariantes.id_cor', '=', 'Cores.id_cor')
                ->where('ProdutosVariantes.id_produto', '=', $idProduto)
                ->groupBy('Cores.id_cor')
                ->get();
        
            return [
                'success' => true,
                'cores' => $query
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao buscar cores do produto',
                'error' => $e->getMessage()
            ];
        }
    }

    public function getDimensionsByProduct($idProduto): ?array
    {
        try {
            $query = $this->QueryBuilder->table('Dimensoes')
                ->select(['dimensao_tipo', 'tamanho'])
                ->where('id_produto', '=', $idProduto)
                ->get();
        
            return [
                'success' => true,
                'dimensoes' => $query
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao buscar dimensões do produto',
                'error' => $e->getMessage()
            ];
        }
    }

    public function getUserByID(int $userID): ?array
    {

        $result = $this->queryBuilder->table('Clientes')
            ->select(['nome_cliente'])
            ->where('id_cliente', '=', $userID)
            ->get();

        return $result[0] ?? null;
    }

    public function getUserByEmail(string $userEmail): ?array
    {

        $result = $this->queryBuilder->table('Clientes')
            ->select(['id_cliente', 'email_cliente', 'pass_cliente'])
            ->where('email_cliente', '=', $userEmail)
            ->get();

        return $result[0] ?? null;
    }

    public function getAdminByEmail(string $adminEmail): ?array
    {

        $result = $this->queryBuilder->table('Admins')
            ->select(['id_admin', 'email_admin', 'pass_admin'])
            ->where('email_admin', '=', $adminEmail)
            ->get();

        return $result[0] ?? null;
    }

    public function getAdminByID(int $adminID): ?array
    {

        $result = $this->queryBuilder->table('Admins')
            ->select(['nome_admin', 'email_admin', 'contacto_admin', 'funcao_admin', 'imagem_admin'])
            ->where('id_admin', '=', $adminID)
            ->get();

        return $result[0] ?? null;
    }

    public function getEncomendas(): array
    {

        return $this->queryBuilder->table('Encomendas')
            ->select(['id_encomenda', 'id_carrinho', 'preco_total_encomenda'])
            ->get();
    }

    public function getEncomendasChart(): array
    {

        return $this->queryBuilder->table('Encomendas')
            ->select(["DATE_FORMAT(data_criacao_encomenda, '%m') AS Month", "status_encomenda", "COUNT(*) AS total"])
            ->groupBy("Month, status_encomenda")
            ->order("Month", "ASC")
            ->order("status_encomenda", "ASC")
            ->get();
    }

    public function getRevenuePerMonth(): array
    {

        return $this->queryBuilder->table('Encomendas')
            ->select(["DATE_FORMAT(data_criacao_encomenda, '%m') AS Month", "SUM(preco_total_encomenda) AS total"])
            ->groupBy("Month")
            ->order("Month", "ASC")
            ->get();

    }

    public function getTopProducts(): array
    {

        return $this->queryBuilder->table('Produtos p')
            ->select(["p.titulo_produto AS nome_produto", "COUNT(DISTINCT ei.id_encomenda) AS total_encomendas", "SUM(ei.quantidade * ei.preco) AS total_dinheiro"])
            ->join('EncomendaItens ei', 'p.id_produto', '=', 'ei.id_produto')
            ->groupBy("p.id_produto, p.titulo_produto")
            ->order("total_dinheiro", "DESC")
            ->limit(10)
            ->get();

    }

    public function getTopProductsByCount(): array
    {

        return $this->queryBuilder->table('Produtos p')
            ->select(["p.id_produto", "p.titulo_produto AS nome_produto", "COUNT(DISTINCT ei.id_encomenda) AS total_encomendas", "p.descricao_produto", "p.preco_produto", "p.imagem_principal AS imagem_produto"])
            ->join('EncomendaItens ei', 'p.id_produto', '=', 'ei.id_produto')
            ->groupBy("p.id_produto, p.titulo_produto")
            ->order("total_encomendas", "DESC")
            ->limit(4)
            ->get();

    }

/*public function getTopProductsByCount(): array
{
    // Primeiro tentar produtos com encomendas (top por vendas)
    $productsWithOrders = $this->queryBuilder->table('Produtos p')
        ->select([
            "p.id_produto", 
            "p.titulo_produto", 
            "p.descricao_produto", 
            "p.preco_produto",
            "p.imagem_produto"
        ])
        ->join('EncomendaItens ei', 'p.id_produto', '=', 'ei.id_produto')   
        ->where('p.status_produto', '=', 1) // Só produtos ativos
        ->groupBy("p.id_produto, p.titulo_produto, p.descricao_produto, p.preco_produto, p.imagem_produto")
        ->order("COUNT(DISTINCT ei.id_encomenda)", "DESC")
        ->limit(4)
        ->get();

    // Se temos pelo menos 4 produtos com vendas, retornar
    if (count($productsWithOrders) >= 4) {
        return $productsWithOrders;
    }

    // Caso contrário, complementar com produtos ativos mais recentes
    $existingIds = array_column($productsWithOrders, 'id_produto');
    $whereNotIn = !empty($existingIds) ? 
        " AND id_produto NOT IN (" . implode(',', $existingIds) . ")" : "";
    
    $additionalProducts = $this->queryBuilder->table('Produtos')
        ->select([
            "id_produto", 
            "titulo_produto", 
            "descricao_produto", 
            "preco_produto",
            "imagem_produto"
        ])
        ->where('status_produto', '=', 1)
        ->order("data_criacao_produto", "DESC")
        ->limit(4 - count($productsWithOrders))
        ->get();

    return array_merge($productsWithOrders, $additionalProducts);
}*/

    
    public function getRecentOrders(): array
    {

        return $this->queryBuilder->table('Encomendas')
            ->select(["Encomendas.id_encomenda as OrderID", "Clientes.nome_cliente as Customer", "Encomendas.status_encomenda as Status", "Encomendas.preco_total_encomenda as Amount"])
            ->join('Carrinhos', 'Encomendas.id_carrinho', '=', 'Carrinhos.id_carrinho')
            ->join('Clientes', 'Carrinhos.id_cliente', '=', 'Clientes.id_cliente')
            ->order("Encomendas.data_criacao_encomenda", "DESC")
            ->limit(10)
            ->get();

    }

    public function numberOfOrdersFilteredByDay($day): array
    {

        return $this->queryBuilder->table('Encomendas')
            ->select(["COUNT(id_encomenda) AS NumberOfOrders"])
            ->where("DATE_FORMAT(data_criacao_encomenda, '%d')", "=", $day)
            ->get();

    }

    public function numberOfOrdersFilteredByMonth($month): array
    {

        return $this->queryBuilder->table('Encomendas')
            ->select(["COUNT(id_encomenda) AS NumberOfOrders"])
            ->where("DATE_FORMAT(data_criacao_encomenda, '%M')", "=", $month)
            ->get();

    }

    public function numberOfOrdersFilteredByYear($year): array
    {

        return $this->queryBuilder->table('Encomendas')
            ->select(["COUNT(id_encomenda) AS NumberOfOrders"])
            ->where("DATE_FORMAT(data_criacao_encomenda, '%Y')", "=", $year)
            ->get();

    }

    public function revenueFilteredByDay($day): array
    {

        return $this->queryBuilder->table('Encomendas')
            ->select(["SUM(preco_total_encomenda) AS Revenue"])
            ->where("DATE_FORMAT(data_criacao_encomenda, '%d')", "=", $day)
            ->get();

    }

    public function revenueFilteredByMonth($month): array
    {

        return $this->queryBuilder->table('Encomendas')
            ->select(["SUM(preco_total_encomenda) AS Revenue"])
            ->where("DATE_FORMAT(data_criacao_encomenda, '%M')", "=", $month)
            ->get();

    }

    public function revenueFilteredByYear($year): array
    {

        return $this->queryBuilder->table('Encomendas')
            ->select(["SUM(preco_total_encomenda) AS Revenue"])
            ->where("DATE_FORMAT(data_criacao_encomenda, '%Y')", "=", $year)
            ->get();

    }

    public function getNumberOfClientsByDay($day): array
    {

        return $this->queryBuilder->table('Clientes')
            ->select(["COUNT(id_cliente) AS NumberOfClients"])
            ->where("DATE_FORMAT(data_criacao_cliente, '%d')", "=", $day)
            ->get();

    }

    public function getNumberOfClientsByMonth($month): array
    {

        return $this->queryBuilder->table('Clientes')
            ->select(["COUNT(id_cliente) AS NumberOfClients"])
            ->where("DATE_FORMAT(data_criacao_cliente, '%M')", "=", $month)
            ->get();

    }

    public function getNumberOfClientsByYear($year): array
    {

        return $this->queryBuilder->table('Clientes')
            ->select(["COUNT(id_cliente) AS NumberOfClients"])
            ->where("DATE_FORMAT(data_criacao_cliente, '%Y')", "=", $year)
            ->get();

    }

    public function getCarrinhos(): array
    {

        return $this->queryBuilder->table('Carrinhos')
            ->select(['id_carrinho', 'id_cliente', 'ip_cliente'])
            ->get();
    }

    public function getEncomendasByUserID(int $userID): array
    {

        return $this->queryBuilder->table('Encomendas')
            ->select(['Clientes.nome_cliente, Carrinhos.id_carrinho, Encomendas.id_encomenda, Encomendas.preco_total_encomenda'])
            ->join('Carrinhos', 'Encomendas.id_carrinho', '=', 'Carrinhos.id_carrinho')
            ->join('Clientes', 'Carrinhos.id_cliente', '=', 'Clientes.id_cliente')
            ->where('Encomendas.status_encomenda', '=', 'pendente')
            ->where('Clientes.id_cliente', '=', $userID)
            ->get();
    }

    public function getProductsSoldByID(int $productID): ?array
    {

        return $this->queryBuilder->table('Encomendas')
            ->select(['Produtos.id_produto', 'COUNT(DISTINCT Encomendas.id_encomenda) AS total_encomendas'])
            ->join('Carrinhos', 'Encomendas.id_carrinho', '=', 'Carrinhos.id_carrinho')
            ->join('CarrinhoItens', 'Carrinhos.id_carrinho', '=', 'CarrinhoItens.id_carrinho')
            ->join('Produtos', 'CarrinhoItens.id_produto', '=', 'Produtos.id_produto')
            ->where('Produtos.id_produto', '=', $productID)
            ->groupBy('Produtos.id_produto')
            ->get();

    }

    public function insertUser(): array
    {

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {

            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }

        $requiredFields = [
            'nome_cliente',
            'email_cliente',
            'pass_cliente'
        ];

        $missingFields = [];

        foreach ($requiredFields as $field) {

            if (empty($data[$field])) {

                $missingFields[] = $field;
            }
        }

        $dataCriacao = date("Y-m-d H:i:s");

        if (!empty($missingFields)) {

            return [
                'error' => 'Invalid data',
                'message' => 'Missing required fields: ' . implode(', ', $missingFields)
            ];
        }

        try {

            $this->queryBuilder->table('Clientes')
                ->insert([
                    'nome_cliente' => $data['nome_cliente'],
                    'email_cliente' => $data['email_cliente'],
                    'pass_cliente' => password_hash($data['pass_cliente'], PASSWORD_DEFAULT),
                    'contacto_cliente' => $data['contacto_cliente'] ?? null,
                    'morada_cliente' => $data['morada_cliente'] ?? null,
                    'cidade_cliente' => $data['cidade_cliente'] ?? null,
                    'state_cliente' => $data['state_cliente'] ?? null,
                    'cod_postal_cliente' => $data['cod_postal_cliente'] ?? null,
                    'pais_cliente' => $data['pais_cliente'] ?? null,
                    'nif_cliente' => $data['nif_cliente'] ?? null,
                    'ip_cliente' => $data['ip_cliente'] ?? null,
                    'imagem_cliente' => $data['imagem_cliente'] ?? null,
                    'id_gift' => $data['id_gift'] ?? null,
                    'id_favoritos' => $data['id_favoritos'] ?? null,
                    'id_boletim' => $data['id_boletim'] ?? null,
                    'id_google' => $data['id_google'] ?? null,
                    'id_facebook' => $data['id_facebook'] ?? null,
                    'data_criacao_cliente' => $dataCriacao,
                ]);

            $lastId = $this->queryBuilder->getLastInsertId();

            return [
                'success' => true,
                'message' => 'User created',
                'id_cliente' => $lastId
            ];

        } catch (PDOException $e) {

            error_log("Database error: " . $e->getMessage());

            return [
                'error' => 'Error creating the user',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function updateUser(): array
    {

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {

            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }

        $key_first_element = array_key_first($data);

        $value_first_element = $data[$key_first_element];

        unset($data[$key_first_element]);

        try {

            $this->queryBuilder->table('Clientes')
                ->update($data)
                ->where($key_first_element, '=', $value_first_element)
                ->execute();

            return ['success' => 'User updated'];
        } catch (PDOException $e) {

            error_log("Database error: " . $e->getMessage());

            return [
                'error' => 'Error updating the user',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function deleteUserByID($userID): array
    {

        try {

            $this->queryBuilder->table('Clientes')
                ->delete()
                ->where('id_cliente', '=', $userID)
                ->execute();

            return ['success' => 'User deleted'];
        } catch (PDOException $e) {

            error_log("Database error: " . $e->getMessage());

            return [
                'error' => 'Error deleting the user',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function getClientIP(): string
    {
        $ipHeaders = [
            'HTTP_CLIENT_IP',
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipHeaders as $header) {
            if (!empty($_SERVER[$header])) {
                return $_SERVER[$header];
            }
        }

        return '0.0.0.0';
    }

    public function insertFeedback(): array
    {

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {
            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }

        $requiredFields = [
            'id_cliente',
            'id_produto',
            'comentario',
            'classificacao',
            'data_review',
            'recommend'
        ];

        try {
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return [
                        'error' => 'Invalid data',
                        'message' => "Missing required field: $field"
                    ];
                }
            }
            $this->queryBuilder->table('Reviews')
                ->insert([
                    'id_cliente' => $data['id_cliente'],
                    'id_produto' => $data['id_produto'],
                    'comentario' => $data['comentario'],
                    'classificacao' => $data['classificacao'],
                    'data_review' => $data['data_review'],
                    'recommend' => $data['recommend']
                ]);

            return ['success' => 'Feedback inserted'];
        } catch (PDOException $e) {

            error_log("Database error: " . $e->getMessage());

            return [
                'error' => 'Error inserting feedback',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function getFeedbacks(): array
    {
        return $this->queryBuilder->table('Reviews')
            ->select(['Clientes.nome_cliente', 'Produtos.titulo_produto', 'Reviews.comentario', 'Reviews.classificacao', 'Reviews.data_review', 'Reviews.recommend'])
            ->join('Produtos', 'Reviews.id_produto', '=', 'Produtos.id_produto')
            ->join('Clientes', 'Reviews.id_cliente', '=', 'Clientes.id_cliente')
            ->order('data_review', 'DESC')
            ->get();
    }

    public function getClassificationFeedback(): ?array
    {

        return $this->queryBuilder->table('Reviews')
            ->select(['classificacao', 'COUNT(*) AS Sum'])
            ->groupBy('classificacao')
            ->get();

    }

    public function getFeedbackAVGProduct($id_product): array
    {
        return $this->queryBuilder->table('Reviews')
            ->select(["AVG(classificacao) AS AverageClassification, COUNT(*) AS TotalCount"])
            ->where("id_produto", "=", $id_product)
            ->get();

    }

    public function getOrders(): array
    {
        try {
            return $this->queryBuilder->table('Encomendas')
                ->select([
                    'Encomendas.id_encomenda',
                    'Encomendas.preco_total_encomenda',
                    'Encomendas.fatura',
                    'Encomendas.status_encomenda',
                    'Encomendas.data_criacao_encomenda',
                    'Encomendas.data_atualizacao_encomenda',
                    'Clientes.id_cliente',
                    'Clientes.nome_cliente',
                    'Clientes.email_cliente'
                ])
                ->join('Carrinhos', 'Encomendas.id_carrinho', '=', 'Carrinhos.id_carrinho')
                ->join('Clientes', 'Carrinhos.id_cliente', '=', 'Clientes.id_cliente')
                ->order('Encomendas.data_criacao_encomenda', 'DESC')
                ->get();
        } catch (PDOException $e) {
            error_log("Database error in getOrders: " . $e->getMessage());
            throw $e;
        } catch (Exception $e) {
            error_log("General error in getOrders: " . $e->getMessage());
            throw $e;
        }

    }

    public function getRecentOrdersByClient($id_cliente): array
    {
        try {
            return $this->queryBuilder->table('Encomendas')
                ->select([
                    'Encomendas.id_encomenda',
                    'Encomendas.data_criacao_encomenda',
                    'Encomendas.status_encomenda'
                ])
                ->join('Carrinhos', 'Encomendas.id_carrinho', '=', 'Carrinhos.id_carrinho')
                ->where('Carrinhos.id_cliente', '=', $id_cliente)
                ->order('Encomendas.data_criacao_encomenda', 'DESC')
                ->limit(5)
                ->get();
        } catch (PDOException $e) {
            error_log("Database error in getRecentOrdersByClient: " . $e->getMessage());
            throw $e;
        } catch (Exception $e) {
            error_log("General error in getRecentOrdersByClient: " . $e->getMessage());
            throw $e;
        }
    }

    public function getNumberOfOrdersByClient($id_cliente): int
    {
        try {
            $result = $this->queryBuilder->table('Encomendas')
                ->select(['COUNT(*) AS total'])
                ->join('Carrinhos', 'Encomendas.id_carrinho', '=', 'Carrinhos.id_carrinho')
                ->where('Carrinhos.id_cliente', '=', $id_cliente)
                ->get();

            return $result[0]['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Database error in getNumberOfOrdersByClient: " . $e->getMessage());
            throw $e;
        } catch (Exception $e) {
            error_log("General error in getNumberOfOrdersByClient: " . $e->getMessage());
            throw $e;
        }
    }

    public function getClientProfile($id_cliente): ?array
    {
        $result = $this->queryBuilder->table('Clientes')
            ->select([
                'id_cliente',
                'nome_cliente',
                'data_criacao_cliente',
                'imagem_cliente'
            ])
            ->where('id_cliente', '=', $id_cliente)
            ->get();

        return $result[0] ?? null;
    }

    public function insertDimension(): array
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {
            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }

        $requiredFields = ['dimensao_tipo', 'tamanho', 'id_produto'];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            return [
                'success' => false,
                'message' => 'Missing required fields: ' . implode(', ', $missingFields)
            ];
        }

        try {
            $this->queryBuilder->table('Dimensoes')
                ->insert([
                    'dimensao_tipo' => $data['dimensao_tipo'],
                    'tamanho' => $data['tamanho'],
                    'id_produto' => $data['id_produto']
                ]);
            $id_dimensao = $this->queryBuilder->getLastInsertId();

            return [
                'success' => true,
                'message' => 'Dimensão inserida com sucesso',
                'id_dimensao' => $id_dimensao
            ];
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao inserir dimensão: ' . $e->getMessage()
            ];
        }
    }

public function insertProductVariant(): array
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {
            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }

        $requiredFields = ['id_produto', 'id_cor'];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            return [
                'success' => false,
                'message' => 'Missing required fields: ' . implode(', ', $missingFields)
            ];
        }

        try {
            $this->queryBuilder->table('ProdutosVariantes')
                ->insert([
                    'id_produto' => $data['id_produto'],
                    'id_cor' => $data['id_cor'],
                    'promocao' => $data['promocao'] ?? 0
                ]);
            $id_variante = $this->queryBuilder->getLastInsertId();

            return [
                'success' => true,
                'message' => 'Variante de produto inserida com sucesso',
                'id_produto_variante' => $id_variante
            ];
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao inserir variante: ' . $e->getMessage()
            ];
        }
    }

    public function updateDimensions(): array
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {
            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }

        $key_first_element = array_key_first($data);
        $value_first_element = $data[$key_first_element];
        unset($data[$key_first_element]);

        $allowedFields = [
            'dimensao_tipo',
            'tamanho',
            'id_produto'
        ];

        $updateData = [];
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }

        if (empty($updateData)) {
            return [
                'success' => false,
                'message' => 'No valid dimension fields to update'
            ];
        }

        try {
            $this->queryBuilder->table('Dimensoes')
                ->update($updateData)
                ->where($key_first_element, '=', $value_first_element)
                ->execute();

            return ['success' => 'Dimension updated'];
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return [
                'error' => 'Error updating the dimension',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function updateProductVariant(): array
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {
            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }

        $key_first_element = array_key_first($data);
        $value_first_element = $data[$key_first_element];
        unset($data[$key_first_element]);

        $allowedFields = [
            'id_produto',
            'id_cor',
            'promocao'
        ];

        $updateData = [];
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }

        if (empty($updateData)) {
            return [
                'success' => false,
                'message' => 'No valid product variant fields to update'
            ];
        }

        try {
            $this->queryBuilder->table('ProdutosVariantes')
                ->update($updateData)
                ->where($key_first_element, '=', $value_first_element)
                ->execute();

            return ['success' => 'Product variant updated'];
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return [
                'error' => 'Error updating the product variant',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function insertColor(): array
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {
            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }


        $requiredFields = ['nome_cor', 'hex_cor'];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            return [
                'error' => 'Invalid data',
                'message' => 'Missing required fields: ' . implode(', ', $missingFields)
            ];
        }

        try {

            $existing = $this->queryBuilder->table('Cores')
                ->select(['id_cor'])
                ->where('nome_cor', '=', $data['nome_cor'])
                ->orWhere('hex_cor', '=', $data['hex_cor'])
                ->get();

            if (!empty($existing)) {
                return [
                    'success' => false,
                    'message' => 'Cor já existe',
                    'id_cor' => $existing[0]['id_cor']
                ];
            }

            $this->queryBuilder->table('Cores')
                ->insert([
                    'nome_cor' => $data['nome_cor'],
                    'hex_cor' => $data['hex_cor']
                ]);
            $id_cor = $this->queryBuilder->getLastInsertId();

            return [
                'success' => true,
                'message' => 'Cor inserida com sucesso',
                'id_cor' => $id_cor
            ];
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function insertProduct(): array
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {
            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }

        $requiredFields = [
            'id_categoria',
            'titulo_produto',
            'descricao_produto',
            'preco_produto',
            'stock_produto',
            'keywords_produto'
        ];

        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $missingFields[] = $field;
            }
        }

        $dataCriacao = date("Y-m-d H:i:s");

        if (!empty($missingFields)) {
            return [
                'error' => 'Invalid data',
                'message' => 'Missing required fields: ' . implode(', ', $missingFields)
            ];
        }

        try {
            $this->queryBuilder->table('Produtos')
                ->insert([
                    'id_categoria' => $data['id_categoria'],
                    'titulo_produto' => $data['titulo_produto'],
                    'modelo3d_produto' => $data['modelo3d_produto'] ?? null,
                    'descricao_produto' => $data['descricao_produto'],
                    'imagem_principal' => $data['imagem_principal'],
                    'preco_produto' => $data['preco_produto'],
                    'stock_produto' => $data['stock_produto'] ?? 0,
                    'keywords_produto' => $data['keywords_produto'],
                    'status_produto' => $data['status_produto'],
                    'data_criacao_produto' => $dataCriacao,
                ]);
            $id_produto = $this->queryBuilder->getLastInsertId();

            return ['success' => 'Product created', 'id_produto' => $id_produto];

        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return [
                'error' => 'Error creating the product',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function updateProduct(): array
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {
            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }

        $key_first_element = array_key_first($data);
        $value_first_element = $data[$key_first_element];
        unset($data[$key_first_element]);

        try {
            $this->queryBuilder->table('Produtos')
                ->update($data)
                ->where($key_first_element, '=', $value_first_element)
                ->execute();

            if (!empty($data['variantes']) && is_array($data['variantes'])) {
                $this->queryBuilder->table('ProdutosVariantes')
                    ->delete()
                    ->where('id_produto', '=', $value_first_element)
                    ->execute();

                foreach ($data['variantes'] as $variante) {

                    $cor_info = $this->queryBuilder->table('Cores')
                        ->select(['nome_cor', 'hex_cor'])
                        ->where('id_cor', '=', $variante['id_cor'])
                        ->first();

                    $this->queryBuilder->table('ProdutosVariantes')
                        ->insert([
                            'id_produto' => $value_first_element,
                            'id_cor' => $variante['id_cor'],
                            'promocao' => $variante['promocao'] ?? 0
                        ]);

                    error_log("Cor atualizada: ID: {$variante['id_cor']}, Nome: {$cor_info['nome_cor']}, Hex: {$cor_info['hex_cor']}");
                }
            }

            if (!empty($data['dimensoes']) && is_array($data['dimensoes'])) {
                $this->queryBuilder->table('Dimensoes')
                    ->delete()
                    ->where('id_produto', '=', $value_first_element)
                    ->execute();

                foreach ($data['dimensoes'] as $dimensao) {
                    $this->queryBuilder->table('Dimensoes')
                        ->insert([
                            'id_produto' => $value_first_element,
                            'dimensao_tipo' => $dimensao['dimensao_tipo'] ?? null,
                            'tamanho' => $dimensao['tamanho'] ?? null
                        ]);
                
                    $id_dimensao = $this->queryBuilder->getLastInsertId();

                    error_log("Dimensão atualizada: ID: {$id_dimensao}, Tipo: {$dimensao['dimensao_tipo']}, Tamanho: {$dimensao['tamanho']}");
                }
            }

            if (!empty($data['imagens_extras']) && is_array($data['imagens_extras'])) {
                $this->queryBuilder->table('ImagemProdutos')
                    ->delete()
                    ->where('id_produto', '=', $value_first_element)
                    ->execute();

                foreach ($data['imagens_extras'] as $img) {
                    $this->queryBuilder->table('ImagemProdutos')
                        ->insert([
                            'id_produto' => $value_first_element,
                            'imagem_extra' => $img['imagem_extra'] ?? null,
                            'imagem_extra_2' => $img['imagem_extra_2'] ?? null,
                            'imagem_extra_3' => $img['imagem_extra_3'] ?? null
                        ]);
                }
            }

            return ['success' => 'Product updated'];

        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return [
                'error' => 'Error updating the product',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function deleteProductByID($productID): array
    {
        try {
            $this->queryBuilder->table('Produtos')
                ->update(['status_produto' => 0])
                ->where('id_produto', '=', $productID)
                ->execute();

            return ['success' => 'Produto deactivated successfully'];

        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());

            return [
                'error' => 'Error deactivating product',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
    
    public function getCarrinhoItens($userID): array
    {
        return $this->queryBuilder->table('CarrinhoItens')
            ->select([
                'CarrinhoItens.id_carrinho_item',
                'CarrinhoItens.quantidade',
                'CarrinhoItens.preco',
                'Produtos.titulo_produto',
                'Produtos.imagem_principal',
                'Produtos.preco_produto',
                'Carrinhos.id_carrinho'
            ])
            ->join('Carrinhos', 'CarrinhoItens.id_carrinho', '=', 'Carrinhos.id_carrinho')
            ->join('Produtos', 'CarrinhoItens.id_produto', '=', 'Produtos.id_produto')
            ->where('Carrinhos.id_cliente', '=', $userID)
            ->order('CarrinhoItens.id_carrinho_item', 'DESC')
            ->get();
    }

    public function getOrderID($id): array
    {
        return $this->queryBuilder->table('Encomendas')
            ->select(['*'])
            ->where('id_encomenda', '=', $id)
            ->get();
    }


    public function getUserOrders(int $userId): array
    {
        try {
            error_log("getUserOrders - Buscando encomendas para o usuário ID: $userId");


            $encomendas = $this->queryBuilder->table('Encomendas')
                ->select([
                    'Encomendas.id_encomenda',
                    'Encomendas.preco_total_encomenda',
                    'Encomendas.status_encomenda',
                    'Encomendas.data_criacao_encomenda',
                    'Encomendas.numero_seguimento',
                    'Encomendas.transportadora'
                ])
                ->join('Carrinhos', 'Encomendas.id_carrinho', '=', 'Carrinhos.id_carrinho')
                ->where('Carrinhos.id_cliente', '=', $userId)
                ->order('Encomendas.data_criacao_encomenda', 'DESC')
                ->get();


            if (empty($encomendas)) {
                error_log("Nenhuma encomenda encontrada para o cliente ID: $userId");
                return [];
            }


            error_log("Encontradas " . count($encomendas) . " encomendas para o cliente $userId");


            foreach ($encomendas as &$encomenda) {
                try {
                    $itens = $this->queryBuilder->table('EncomendaItens')
                        ->select([
                            'EncomendaItens.id_encomenda_item',
                            'EncomendaItens.quantidade',
                            'EncomendaItens.preco AS preco_item',
                            'EncomendaItens.nome_cor',
                            'EncomendaItens.tamanho',
                            'Produtos.id_produto',
                            'Produtos.titulo_produto',
                            'Produtos.imagem_principal'
                        ])
                        ->leftJoin('Produtos', 'EncomendaItens.id_produto', '=', 'Produtos.id_produto')
                        ->where('EncomendaItens.id_encomenda', '=', $encomenda['id_encomenda'])
                        ->get();

                    $encomenda['itens'] = $itens;
                } catch (Exception $innerEx) {
                    error_log("Erro ao buscar itens da encomenda " . $encomenda['id_encomenda'] . ": " . $innerEx->getMessage());
                    $encomenda['itens'] = [];
                }
            }


            return $encomendas;
        } catch (Exception $e) {
            error_log("Erro ao buscar encomendas do cliente $userId: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return [];
        }
    }

    public function getOrderById(int $orderId): ?array
    {
        try {
            $result = $this->queryBuilder->table('Encomendas')
                ->select([
                    'Encomendas.id_encomenda',
                    'Encomendas.preco_total_encomenda',
                    'Encomendas.fatura',
                    'Encomendas.status_encomenda',
                    'Encomendas.data_criacao_encomenda',
                    'Encomendas.data_atualizacao_encomenda',
                    'Encomendas.transportadora',
                    'Encomendas.numero_seguimento',
                    'Encomendas.notas_encomenda',
                    'Clientes.id_cliente',
                    'Clientes.nome_cliente',
                    'Clientes.email_cliente',
                    'Clientes.contacto_cliente',
                    'Clientes.morada_cliente',
                    'Clientes.nif_cliente',
                    'Clientes.data_criacao_cliente'
                ])
                ->join('Carrinhos', 'Encomendas.id_carrinho', '=', 'Carrinhos.id_carrinho')
                ->leftJoin('Clientes', 'Carrinhos.id_cliente', '=', 'Clientes.id_cliente')
                ->leftJoin('Pagamento', 'Encomendas.id_encomenda', '=', 'Pagamento.id_encomenda')
                ->leftJoin('MetodoPagamento', 'Pagamento.id_metodo_pagamento', '=', 'MetodoPagamento.id_metodo_pagamento')
                ->where('Encomendas.id_encomenda', '=', $orderId)
                ->get();


            if (!empty($result) && isset($result[0])) {
                if (!isset($result[0]['nome_metodo_pagamento'])) {
                    $result[0]['nome_metodo_pagamento'] = 'Não especificado';
                }
            }


            return $result[0] ?? null;
        } catch (Exception $e) {
            error_log("Error in getOrderById: " . $e->getMessage());
            return null;
        }
    }
    public function getOrderItems(int $orderId): array
    {
        try {
            error_log("Getting order items for order ID: $orderId");

            $query = $this->queryBuilder->table('EncomendaItens')
                ->select([
                    'EncomendaItens.id_encomenda_item',
                    'EncomendaItens.quantidade',
                    'EncomendaItens.preco',
                    'EncomendaItens.nome_cor',
                    'EncomendaItens.tamanho',
                    'EncomendaItens.id_personalizacao',
                    'Produtos.id_produto',
                    'Produtos.titulo_produto',
                    'Produtos.imagem_principal',
                    'Produtos.descricao_produto',
                    'Produtos.preco_produto',
                ])
                ->join('Encomendas', 'EncomendaItens.id_encomenda', '=', 'Encomendas.id_encomenda')
                ->join('Produtos', 'EncomendaItens.id_produto', '=', 'Produtos.id_produto')
                ->where('EncomendaItens.id_encomenda', '=', $orderId)
                ->order('EncomendaItens.id_encomenda_item', 'DESC');
            $result = $query->get();
            error_log("Found " . count($result) . " items for order ID: $orderId");
            return $result;
        } catch (Exception $e) {
            error_log("Error in getOrderItems: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return [];
        }
    }

    public function getOrderPaymentInfo(int $orderId): ?array
    {
        $result = $this->queryBuilder->table('Pagamento')
            ->select([
                'Pagamento.id_pagamento',
                'Pagamento.id_metodo_pagamento',
                'Pagamento.valor_pago',
                'Pagamento.data_pagamento',
            ])
            ->where('Pagamento.id_encomenda', '=', $orderId)
            ->get();

        return $result[0] ?? null;
    }


    public function updateOrderStatus(int $orderId, string $status): array
    {
        try {
            $json = file_get_contents('php://input');
            $data = null;

            if (!empty($json)) {
                $data = json_decode($json, true);
            }

            $updateData = [
                'status_encomenda' => $status,
                'data_atualizacao_encomenda' => date('Y-m-d H:i:s')
            ];

            if ($data && isset($data['tracking_number'])) {
                $updateData['numero_seguimento'] = $data['tracking_number'];
            }

            if ($data && isset($data['carrier'])) {
                $updateData['transportadora'] = $data['carrier'];
            }

            error_log("Updating order $orderId with data: " . json_encode($updateData));

            $this->queryBuilder->table('Encomendas')
                ->update($updateData)
                ->where('id_encomenda', '=', $orderId)
                ->execute();

            $notificationSent = false;
            if ($data && isset($data['notify_customer']) && $data['notify_customer'] == 1) {
                $notificationSent = true;
                error_log("Email notification would be sent for order $orderId");
            }

            return [
                'success' => true,
                'message' => 'Status da encomenda atualizado para ' . $status,
                'notification_sent' => $notificationSent,
                'tracking_number' => $data['numero_seguimento'] ?? null,
                'carrier' => $data['transportadora'] ?? null
            ];

        } catch (PDOException $e) {
            error_log("Database error in updateOrderStatus: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao atualizar status da encomenda',
                'error' => $e->getMessage()
            ];
        } catch (Exception $e) {
            error_log("General error in updateOrderStatus: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro inesperado ao processar solicitação',
                'error' => $e->getMessage()
            ];
        }
    }
    public function getDadosClientePorCarrinho($userID): array
    {
        return $this->queryBuilder->table('Carrinhos')
            ->select([
                'Clientes.email_cliente',
                'Clientes.nome_cliente',
                'Clientes.contacto_cliente',
                'Clientes.morada_cliente',
                'Clientes.cidade_cliente',
                'Clientes.state_cliente',
                'Clientes.cod_postal_cliente',
                'Clientes.nif_cliente'
            ])
            ->join('Clientes', 'Carrinhos.id_cliente', '=', 'Clientes.id_cliente')
            ->where('Carrinhos.id_cliente', '=', $userID)
            ->get();
    }

    public function insertShoppingCart(): array
    {

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {

            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }

        $requiredFields = ['id_cliente'];

        $missingFields = [];

        foreach ($requiredFields as $field) {

            if (empty($data[$field])) {

                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {

            return [
                'error' => 'Invalid data',
                'message' => 'Missing required fields: ' . implode(', ', $missingFields)
            ];
        }

        try {

            $this->queryBuilder->table('Carrinhos')
                ->insert([
                    'id_cliente' => $data['id_cliente'],
                    'ip_cliente' => $data['ip_cliente'] ?? null,
                ]);

            $lastId = $this->queryBuilder->getLastInsertId();

            return [
                'success' => true,
                'message' => 'Shopping cart created',
                'id_cart' => $lastId
            ];

        } catch (PDOException $e) {

            error_log("Database error: " . $e->getMessage());

            return [
                'error' => 'Error creating the user',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function getCarrinhoByUserId($id_cliente): array
    {

        return $this->queryBuilder->table('Carrinhos')
            ->select(['id_carrinho'])
            ->where('id_cliente', '=', $id_cliente)
            ->get();

    }

    public function getCarrinhoItensByCarrinhoId($id_carrinho): array
    {

        return $this->queryBuilder->table('CarrinhoItens')
            ->select(['id_carrinho_item as ID', 'titulo_produto as Name', 'imagem_principal as Image', 'preco as Price', 'quantidade as Quantity', 'tamanho as Size', 'cor as Color', 'id_personalizacao as Personalization'])
            ->join('Produtos', 'CarrinhoItens.id_produto', '=', 'Produtos.id_produto')
            ->where('CarrinhoItens.id_carrinho', '=', $id_carrinho)
            ->get();

    }

    public function checkCarrinhoItem($id_carrinho, $id_product, $tamanho, $cor, $personalizado): array
    {

        return $this->queryBuilder->table('CarrinhoItens')
            ->select(['id_carrinho_item', 'quantidade', 'preco'])
            ->where('id_carrinho', '=', $id_carrinho)
            ->where('id_produto', '=', $id_product)
            ->where('tamanho', '=', $tamanho)
            ->where('cor', '=', $cor)
            ->where('id_personalizacao', '=', $personalizado)
            ->get();

    }

    public function insertCarrinhoItem(): array
    {

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {

            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }

        $requiredFields = ['id_produto', 'id_carrinho', 'quantidade', 'preco', 'tamanho', 'cor'];

        $missingFields = [];

        foreach ($requiredFields as $field) {

            if (empty($data[$field])) {

                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {

            return [
                'error' => 'Invalid data',
                'message' => 'Missing required fields: ' . implode(', ', $missingFields)
            ];
        }

        try {

            $this->queryBuilder->table('CarrinhoItens')
                ->insert([
                    'id_carrinho' => $data['id_carrinho'],
                    'id_produto' => $data['id_produto'],
                    'quantidade' => $data['quantidade'],
                    'preco' => $data['preco'],
                    'tamanho' => $data['tamanho'],
                    'cor' => $data['cor'],
                    'id_personalizacao' => $data['id_personalizacao'] ?? null
                ]);

            $lastId = $this->queryBuilder->getLastInsertId();

            return [
                'success' => true,
                'message' => 'Added to Cart',
                'id_cart_item' => $lastId
            ];

        } catch (PDOException $e) {

            error_log("Database error: " . $e->getMessage());

            return [
                'error' => 'Error creating the user',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function updateItemFromCarrinhoItens(): array
    {

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {
            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }

        $requiredFields = [
            'id_carrinho_item',
            'quantidade',
            'preco'
        ];

        $missingFields = [];

        foreach ($requiredFields as $field) {

            if (!isset($data[$field])) {

                return [
                    'error' => 'Invalid data',
                    'message' => "Missing required field: $field"
                ];
            }
        }

        if (!empty($missingFields)) {

            return [
                'error' => 'Invalid data',
                'message' => 'Missing required fields: ' . implode(', ', $missingFields)
            ];
        }

        try {

            $this->queryBuilder->table('CarrinhoItens')
                ->update([
                    'quantidade' => $data['quantidade'],
                    'preco' => $data['preco']
                ])
                ->where('id_carrinho_item', '=', $data['id_carrinho_item'])
                ->execute();

            return ['success' => 'Cart item updated'];

        } catch (PDOException $e) {

            error_log("Database error: " . $e->getMessage());

            return [
                'error' => 'Error updating the user',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function deleteCartItem(): array
    {

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {
            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }

        $requiredFields = ['id_carrinho_item'];

        $missingFields = [];

        foreach ($requiredFields as $field) {

            if (!isset($data[$field])) {

                return [
                    'error' => 'Invalid data',
                    'message' => "Missing required field: $field"
                ];
            }
        }

        if (!empty($missingFields)) {

            return [
                'error' => 'Invalid data',
                'message' => 'Missing required fields: ' . implode(', ', $missingFields)
            ];
        }

        try {

            $this->queryBuilder->table('CarrinhoItens')
                ->delete()
                ->where('id_carrinho_item', '=', $data['id_carrinho_item'])
                ->execute();

            return [
                'success' => true,
                'message' => 'Cart item deleted'
            ];

        } catch (PDOException $e) {

            error_log("Database error: " . $e->getMessage());

            return [
                'error' => 'Error deleting the user',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function insertPersonalizacao(): array
    {

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {

            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }

        $requiredFields = [
            'imagem_escolhida',
            'modelo3d_personalizado',
            'preco_personalizado'
        ];

        $missingFields = [];

        foreach ($requiredFields as $field) {

            if (!isset($data[$field])) {

                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {

            return [
                'error' => 'Invalid data',
                'message' => 'Missing required fields: ' . implode(', ', $missingFields)
            ];
        }

        try {

            $this->queryBuilder->table('Personalizacao')
                ->insert([
                    'imagem_escolhida' => $data['imagem_escolhida'],
                    'modelo3d_personalizado' => $data['modelo3d_personalizado'],
                    'preco_personalizado' => $data['preco_personalizado'] ?? 0,
                    'mensagem_personalizada' => $data['mensagem_personalizada'] ?? '',
                ]);

                $id_personalizacao = $this->queryBuilder->getLastInsertId();

            return [
                'success' => true,
                'message' => 'Personalization created',
                'id_personalizacao' => $id_personalizacao
            ];

        } catch (PDOException $e) {

            error_log("Database error: " . $e->getMessage());

            return [
                'error' => 'Error creating the personalization',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function updateCustomerInfo(): array
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
    
        if (!is_array($data)) {
            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }
    
        $requiredFields = ['order_id', 'customer_info'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return ['success' => false, 'message' => "Missing required field: $field"];
            }
        }
    
        $orderId = (int) $data['order_id'];
        $customerInfo = $data['customer_info'];
    
        try {
            error_log("=== UPDATE CUSTOMER INFO DEBUG ===");
            error_log("Order ID: " . $orderId);
            error_log("Customer Info: " . json_encode($customerInfo));
    
            // Get customer ID from order
            $order = $this->queryBuilder->table('Encomendas')
                ->select(['Carrinhos.id_cliente'])
                ->join('Carrinhos', 'Encomendas.id_carrinho', '=', 'Carrinhos.id_carrinho')
                ->where('id_encomenda', '=', $orderId)
                ->get();
    
            if (empty($order)) {
                return ['success' => false, 'message' => 'Order not found'];
            }
    
            $clienteId = $order[0]['id_cliente'];
            error_log("Cliente ID found: " . $clienteId);
    
            // Create update data array with field validations
            $updateData = [];
            
            if (!empty($customerInfo['nome'])) {
                $updateData['nome_cliente'] = $customerInfo['nome'];
            }
            
            if (!empty($customerInfo['email'])) {
                $updateData['email_cliente'] = $customerInfo['email'];
            }
            
            if (isset($customerInfo['telefone'])) {
                $updateData['contacto_cliente'] = $customerInfo['telefone'];
            }
            
            if (isset($customerInfo['nif'])) {
                $updateData['nif_cliente'] = $customerInfo['nif'];
            }
            
            if (!empty($customerInfo['morada'])) {
                $updateData['morada_cliente'] = $customerInfo['morada'];
            }
            
            if (empty($updateData)) {
                return ['success' => false, 'message' => 'No valid fields to update'];
            }
            
            // Use a direct PDO approach to avoid QueryBuilder binding issues
            $pdo = Database::getConnection();
            
            // Build the query manually for better control
            $setClause = [];
            foreach ($updateData as $field => $value) {
                $setClause[] = "$field = :$field";
            }
            
            $sql = "UPDATE Clientes SET " . implode(', ', $setClause) . " WHERE id_cliente = :id_cliente";
            $stmt = $pdo->prepare($sql);
            
            // Bind all parameters explicitly
            foreach ($updateData as $field => $value) {
                $stmt->bindValue(":$field", $value);
            }
            $stmt->bindValue(':id_cliente', $clienteId, PDO::PARAM_INT);
            
            // Execute and check result
            $result = $stmt->execute();
            
            if (!$result) {
                error_log("SQL Error: " . print_r($stmt->errorInfo(), true));
                return ['success' => false, 'message' => 'Database error during update'];
            }
            
            // Get updated customer info
            $clienteAfter = $this->queryBuilder->table('Clientes')
                ->select(['*'])
                ->where('id_cliente', '=', $clienteId)
                ->get();
    
            return [
                'success' => true,
                'message' => 'Customer information updated successfully',
                'data' => [
                    'order_id' => $orderId,
                    'client_id' => $clienteId,
                    'updated_fields' => array_keys($updateData),
                    'updated_data' => $updateData,
                    'customer' => $clienteAfter[0] ?? null
                ]
            ];
    
        } catch (PDOException $e) {
            error_log("Database error in updateCustomerInfo: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        } catch (Exception $e) {
            error_log("General error in updateCustomerInfo: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error updating customer information: ' . $e->getMessage()];
        }
    }

    public function updateClientInfo(): array
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
    
        if (!is_array($data)) {
            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }
    
        $requiredFields = ['id_cliente'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return ['success' => false, 'message' => "Missing required field: $field"];
            }
        }
    
        $clienteId = (int) $data['id_cliente'];
    
        try {
            error_log("=== UPDATE CLIENT INFO DEBUG ===");
            error_log("Client ID: " . $clienteId);
            error_log("Client Data: " . json_encode($data));
    
            // Check if client exists
            $cliente = $this->queryBuilder->table('Clientes')
                ->select(['*'])
                ->where('id_cliente', '=', $clienteId)
                ->get();
    
            if (empty($cliente)) {
                return ['success' => false, 'message' => 'Cliente não encontrado'];
            }
    
            // Create update data array with field validations
            $updateData = [];
            
            if (!empty($data['nome_cliente'])) {
                $updateData['nome_cliente'] = $data['nome_cliente'];
            }
            
            if (!empty($data['email_cliente'])) {
                $updateData['email_cliente'] = $data['email_cliente'];
            }
            
            if (isset($data['contacto_cliente'])) {
                $updateData['contacto_cliente'] = $data['contacto_cliente'];
            }
            
            if (isset($data['morada_cliente'])) {
                $updateData['morada_cliente'] = $data['morada_cliente'];
            }
            
            if (isset($data['cidade_cliente'])) {
                $updateData['cidade_cliente'] = $data['cidade_cliente'];
            }
            
            if (isset($data['state_cliente'])) {
                $updateData['state_cliente'] = $data['state_cliente'];
            }
            
            if (isset($data['cod_postal_cliente'])) {
                $updateData['cod_postal_cliente'] = $data['cod_postal_cliente'];
            }
            
            if (isset($data['pais_cliente'])) {
                $updateData['pais_cliente'] = $data['pais_cliente'];
            }
            
            if (isset($data['nif_cliente'])) {
                $updateData['nif_cliente'] = $data['nif_cliente'];
            }
            
            // Handle password update separately if provided
            if (isset($data['pass_cliente']) && !empty($data['pass_cliente'])) {
                // Check current password if provided
                if (isset($data['current_password']) && !empty($data['current_password'])) {
                    $storedHashedPassword = $cliente[0]['pass_cliente'];
                    
                    if (password_verify($data['current_password'], $storedHashedPassword)) {
                        // Current password matches, hash the new password
                        $updateData['pass_cliente'] = password_hash($data['pass_cliente'], PASSWORD_DEFAULT);
                    } else {
                        return ['success' => false, 'message' => 'Senha atual incorreta'];
                    }
                } else {
                    // If no current password provided, but new password is, reject
                    return ['success' => false, 'message' => 'Senha atual é necessária para alterar a senha'];
                }
            }
            
            error_log("Fields to update: " . json_encode($updateData));
    
            if (empty($updateData)) {
                return ['success' => false, 'message' => 'Nenhum campo válido para atualização'];
            }
    
            // Use direct PDO approach to avoid QueryBuilder binding issues
            $pdo = Database::getConnection();
            
            // Build the query manually for better control
            $setClause = [];
            foreach ($updateData as $field => $value) {
                $setClause[] = "$field = :$field";
            }
            
            $sql = "UPDATE Clientes SET " . implode(', ', $setClause) . " WHERE id_cliente = :id_cliente";
            $stmt = $pdo->prepare($sql);
            
            // Bind all parameters explicitly
            foreach ($updateData as $field => $value) {
                $stmt->bindValue(":$field", $value);
            }
            $stmt->bindValue(':id_cliente', $clienteId, PDO::PARAM_INT);
            
            // Execute and check result
            $result = $stmt->execute();
            
            if (!$result) {
                error_log("SQL Error: " . print_r($stmt->errorInfo(), true));
                return ['success' => false, 'message' => 'Erro de banco de dados durante atualização'];
            }
            
            // Get updated client info
            $clienteAfter = $this->queryBuilder->table('Clientes')
                ->select(['*'])
                ->where('id_cliente', '=', $clienteId)
                ->get();
    
            return [
                'success' => true,
                'message' => 'Informações do cliente atualizadas com sucesso',
                'data' => [
                    'client_id' => $clienteId,
                    'updated_fields' => array_keys($updateData),
                    'updated_data' => $updateData,
                    'client' => $clienteAfter[0] ?? null
                ]
            ];
    
        } catch (PDOException $e) {
            error_log("Erro de banco de dados em updateClientInfo: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro de banco de dados: ' . $e->getMessage()];
        } catch (Exception $e) {
            error_log("Erro geral em updateClientInfo: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao atualizar informações: ' . $e->getMessage()];
        }
    }

    public function showClientInfo($id_cliente): array
    {
        try {
            $result = $this->queryBuilder->table('Clientes')
                ->select([
                    'id_cliente',
                    'nome_cliente',
                    'email_cliente',
                    'contacto_cliente',
                    'morada_cliente',
                    'cidade_cliente',
                    'state_cliente',
                    'cod_postal_cliente',
                    'pais_cliente',
                    'imagem_cliente',
                    'nif_cliente'
                ])
                ->where('id_cliente', '=', $id_cliente)
                ->get();

            if (empty($result)) {
                return [
                    'success' => false,
                    'message' => 'Cliente não encontrado'
                ];
            }

            return [
                'success' => true,
                'data' => $result[0]
            ];

        } catch (Exception $e) {
            error_log("Erro em showClientInfo: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao buscar informações do cliente: ' . $e->getMessage()
            ];
        }
    }

    public function showClientCardInfo($id_cliente): array
    {
        try {
            $result = $this->queryBuilder->table('PagamentoCartao')
                ->select([
                    'PagamentoCartao.id_cartao',
                    'PagamentoCartao.numero_cartao',
                    'PagamentoCartao.validade_cartao',
                    'PagamentoCartao.cvv_cartao',
                    'PagamentoCartao.nome_cartao',
                    'MetodoPagamento.nome_metodo_pagamento'
                ])
                ->join('MetodoPagamento', 'MetodoPagamento.id_cartao', '=', 'PagamentoCartao.id_cartao')
                ->where('MetodoPagamento.id_cliente', '=', $id_cliente)
                ->get();


            if (empty($result)) {
                return [
                    'success' => false,
                    'message' => 'Nenhum cartão encontrado para este cliente'
                ];
            }


            return [
                'success' => true,
                'data' => $result[0]
            ];
        } catch (Exception $e) {
            error_log("Erro em showClientCardInfo: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao buscar informações do cartão: ' . $e->getMessage()
            ];
        }
    }

    public function updateClientCardInfo($id_cliente, $data): bool
    {
        $cartao = $this->queryBuilder->table('MetodoPagamento')
            ->select(['id_cartao'])
            ->where('id_cliente', '=', $id_cliente)
            ->get();

        if (empty($cartao[0]['id_cartao'])) {
            return false;
        }

        $id_cartao = $cartao[0]['id_cartao'];

        return $this->queryBuilder->table('PagamentoCartao')
            ->where('id_cartao', '=', $id_cartao)
            ->update([
                'numero_cartao' => $data['numero_cartao'],
                'validade_cartao' => $data['validade_cartao'],
                'cvv_cartao' => $data['cvv_cartao'],
                'nome_cartao' => $data['nome_cartao']
            ])
            ->execute();
    }

    public function insertClientCard(): array
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {
            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }

        $requiredFields = [
            'id_cliente',
            'numero_cartao',
            'validade_cartao',
            'cvv_cartao',
            'nome_cartao'
        ];

        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            return [
                'error' => 'Invalid data',
                'message' => 'Missing required fields: ' . implode(', ', $missingFields)
            ];
        }

        try {
            $this->queryBuilder->table('PagamentoCartao')
                ->insert([
                    'numero_cartao' => $data['numero_cartao'],
                    'validade_cartao' => $data['validade_cartao'],
                    'cvv_cartao' => $data['cvv_cartao'],
                    'nome_cartao' => $data['nome_cartao']
                ]);
            $id_cartao = $this->queryBuilder->getLastInsertId();

            $this->queryBuilder->table('MetodoPagamento')
                ->insert([
                    'id_cliente' => $data['id_cliente'],
                    'id_cartao' => $id_cartao
                ]);

            return [
                'success' => true,
                'message' => 'Card added and associated to client',
                'id_cartao' => $id_cartao
            ];

        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return [
                'error' => 'Error adding card',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function getClientPaymentMethod(int $id_cliente): ?array
    {

        return $this->queryBuilder->table('MetodoPagamento')
            ->select(['MetodoPagamento.id_metodo_pagamento', 'PagamentoCartao.numero_cartao', 'PagamentoCartao.validade_cartao', 'PagamentoCartao.cvv_cartao', 'PagamentoCartao.nome_cartao'])
            ->join('PagamentoCartao', 'MetodoPagamento.id_cartao', '=', 'PagamentoCartao.id_cartao')
            ->where('MetodoPagamento.id_cliente', '=', $id_cliente)
            ->get();

    }

    public function insertEncomenda(): array
    {

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {

            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }

        $requiredFields = ['id_carrinho', 'preco_total_encomenda', 'fatura', 'status_encomenda'];

        $missingFields = [];

        foreach ($requiredFields as $field) {

            if (empty($data[$field])) {

                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {

            return [
                'error' => 'Invalid data',
                'message' => 'Missing required fields: ' . implode(', ', $missingFields)
            ];
        }

        try {

            $dataCriacao = date("Y-m-d H:i:s");

            $this->queryBuilder->table('Encomendas')
                ->insert([
                    'id_carrinho' => $data['id_carrinho'],
                    'preco_total_encomenda' => $data['preco_total_encomenda'],
                    'fatura' => $data['fatura'],
                    'status_encomenda' => $data['status_encomenda'],
                    'data_criacao_encomenda' => $dataCriacao,
                    'data_atualizacao_encomenda' => $data['data_atualizacao_encomenda'] ?? null,
                    'numero_seguimento' => $data['numero_seguimento'] ?? '',
                    'transportadora' => $data['transportadora'] ?? '',
                    'notas_encomenda' => $data['notas_encomenda'] ?? ''
                ]);

            $lastId = $this->queryBuilder->getLastInsertId();

            return [
                'success' => true,
                'message' => 'Order created',
                'id_encomenda' => $lastId
            ];

        } catch (PDOException $e) {

            error_log("Database error: " . $e->getMessage());

            return [
                'error' => 'Error creating the order',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function getCarrinhoItensbackup($id_carrinho): array
    {

        return $this->queryBuilder->table('CarrinhoItens')
            ->select(['CarrinhoItens.id_produto as ProductId', 'preco as Price', 'quantidade as Quantity', 'tamanho as Size', 'cor as Color', 'id_personalizacao AS PersonalizacaoId'])
            ->join('Produtos', 'CarrinhoItens.id_produto', '=', 'Produtos.id_produto')
            ->where('CarrinhoItens.id_carrinho', '=', $id_carrinho)
            ->get();

    }

    public function insertEncomendaItens(): array
    {

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {

            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }

        $requiredFields = ['id_encomenda', 'id_produto', 'quantidade', 'preco', 'nome_cor', 'tamanho'];

        $missingFields = [];

        foreach ($requiredFields as $field) {

            if (empty($data[$field])) {

                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {

            return [
                'error' => 'Invalid data',
                'message' => 'Missing required fields: ' . implode(', ', $missingFields)
            ];
        }

        try {

            $this->queryBuilder->table('EncomendaItens')
                ->insert([
                    'id_encomenda' => $data['id_encomenda'],
                    'id_produto' => $data['id_produto'],
                    'quantidade' => $data['quantidade'],
                    'preco' => $data['preco'],
                    'nome_cor' => $data['nome_cor'],
                    'tamanho' => $data['tamanho'],
                    'id_personalizacao' => $data['id_personalizacao'] ?? null
                ]);

            return [
                'success' => true,
                'message' => 'Order Item created'
            ];

        } catch (PDOException $e) {

            error_log("Database error: " . $e->getMessage());

            return [
                'error' => 'Error creating the order',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function deleteCartItemBackup(): array
    {

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {
            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }

        $requiredFields = ['id_carrinho'];

        $missingFields = [];

        foreach ($requiredFields as $field) {

            if (!isset($data[$field])) {

                return [
                    'error' => 'Invalid data',
                    'message' => "Missing required field: $field"
                ];
            }
        }

        if (!empty($missingFields)) {

            return [
                'error' => 'Invalid data',
                'message' => 'Missing required fields: ' . implode(', ', $missingFields)
            ];
        }

        try {

            $this->queryBuilder->table('CarrinhoItens')
                ->delete()
                ->where('id_carrinho', '=', $data['id_carrinho'])
                ->execute();

            return [
                'success' => true,
                'message' => 'Cart itens deleted'
            ];

        } catch (PDOException $e) {

            error_log("Database error: " . $e->getMessage());

            return [
                'error' => 'Error deleting the user',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function insertPayment(): array
    {

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {

            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }

        $requiredFields = ['id_encomenda', 'id_metodo_pagamento', 'valor_pago'];

        $missingFields = [];

        foreach ($requiredFields as $field) {

            if (empty($data[$field])) {

                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {

            return [
                'error' => 'Invalid data',
                'message' => 'Missing required fields: ' . implode(', ', $missingFields)
            ];
        }

        try {

            $dataCriacao = date("Y-m-d H:i:s");

            $this->queryBuilder->table('Pagamento')
                ->insert([
                    'id_encomenda' => $data['id_encomenda'],
                    'id_metodo_pagamento' => $data['id_metodo_pagamento'],
                    'valor_pago' => $data['valor_pago'],
                    'data_pagamento' => $dataCriacao
                ]);

            return [
                'success' => true,
                'message' => 'Payment created'
            ];

        } catch (PDOException $e) {

            error_log("Database error: " . $e->getMessage());

            return [
                'error' => 'Error creating the order',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function checkPaymentByCard($card_number): array
    {

        return $this->queryBuilder->table('PagamentoCartao')
            ->select(['id_cartao'])
            ->where('numero_cartao', '=', $card_number)
            ->get();

    }

    public function insertCardPayment(): array
    {

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {

            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }

        $requiredFields = ['numero_cartao', 'validade_cartao', 'cvv_cartao', 'nome_cartao'];

        $missingFields = [];

        foreach ($requiredFields as $field) {

            if (empty($data[$field])) {

                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {

            return [
                'error' => 'Invalid data',
                'message' => 'Missing required fields: ' . implode(', ', $missingFields)
            ];
        }

        try {

            $this->queryBuilder->table('PagamentoCartao')
                ->insert([
                    'numero_cartao' => $data['numero_cartao'],
                    'validade_cartao' => $data['validade_cartao'],
                    'cvv_cartao' => $data['cvv_cartao'],
                    'nome_cartao' => $data['nome_cartao']
                ]);

            $lastId = $this->queryBuilder->getLastInsertId();

            return [
                'success' => true,
                'message' => 'Payment Card created',
                'id_cartao' => $lastId
            ];

        } catch (PDOException $e) {

            error_log("Database error: " . $e->getMessage());

            return [
                'error' => 'Error creating the order',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function insertPaymentMethod(): array
    {

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {

            return ['success' => false, 'message' => 'Invalid JSON data received'];
        }

        $requiredFields = ['id_cartao', 'id_cliente'];

        $missingFields = [];

        foreach ($requiredFields as $field) {

            if (empty($data[$field])) {

                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {

            return [
                'error' => 'Invalid data',
                'message' => 'Missing required fields: ' . implode(', ', $missingFields)
            ];
        }

        try {

            $this->queryBuilder->table('MetodoPagamento')
                ->insert([
                    'id_cartao' => $data['id_cartao'],
                    'id_cliente' => $data['id_cliente']
                ]);

            $lastId = $this->queryBuilder->getLastInsertId();

            return [
                'success' => true,
                'message' => 'Payment Card created',
                'id_metodo_pagamento' => $lastId
            ];

        } catch (PDOException $e) {

            error_log("Database error: " . $e->getMessage());

            return [
                'error' => 'Error creating the order',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    

public function getAdminInfoByID(int $adminID): ?array
{
    $result = $this->queryBuilder->table('Admins')
        ->select(['id_admin', 'nome_admin', 'email_admin', 'contacto_admin', 'funcao_admin', 'data_criacao_admin'])
        ->where('id_admin', '=', $adminID)
        ->get();

    return $result[0] ?? null;
}

public function updateAdminPassword(): array
{
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!is_array($data)) {
        return ['success' => false, 'message' => 'Invalid JSON data received'];
    }

    $adminId = $data['id_admin'] ?? null;
    $currentPassword = $data['current_password'] ?? null;
    $newPassword = $data['new_password'] ?? null;

    if (!$adminId || !$currentPassword || !$newPassword) {
        return ['success' => false, 'message' => 'Missing required fields'];
    }

    try {
        
        $admin = $this->queryBuilder->table('Admins')
            ->select(['pass_admin'])
            ->where('id_admin', '=', $adminId)
            ->get();

        if (empty($admin) || $admin[0]['pass_admin'] !== $currentPassword) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }

     
        $this->queryBuilder->table('Admins')
            ->update(['pass_admin' => $newPassword])
            ->where('id_admin', '=', $adminId)
            ->execute();

        return ['success' => true, 'message' => 'Password updated successfully'];

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}

public function updateAdmin(): array
{
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!is_array($data)) {
        return ['success' => false, 'message' => 'Invalid JSON data received'];
    }

    $key_first_element = array_key_first($data);
    $value_first_element = $data[$key_first_element];
    unset($data[$key_first_element]);

    try {
        $this->queryBuilder->table('Admins')
            ->update($data)
            ->where($key_first_element, '=', $value_first_element)
            ->execute();

        return ['success' => true, 'message' => 'Admin updated successfully'];
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Error updating the admin',
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}

public function getTeamMembers(): array
{
    try {        
        return $this->queryBuilder->table('TeamMembers')
            ->select([
                'TeamMembers.id_team_member as id',
                'TeamMembers.role_member as role',
                'TeamMembers.status_member as status',
                'Clientes.nome_cliente as first_name',
                'Clientes.email_cliente as email',
                'Teams.nome_team as team_name'
            ])
            ->join('Clientes', 'TeamMembers.id_cliente', '=', 'Clientes.id_cliente')
            ->leftJoin('Teams', 'TeamMembers.id_team', '=', 'Teams.id_team')
            ->where('TeamMembers.status_member', '=', 'active')
            ->order('TeamMembers.data_adicao', 'DESC')
            ->get();
    } catch (Exception $e) {
        error_log("Error in getTeamMembers: " . $e->getMessage());
        return [];
    }
}

public function addTeamMember(): array
{
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!is_array($data)) {
        return ['success' => false, 'message' => 'Invalid JSON data received'];
    }

    $requiredFields = ['id_cliente', 'role'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            return [
                'success' => false,
                'message' => "Missing required field: $field"
            ];
        }
    }

    try {
        
        $teamId = $data['id_team'] ?? 1; 
        $role = $data['role'];
        $clienteId = $data['id_cliente'];

      
        $existing = $this->queryBuilder->table('TeamMembers')
            ->select(['id_team_member'])
            ->where('id_team', '=', $teamId)
            ->where('id_cliente', '=', $clienteId)
            ->get();

        if (!empty($existing)) {
            return ['success' => false, 'message' => 'User is already a member of this team'];
        }

        $this->queryBuilder->table('TeamMembers')
            ->insert([
                'id_team' => $teamId,
                'id_cliente' => $clienteId,
                'role_member' => $role,
                'status_member' => 'active',
                'data_adicao' => date('Y-m-d H:i:s')
            ]);

        return ['success' => true, 'message' => 'Team member added successfully'];

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}


public function removeTeamMember(int $memberId): array
{
    try {
        $this->queryBuilder->table('TeamMembers')
            ->delete()
            ->where('id_team_member', '=', $memberId)
            ->execute();

        return ['success' => true, 'message' => 'Team member removed successfully'];
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}


public function getAllUsers(): array
{
    try {
        return $this->queryBuilder->table('Clientes')
            ->select([
                'id_cliente',
                'nome_cliente', 
                'email_cliente',
                'contacto_cliente',
                'morada_cliente',
                'data_criacao_cliente'
            ])
            ->order('nome_cliente', 'ASC')
            ->get();
    } catch (Exception $e) {
        error_log("Error in getAllUsers: " . $e->getMessage());
        return [
            'error' => true,
            'message' => 'Error retrieving users: ' . $e->getMessage()
        ];
    }
}

public function getTeamsForSelect(): array
{
    try {
        return $this->queryBuilder->table('Teams')
            ->select([
                'id_team',
                'nome_team',
                'descricao_team',
                'data_criacao_team'
            ])
            ->where('status_team', '=', 'active')
            ->order('nome_team', 'ASC')
            ->get();
    } catch (Exception $e) {
        error_log("Error in getTeamsForSelect: " . $e->getMessage());
        return [
            'error' => true,
            'message' => 'Error retrieving teams: ' . $e->getMessage()
        ];
    }
}

public function getTeams(): array
{
    try {
        
        return $this->queryBuilder->table('Teams')
            ->select([
                'Teams.id_team',
                'Teams.nome_team',
                'Teams.descricao_team',
                'Teams.data_criacao_team',
                'Teams.status_team',
                'Admins.nome_admin as created_by_name',
                'COUNT(TeamMembers.id_team_member) as member_count'
            ])
            ->leftJoin('Admins', 'Teams.created_by_admin', '=', 'Admins.id_admin')
            ->leftJoin('TeamMembers', 'Teams.id_team', '=', 'TeamMembers.id_team')
            ->where('Teams.status_team', '=', 'active')
            ->groupBy('Teams.id_team')
            ->order('Teams.data_criacao_team', 'DESC')
            ->get();
    } catch (Exception $e) {
        error_log("Error in getTeams: " . $e->getMessage());
        return [];
    }
}

public function createTeam(): array
{
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!is_array($data)) {
        return ['success' => false, 'message' => 'Invalid JSON data received'];
    }

    if (empty($data['nome_team'])) {
        return ['success' => false, 'message' => 'Team name is required'];
    }

    try {
        
        $teamName = trim($data['nome_team']);
        $description = isset($data['descricao_team']) ? trim($data['descricao_team']) : '';
        $createdBy = isset($data['created_by_admin']) ? $data['created_by_admin'] : 1;

        $existing = $this->queryBuilder->table('Teams')
            ->select(['id_team'])
            ->where('nome_team', '=', $teamName)
            ->where('status_team', '=', 'active')
            ->get();

        if (!empty($existing)) {
            return ['success' => false, 'message' => 'Team name already exists'];
        }

        $this->queryBuilder->table('Teams')
            ->insert([
                'nome_team' => $teamName,
                'descricao_team' => $description,
                'created_by_admin' => $createdBy,
                'data_criacao_team' => date('Y-m-d H:i:s'),
                'status_team' => 'active'
            ]);

        $teamId = $this->queryBuilder->getLastInsertId();

        return [
            'success' => true,
            'message' => 'Team created successfully',
            'id_team' => $teamId
        ];

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}

public function deleteTeam(): array
{
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!is_array($data) || empty($data['team_id'])) {
        return ['success' => false, 'message' => 'Team ID is required'];
    }

    try {
        $teamId = intval($data['team_id']);

        
        $this->queryBuilder->table('Teams')
            ->update(['status_team' => 'inactive'])
            ->where('id_team', '=', $teamId)
            ->execute();

        return ['success' => true, 'message' => 'Team deleted successfully'];

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}


}

?>