<?php
// Exercício PHP em arquivo único abordando 5 conceitos:
// 1. Variáveis
// 2. Arrays
// 3. Condicionais
// 4. Loops
// 5. Funções

// ---- 1. VARIÁVEIS ----
echo "<h2>1. Variáveis</h2>";


$nome = "João";
$idade = 25;
$profissao = "Desenvolvedor";
$altura = 5000.50;
$ativo = true;

echo "Nome: $nome <br>";
echo "Idade: $idade anos <br>";
echo "Profissão: $profissao <br>";
echo "salário: R$ " . number_format($salario, 2, ',', '.') . "<br>";
echo "Status: " . ($ativo ? "Ativo" : "Inativo") . "<br>";
echo "<br>";
echo "=============================================================================";

// ---- 2. ARRAYS ----
echo "<h2>2. Arrays</h2>";

// Array indexado
$frutas = ["Maçã", "Banana", "Laranja", "Uva", "Pera"];

// Array associativo
$funcionario = [
    "nome" => "Carlos",
    "cargo" => "Analista",
    "departamento" => "TI",
    "salario" => 4500
];

// Array multidimensional
$alunos = [
    ["nome" => "Ana", "nota" => 8.5],
    ["nome" => "Pedro", "nota" => 9.0],
    ["nome" => "Maria", "nota" => 7.5]
];
echo "<strong> Lista de Frutas: </strong><br>";
foreach ($frutas as $fruta) {
    echo "- $fruta <br>";
}

echo "<br><strong> dados do Funcionário: </strong><br>";
// Função ucfirst() no PHP serve para converter a primeira letra de uma string para maiúscula e deixar o restante em minúscula.

// No entanto, ucfirst() só altera a primeira letra da string. Se você precisar  de uma formatação mais complexa (por exemplo, deixar todas as palavras com a primeira letra maiúscula, como em título), você pode usar a função ucwords().

foreach ($funcionario as $chave => $valor) {
     echo ucfirst($chave) . ": " . $valor . "<br>";
}

echo "<br><strong>  Alunos e Notas: </strong><br>";
foreach ($alunos as $aluno) {
    echo "Aluno: {$aluno['nome']} - Nota: {$aluno['nota']}<br>";
}

echo "<br>";
echo "=============================================================================";

// ---- 3. CONDICIONAIS ----
echo "<h2>3. Condicionais</h2>";


$temperatura = 28;

echo "Temperatura atual: . $temperatura . °C<br>";

// Normal
if ($temperatura >= 15) {
    echo "Está frio! <br>";
} elseif ($temperatura < 15  && $temperatura < 25) { // Frio
    echo " A Temperatura está agradável. <br>";
} else { 
    echo "Está calor!<br>";
}

// Com ternário
echo ($temperatura < 15) ? "Está frio! <br>" : (($temperatura  >= 15 && $temperatura < 
25) ? "A temperatura está agradável. <br>" : "Está calor!<br>");

//  Switch case
$dia_semana = date("w");
echo "Hoje é: " ;
switch ($dia_semana) {
    case 0:
        echo "Domingo<br>";
        break;
    case 1:
        echo "Segunda-feira<br>";
        break;
    case 2:
        echo "Terça-feira<br>";
        break;
    case 3:
        echo "Quarta-feira<br>";
        break;
    case 4:
        echo "Quinta-feira<br>";
        break;
    case 5:
        echo "Sexta-feira<br>";
        break;
    case 6:
        echo "Sábado<br>";
        break;
}
echo "<br>";

// Operação ternária
$idade = 18;
echo "com $idade anos: " . ($idade >= 18 ? "Maior de idade" : "Menor de idade") . "<br>";

echo "<br>";
echo "=============================================================================";

// ---- 4. LOOPS ----
echo "<h2>4. Loops</h2>";


echo "<h2>4. Loops</h2>";

// For
echo "<strong>Contagem com  for:</strong><br>";
for ($i = 1; $i <= 5; $i++) {
    echo "Número: $i<br>";
}

// While
echo "<br><strong>Contagem regressiva com while:</strong><br>";
$contador = 5;
while ($contador > 0) {
    echo "Contagem: $contador<br>";
    $contador--;
}

// Foreach com array
echo "<br><strong>Percorrendo array com foreach:</strong><br>";
$linguagens = ["PHP", "JavaScript", "Python", "Java", "c#"];
foreach ($linguagens as $indice => $linguagem) {
    echo "Linguagem: $indice: $linguagem<br>";
}

echo "<br>";
echo "=============================================================================";

// ---- 5. FUNÇÕES ----
echo "<h2>5. Funções</h2>";

// Função simples
function saudacao($nome) {
    return "Olá, $nome!<br>";
}
// Função com parâmero default
function calcularMedia($nota1, $nota2, $nota3 = 0) {
    return ($nota1 + $nota2 + $nota3) / 3;
}

// Função anônima 
// A função anônima é uma função sem nome, que pode ser atribuída a uma variável.
$dobro = function($numero) {
    return $numero * 2;
};

echo saudacao("Maria") . "<br>";
echo "Médias das notas:" .  number_format(calcularMedia(7.5, 8.5, 9.0), 1, ',', '.') .
 "<br>";
 echo "o dobro de15 é " . $dobro(15) . "<br>";


// O fatorial de um número n é o produto de todos os inteiros de 1 até n. O fatorial de 5, por exemplo, é 5 * 4 * 3 * 2 * 1 = 120.

// Função Recursiva:

// Recursão é um processo onde a função chama a si mesma. No caso da função fatorial(), ela chama a si mesma com o valor de $n - 1 até que $n seja menor ou igual a 1.
// A base da recursão (condição de parada) é quando $n <= 1, onde a função retorna 1. Isso é necessário para evitar que a função continue chamando a si mesma indefinidamente.

function fatorial($n) {
    if ($n <= 1) return 1;
        return $n * fatorial($n - 1);
}
echo "Fatorial de 5: " . fatorial(5) . "<br>";
echo "<br>";
echo "=============================================================================";

// EXERCÍCIO PRÁTICO: Combinando todos os conceitos
echo "<h2>Exercício Prático</h2>";

// Função para calcular média de notas


function calcularMediaNotas($notas) {
    $soma = array_sum($notas);
    $quantidade = count($notas);
    return $soma / $quantidade;
}



// Criando um array com alunos e notas
$turma = [
    ["nome" => "Ana Silva", "notas" => [8.5, 9.0, 7.5]],
    ["nome" => "Pedro Santos", "notas" => [6.0, 7.5, 8.0]],
    ["nome" => "Maria Oliveira", "notas" => [9.5, 9.0, 10.0]],
    ["nome" => "João Costa", "notas" => [5.0, 6.5, 7.0]],
    ["nome" => "Lucia Pereira", "notas" => [7.5, 8.0, 6.5]]
];

// Exibindo os resultados

foreach ($turma as $aluno) {
    $media = calcularMediaNotas($aluno['notas']);
    $status = $media >= 7 ? "Aprovado" : "Reprovado";
    echo "Aluno: {$aluno['nome']} - Média: " . number_format($media, 1, ',', '.') . " - Status: $status<br>";
}


?>