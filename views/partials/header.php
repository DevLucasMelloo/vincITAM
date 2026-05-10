<!DOCTYPE html>
<!-- Partial de cabeçalho compartilhado por todas as views. Contém o HTML base, Bootstrap 5, sidebar de navegação e navbar superior. -->
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloPagina ?? 'VincITAM') ?> — VincITAM</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-bg: #0f1923;
            --sidebar-width: 220px;
            --sidebar-active: #2d5be3;
            --topbar-height: 60px;
            --content-bg: #f0f2f7;
        }

        body {
            background-color: var(--content-bg);
            font-family: 'Segoe UI', sans-serif;
        }

        /* Sidebar */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background-color: var(--sidebar-bg);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
        }

        #sidebar .sidebar-brand {
            padding: 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        #sidebar .sidebar-brand .brand-icon {
            background-color: var(--sidebar-active);
            border-radius: 8px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #fff;
        }

        #sidebar .brand-name {
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }

        #sidebar .brand-sub {
            font-size: 10px;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        #sidebar .nav-link {
            color: rgba(255,255,255,0.55);
            padding: 10px 16px;
            border-radius: 8px;
            margin: 2px 10px;
            font-size: 13.5px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
        }

        #sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255,255,255,0.07);
        }

        #sidebar .nav-link.active {
            color: #fff;
            background-color: var(--sidebar-active);
        }

        #sidebar .nav-link i {
            font-size: 16px;
            width: 20px;
        }

        /* Topbar */
        #topbar {
            height: var(--topbar-height);
            background-color: #fff;
            border-bottom: 1px solid #e5e7eb;
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            z-index: 99;
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 16px;
        }

        #topbar .search-bar {
            flex: 1;
            max-width: 360px;
        }

        #topbar .search-bar input {
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background-color: #f8f9fc;
            font-size: 13px;
            padding: 7px 14px 7px 36px;
            width: 100%;
        }

        #topbar .search-bar .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 14px;
        }

        #topbar .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        #topbar .icon-btn {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
        }

        #topbar .icon-btn:hover {
            background-color: #f3f4f6;
            color: #111;
        }

        #topbar .user-info {
            font-size: 12px;
            text-align: right;
            line-height: 1.3;
        }

        #topbar .user-info .user-name {
            font-weight: 600;
            color: #111827;
        }

        #topbar .user-info .user-role {
            color: #9ca3af;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        #topbar .avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background-color: var(--sidebar-active);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
        }

        /* Conteúdo principal */
        #main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 28px;
            min-height: calc(100vh - var(--topbar-height));
        }

        /* Badges de status */
        .badge-ativo       { background-color: #d1fae5; color: #065f46; font-weight: 500; }
        .badge-manutencao  { background-color: #fef3c7; color: #92400e; font-weight: 500; }
        .badge-desativado  { background-color: #fee2e2; color: #991b1b; font-weight: 500; }

        /* Cards de KPI */
        .kpi-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e5e7eb;
        }

        .kpi-card .kpi-value {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
        }

        .kpi-card .kpi-label {
            font-size: 13px;
            color: #6b7280;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<nav id="sidebar">
    <div class="sidebar-brand d-flex align-items-center gap-2">
        <div class="brand-icon"><i class="bi bi-hdd-stack"></i></div>
        <div>
            <div class="brand-name">VincITAM</div>
            <div class="brand-sub">Gestão de Ativos IT</div>
        </div>
    </div>

    <ul class="nav flex-column mt-3 px-0">
        <li class="nav-item">
            <a href="/vincitam/public/" class="nav-link <?= ($paginaAtiva ?? '') === 'dashboard' ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="/vincitam/public/ativos" class="nav-link <?= ($paginaAtiva ?? '') === 'ativos' ? 'active' : '' ?>">
                <i class="bi bi-server"></i> Inventário de Ativos
            </a>
        </li>
        <li class="nav-item">
            <a href="/vincitam/public/vinculos" class="nav-link <?= ($paginaAtiva ?? '') === 'vinculos' ? 'active' : '' ?>">
                <i class="bi bi-diagram-3"></i> Mapeamento de Dependências
            </a>
        </li>
        <li class="nav-item">
            <a href="/vincitam/public/relatorios" class="nav-link <?= ($paginaAtiva ?? '') === 'relatorios' ? 'active' : '' ?>">
                <i class="bi bi-bar-chart-line"></i> Relatórios Gerenciais
            </a>
        </li>
    </ul>

</nav>

<!-- Topbar -->
<div id="topbar">
    <div class="search-bar position-relative">
        <i class="bi bi-search search-icon"></i>
        <input type="text" placeholder="Buscar ativos ou servidores...">
    </div>

    <div class="topbar-right">
        <a href="#" class="icon-btn"><i class="bi bi-bell"></i></a>
        <a href="#" class="icon-btn"><i class="bi bi-question-circle"></i></a>
        <a href="#" class="icon-btn"><i class="bi bi-grid-3x3-gap"></i></a>
        <div class="user-info">
            <div class="user-name">Gestor de TI</div>
            <div class="user-role">Administrador</div>
        </div>
        <div class="avatar">G</div>
    </div>
</div>

<!-- Conteúdo principal -->
<div id="main-content">
