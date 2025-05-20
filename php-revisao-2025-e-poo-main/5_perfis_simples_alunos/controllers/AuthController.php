<?php

// Controlar a autenticação
class AuthController {

    // Exibir a página de login
    public function login($erro = '') {
        require_once 'views/login.php';
        // Função que renderiza a tela
        renderizarLogin($erro);
    }

    // Processa a tentativa de login
    public function autenticar() {
        // Obtem valor do formulário ou usa vazio
        $usuario = $_POST['usuario'] ?? '';
        $senha = $_POST['senha'] ?? '';

        // Verifica se algum campo esta vazio
        if (empty($usuario) || empty($senha)) {
            $this->login('Preencha todos os campos!');
        }

        // Tenta autenticar chamando método "Auth"
        $dadosUsuario = Auth::autenticar($usuario, $senha);

        if($dadosUsuario) {
            // Se credenciais válidas inicia sessão
            Auth::iniciarSessao($dadosUsuario);

            // Redireciona para página inicial (Protegida)
            header('Location: index.php?pagina=lista');
            exit;
        } else {
            $this->login('Usuário ou senha incorretos!');
        }
    }
        // Processa o logout
        public function logout() {
            Auth::encerrarSessao();
            header('Location: index.php?pagina=login');
        }
    }
