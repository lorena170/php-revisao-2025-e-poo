<?php
// Criação "Auth" - Classe que irá controlar o
// serviço de autenticação e controle de acesso

class Auth {

    // Método autenticar (verifica se um usuário
    // existe e se suas credenciais são válidas)
    public static function autenticar($usuario, $senha) {
        $conn = conectarBD();
        $query = "SELECT * FROM usuarios WHERE usuario = :usuario";
        $stmt = $conn->prepare($query);
        $stmt->execute(['usuario'=>$usuario]);
        $user = $stmt->fetch();

        if ($user && password_verify($senha, $user['senha'])) {
            return [
                'id' => $user['id'],
                'nome'=> $user['nome'],
                'usuario'=> $user['usuario'],
                'perfil'=> $user['perfil']
            ];
        }

        return false;
    }

    // Método para iniciar a sessão
    public static function iniciarSessao($usuario) {
        // Verifica se a sessão ainda não foi iniciada
        if (session_status() === PHP_SESSION_NONE) {
            // Inicia a sessão
            session_start();
        }

        // gera um id para a sessão (Evita ataques)
        session_regenerate_id(true);
 
        // Armazena os dados (Usuário autenticado)
        $_SESSION['auth'] = [
            'logado'=> true,
            'id' => $usuario['id'],
            'nome'=> $usuario['nome'],
            'usuario'=> $usuario['usuario'],
            'perfil'=> $usuario['perfil']
        ];
    }

    // Método para encerrar uma sessão
    public static function encerrarSessao() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Limpa todas as variáveis de sessão
        $_SESSION = [];

        // Verifica se existe um cookie de sessão ativa
        if (isset($_COOKIE[session_name()])) {
            // Remove o cookie definindo um tempo para expirar
            setcookie(session_name(), '', time()-42000, '/');
        }
        // Destroi a sessão (encerrando a autenticação)
        session_destroy();
    }

    // Método esta logado
    public static function estaLogado() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    // retorna "true" se a sessão existir 
    // indica que o usuário foi autenticado
    return isset($_SESSION['auth']) && $_SESSION['auth']['logado'] ===true;
    }

    // Método obter usuário
    public static function obterUsuario() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Retorna os dados armazenados na sessão Auth,
    // caso contrário retorna null (Operador de coalescência nula)
    return $_SESSION['auth'] ?? null;
    }

    // Verifica se o usuário tem um perfil específico
    public static function temPerfil($perfil)  {
        // Usado para obter os dados do usuário
        // Obs: "self::" Usado para acessar métodos dentro
        // da mesma classe
        $usuario = self::obterUsuario();
        return $usuario && $usuario['perfil'] === $perfil;
    }

       // Verifica se o usuário é Admin
        public static function isAdmin()  {
          return self::temPerfil('admin');
    }

    // Verifica se o usuário tem a permissão para uma
    // determinada ação
    public static function temPermissao($acao)  {
        $usuario = self::obterUsuario();

        // Se não houver usuário autenticado
        // (sem permissão)
        if (!$usuario) {
            return false;
        }

        // Matriz de permissões para cada perfil
        $permissoes = [
            // Admin (Permissão para tudo)
             'admin'=> [
                'visualizar' => true,
                'adicionar' => true,
                'editar' => true,
                'excluir' => true
            ],
            // Usuário (Permissão apenas para visualizar)
             'usuario'=> [
                'visualizar' => true,
                'adicionar' => false,
                'editar' => false,
                'excluir' => false
            ],
        ];

        if (!isset($permissoes[$usuario['perfil']]) || !isset($permissoes[$usuario['perfil']][$acao])) 
        {
            // Caso não exista (Nega a permissão)
            return false;
        }
        // Retorna a ação permitida para cada tipo
        // de usuário
        return $permissoes[$usuario['perfil']][$acao];
    }
}