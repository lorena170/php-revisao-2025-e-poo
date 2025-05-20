<?php

// Classe Abstrata Base
abstract class Veiculo {
    protected string $modelo;
    protected string $placa;
    protected bool $disponivel;

    public function __construct(string $modelo, string $placa) {
        $this->modelo = $modelo;
        $this->placa = $placa;
        $this->disponivel = true;
    }

    abstract public function calcularAluguel(int $dias): float;

    public function isDisponivel(): bool {
        return $this->disponivel;
    }

    public function getModelo(): string {
        return $this->modelo;
    }

    public function alugar(): string {
        if ($this->disponivel) {
            $this->disponivel = false;
            return "Veículo '{$this->modelo}' alugado com sucesso!";
        }
        return "Veículo '{$this->modelo}' não está disponível.";
    }

    public function devolver(): string {
        if (!$this->disponivel) {
            $this->disponivel = true;
            return "Veículo '{$this->modelo}' devolvido com sucesso!";
        }
        return "Veículo '{$this->modelo}' já está na locadora.";
    }
}

// Classes Concretas
class Carro extends Veiculo {
    public function calcularAluguel(int $dias): float {
        return $dias * 100.00;
    }
}

class Moto extends Veiculo {
    public function calcularAluguel(int $dias): float {
        return $dias * 50.00;
    }
}

// Classe Gerenciadora
class Locadora {
    private array $veiculos = [];

    public function adicionarVeiculo(Veiculo $veiculo): string {
        $this->veiculos[$veiculo->getModelo()] = $veiculo;
        return "Veículo '{$veiculo->getModelo()}' adicionado ao acervo.";
    }

    public function alugarVeiculo(string $modelo): string {
        return isset($this->veiculos[$modelo]) ? $this->veiculos[$modelo]->alugar() : "Veículo não encontrado.";
    }

    public function devolverVeiculo(string $modelo): string {
        return isset($this->veiculos[$modelo]) ? $this->veiculos[$modelo]->devolver() : "Veículo não encontrado.";
    }
}

// Exemplo de uso
$locadora = new Locadora();

// Criando veículos
$carro1 = new Carro("HB20", "ABC-1234");
$moto1 = new Moto("Yamaha XTZ", "XYZ-5678");

// Adicionando à locadora
echo $locadora->adicionarVeiculo($carro1) . "<br>";
echo $locadora->adicionarVeiculo($moto1) . "<br><br>";

// Testando aluguéis
echo $locadora->alugarVeiculo("HB20") . "<br>";
echo $locadora->alugarVeiculo("Yamaha XTZ") . "<br><br>";

// Testando devolução
echo $locadora->devolverVeiculo("HB20") . "<br><br>";

// Testando cálculo de aluguel
echo "Valor do aluguel do carro por 3 dias: R$" . number_format($carro1->calcularAluguel(3), 2) . "<br>";
echo "Valor do aluguel da moto por 3 dias: R$" . number_format($moto1->calcularAluguel(3), 2);
