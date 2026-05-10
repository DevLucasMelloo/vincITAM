<?php

// Representa a entidade Vinculo do sistema. Espelha a tabela 'vinculos' do banco de dados e define a relação hierárquica entre dois ativos (pai e filho).

declare(strict_types=1);

namespace models;

class VinculoModel
{
    // Constructor Promotion do PHP 8 — declara e inicializa as propriedades diretamente no construtor (RNF-005)
    public function __construct(
        public readonly ?int    $id           = null,  // Chave primária — null quando o vínculo ainda não foi salvo no banco
        public int              $idAtivoPai   = 0,     // ID do ativo que hospeda/provê — corresponde a id_ativo_pai no banco (obrigatório)
        public int              $idAtivoFilho = 0,     // ID do ativo dependente/hospedado — corresponde a id_ativo_filho no banco (obrigatório)
        public string           $tipoRelacao  = '',    // Descreve a natureza do vínculo ex: "Hospeda", "Conectado via Fibra" (obrigatório)
        public readonly ?string $dataVinculo  = null,  // Preenchido automaticamente pelo banco via CURRENT_TIMESTAMP — não enviado pelo formulário
        public readonly ?string $nomePai      = null,  // Nome do ativo pai — preenchido via JOIN no DAO para exibição na View
        public readonly ?string $nomeFilho    = null   // Nome do ativo filho — preenchido via JOIN no DAO para exibição na View
    ) {}
}
