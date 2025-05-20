<?php
/**
 * Arquivo principal do sistema - Atualização 3
 */
session_start();

// Carrega os arquivos necessários
require_once 'config/database.php';
require_once 'services/Auth.php';
require_once 'views/templates/header.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/ItemController.php';

// Instancia os controladores
$authController = new AuthController();
$itemController = new ItemController();

// Define a ação padrão
$pagina = $_GET['pagina'] ?? '';

// Verifica autenticação
if (!Auth::estaLogado() && !in_array($pagina, ['login', 'autenticar'])) {
    $pagina = 'login';
}

// Roteamento
switch ($pagina) {
    // Ações de autenticação
    case 'login':
        $authController->login();
        break;
    case 'autenticar':
        $authController->autenticar();
        break;
    case 'logout':
        $authController->logout();
        break;
    
    // Ações de gerenciamento de itens
    case 'lista':
        $itemController->listar();
        break;
    case 'visualizar':
        $itemController->visualizar($_GET['id'] ?? 0);
        break;
    case 'adicionar':
        $itemController->adicionar();
        break;
    case 'salvar':
        $itemController->salvar();
        break;
    case 'editar':
        $itemController->editar($_GET['id'] ?? 0);
        break;
    case 'atualizar':
        $itemController->atualizar($_GET['id'] ?? 0);
        break;
    case 'excluir':
        $itemController->confirmarExclusao($_GET['id'] ?? 0);
        break;
    case 'excluir_confirmar':
        $itemController->excluir($_GET['id'] ?? 0);
        break;
    
    // Ação padrão
    default:
        header('Location: index.php?pagina=' . (Auth::estaLogado() ? 'lista' : 'login'));
        exit;
}