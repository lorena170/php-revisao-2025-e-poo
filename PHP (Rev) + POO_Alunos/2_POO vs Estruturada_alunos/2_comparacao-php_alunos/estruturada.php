<?php
// ==========================================
// PARTE 1: PROGRAMAÇÃO ESTRUTURADA
// ==========================================


// 1ª Digitação (Aqui)
// dados do primeiro cachorro
$nome_cachorro_1 = "Nelson";
$comida_cachorro_1 = 3;
$sono_cachorro_1 = false;

// dados do segundo cachorro
$nome_cachorro_2 = "Jeremias";
$comida_cachorro_2 = 1;     
$sono_cachorro_2 = true;

// Função para manipular os dados
function comer ($quantidade_comida) {
    return $quantidade_comida - 1;
}

function dormir ($sono) {
    return true;
}

//  Usando as funções 
$comida_cachorro_1 = comer($comida_cachorro_1);
$sono_cachorro_2 = dormir($sono_cachorro_2);

echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Resultados dos Cachorros</title>
</head>
<body>
    <h1>Resultados dos Cachorros</h1>
    <p><strong>$nome_cachorro_1</strong> agora tem <strong>$comida_cachorro_1</strong> unidades </p>
   <p><strong>$nome_cachorro_2</strong> está com sono? <strong>" . ($sono_cachorro_2 ? 'Sim' : 'Não') . "</strong> </p>
</body>
</html>";

?>

