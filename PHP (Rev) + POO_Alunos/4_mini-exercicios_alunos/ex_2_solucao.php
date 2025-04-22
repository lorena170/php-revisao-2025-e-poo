<?php

// Classe abstrata Veiculo
abstract class Veiculo {
    protected $modelo;
    protected $placa;
    protected $disponivel;

    public function __construct($modelo, $placa) {
        $this->modelo = $modelo;
        $this->placa = $placa;
        $this->disponivel = true;
    }

    // Método abstrato para calcular o aluguel
    abstract public function calcularAluguel($dias);

    // Verifica se o veículo está disponível para locação
    public function isDisponivel() {
        return $this->disponivel;
    }

    // Retorna o modelo do veículo
    public function getModelo() {
        return $this->modelo;
    }

    // Realiza o aluguel do veículo
    public function alugar() {
        if ($this->disponivel) {
            $this->disponivel = false;
            echo get_class($this) . " '{$this->modelo}' alugado com sucesso!\n";
        } else {
            echo get_class($this) . " '{$this->modelo}' não está disponível para aluguel.\n";
        }
    }

    // Realiza a devolução do veículo
    public function devolver() {
        $this->disponivel = true;
        echo get_class($this) . " '{$this->modelo}' devolvido com sucesso!\n";
    }
}

// Classe Carro que herda de Veiculo
class Carro extends Veiculo {
    public function calcularAluguel($dias) {
        return 100.00 * $dias; // R$100 por dia
    }
}

// Classe Moto que herda de Veiculo
class Moto extends Veiculo {
    public function calcularAluguel($dias) {
        return 50.00 * $dias; // R$50 por dia
    }
}

// Classe Locadora que gerencia os veículos
class Locadora {
    private $veiculos = [];

    // Adiciona um veículo à locadora
    public function adicionarVeiculo(Veiculo $veiculo) {
        $this->veiculos[$veiculo->getModelo()] = $veiculo;
        echo "Veículo '{$veiculo->getModelo()}' adicionado ao acervo.\n";
    }

    // Aluga um veículo baseado no modelo
    public function alugarVeiculo($modelo) {
        if (isset($this->veiculos[$modelo])) {
            $this->veiculos[$modelo]->alugar();
        } else {
            echo "Veículo '{$modelo}' não encontrado na locadora.\n";
        }
    }

    // Devolve um veículo baseado no modelo
    public function devolverVeiculo($modelo) {
        if (isset($this->veiculos[$modelo])) {
            $this->veiculos[$modelo]->devolver();
        } else {
            echo "Veículo '{$modelo}' não encontrado na locadora.\n";
        }
    }
}

// Teste do sistema
$locadora = new Locadora();

// Criação de veículos (Carro e Moto)
$carro = new Carro("HB20", "ABC-1234");
$moto = new Moto("Yamaha XTZ", "XYZ-5678");

// Adicionando veículos à locadora
$locadora->adicionarVeiculo($carro);
$locadora->adicionarVeiculo($moto);

// Realizando o aluguel dos veículos
$locadora->alugarVeiculo("HB20");
$locadora->alugarVeiculo("Yamaha XTZ");

// Devolvendo o carro
$locadora->devolverVeiculo("HB20");

// Calculando e exibindo o valor do aluguel por 3 dias
$dias = 3;
echo "\nValor do aluguel do carro por {$dias} dias: R$" . number_format($carro->calcularAluguel($dias), 2, ',', '.') . "\n";
echo "Valor do aluguel da moto por {$dias} dias: R$" . number_format($moto->calcularAluguel($dias), 2, ',', '.') . "\n";

?>