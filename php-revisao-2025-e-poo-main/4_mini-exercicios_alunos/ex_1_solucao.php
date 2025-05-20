<?php

// Classe Abstrata Base
abstract class ItemBiblioteca {
    protected string $titulo;
    protected string $codigo;
    protected bool $disponivel;

    // Inicialização com método Construtor
    public function __construct(string $titulo, string $codigo) {
        $this->titulo = $titulo;
        $this->codigo = $codigo;
        $this->disponivel = true;
    }

    // Método abstrato (Não implementado agora)
    abstract public function calcularMulta(int $diasAtraso): float;

    // Métodos concretos (Já implementados)
    public function emprestar(): string {
        if ($this->disponivel){
            $this->disponivel = false;
            return "Item '{$this->titulo}' emprestado com sucesso!";
        }
        return "Item '{$this->titulo}' não está disponível";
    }

    public function devolver(): string {
        if ($this->disponivel){
            $this->disponivel = true;
            return "Item '{$this->titulo}' devolvido com sucesso!";
        }
        return "Item '{$this->titulo}' já esta na biblioteca";
    }

    public function getTitulo(): string {
        return $this->titulo;
    }
}

// Classes Concretas (Livro e Revista)
// Pilar da Herança aplicado abaixo
class Livro extends ItemBiblioteca {
    public function calcularMulta(int $diasAtraso): float
    {
        return $diasAtraso * 0.50;
    }
}

class Revista extends ItemBiblioteca {
    public function calcularMulta(int $diasAtraso): float
    {
        return $diasAtraso * 0.25;
    }
}

// Classe gerenciadora (Biblioteca)
class Biblioteca {
    // Criando um dicionário
    private array $itens =[];

    // Métodos para gerenciar (adicionar, emprestar e devolver)
    public function adicionarItem(ItemBiblioteca$item): string {
            $this->itens[$item->getTitulo()]= $item;
            return "Item '{$item->getTitulo()}' adicionado ao acervo!";
    }

    public function emprestarItem(string $titulo): string {
            // isset (verifica se o título existe no array)
            return isset($this->itens[$titulo]) ? $this->itens[$titulo]->emprestar():"Item não encontrado.";
    }
    public function devolverItem(string $titulo): string {
        return isset($this->itens[$titulo]) ? $this->itens[$titulo]->devolver():"Item não encontrado.";
    }

}

// Criando um Objeto/ Instância
$biblioteca = new Biblioteca();

// Criando itens (1 Licro e 1 Revista)
$livro1 = new Livro ("Python para Iniciantes", "L001");
$Revista1 = new Revista ("TechNews", "R001");

// Adicionar itens à biblioteca e exibir
echo $biblioteca->adicionarItem($livro1) . "<br>";
echo $biblioteca->adicionarItem($Revista1) . "<br><br>";

// Testando empréstimos
echo $biblioteca->emprestarItem("Python para Iniciantes") . "<br>";
echo $biblioteca->emprestarItem("TechNews") . "<br><br>";

// Testando devolução
echo $biblioteca->devolverItem("Python para Iniciantes") . "<br><br>";

// Calcular multa atraso para 5 dias
echo "Multa do livro (5 dias): R$" . number_format($livro1->calcularMulta(5), 2) . "<br>";
echo "Multa da revista (5 dias): R$" . number_format($Revista1->calcularMulta(5), 2) . "<br><br>";

    










?>