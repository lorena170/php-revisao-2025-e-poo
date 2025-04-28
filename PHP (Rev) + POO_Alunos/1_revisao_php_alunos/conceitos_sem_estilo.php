<?php
// Inicia a sessão PHP para permitir o armazenamento de dados entre páginas através de cookies
session_start();

// _______________________________________________________________________________________
// Conexão com o banco de dados mysql (Nome do BD: sistema_simples)   

// Define as variaveis de configuração para conexão com o banco de dados MySQL
$host = "localhost"; // Endereço do servidor de banco dedados MySQL
$usuario = "root"; // Nome de usuário do MySQL
$senha = "Senai@118"; // Senha do usuário do MySQL
$banco = "sistema_simples"; // Nome do banco de dados a ser utilizado

//  função que estabilece conexão com o banco de dados e retorna o objeto de conexão
function conectorBD() {
    // indica que serão usadas as variáveis globais definidas anteriormente
    global $host, $usuario, $senha, $banco;
    // criar um novo objeto de conexão MySQLi com os parâmetros fornecidos
    $conexao = new mysqli($host, $usuario, $senha, $banco);

    // Verifica se houve erro na conexão
    if ($conexao->connect_error) {
        // Se houver erro, interrompe a execução e exibe a mensagem de erro 
        die("Falha na conexão: " . $conexao->connect_error);
    }

    // Retorna o objeto de conexão para se ultilizado nas operações de banco de dados 
    return $conexao;
}

// _______________________________________________________________________________
// Vlidação e inicialização de variáveis

// Função que valida se um campo de formulário não está vazio
function validarCampo($campo) {
//    REmove espaços em branco do início e fim do texto 
    $campo = trim($campo);
    // Retorna verdadeiro se o campo não estiver vazio, falso caso contrário
    return !empty($campo);
}

// Função que limpa dados de entrada para prevenir injeção de código malicioso
function sanitizar($dado) {
    // Remove espaços em branco do início e fim do texto
    $dado = trim($dado);
    // Remove barras invertidas adicionadas por escape automático
    $dado = stripslashes($dado);
    // Converte caracteres especiais em entidades HTML para evitar ataques XSS
    $dado = htmlspecialchars($dado);
    // Retorna o dado limpo e seguro
    return $dado;
}

// Inicializa variáveis que serão utilizados no sistema
$mensagem = ""; // Armazena mensagens de erro de feedback para o usuário
$nome = ""; // Armazena o nome do item a ser cadastrado ou editado
$id_para_editar = 0; // Armazena o ID do item a ser editado
$operacao = "cadastrar"; // Define a operação atual (cadastrar ou editar)

// Verifica se a requisição atual é do tipo GET (via URL)
// Obs: Explicação detalhada no arquivo (guia_variaveis_explicacao.md)
if ($_server["REQUEST_METHOD"] == "GET") {

    // __________________________________________________________________________________
    // Saída do sistema (Logout)

    // Verifica se foi solicitado o logout (presença do parâmetro logout na URL)
    if(isset($GET["logout"])) {
        // Destrói a asessão atual, remove todas as variáveis de sessão 
        session_destroy();
        // Redireciona para a propria página, agora sem a sessão ativa
        header("Location: " . $_SERVER["PHP_SELF"]);
        // Encerra a execução do script para garantir o redirecionamento
        exit;
    }

    // ______________________________________________________________________________
    // Edição (Update)

    // verifica se foi solicitada a edição de um item se o usuário está logado
    if (isset($_GET["editar"]) && is_numeric($_GET["editar"]) && isset($_SESSION["logado"])) {
        // Converte o ID recebido para interior e armazena
        $id_para_editar = (int)$_GET["editar"];
        // Muda a para modo de edição
        $operacao = "editar";

        // Conecta ao banco de dados
        $conexao = conectorBD();
        // Prepara a consulta SQL para selecionar o nome do item pelo ID
        // $stmt é uma abreviação comum para "statement" (declaração), usada para armazenar uma consulta SQL preparada
        $stmt = $conexao->prepare("SELECT nome FROM itens WHERE id= ?");
        // Vinclua o parametro ID na consulta SQL (i=integer)
        $stmt->bind_param("i", $id_para_editar);
        // Executa a consulta preparada
        $stmt->execute();
        // Obtém o resultado da consulta
        $resultado = $stmt->get_result();

        // Verifica se encontra algum registro
        // fetch_assoc() é um método do objeto mysqli_resut no PHP. ele é usado para buscar uma linha do resultado da consulta SQL e retorná-la como um array associativo.
        if ($registro = $resultado->fetch_assoc()) {
        //  Atribui o nome encontrado á variável $nome para preenche o formulário
        $nome = $registro["nome"];
        }
        // Fecha a declaração preparada para liberar recursos
        $stmt->close();
        // Fecha a conexão com o banco de dados
        $conexao->close();
    }
    // _________________________________________________________________________________
    // Exclusão (Delete)

    // Verifica se foi solicitado a exclusão de um item se o usuário está logado
    if (isset($_GET["excluir"]) && is_numeric($_GET["excluir"]) && isset($_SESSION["logado"])) {
        // Converte o ID recebido para inteiro e armazena
        $id_para_excluir = (int)$_GET["excluir"];

        // Conecta ao banco de dados
        $conexao = conectorBD();
        // Prepara a consulta SQL para excluir o item pelo ID
        $stmt = $conexao->prepare("DELETE FROM itens WHERE id= ?");
        // Vincula o parâmetro ID na consulta SQL (i=integer)
        $stmt->bind_param("i", $id_para_excluir);

        // Executa a consulta preparada
        if ($stmt->execute()) {
            // Se a exclusão for bem-sucedida, exibe mensagem de sucesso
            $mensagem = "Item excluído com sucesso!";
        } else {
            // Se ocorrer um erro, exibe mensagem de erro
            $mensagem = "Erro ao excluir o item: " . $conexão->error;
        }
        // Fecha a declaração preparada para liberar recursos
        $stmt->close();
        // Fecha a conexão com o banco de dados
        $conexao->close();
    }
}

// _______________________________________________________________________________________
// Entrada no sistema (Login)

// Verifica se a requisição atual é do tipo POST (enviada via formulário)
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se a ação é de login
    if (isset($_POST["acao"]) && $_POST["acao"] == "login") {
    // obtém e santiza os dados de usuário e senha
        $usuario_login = sanitizar($_POST["usuario"] ?? "");
        $senha_login = sanitizar($_POST["senha"] ?? "");

        // Valida se ambos os campos foram preenchidos
        if ($_SERVER["REQUEST_METHOD"] == "POST" ) {
            // Verifique se ação é de login
            if(isset($_POST["acao"]) && $POST["acao"] == "login") {
                // Obtém e sanitiza os dados de usuário e senha
                $usuario_login = sanitizar($_POST["usuario"] ?? "");
                $senha_login = sanitizar($_POST["senha"] ?? "");

                // Valida se ambos os campos foram preenchidos
                if (!validarCampo($usuario_login) || !validarCampo($senha_login)) {
                    // Define mensagem de erro se algum campo estiver vazio
                    $mensagem = "Por favor, preencha todos os campos!";
                } else { 
                    // Verifica se as credenciasis correspondem ao par fixo admin/admin
                    if ($usuario_login == "admin" && $senha_login == "admin") {
                        // Se as credenciais estiverem corretas, armazena os dados na sessão
                        $_SESSION["logado"] = true;
                        // Armazena o nome do usuário na sessão
                        $_SESSION["usuario"] = $usuario_login;
                        // Define mensagem de sucesso 
                        $mensagem = "Login realizado com sucesso!";
                    } else {
                        // Define mensagem de erro para credenciais invválidas
                        $mensagem = "Usuário ou senha incorretos!";
                        
                }
            }
        }
        
        // _______________________________________________________________________________________________
        // Verificar se ação e de cadastro ou atualização (Insert/Update)

        // Verifica se a ação é de cadastro ou atualização de itm e se o usuário está logado
        if (isset ($_POST['acao']) && ($_POST['acao'] == 'cadastrar' || $_POST['acao'] == 'atualizar') && isset($_SESSION["logado"])) {
            // Obtém e sanitiza o nome do item
            $nome = sanitizar($_POST["nome"] ?? "");

            // Valida se o campo nome foi preenchido
            if (!validarCampo($nome)) {
                // Define mensagem de erro se o campo estiver vazio
                $mensagem = "Por favor, preencha o campo nome!";
            } else {
                // Conecta ao banco de dados
                $conexao = conectorBD();
                // _________________________________________________________________________________________________________________________
                // Cadastro (Isert)

                // Verifique se a ação é cdastrar novo ite 
                if($_POST["acao"] == "cadastrar") {
                    // Prepara uma consulta SQL para inserir o novo item
                    $stmt = $conexão->prepare("InSERT INTO   itens (nome) VALUES (?)");
                    // Vinculo o parâmetro nome à consulta (s= string)
                    $stmt->bind_param("s", $nome);
                    
                    // Executa a consulta e verifica se fou bem-sucedida
                    if($stmt->execute()) {
                        // Define mensagem de sucesso 
                        $mensagem = "Item cadastrado com sucesso!";
                        // limpar o campo neome para permitir novo cadastro
                        $nome = "";
                    } else {
                        // Define mensagem de erro com detalhes
                        $mensagem = "Erro ao cadastrar o item: " . $conexao->error;
                    }
}

        //    ____________________________________________________________________________________________________
        // Atualização (Update)

        // Verifique se a ação é atualizada item existente
        else if ($_POST["acao"] == "Atualizar") {
            // Obtém o ID do item a ser atualizado
            $id = (int)$_POST["id"];
           // Prepara uma consulta SQL para atualizar o item  
            $stmt = $conexao->prepare("UPDATE itens SET nome = ? WHERE id = ?");
            //Vincula os parârametros à consulta (S = string, i = integer)
            $stmt->bind_param("si", $nome, $id);

            // Executa a consulta e verifica se foi bem-sucedida
            if ($stmt->execute()) {
                // Define mensagem de sucesso 
                $mensagem = "Item atualizado com sucesso!";
            //    Redireciona para a própria página para resetar o formulário
                header("Location: " . $_SERVER["PHP_SELF"]);
                // Encerra a execução do script para garantir o redirecionamento
                exit;
            } else {
                // Define mensagem de erro com detalhes
                $mensagem = "Erro ao atualizar o item: " . $conexao->error;
            }

        }

        // Fecha a declaração preparada
        $stmt->close();
        // Fecha a conexão com o banco de dados
        $conexao->close();
        }
    }
    
    // ______________________________________________________________________________________
    // Retorna todos os itens cadastrados no banco de dados (Read)

    // Função que consulta e retorna todos os itens cadastrados no banco de dados
    function listarItens(){
        // Inicializa um array vazio para armazenar os itens
        $itens = array();

        // Conecta ao banco de dados
        $conexao = conectorBD();
        // Executa uma consulta SQL para selecionar todos os itens cadastrados
        $resultado = $conexao->query("SELECT id, nome FROM itens ORDER BY id ASC");

        // Verifica se uma consulta retornou algum resultado 
        if ($resultado->num_rows > 0) {
        //    Loop para percorrer cada linha do resultado
            while ($registro = $resultado ->fetch_assoc()) {
                // Adiciona cada item ao array de itens
                $itens[] = $registro;
        }
    }
    
    // Fecha a conexão com banco de dados 
    $conexao->close();
    // Retorna o array de itens
    return $itens;
    }
?>

<!-- __________________________________________________________________________________
HTML + PHP Para exibição do sistema -->

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema PHP Simplificado</title>
</head>
<body>
    <!-- Cabeçalho com informações de login -->
    <h1>Sistema PHP Simplificado</h1>
    <?php if (isset($_SESSION["logado"])): ?>
        <!-- Exibe mensagem de boas-vindas e link para logout caso o usuário esteja logado -->
        <div>
            Bem-vindo, <?php echo $_SESSION["usuario"]; ?>! 
            <!-- 🛠 O que significa ?logout=1?
                ? Inicia a query string na URL
                logout=1 Define um parâmetro chamado logout com o valor 1
                Isso é uma forma de passar um comando via URL para o PHP detectar que o usuário quer sair do sistema. -->
            <a href="?logout=1">Sair</a>
        </div>
    <?php endif; ?>
    
    <!-- Mensagens de sistema -->
    <?php if (!empty($mensagem)): ?>
        <!-- Exibe mensagens de erro ou sucesso -->
        <div>
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>
    
    <!-- Formulário de Login (exibido apenas quando não estiver logado) -->
    <?php if (!isset($_SESSION["logado"])): ?>
        <h2>Login</h2>
        <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <input type="hidden" name="acao" value="login">
            
            <!-- Campo de entrada para o nome de usuário -->
            <div>
                <label for="usuario">Usuário:</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>
            
            <!-- Campo de entrada para a senha -->
            <div>
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            
            <!-- Botão de envio do formulário de login -->
            <div>
                <input type="submit" value="Entrar">
            </div>
        </form>
        
        <p>Dica: Use usuário "admin" e senha "admin" para entrar.</p>
    <?php else: ?>
        <!-- Formulário de Cadastro/Edição de Item -->
        <h2><?php echo ($operacao == "editar" ? "Editar" : "Cadastrar"); ?> Item</h2>
        <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <input type="hidden" name="acao" value="<?php echo ($operacao == "editar" ? "atualizar" : "cadastrar"); ?>">
            
            <!-- Campo oculto para armazenar o ID do item ao editar -->
            <?php if ($operacao == "editar"): ?>
                <input type="hidden" name="id" value="<?php echo $id_para_editar; ?>">
            <?php endif; ?>
            
            <!-- Campo de entrada para o nome do item -->
            <div>
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?php echo $nome; ?>" required>
            </div>
            
            <!-- Botão de envio do formulário -->
            <div>
                <input type="submit" value="<?php echo ($operacao == "editar" ? "Atualizar" : "Cadastrar"); ?>">
                <?php if ($operacao == "editar"): ?>
                    <!-- Link para cancelar a edição e voltar ao modo de cadastro -->
                    <a href="<?php echo $_SERVER["PHP_SELF"]; ?>">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
        
        <!-- Listagem de Itens -->
        <h2>Itens Cadastrados</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Obtém a lista de itens cadastrados
                $itens = listarItens();
                if (count($itens) > 0):
                    foreach ($itens as $item):
                ?>
                <tr>
                    <!-- Exibe o ID do item -->
                    <td><?php echo $item["id"]; ?></td>
                    <!-- Exibe o nome do item -->
                    <td><?php echo $item["nome"]; ?></td>
                    <td>
                        <!-- Links para edição e exclusão do item -->
                        <a href="?editar=<?php echo $item["id"]; ?>">Editar</a>
                        <a href="?excluir=<?php echo $item["id"]; ?>" onclick="return confirm('Tem certeza que deseja excluir este item?')">Excluir</a>
                    </td>
                </tr>
                <?php
                    endforeach;
                else:
                ?>
                <!-- Exibe mensagem caso não existam itens cadastrados -->
                <tr>
                    <td colspan="3">Nenhum item cadastrado</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
