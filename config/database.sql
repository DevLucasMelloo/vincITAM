-- Script de criação do banco de dados VincITAM. Executa a criação das tabelas do sistema caso ainda não existam, garantindo integridade referencial via FOREIGN KEYS.

CREATE DATABASE IF NOT EXISTS vincitam
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE vincitam;

-- Tabela principal de ativos de TI (servidores, bancos de dados, dispositivos de rede, estações de trabalho)
CREATE TABLE IF NOT EXISTS ativos (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    nome           VARCHAR(255) NOT NULL,                                                                -- Nome identificador do ativo (obrigatório)
    tipo           ENUM('servidor', 'banco_de_dados', 'dispositivo_rede', 'estacao_trabalho') NOT NULL, -- Categoria pré-definida do ativo (RNF-002)
    numero_serie   VARCHAR(100)  DEFAULT NULL,                                                          -- Número de série ou tag física (opcional)
    data_aquisicao DATE          DEFAULT NULL,                                                          -- Data de aquisição para controle de garantia (opcional)
    status         ENUM('ativo', 'manutencao', 'desativado') NOT NULL DEFAULT 'ativo'                   -- Estado atual do ativo (padrão: ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de vínculos entre ativos — define a hierarquia pai/filho (CMDB)
CREATE TABLE IF NOT EXISTS vinculos (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    id_ativo_pai   INT          NOT NULL,                        -- Ativo que hospeda ou provê (ex: Servidor)
    id_ativo_filho INT          NOT NULL,                        -- Ativo dependente ou hospedado (ex: Banco de Dados)
    tipo_relacao   VARCHAR(100) NOT NULL,                        -- Natureza do vínculo ex: "Hospeda", "Conectado via Fibra"
    data_vinculo   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Preenchido automaticamente no momento da criação (RF-005)

    -- Chaves estrangeiras garantem que vínculos não apontem para ativos inexistentes (RNF-006)
    FOREIGN KEY (id_ativo_pai)   REFERENCES ativos(id) ON DELETE CASCADE,
    FOREIGN KEY (id_ativo_filho) REFERENCES ativos(id) ON DELETE CASCADE,

    -- Impede vínculos duplicados para a mesma relação pai-filho (Regra de Negócio seção 7)
    UNIQUE KEY unico_vinculo (id_ativo_pai, id_ativo_filho)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
