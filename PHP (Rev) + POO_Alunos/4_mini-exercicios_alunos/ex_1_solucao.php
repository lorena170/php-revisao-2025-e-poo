<?php

// Classe abstrata ItemBiblioteca
abstract class ItemBiblioteca {
    protected $titulo;
    protected $codigo;
    protected $disponivel;

    public function __construct($titulo, $codigo) {
        $this->titulo = $titulo;
        $this->codigo = $codigo;
        $this->disponivel = true;
    }

    // Método abstrato para calcular multa
    abstract public function calcular_multa($dias_atraso);

    // Método para emprestar o item
    public function emprestar() {
        if ($this->disponivel) {
            $this->disponivel = false;
            echo get_class($this) . " '{$this->titulo}' emprestado com sucesso!\n";
        } else {
            echo get_class($this) . " '{$this->titulo}' não está disponível para empréstimo.\n";
        }
    }

    // Método para devolver o item
    public function devolver() {
        $this->disponivel = true;
        echo get_class($this) . " '{$this->titulo}' devolvido com sucesso!\n";
    }

    // Método para verificar se o item está disponível
    public function esta_disponivel() {
        return $this->disponivel;
    }

    // Método para retornar o título do item
    public function titulo() {
        return $this->titulo;
    }
}

// Classe Livro que herda de ItemBiblioteca
class Livro extends ItemBiblioteca {
    public function calcular_multa($dias_atraso) {
        return 0.50 * $dias_atraso;
    }
}

// Classe Revista que herda de ItemBiblioteca
class Revista extends ItemBiblioteca {
    public function calcular_multa($dias_atraso) {
        return 0.25 * $dias_atraso;
    }
}

// Classe Biblioteca para gerenciar os itens
class Biblioteca {
    private $itens = [];

    // Adiciona um item ao acervo da biblioteca
    public function adicionar_item(ItemBiblioteca $item) {
        $this->itens[$item->titulo()] = $item;
        echo "Item '{$item->titulo()}' adicionado ao acervo.\n";
    }

    // Empresta um item baseado no título
    public function emprestar_item($titulo) {
        if (isset($this->itens[$titulo])) {
            $this->itens[$titulo]->emprestar();
        } else {
            echo "Item '{$titulo}' não encontrado no acervo.\n";
        }
    }

    // Devolve um item baseado no título
    public function devolver_item($titulo) {
        if (isset($this->itens[$titulo])) {
            $this->itens[$titulo]->devolver();
        } else {
            echo "Item '{$titulo}' não encontrado no acervo.\n";
        }
    }
}

// Teste do sistema
$biblioteca = new Biblioteca();

// Criação de itens (Livro e Revista)
$livro = new Livro("Python para Iniciantes", "L001");
$revista = new Revista("TechNews", "R001");

// Adicionando itens à biblioteca
$biblioteca->adicionar_item($livro);
$biblioteca->adicionar_item($revista);

// Realizando o empréstimo dos itens
$biblioteca->emprestar_item("Python para Iniciantes");
$biblioteca->emprestar_item("TechNews");

// Devolvendo o livro
$biblioteca->devolver_item("Python para Iniciantes");

// Calculando e exibindo as multas
$dias_atraso = 5;
echo "\nMulta do livro (5 dias): R$" . number_format($livro->calcular_multa($dias_atraso), 2, ',', '.') . "\n";
echo "Multa da revista (5 dias): R$" . number_format($revista->calcular_multa($dias_atraso), 2, ',', '.') . "\n";

?>