<?php

// Abstract class definition in PHP
abstract class ItemBiblioteca {
    protected string $titulo;
    protected string $codigo;
    protected bool $disponivel;

    public function __construct(string $titulo, string $codigo) {
        $this->titulo = $titulo;
        $this->codigo = $codigo;
        $this->disponivel = true;
    }

    abstract public function calcularMulta(int $diasAtraso): float;

    public function emprestar(): void {
        if ($this->disponivel) {
            $this->disponivel = false;
            echo get_class($this) . " '{$this->titulo}' emprestado com sucesso!<br>";
        } else {
            echo get_class($this) . " '{$this->titulo}' não está disponível para empréstimo.<br>";
        }
    }

    public function devolver(): void {
        $this->disponivel = true;
        echo get_class($this) . " '{$this->titulo}' devolvido com sucesso!<br>";
    }

    public function isDisponivel(): bool {
        return $this->disponivel;
    }

    public function getTitulo(): string {
        return $this->titulo;
    }
}

# Subclasse Livro
class Livro extends ItemBiblioteca {
    public function calcularMulta(int $diasAtraso): float {
        return $diasAtraso * 0.50;
    }
}

# Subclasse Revista
class Revista extends ItemBiblioteca {
    public function calcularMulta(int $diasAtraso): float {
        return $diasAtraso * 0.25;
    }
}
class Biblioteca {
    private array $itens = [];

    public function adicionarItem(ItemBiblioteca $item): void {
        $this->itens[$item->getTitulo()] = $item;
        echo "Item '{$item->getTitulo()}' adicionado ao acervo.<br>";
    }

    public function emprestarItem(string $titulo): void {
        if (isset($this->itens[$titulo])) {
            $this->itens[$titulo]->emprestar();
        } else {
            echo "Item '{$titulo}' não encontrado no acervo.<br>";
        }
    }

    public function devolverItem(string $titulo): void {
        if (isset($this->itens[$titulo])) {
            $this->itens[$titulo]->devolver();
        } else {
            echo "Item '{$titulo}' não encontrado no acervo.<br>";
        }
    }
}

# Testes e Simulação
// Testes e Simulação
$biblioteca = new Biblioteca();

$livro = new Livro("Python para Iniciantes", "L001");
$revista = new Revista("TechNews", "R001");

$biblioteca->adicionarItem($livro);
$biblioteca->adicionarItem($revista);

echo "<br>";
$biblioteca->emprestarItem("Python para Iniciantes");
$biblioteca->emprestarItem("TechNews");

echo "<br>";
$biblioteca->devolverItem("Python para Iniciantes");

echo "<br>";
echo "Multa do livro (5 dias): R$" . number_format($livro->calcularMulta(5), 2, ',', '.') . "<br>";
echo "Multa da revista (5 dias): R$" . number_format($revista->calcularMulta(5), 2, ',', '.') . "<br>";

                                
?>
