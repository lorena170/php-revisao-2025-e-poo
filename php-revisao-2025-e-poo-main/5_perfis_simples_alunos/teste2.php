<?php
require_once 'config/database.php';
require_once 'models/Item.php';

// Testar buscar todos os itens
$itens = Item::buscarTodos();
echo "<h2>Todos os itens:</h2>";
print_r($itens);

// Testar buscar um item específico
echo "<h2>Item específico:</h2>";
$item = Item::buscarPorId(1);
print_r($item);