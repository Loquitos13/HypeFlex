<?php

require 'Database.php';
require 'QueryBuilder.php';
require 'ApiController.php';
require 'Router.php';

Database::connect();

header('Content-Type: application/json');

$controller = new ApiController();

$router = new Router();

$router->add('GET', '/serverTimeStamp', fn() => $controller->getServerTimeStamp());

$router->add('GET', '/users', fn() => $controller->getUsers());

$router->add('GET', '/categories', fn() => $controller->getCategories());

$router->add('GET', '/userById/$id', fn($id) => $controller->getUserByID($id));

$router->add('GET', '/userInfoByID/$id', fn($id) => $controller->getUserInfoByID($id));

$router->add('GET', '/userByEmail/$userEmail', fn($userEmail) => $controller->getUserByEmail($userEmail));

$router->add('GET', '/adminByEmail/$adminEmail', fn($adminEmail) => $controller->getAdminByEmail($adminEmail));

$router->add('GET', '/adminByID/$id', fn($id) => $controller->getAdminByID($id));

$router->add('GET', '/encomendas', fn() => $controller->getEncomendas());

$router->add('GET', '/encomendasChart', fn() => $controller->getEncomendasChart());

$router->add('GET', '/revenuePerMonth', fn() => $controller->getRevenuePerMonth());

$router->add('GET', '/topProducts', fn() => $controller->getTopProducts());

$router->add('GET', '/topProductsIndex', fn() => $controller->getTopProductsByCount());

$router->add('GET', '/recentOrders', fn() => $controller->getRecentOrders());

$router->add('GET', '/numberOfOrdersByDay/$day', fn($day) => $controller->numberOfOrdersFilteredByDay($day));

$router->add('GET', '/numberOfOrdersByMonth/$month', fn($month) => $controller->numberOfOrdersFilteredByMonth($month));

$router->add('GET', '/numberOfOrdersByYear/$year', fn($year) => $controller->numberOfOrdersFilteredByYear($year));

$router->add('GET', '/revenueByDay/$day', fn($day) => $controller->revenueFilteredByDay($day));

$router->add('GET', '/revenueByMonth/$month', fn($month) => $controller->revenueFilteredByMonth($month));

$router->add('GET', '/revenueByYear/$year', fn($year) => $controller->revenueFilteredByYear($year));

$router->add('GET', '/numberOfClientsByDay/$day', fn($day) => $controller->getNumberOfClientsByDay($day));

$router->add('GET', '/numberOfClientsByMonth/$month', fn($month) => $controller->getNumberOfClientsByMonth($month));

$router->add('GET', '/numberOfClientsByYear/$year', fn($year) => $controller->getNumberOfClientsByYear($year));

$router->add('GET', '/recentOrdersByClient/$id', fn($id) => $controller->getRecentOrdersByClient($id));

$router->add('GET', '/getAllOrdersByClient/$id', fn($id) => $controller->getNumberOfOrdersByClient($id));

$router->add('GET', '/getUserProfile/$id', fn($id) => $controller->getClientProfile($id));

$router->add('GET', '/carrinhos', fn() => $controller->getCarrinhos());

$router->add('GET', '/encomendasByUserID/$id', fn($id) => $controller->getEncomendasByUserID($id));

$router->add('GET', '/productsSoldByID/$productID', fn($productID) => $controller->getProductsSoldByID($productID));

$router->add('POST', '/insertUser', fn() => $controller->insertUser());

$router->add('POST', '/insertShoppingCart', fn() => $controller->insertShoppingCart());

$router->add('GET', '/feedbackAVGProduct/$id_product', fn($id_product) => $controller->getFeedbackAVGProduct($id_product));

$router->add('PUT', '/updateUser', fn() => $controller->updateUser());

$router->add('DELETE', '/deleteUserByID/$id', fn($id) => $controller->deleteUserByID($id));

$router->add('GET', '/getOrders', fn() => $controller->getOrders());

$router->add('GET', '/getOrderItems/$id', fn($id) => $controller->getOrderItems($id));

$router->add('GET', '/getUserOrders/$id', fn($id) => $controller->getUserOrders($id));

$router->add('POST', '/insertProduct', fn() => $controller->insertProduct());

$router->add('POST', '/insertColor', fn() => $controller->insertColor());

$router->add('POST', '/insertDimension', fn() => $controller->insertDimension());

$router->add('POST', '/insertProductVariant', fn() => $controller->insertProductVariant());

$router->add('POST', '/updateDimensions', fn() => $controller->updateDimensions());

$router->add('POST', '/updateProductVariant', fn() => $controller->updateProductVariant());

$router->add('GET', '/colors', fn() => $controller->getColors());

$router->add('GET', '/dimensions', fn() => $controller->getDimensions());

$router->add('GET', '/getColorsByProduct/$id', fn($id) => $controller->getColorsByProduct($id));

$router->add('GET', '/getDimensionsByProduct/$id', fn($id) => $controller->getDimensionsByProduct($id));

$router->add('GET', '/productVariants', fn() => $controller->getProductVariants());

$router->add('GET', '/feedbacks', fn() => $controller->getFeedbacks());

$router->add('GET', '/classificationFeedback', fn() => $controller->getClassificationFeedback());

$router->add('POST', '/insertFeedback', fn() => $controller->insertFeedback());

$router->add('GET', '/products', fn() => $controller->getProducts());

$router->add('GET', '/productsAdmin', fn() => $controller->getProductsAdmin());

$router->add('GET', '/getCategoriesByID', fn() => $controller->getCategoriesByID());

$router->add('GET', '/getSizesByCategories/$categorias', fn($categorias) => $controller->getSizesByCategories($categorias));

$router->add('GET', '/getColorsByCategories/$categorias', fn($categorias) => $controller->getColorsByCategories($categorias));

$router->add('GET', '/productByID/$id', fn($id) => $controller->getProductByID($id));

$router->add('GET', '/getProductsBYCategory/$productCategory/$currentProductID', fn($productCategory, $currentProductID) => $controller->getProductsBYCategory($productCategory, $currentProductID));

$router->add('PUT', '/updateProduct', fn() => $controller->updateProduct());

$router->add('POST', '/deleteProductByID/$id', fn($id) => $controller->deleteProductByID($id));

$router->add('GET', '/filterProducts/$categoria/$precoMinimo/$precoMaximo/$cor/$tamanho', fn($categoria, $precoMinimo, $precoMaximo, $cor, $tamanho) => $controller->getFiltersProducts($categoria, $precoMinimo, $precoMaximo, $cor, $tamanho));

$router->add('GET', '/getCarrinhoItens/$userID', fn($userID) => $controller->getCarrinhoItens($userID));

$router->add('GET', '/orderById/$id', fn($id) => $controller->getOrderById($id));

$router->add('GET', '/orderItems/$id', fn($id) => $controller->getOrderItems($id));

$router->add('GET', '/orderPaymentInfo/$id', fn($id) => $controller->getOrderPaymentInfo($id));

$router->add('POST', '/updateOrderStatus/$id/$status', fn($id, $status) => $controller->updateOrderStatus($id, $status));

$router->add('GET', '/getDadosClientePorCarrinho/$userID', fn($userID) => $controller->getDadosClientePorCarrinho($userID));

$router->add('GET', '/getOrderID/$id', fn($id) => $controller->getOrderID($id));

$router->add('GET', '/searchProductsByTitle/$searchTerm', fn($searchTerm) => $controller->searchProductsByTitle($searchTerm));

$router->add('GET', '/carrinhoByUserId/$id_cliente', fn($id_cliente) => $controller->getCarrinhoByUserId($id_cliente));

$router->add('GET', '/carrinhoItensByCarrinhoId/$id_carrinho', fn($id_carrinho) => $controller->getCarrinhoItensByCarrinhoId($id_carrinho));

$router->add('GET', '/checkCarrinhoItem/$id_carrinho/$id_produto/$tamanho/$cor/$personalizado', fn($id_carrinho, $id_produto, $tamanho, $cor, $personalizado) => $controller->checkCarrinhoItem($id_carrinho, $id_produto, $tamanho, $cor, $personalizado));

$router->add('POST', '/insertCarrinhoItem', fn() => $controller->insertCarrinhoItem());

$router->add('POST', '/updateItemFromCarrinhoItens', fn() => $controller->updateItemFromCarrinhoItens());

$router->add('POST', '/deleteCartItem', fn() => $controller->deleteCartItem());

$router->add('POST', '/insertPersonalizacao', fn() => $controller->insertPersonalizacao());

$router->add('POST', '/updateCustomerInfo', fn() => $controller->updateCustomerInfo());

$router->add('GET', '/showClientInfo/$id_cliente', fn($id_cliente) => $controller->showClientInfo($id_cliente));

$router->add('GET', '/showClientCardInfo/$id', fn($id) => $controller->showClientCardInfo($id));

$router->add('POST', '/insertClientCard', fn() => $controller->insertClientCard());

$router->add('GET', '/userOrders/$userId', fn($userId) => $controller->getUserOrders($userId));

$router->add('POST', '/updateClientInfo', fn() => $controller->updateClientInfo());

$router->add('GET', '/clientPaymentMethod/$id_cliente', fn($id_cliente) => $controller->getClientPaymentMethod($id_cliente));

$router->add('POST', '/insertEncomenda', fn() => $controller->insertEncomenda());

$router->add('GET', '/carrinhoItensbackup/$id_carrinho', fn($id_carrinho) => $controller->getCarrinhoItensbackup($id_carrinho));

$router->add('POST', '/insertEncomendaItens', fn() => $controller->insertEncomendaItens());

$router->add('POST', '/deleteCartItemBackup', fn() => $controller->deleteCartItemBackup());

$router->add('POST', '/insertPayment', fn() => $controller->insertPayment());

$router->add('GET', '/checkPaymentByCard/$card_number', fn($card_number) => $controller->checkPaymentByCard($card_number));

$router->add('POST', '/insertCardPayment', fn() => $controller->insertCardPayment());

$router->add('POST', '/insertPaymentMethod', fn() => $controller->insertPaymentMethod());

$router->add('GET', '/adminInfoByID/$id', fn($id) => $controller->getAdminInfoByID($id));

$router->add('POST', '/updateAdmin', fn() => $controller->updateAdmin());

$router->add('POST', '/updateAdminPassword', fn() => $controller->updateAdminPassword());

$router->add('GET', '/teamMembers', fn() => $controller->getTeamMembers());

$router->add('POST', '/addTeamMember', fn() => $controller->addTeamMember());

$router->add('POST', '/removeTeamMember/$id', fn($id) => $controller->removeTeamMember($id));

$router->add('POST', '/createTeam', fn() => $controller->createTeam());

$router->add('POST', '/deleteTeam', fn() => $controller->deleteTeam());

$router->add('GET', '/getTeams', fn() => $controller->getTeams());

$router->add('GET', '/getTeamsForSelect', fn() => $controller->getTeamsForSelect());

$router->add('GET', '/getAllUsers', fn() => $controller->getAllUsers());


$router->dispatch();
