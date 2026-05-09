<?php

// Representa a entidade Ativo do sistema. Espelha a tabela 'ativos' do banco de dados e trafega dados entre as camadas DAO, Service e Controller.

declare(strict_types=1);

namespace models;

class AtivoModel
{
    // Inicializa as propriedades diretamente no construtor (RNF-005)
    public function __construct(
        public readonly ?int    $id            = null, // Chave primária — null quando o ativo ainda não foi salvo no banco
        public string           $nome          = '',   // Nome identificador do ativo (obrigatório)
        public string           $tipo          = '',   // Categoria do ativo: servidor, banco_de_dados, dispositivo_rede, estacao_trabalho (obrigatório)
        public ?string          $numeroSerie   = null, // Número de série ou tag física — opcional
        public ?string          $dataAquisicao = null, // Data de aquisição no formato AAAA-MM-DD — opcional
        public string           $status        = 'ativo' // Estado atual do ativo: ativo, manutencao, desativado (padrão: ativo)
    ) {}
}
