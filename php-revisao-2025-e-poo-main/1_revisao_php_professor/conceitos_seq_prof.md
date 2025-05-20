# Guia Passo a Passo: Sistema CRUD em PHP Simplificado

Este guia apresenta uma sequência completa para desenvolver um sistema CRUD (Create, Read, Update, Delete) simples em PHP com autenticação de usuário. Cada etapa constrói progressivamente o sistema, permitindo testes incrementais.

## Etapa 1: Estrutura Básica e Início da Sessão

**Objetivo**: Configurar o início do arquivo PHP e a sessão.

```php
<?php
// Inicia a sessão PHP para permitir o armazenamento de dados entre páginas através de cookies
session_start();

// Teste simples para verificar se a sessão está ativa
$_SESSION["teste"] = "Sessão funcionando!";
echo "Sessão iniciada. Valor de teste: " . $_SESSION["teste"];
?>
```

**Teste**: Ao acessar a página, você deve ver "Sessão iniciada. Valor de teste: Sessão funcionando!".

## Etapa 2: Conexão com o Banco de Dados

**Objetivo**: Configurar e testar a conexão com o banco de dados MySQL.

```php
// Conexão com o Banco de Dados MySQL (Nome do BD: sistema_simples)
$host = "localhost";       // Endereço do servidor de banco de dados (local)
$usuario = "root";         // Nome de usuário do MySQL
$senha = "Senai@118";      // Senha do usuário MySQL
$banco = "sistema_simples"; // Nome do banco de dados a ser utilizado

// Função que estabelece conexão com o banco de dados e retorna o objeto de conexão
function conectarBD() {
    global $host, $usuario, $senha, $banco;
    $conexao = new mysqli($host, $usuario, $senha, $banco);
    
    if ($conexao->connect_error) {
        die("Falha na conexão: " . $conexao->connect_error);
    }
    
    // Teste de conexão bem-sucedida
    echo "Conexão com o banco de dados realizada com sucesso!<br>";
    var_dump($conexao); // Mostra detalhes do objeto de conexão
    
    return $conexao;
}

// Teste da função
$conexao = conectarBD();
```

**Teste**: Você verá "Conexão com o banco de dados realizada com sucesso!" e um var_dump do objeto de conexão se tudo estiver correto.

## Etapa 3: Funções de Validação e Sanitização

**Objetivo**: Implementar funções para validar e sanitizar os dados de entrada.

```php
// Função que valida se um campo de formulário não está vazio
function validarCampo($campo) {
    $campo = trim($campo);
    return !empty($campo);
}

// Função que limpa dados de entrada para prevenir injeção de código malicioso
function sanitizar($dado) {
    $dado = trim($dado);
    $dado = stripslashes($dado);
    $dado = htmlspecialchars($dado);
    return $dado;
}

// Teste das funções
$teste_vazio = "";
$teste_preenchido = "   Teste  ";
$teste_malicioso = "<script>alert('XSS');</script>";

echo "Campo vazio é válido? " . (validarCampo($teste_vazio) ? "Sim" : "Não") . "<br>";
echo "Campo preenchido é válido? " . (validarCampo($teste_preenchido) ? "Sim" : "Não") . "<br>";
echo "Sanitização: " . sanitizar($teste_malicioso) . "<br>";
```

**Teste**: Você deve ver "Campo vazio é válido? Não", "Campo preenchido é válido? Sim" e a versão sanitizada do código malicioso.

## Etapa 4: Inicialização de Variáveis

**Objetivo**: Inicializar as variáveis essenciais para o sistema.

```php
// Inicializa variáveis que serão utilizadas no sistema
$mensagem = "";             // Armazena mensagens de feedback para o usuário
$nome = "";                 // Armazena o nome do item a ser cadastrado/editado
$id_para_editar = 0;        // Armazena o ID do item a ser editado
$operacao = "cadastrar";    // Define a operação padrão como cadastro de novo item

// Teste das variáveis inicializadas
echo "Variáveis inicializadas:<br>";
echo "Mensagem: '$mensagem'<br>";
echo "Nome: '$nome'<br>";
echo "ID para editar: $id_para_editar<br>";
echo "Operação: $operacao<br>";
```

**Teste**: Você verá as variáveis inicializadas com seus valores padrão.

## Etapa 5: Implementação do Logout

**Objetivo**: Implementar a funcionalidade de logout.

```php
// Verifica se a requisição atual é do tipo GET (via URL)
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Verifica se foi solicitado logout (presença do parâmetro logout na URL)
    if (isset($_GET["logout"])) {
        // Para teste, mostra estado da sessão antes do logout
        echo "Sessão antes do logout: ";
        var_dump($_SESSION);
        
        // Destrói a sessão atual, removendo todas as variáveis de sessão
        session_destroy();
        
        // Para teste, mostra que a sessão foi destruída
        echo "<br>Sessão destruída!<br>";
        
        // Redireciona para a própria página, agora sem sessão ativa
        // Descomente para uso real (deixe comentado durante testes)
        // header("Location: " . $_SERVER["PHP_SELF"]);
        // exit;
    }
}

// Para testar o logout, cria uma sessão de teste se não existir
if (!isset($_SESSION["logado"])) {
    $_SESSION["logado"] = true;
    $_SESSION["usuario"] = "teste";
    echo "Sessão de teste criada. <a href='?logout=1'>Testar Logout</a>";
}
```

**Teste**: Acesse a página e você verá um link "Testar Logout". Clique nele e você verá o estado da sessão antes do logout e a mensagem "Sessão destruída!".

## Etapa 6: Implementação da Edição de Item

**Objetivo**: Implementar a funcionalidade de edição de item.

```php
// Dentro do bloco if ($_SERVER["REQUEST_METHOD"] == "GET")
// Após o código de logout:

// Edição (Update)
if (isset($_GET["editar"]) && is_numeric($_GET["editar"]) && isset($_SESSION["logado"])) {
    // Para teste, mostra o ID recebido
    echo "ID para editar recebido: " . $_GET["editar"] . "<br>";
    
    // Converte o ID recebido para inteiro e armazena
    $id_para_editar = (int)$_GET["editar"];
    // Muda a operação para modo de edição
    $operacao = "editar";
    
    // Conecta ao banco de dados
    $conexao = conectarBD();
    // Prepara uma consulta SQL para selecionar o nome do item pelo ID
    $stmt = $conexao->prepare("SELECT nome FROM itens WHERE id = ?");
    // Vincula o parâmetro ID à consulta (i = integer)
    $stmt->bind_param("i", $id_para_editar);
    // Executa a consulta preparada
    $stmt->execute();
    // Obtém o resultado da consulta
    $resultado = $stmt->get_result();
    
    // Verifica se encontrou algum registro
    if ($registro = $resultado->fetch_assoc()) {
        // Atribui o nome encontrado à variável $nome para preencher o formulário
        $nome = $registro["nome"];
        echo "Nome encontrado: " . $nome . "<br>";
    } else {
        echo "Nenhum item encontrado com o ID: " . $id_para_editar . "<br>";
    }
    
    // Fecha a declaração preparada para liberar recursos
    $stmt->close();
    // Fecha a conexão com o banco de dados
    $conexao->close();
}
```

**Teste**: Para testar, acesse a página com o parâmetro ?editar=1 (ou outro ID válido no seu banco). Você verá os detalhes da operação de edição.

http://localhost/1_revisao_php_professor/tt.php/?editar=1

## Etapa 7: Implementação da Exclusão de Item

**Objetivo**: Implementar a funcionalidade de exclusão de item.

```php
// Dentro do bloco if ($_SERVER["REQUEST_METHOD"] == "GET")
// Após o código de edição:

// Exclusão (Delete)
if (isset($_GET["excluir"]) && is_numeric($_GET["excluir"]) && isset($_SESSION["logado"])) {
    // Para teste, mostra o ID recebido
    echo "ID para exclusão recebido: " . $_GET["excluir"] . "<br>";
    
    // Converte o ID recebido para inteiro e armazena
    $id_para_excluir = (int)$_GET["excluir"];
    
    // Conecta ao banco de dados
    $conexao = conectarBD();
    
    // Para teste, mostra o item antes da exclusão
    $stmt_select = $conexao->prepare("SELECT nome FROM itens WHERE id = ?");
    $stmt_select->bind_param("i", $id_para_excluir);
    $stmt_select->execute();
    $resultado = $stmt_select->get_result();
    
    if ($registro = $resultado->fetch_assoc()) {
        echo "Item a ser excluído: " . $registro["nome"] . " (ID: $id_para_excluir)<br>";
    } else {
        echo "Nenhum item encontrado com o ID: $id_para_excluir<br>";
    }
    $stmt_select->close();
    
    // Prepara uma consulta SQL para excluir o item pelo ID
    $stmt = $conexao->prepare("DELETE FROM itens WHERE id = ?");
    // Vincula o parâmetro ID à consulta (i = integer)
    $stmt->bind_param("i", $id_para_excluir);
    
    // Executa a consulta e verifica se foi bem-sucedida
    if ($stmt->execute()) {
        // Define mensagem de sucesso
        $mensagem = "Item excluído com sucesso!";
        echo $mensagem . "<br>";
    } else {
        // Define mensagem de erro com detalhes
        $mensagem = "Erro ao excluir o item: " . $conexao->error;
        echo $mensagem . "<br>";
    }
    
    // Fecha a declaração preparada
    $stmt->close();
    // Fecha a conexão com o banco de dados
    $conexao->close();
}
```

**Teste**: Para testar, acesse a página com o parâmetro ?excluir=1 (ou outro ID válido no seu banco). Você verá os detalhes da operação de exclusão e o resultado.

http://localhost/1_revisao_php_professor/tt.php/?excluir=1

## Etapa 8: Implementação do Login

**Objetivo**: Implementar a funcionalidade de login.

```php
// Entrada no sistema (Login)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se a ação é de login
    if (isset($_POST["acao"]) && $_POST["acao"] == "login") {
        // Obtém e sanitiza os dados de usuário e senha
        $usuario_login = isset($_POST["usuario"]) ? sanitizar($_POST["usuario"]) : "";
        $senha_login = isset($_POST["senha"]) ? sanitizar($_POST["senha"]) : "";
        
        // Para teste, mostra os dados recebidos (descomente para depuração)
        echo "Dados de login recebidos:<br>";
        echo "Usuário: $usuario_login<br>";
        echo "Senha: $senha_login<br>";
        
        // Valida se ambos os campos foram preenchidos
        if (!validarCampo($usuario_login) || !validarCampo($senha_login)) {
            // Define mensagem de erro se algum campo estiver vazio
            $mensagem = "Por favor, preencha todos os campos!";
            echo "Erro: $mensagem<br>";
        } else {
            // Verifica se as credenciais correspondem ao par fixo admin/admin
            if ($usuario_login == "admin" && $senha_login == "admin") {
                // Se as credenciais estiverem corretas, marca o usuário como logado na sessão
                $_SESSION["logado"] = true;
                // Armazena o nome do usuário na sessão
                $_SESSION["usuario"] = $usuario_login;
                // Define mensagem de sucesso
                $mensagem = "Login realizado com sucesso!";
                echo "Sucesso: $mensagem<br>";
                echo "Estado da sessão após login:<br>";
                var_dump($_SESSION);
            } else {
                // Define mensagem de erro para credenciais inválidas
                $mensagem = "Usuário ou senha incorretos!";
                echo "Erro: $mensagem<br>";
            }
        }
    }
}

// Mostra formulário para teste de login
if (!isset($_SESSION["logado"])) {
    echo "<h3>Formulário de teste de login</h3>";
    echo "<form method='post' action='" . $_SERVER["PHP_SELF"] . "'>";
    echo "<input type='hidden' name='acao' value='login'>";
    echo "Usuário: <input type='text' name='usuario'><br>";
    echo "Senha: <input type='password' name='senha'><br>";
    echo "<input type='submit' value='Entrar'>";
    echo "</form>";
    echo "<p>Use usuário 'admin' e senha 'admin' para testar.</p>";
} else {
    echo "<h3>Usuário logado: " . $_SESSION["usuario"] . "</h3>";
    echo "<a href='?logout=1'>Sair</a>";
}
```

**Teste**: Acesse a página e você verá um formulário de login de teste. Tente entrar com credenciais incorretas e depois com "admin"/"admin" para ver os diferentes resultados.

## Etapa 9: Implementação do Cadastro e Atualização de Itens

**Objetivo**: Implementar as funcionalidades de cadastro e atualização de itens.

```php
// Dentro do bloco if ($_SERVER["REQUEST_METHOD"] == "POST")
// Após o código de login:

// Verifica se ação é de cadastro ou atualização (Insert/Update)
if (isset($_POST["acao"]) && ($_POST["acao"] == "cadastrar" || $_POST["acao"] == "atualizar") && isset($_SESSION["logado"])) {
    // Obtém e sanitiza o nome do item
    $nome = isset($_POST["nome"]) ? sanitizar($_POST["nome"]) : "";
    
    // Para teste
    echo "Ação solicitada: " . $_POST["acao"] . "<br>";
    echo "Nome recebido: $nome<br>";
    
    // Valida se o campo nome foi preenchido
    if (!validarCampo($nome)) {
        // Define mensagem de erro se o campo estiver vazio
        $mensagem = "Por favor, preencha o campo nome!";
        echo "Erro: $mensagem<br>";
    } else {
        // Conecta ao banco de dados
        $conexao = conectarBD();

        // Cadastro (Insert)
        if ($_POST["acao"] == "cadastrar") {
            // Prepara uma consulta SQL para inserir o novo item
            $stmt = $conexao->prepare("INSERT INTO itens (nome) VALUES (?)");
            // Vincula o parâmetro nome à consulta (s = string)
            $stmt->bind_param("s", $nome);
            
            // Executa a consulta e verifica se foi bem-sucedida
            if ($stmt->execute()) {
                // Define mensagem de sucesso
                $mensagem = "Item cadastrado com sucesso!";
                echo "Sucesso: $mensagem<br>";
                echo "ID do novo item: " . $conexao->insert_id . "<br>";
                // Limpa o campo nome para permitir novo cadastro
                $nome = "";
            } else {
                // Define mensagem de erro com detalhes
                $mensagem = "Erro ao cadastrar item: " . $conexao->error;
                echo "Erro: $mensagem<br>";
            }
        } 
        // Atualização (Update)
        else if ($_POST["acao"] == "atualizar") {
            // Obtém o ID do item a ser atualizado
            $id = isset($_POST["id"]) ? (int)$_POST["id"] : 0;
            echo "ID para atualização: $id<br>";
            
            // Prepara uma consulta SQL para atualizar o item
            $stmt = $conexao->prepare("UPDATE itens SET nome = ? WHERE id = ?");
            // Vincula os parâmetros à consulta (s = string, i = integer)
            $stmt->bind_param("si", $nome, $id);
            
            // Executa a consulta e verifica se foi bem-sucedida
            if ($stmt->execute()) {
                // Define mensagem de sucesso
                $mensagem = "Item atualizado com sucesso!";
                echo "Sucesso: $mensagem<br>";
                // Redireciona para a própria página para resetar o formulário
                // Descomente para uso real
                // header("Location: " . $_SERVER["PHP_SELF"]);
                // exit;
            } else {
                // Define mensagem de erro com detalhes
                $mensagem = "Erro ao atualizar item: " . $conexao->error;
                echo "Erro: $mensagem<br>";
            }
        }
        
        // Fecha a declaração preparada
        $stmt->close();
        // Fecha a conexão com o banco de dados
        $conexao->close();
    }
}

// Para testar o cadastro/atualização (se estiver logado)
if (isset($_SESSION["logado"])) {
    echo "<h3>Formulário de teste de " . ($operacao == "editar" ? "edição" : "cadastro") . "</h3>";
    echo "<form method='post' action='" . $_SERVER["PHP_SELF"] . "'>";
    echo "<input type='hidden' name='acao' value='" . ($operacao == "editar" ? "atualizar" : "cadastrar") . "'>";
    
    if ($operacao == "editar") {
        echo "<input type='hidden' name='id' value='$id_para_editar'>";
    }
    
    echo "Nome: <input type='text' name='nome' value='$nome'><br>";
    echo "<input type='submit' value='" . ($operacao == "editar" ? "Atualizar" : "Cadastrar") . "'>";
    
    if ($operacao == "editar") {
        echo " <a href='" . $_SERVER["PHP_SELF"] . "'>Cancelar</a>";
    }
    
    echo "</form>";
}
```

**Teste**: Se estiver logado, você verá um formulário para cadastrar ou editar um item. Preencha-o e envie para ver o resultado da operação.

## Etapa 10: Listagem de Itens

**Objetivo**: Implementar a funcionalidade de listar todos os itens cadastrados.

```php
// Função que consulta e retorna todos os itens cadastrados no banco de dados
function listarItens() {
    $itens = array();
    
    $conexao = conectarBD();
    $resultado = $conexao->query("SELECT id, nome FROM itens ORDER BY id ASC");
    
    // Para teste, mostra informações sobre o resultado
    echo "Consulta executada. Número de itens encontrados: " . $resultado->num_rows . "<br>";
    
    if ($resultado->num_rows > 0) {
        while ($registro = $resultado->fetch_assoc()) {
            $itens[] = $registro;
        }
    }
    
    $conexao->close();
    return $itens;
}

// Teste da função de listagem
if (isset($_SESSION["logado"])) {
    echo "<h3>Itens cadastrados:</h3>";
    $itens = listarItens();
    
    if (count($itens) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Nome</th><th>Ações</th></tr>";
        
        foreach ($itens as $item) {
            echo "<tr>";
            echo "<td>" . $item["id"] . "</td>";
            echo "<td>" . $item["nome"] . "</td>";
            echo "<td>";
            echo "<a href='?editar=" . $item["id"] . "'>Editar</a> | ";
            echo "<a href='?excluir=" . $item["id"] . "' onclick=\"return confirm('Tem certeza que deseja excluir este item?')\">Excluir</a>";
            echo "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "Nenhum item cadastrado.";
    }
}
```

**Teste**: Se estiver logado, você verá uma tabela com todos os itens cadastrados, com opções para editar e excluir cada um.

## Versão Final: Sistema Completo

**Objetivo**: Sistema completo com todos os componentes, sem códigos de teste.

```php
<?php
// Inicia a sessão PHP para permitir o armazenamento de dados entre páginas através de cookies
session_start();

// Conexão com o Banco de Dados MySQL (Nome do BD: sistema_simples)
$host = "localhost";       // Endereço do servidor de banco de dados (local)
$usuario = "root";         // Nome de usuário do MySQL
$senha = "Senai@118";      // Senha do usuário MySQL
$banco = "sistema_simples"; // Nome do banco de dados a ser utilizado

// Função que estabelece conexão com o banco de dados e retorna o objeto de conexão
function conectarBD() {
    global $host, $usuario, $senha, $banco;
    $conexao = new mysqli($host, $usuario, $senha, $banco);
    
    if ($conexao->connect_error) {
        die("Falha na conexão: " . $conexao->connect_error);
    }
    
    return $conexao;
}

// Função que valida se um campo de formulário não está vazio
function validarCampo($campo) {
    $campo = trim($campo);
    return !empty($campo);
}

// Função que limpa dados de entrada para prevenir injeção de código malicioso
function sanitizar($dado) {
    $dado = trim($dado);
    $dado = stripslashes($dado);
    $dado = htmlspecialchars($dado);
    return $dado;
}

// Inicializa variáveis que serão utilizadas no sistema
$mensagem = "";             // Armazena mensagens de feedback para o usuário
$nome = "";                 // Armazena o nome do item a ser cadastrado/editado
$id_para_editar = 0;        // Armazena o ID do item a ser editado
$operacao = "cadastrar";    // Define a operação padrão como cadastro de novo item

// Verifica se a requisição atual é do tipo GET (via URL)
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Saída do sistema (Logout)
    if (isset($_GET["logout"])) {
        session_destroy();
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit;
    }

    // Edição (Update)
    if (isset($_GET["editar"]) && is_numeric($_GET["editar"]) && isset($_SESSION["logado"])) {
        $id_para_editar = (int)$_GET["editar"];
        $operacao = "editar";
        
        $conexao = conectarBD();
        $stmt = $conexao->prepare("SELECT nome FROM itens WHERE id = ?");
        $stmt->bind_param("i", $id_para_editar);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($registro = $resultado->fetch_assoc()) {
            $nome = $registro["nome"];
        }
        
        $stmt->close();
        $conexao->close();
    }

    // Exclusão (Delete)
    if (isset($_GET["excluir"]) && is_numeric($_GET["excluir"]) && isset($_SESSION["logado"])) {
        $id_para_excluir = (int)$_GET["excluir"];
        
        $conexao = conectarBD();
        $stmt = $conexao->prepare("DELETE FROM itens WHERE id = ?");
        $stmt->bind_param("i", $id_para_excluir);
        
        if ($stmt->execute()) {
            $mensagem = "Item excluído com sucesso!";
        } else {
            $mensagem = "Erro ao excluir o item: " . $conexao->error;
        }
        
        $stmt->close();
        $conexao->close();
    }
}

// Entrada no sistema (Login)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["acao"]) && $_POST["acao"] == "login") {
        $usuario_login = sanitizar($_POST["usuario"] ?? "");
        $senha_login = sanitizar($_POST["senha"] ?? "");
        
        if (!validarCampo($usuario_login) || !validarCampo($senha_login)) {
            $mensagem = "Por favor, preencha todos os campos!";
        } else {
            if ($usuario_login == "admin" && $senha_login == "admin") {
                $_SESSION["logado"] = true;
                $_SESSION["usuario"] = $usuario_login;
                $mensagem = "Login realizado com sucesso!";
            } else {
                $mensagem = "Usuário ou senha incorretos!";
            }
        }
    }

    // Verifica se ação é de cadastro ou atualização (Insert/Update)
    if (isset($_POST["acao"]) && ($_POST["acao"] == "cadastrar" || $_POST["acao"] == "atualizar") && isset($_SESSION["logado"])) {
        $nome = sanitizar($_POST["nome"] ?? "");
        
        if (!validarCampo($nome)) {
            $mensagem = "Por favor, preencha o campo nome!";
        } else {
            $conexao = conectarBD();

            // Cadastro (Insert)
            if ($_POST["acao"] == "cadastrar") {
                $stmt = $conexao->prepare("INSERT INTO itens (nome) VALUES (?)");
                $stmt->bind_param("s", $nome);
                
                if ($stmt->execute()) {
                    $mensagem = "Item cadastrado com sucesso!";
                    $nome = "";
                } else {
                    $mensagem = "Erro ao cadastrar item: " . $conexao->error;
                }
            } 
            // Atualização (Update)
            else if ($_POST["acao"] == "atualizar") {
                $id = (int)$_POST["id"];
                $stmt = $conexao->prepare("UPDATE itens SET nome = ? WHERE id = ?");
                $stmt->bind_param("si", $nome, $id);
                
                if ($stmt->execute()) {
                    $mensagem = "Item atualizado com sucesso!";
                    header("Location: " . $_SERVER["PHP_SELF"]);
                    exit;
                } else {
                    $mensagem = "Erro ao atualizar item: " . $conexao->error;
                }
            }
            
            $stmt->close();
            $conexao->close();
        }
    }
}

// Função que consulta e retorna todos os itens cadastrados no banco de dados
function listarItens() {
    $itens = array();
    
    $conexao = conectarBD();
    $resultado = $conexao->query("SELECT id, nome FROM itens ORDER BY id ASC");
    
    if ($resultado->num_rows > 0) {
        while ($registro = $resultado->fetch_assoc()) {
            $itens[] = $registro;
        }
    }
    
    $conexao->close();
    return $itens;
}
?>
```

Este código completo fornece todas as funcionalidades de um sistema CRUD básico com autenticação simples. Ele está pronto para ser integrado com uma interface HTML/CSS.

