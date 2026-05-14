# VincITAM — Sistema de Gestão de Ativos de TI

Sistema web de **ITAM/CMDB** (IT Asset Management / Configuration Management Database) desenvolvido em PHP 8 com arquitetura multicamada MVC. Permite cadastrar ativos de TI, mapear dependências entre eles e gerar relatórios de inventário.

---

## Funcionalidades

- **Dashboard** — visão geral com KPIs de ativos por tipo e status
- **Inventário de Ativos** — CRUD completo com busca por nome, número de série, tipo e status
- **Mapeamento de Dependências** — criação e visualização de vínculos entre ativos (pai → filho)
- **Topologia** — exibe todos os ativos dependentes de um ativo específico
- **Relatórios Gerenciais** — listagem filtrável com exportação CSV (inclui vínculos)
- **Ordenação por coluna** — clique no cabeçalho de qualquer tabela para ordenar crescente/decrescente

---

## Tecnologias

| Camada | Tecnologia |
|--------|------------|
| Backend | PHP 8.x com `strict_types`, Constructor Promotion, `readonly` |
| Banco de dados | MySQL 8.x |
| Acesso a dados | PDO com Prepared Statements |
| Frontend | Bootstrap 5.3 + Bootstrap Icons |
| Servidor | Apache (XAMPP) com mod_rewrite |

---

## Arquitetura

```
vincitam/
├── config/          # Conexão PDO com o banco de dados
├── controllers/     # Recebem requisições e orquestram Service + View
├── dao/             # SQL puro via PDO — única camada que acessa o banco
├── models/          # Entidades PHP (AtivoModel, VinculoModel)
├── routes/          # Mapeamento URL → Controller@método
├── services/        # Regras de negócio e validações
├── views/           # Templates HTML/PHP
│   ├── ativos/
│   ├── vinculos/
│   ├── relatorios/
│   ├── dashboard/
│   └── partials/    # header.php e footer.php compartilhados
└── public/          # Ponto de entrada (index.php) e .htaccess
```

Fluxo obrigatório: **Controller → Service → DAO** (Controller nunca acessa o DAO diretamente).

---

## Requisitos

- PHP 8.1 ou superior
- MySQL 8.0 ou superior
- Apache com `mod_rewrite` habilitado (XAMPP recomendado)

---

## Instalação

### 1. Clone o repositório

```bash
git clone https://github.com/DevLucasMelloo/vincITAM.git
```

### 2. Configure o servidor web

**XAMPP:** crie um link simbólico ou mova a pasta para `C:\xampp\htdocs\vincitam`.

**Linux/Mac:**
```bash
ln -s /caminho/do/projeto /var/www/html/vincitam
```

### 3. Crie o banco de dados

Acesse o MySQL e execute o script:

```bash
mysql -u root -p < config/database.sql
```

Ou cole o conteúdo de `config/database.sql` no phpMyAdmin.

### 4. Configure a conexão

Copie o arquivo de exemplo e preencha com suas credenciais:

```bash
cp config/database.example.php config/database.php
```

Edite `config/database.php`:

```php
$host    = '127.0.0.1';
$banco   = 'vincitam';
$usuario = 'root';
$senha   = 'sua_senha';
```

### 5. Acesse o sistema

```
http://localhost/vincitam/public/
```

---

## Segurança (RNF-002)

- **SQL Injection** — prevenido com Prepared Statements nativos do PDO (`ATTR_EMULATE_PREPARES = false`)
- **XSS** — prevenido com `htmlspecialchars()` em todos os campos de saída nas Views
- Arquivos de configuração fora do diretório público (`/public`)

---

## Regras de Negócio dos Vínculos

| Regra | Descrição |
|-------|-----------|
| Auto-vínculo | Um ativo não pode ser pai de si mesmo |
| Hierarquia | Banco de Dados não pode hospedar Servidor ou Dispositivo de Rede |
| Unicidade | Não é permitido criar vínculo duplicado entre o mesmo par pai-filho |
| Cascata | Ao excluir um ativo, todos os seus vínculos são removidos automaticamente (`ON DELETE CASCADE`) |

---

## Tipos de Ativo

`servidor` · `banco_de_dados` · `dispositivo_rede` · `estacao_trabalho`

## Status de Ativo

`ativo` · `manutencao` · `desativado`
