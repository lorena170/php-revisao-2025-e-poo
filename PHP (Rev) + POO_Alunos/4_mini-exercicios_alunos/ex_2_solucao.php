<?php

// Classe Abstrata Veiculo
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

    public function alugar(): void {
        if ($this->disponivel) {
            $this->disponivel = false;
            echo get_class($this) . " '{$this->modelo}' alugado com sucesso!<br>";
        } else {
            echo get_class($this) . " '{$this->modelo}' não está disponível para aluguel.<br>";
        }
    }

    public function devolver(): void {
        $this->disponivel = true;
        echo get_class($this) . " '{$this->modelo}' devolvido com sucesso!<br>";
    }
}

# Subclasse Carro
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

# Classe Locadora
class Locadora {
    private array $veiculos = [];

    public function adicionarVeiculo(Veiculo $veiculo): void {
        $this->veiculos[] = $veiculo;
        echo "Veículo '{$veiculo->getModelo()}' adicionado ao acervo.<br>";
    }

    public function alugarVeiculo(string $modelo): void {
        foreach ($this->veiculos as $veiculo) {
            if ($veiculo->getModelo() === $modelo) {
                $veiculo->alugar();
                return;
            }
        }
        echo "Veículo '{$modelo}' não encontrado na locadora.<br>";
    }

    public function devolverVeiculo(string $modelo): void {
        foreach ($this->veiculos as $veiculo) {
            if ($veiculo->getModelo() === $modelo) {
                $veiculo->devolver();
                return;
            }
        }
        echo "Veículo '{$modelo}' não encontrado na locadora.<br>";
    }
}

# Testes e Simulação
// Script Execution
$locadora = new Locadora();

$carro = new Carro("HB20", "ABC1234");
$moto = new Moto("Yamaha XTZ", "XYZ5678");

$locadora->adicionarVeiculo($carro);
$locadora->adicionarVeiculo($moto);

echo "<br>";
$locadora->alugarVeiculo("HB20");
$locadora->alugarVeiculo("Yamaha XTZ");

echo "<br>";
$locadora->devolverVeiculo("HB20");

echo "<br>";
echo "Valor do aluguel do carro por 3 dias: R$" . number_format($carro->calcularAluguel(3), 2, ',', '.') . "<br>";
echo "Valor do aluguel da moto por 3 dias: R$" . number_format($moto->calcularAluguel(3), 2, ',', '.') . "<br>";