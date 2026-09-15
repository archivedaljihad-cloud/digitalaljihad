<!-- resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    @php
        // 1. Mengatur Nama Aplikasi
        $appName = isset($setting) ? (is_array($setting) ? ($setting['nama_aplikasi'] ?? 'Sistem Informasi Masjid Digital') : ($setting->nama_aplikasi ?? 'Sistem Informasi Masjid Digital')) : 'Sistem Informasi Masjid Digital';
        
        // 2. Mengatur Path Logo
        $logoUrl = asset('img/logo.png'); // Default logo
        if (isset($setting)) {
            $rawLogo = is_array($setting) ? ($setting['logo'] ?? null) : ($setting->logo ?? null);
            if (!empty($rawLogo)) {
                $cleanLogo = str_replace(['storage/', 'public/'], '', $rawLogo);
                $logoUrl = asset('storage/' . $cleanLogo);
            }
        }

        // 3. Mengatur Path Favicon
        $faviconUrl = $logoUrl; // Default fallback ke logo jika favicon belum diatur
        if (isset($setting)) {
            $rawFavicon = is_array($setting) ? ($setting['favicon'] ?? null) : ($setting->favicon ?? null);
            if (!empty($rawFavicon)) {
                $cleanFavicon = str_replace(['storage/', 'public/'], '', $rawFavicon);
                $faviconUrl = asset('storage/' . $cleanFavicon);
            }
        }
    @endphp

    <meta name="description" content="{{ $appName }}">
    <meta name="author" content="Masjid Al-Jihad Dev. System">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $appName }} - Panel Admin</title>

    <!-- Font Awesome -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Flatpickr (24-Hour System Timepicker) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Favicon (Diperbarui dengan Cache Busting) -->
    <link rel="icon" href="{{ $faviconUrl }}?v={{ time() }}" type="image/x-icon">

    <!-- Islamic Admin Custom Styles -->
    <style>
        .flatpickr-time {
            background: #ffffff !important;
            border-radius: 8px !important;
            border: 1px solid #c9a03d !important;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15) !important;
        }
        .flatpickr-time input.flatpickr-hour,
        .flatpickr-time input.flatpickr-minute {
            font-weight: 700 !important;
            font-size: 1.25rem !important;
            color: #1e5a3a !important;
        }
        .flatpickr-time .flatpickr-time-separator {
            font-weight: 800 !important;
            color: #c9a03d !important;
        }
        .flatpickr-am-pm {
            display: none !important;
        }
        :root {
            --islamic-green: #1e5a3a;
            --islamic-gold: #c9a03d;
            --islamic-dark: #0a2e1f;
            --islamic-light: #f5f7f2;
            --islamic-maroon: #8b4513;
        }

        body {
            font-family: 'Poppins', 'Nunito', sans-serif;
            background: linear-gradient(135deg, #f5f7f2 0%, #e8ede5 100%);
        }

        /* Islamic Sidebar Style */
        .bg-gradient-primary {
            background: linear-gradient(180deg, var(--islamic-dark) 0%, var(--islamic-green) 100%) !important;
            position: relative;
            overflow: hidden;
        }

        .bg-gradient-primary::before {
            content: "";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            bottom: 20px;
            right: 20px;
            font-size: 150px;
            opacity: 0.05;
            color: white;
            pointer-events: none;
        }

        .bg-gradient-primary::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100' opacity='0.03'%3E%3Cpath fill='white' d='M50,0 L61.8,19 L84.5,22.5 L69,38 L72.5,61.8 L50,70 L27.5,61.8 L31,38 L15.5,22.5 L38.2,19 L50,0 Z'/%3E%3Ccircle cx='50' cy='50' r='15' fill='white'/%3E%3C/svg%3E");
            background-repeat: repeat;
            background-size: 40px;
            pointer-events: none;
        }

        /* Sidebar Brand */
        .sidebar-brand {
            background: rgba(255, 255, 255, 0.05);
            border-bottom: 2px solid var(--islamic-gold);
            position: relative;
        }

        .sidebar-brand-text {
            font-family: 'Amiri', serif;
            font-size: 1.1rem;
            letter-spacing: 1px;
        }

        /* Sidebar Navigation */
        .sidebar .nav-item .nav-link {
            color: rgba(255, 255, 255, 0.85);
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .sidebar .nav-item .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border-left-color: var(--islamic-gold);
            transform: translateX(5px);
        }

        .sidebar .nav-item.active .nav-link {
            color: white;
            background: rgba(255, 255, 255, 0.15);
            border-left-color: var(--islamic-gold);
            font-weight: 600;
        }

        .sidebar .nav-item .nav-link i {
            color: var(--islamic-gold);
        }

        /* Sidebar Heading */
        .sidebar-heading {
            color: var(--islamic-gold);
            font-size: 0.7rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 700;
        }

        /* Topbar Style */
        .topbar {
            background: linear-gradient(135deg, #ffffff 0%, #fef9e6 100%);
            border-bottom: 3px solid var(--islamic-gold);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .navbar-search .btn-primary {
            background: linear-gradient(135deg, var(--islamic-green), var(--islamic-dark));
            border: none;
        }

        .navbar-search .btn-primary:hover {
            background: linear-gradient(135deg, var(--islamic-dark), var(--islamic-green));
        }

        /* Badge Style */
        .badge-success {
            background: linear-gradient(135deg, var(--islamic-green), var(--islamic-dark)) !important;
        }

        /* Card Style */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            background: linear-gradient(135deg, var(--islamic-green), var(--islamic-dark));
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border-bottom: 2px solid var(--islamic-gold);
        }

        .card-header h1, .card-header h2, .card-header h3, 
        .card-header h4, .card-header h5, .card-header h6,
        .card-header .text-primary {
            color: #ffffff !important;
        }

        .card-header .text-muted {
            color: rgba(255, 255, 255, 0.88) !important;
        }

        /* Button Style */
        .btn-primary {
            background: linear-gradient(135deg, var(--islamic-green), var(--islamic-dark));
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--islamic-dark), var(--islamic-green));
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 90, 58, 0.3);
        }

        .btn-outline-primary {
            border-color: var(--islamic-green);
            color: var(--islamic-green);
        }

        .btn-outline-primary:hover {
            background: linear-gradient(135deg, var(--islamic-green), var(--islamic-dark));
            border-color: transparent;
        }

        /* Footer Style */
        .sticky-footer {
            background: linear-gradient(135deg, var(--islamic-dark), var(--islamic-green));
            color: white;
            border-top: 2px solid var(--islamic-gold);
        }

        .sticky-footer a {
            color: var(--islamic-gold);
            text-decoration: none;
        }

        .sticky-footer a:hover {
            color: #ffd700;
        }

        /* Islamic Corner Decoration */
        .islamic-corner {
            position: fixed;
            width: 150px;
            height: 150px;
            pointer-events: none;
            z-index: 999;
            opacity: 0.3;
        }

        .corner-br {
            bottom: 0;
            right: 0;
            background: radial-gradient(circle at bottom right, var(--islamic-gold), transparent 70%);
            border-radius: 150px 0 0 0;
        }

        /* Profile Image Style */
        .img-profile {
            background: linear-gradient(135deg, var(--islamic-gold), #ffd700) !important;
            color: var(--islamic-dark) !important;
            font-weight: bold;
        }

        /* Dropdown Menu */
        .dropdown-menu {
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .dropdown-item:hover {
            background: linear-gradient(135deg, rgba(30, 90, 58, 0.1), rgba(10, 46, 31, 0.1));
            color: var(--islamic-dark);
        }

        /* Scroll to Top */
        .scroll-to-top {
            background: linear-gradient(135deg, var(--islamic-green), var(--islamic-dark));
        }

        .scroll-to-top:hover {
            background: linear-gradient(135deg, var(--islamic-dark), var(--islamic-green));
            transform: translateY(-3px);
        }

        /* Table Style */
        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        table thead th,
        .table thead th,
        .table thead.thead-light th,
        .table .thead-light th,
        .table thead tr th,
        thead.thead-light th,
        thead th {
            background: linear-gradient(135deg, var(--islamic-green), var(--islamic-dark)) !important;
            color: #ffffff !important;
            border-top: none !important;
            border-bottom: 2px solid var(--islamic-gold) !important;
            font-weight: 700 !important;
            font-size: 0.92rem !important;
            letter-spacing: 0.3px;
            vertical-align: middle !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.35);
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(30, 90, 58, 0.05);
        }

        /* Modal Style */
        .modal-content {
            border-radius: 15px;
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--islamic-green), var(--islamic-dark));
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .modal-header .close {
            color: white;
        }

        /* Alert Style */
        .alert {
            border-radius: 10px;
            border-left: 4px solid var(--islamic-gold);
        }

        /* Pagination */
        .page-link {
            color: var(--islamic-green);
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, var(--islamic-green), var(--islamic-dark));
            border-color: var(--islamic-dark);
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .main-content {
            animation: fadeInUp 0.5s ease-out;
        }

        /* Dashboard Card Style */
        .dashboard-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .dashboard-card::before {
            content: "";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            bottom: -20px;
            right: -20px;
            font-size: 80px;
            opacity: 0.05;
            color: var(--islamic-green);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar-brand-text {
                font-size: 0.9rem;
            }
        }

        /* =====================================================
           🕌 ISLAMIC MATERIAL DESIGN 3 — Redesain v4.0
           DIGITALv304 Admin Panel Enhancement Layer
           ===================================================== */

        /* === SIDEBAR: Width Override (260px) === */
        .sidebar {
            width: 260px !important;
        }

        /* === SIDEBAR: Deeper Dark Gradient === */
        .bg-gradient-primary {
            background: linear-gradient(180deg, #071a10 0%, #0e3521 45%, #1a5235 100%) !important;
        }

        /* === SIDEBAR: Enhanced Geometric Pattern === */
        .bg-gradient-primary::after {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60'%3E%3Cpolygon points='30,3 55,16 55,44 30,57 5,44 5,16' fill='none' stroke='rgba(201,160,61,0.14)' stroke-width='0.8'/%3E%3Ccircle cx='30' cy='30' r='7' fill='none' stroke='rgba(201,160,61,0.08)' stroke-width='0.8'/%3E%3C/svg%3E") !important;
            background-size: 60px !important;
        }

        /* === SIDEBAR: Brand Refinement === */
        .sidebar-brand {
            background: rgba(0, 0, 0, 0.22) !important;
            border-bottom: 1px solid rgba(201, 160, 61, 0.28) !important;
            padding: 18px 16px 14px !important;
        }

        /* === SIDEBAR: USER PANEL (new component) === */
        .sidebar-user-panel {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 16px;
            background: rgba(255, 255, 255, 0.04);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            position: relative;
            z-index: 1;
        }

        .sup-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--islamic-gold), #f0c040);
            color: var(--islamic-dark);
            font-weight: 800;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(201, 160, 61, 0.4);
        }

        .sup-info {
            overflow: hidden;
            flex: 1;
        }

        .sup-name {
            font-size: 0.78rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sup-role {
            font-size: 0.62rem;
            color: var(--islamic-gold);
            font-weight: 500;
            margin-top: 2px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .sup-role-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #4ade80;
            flex-shrink: 0;
            animation: agy-pulse-dot 2.5s infinite;
        }

        @keyframes agy-pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.35; }
        }

        /* === SIDEBAR NAV: PILL STYLE (override SB Admin 2) === */
        .sidebar .nav-item .nav-link {
            display: flex !important;
            align-items: center !important;
            color: rgba(255, 255, 255, 0.75) !important;
            padding: 9px 12px !important;
            margin: 2px 10px !important;
            border-radius: 10px !important;
            border-left: none !important;
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1) !important;
            font-size: 0.83rem !important;
            font-weight: 500 !important;
            position: relative;
        }

        .sidebar .nav-item .nav-link:hover {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.1) !important;
            transform: none !important;
            border-left-color: transparent !important;
        }

        .sidebar .nav-item.active .nav-link {
            color: #ffffff !important;
            background: linear-gradient(135deg, rgba(255,255,255,0.17), rgba(255,255,255,0.07)) !important;
            box-shadow: 0 2px 14px rgba(0, 0, 0, 0.25), inset 0 1px 0 rgba(255,255,255,0.08) !important;
            font-weight: 700 !important;
            border-left-color: transparent !important;
        }

        /* Gold dot indicator for active item */
        .sidebar .nav-item.active .nav-link::after {
            content: '';
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--islamic-gold);
            box-shadow: 0 0 6px rgba(201, 160, 61, 0.6);
        }

        /* === NAV ICON CHIP (injected by JS) === */
        .nav-icon-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 7px;
            margin-right: 10px;
            flex-shrink: 0;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar .nav-item .nav-link:hover .nav-icon-chip {
            background: rgba(255, 255, 255, 0.18);
            transform: scale(1.08);
        }

        .sidebar .nav-item.active .nav-link .nav-icon-chip {
            background: linear-gradient(135deg, rgba(201,160,61,0.28), rgba(201,160,61,0.12));
        }

        .sidebar .nav-item .nav-link .nav-icon-chip i {
            color: rgba(255, 255, 255, 0.85) !important;
            font-size: 0.73rem;
            transition: color 0.22s ease;
        }

        .sidebar .nav-item.active .nav-link .nav-icon-chip i {
            color: var(--islamic-gold) !important;
        }

        /* === SIDEBAR HEADING: Enhanced === */
        .sidebar-heading {
            color: rgba(201, 160, 61, 0.7) !important;
            font-size: 0.6rem !important;
            letter-spacing: 2.5px !important;
            padding: 14px 20px 5px !important;
        }

        .sidebar-heading i {
            color: rgba(201, 160, 61, 0.5) !important;
            font-size: 0.55rem !important;
        }

        /* === SIDEBAR DIVIDER: Subtler === */
        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.06) !important;
            margin: 4px 16px !important;
        }

        /* === SIDEBAR BADGES: Smaller, rounder === */
        .sidebar .badge {
            font-size: 0.55rem !important;
            padding: 2px 6px !important;
            border-radius: 20px;
        }

        .sidebar .badge-secondary {
            background: rgba(255,255,255,0.13) !important;
            color: rgba(255,255,255,0.5) !important;
        }

        /* === TOPBAR: Premium white clean look === */
        .topbar {
            background: #ffffff !important;
            border-bottom: 1.5px solid rgba(201, 160, 61, 0.18) !important;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05) !important;
        }

        /* === TOPBAR: Live Clock Widget === */
        .topbar-clock-widget {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding: 5px 12px;
            background: linear-gradient(135deg, rgba(30,90,58,0.06), rgba(10,46,31,0.03));
            border-radius: 10px;
            border: 1px solid rgba(30, 90, 58, 0.12);
            margin-right: 12px;
            cursor: default;
            flex-shrink: 0;
            user-select: none;
        }

        .topbar-clock-time {
            font-size: 1.0rem;
            font-weight: 800;
            color: var(--islamic-green);
            line-height: 1.15;
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.5px;
        }

        .topbar-clock-date {
            font-size: 0.56rem;
            color: #a0a0a0;
            font-weight: 500;
            margin-top: 1px;
        }

        /* === TOPBAR: Search Pill Shape === */
        .navbar-search .form-control {
            border-radius: 20px 0 0 20px !important;
            border: 1.5px solid rgba(30, 90, 58, 0.15) !important;
            background: #f5f7f5 !important;
            transition: all 0.25s ease;
            font-size: 0.83rem;
        }

        .navbar-search .form-control:focus {
            border-color: rgba(30, 90, 58, 0.4) !important;
            background: #fff !important;
            box-shadow: 0 0 0 3px rgba(30, 90, 58, 0.07) !important;
        }

        .navbar-search .btn-primary {
            border-radius: 0 20px 20px 0 !important;
        }

        /* === TOPBAR: Prayer Time Pill === */
        .topbar-prayer-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: linear-gradient(135deg, var(--islamic-dark), var(--islamic-green));
            color: #fff;
            border-radius: 20px;
            padding: 5px 13px 5px 8px;
            font-size: 0.7rem;
            font-weight: 600;
            box-shadow: 0 2px 10px rgba(30, 90, 58, 0.28);
            cursor: default;
            white-space: nowrap;
            user-select: none;
        }

        .topbar-prayer-pill .pill-icon {
            width: 22px;
            height: 22px;
            background: rgba(255, 255, 255, 0.18);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.62rem;
            flex-shrink: 0;
        }

        .topbar-prayer-pill .pill-label {
            color: rgba(255,255,255,0.7);
            font-size: 0.59rem;
            font-weight: 400;
        }

        .topbar-prayer-pill .pill-time {
            color: var(--islamic-gold);
            font-weight: 700;
        }

        /* === TOPBAR: User Avatar Hover === */
        .topbar .img-profile {
            box-shadow: 0 2px 8px rgba(201, 160, 61, 0.35) !important;
            transition: transform 0.22s ease, box-shadow 0.22s ease !important;
        }

        .topbar .img-profile:hover {
            transform: scale(1.08) !important;
            box-shadow: 0 4px 14px rgba(201, 160, 61, 0.5) !important;
        }

        /* === DROPDOWN MENUS: Rounded, elevated === */
        .dropdown-menu {
            border-radius: 14px !important;
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.12) !important;
            border: 1px solid rgba(0,0,0,0.06) !important;
        }

        .dropdown-item {
            border-radius: 8px;
            margin: 1px 4px;
            padding: 8px 12px !important;
            font-size: 0.85rem !important;
        }

        .dropdown-list .dropdown-item .icon-circle {
            border-radius: 50%;
        }

        /* === CARDS: Smoother elevation === */
        .card {
            border-radius: 16px !important;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s ease !important;
        }

        .card:hover {
            transform: translateY(-4px) !important;
        }

        .card-header {
            border-radius: 16px 16px 0 0 !important;
        }

        /* === MODAL: More rounded === */
        .modal-content {
            border-radius: 18px !important;
            box-shadow: 0 20px 60px rgba(0,0,0,0.18) !important;
        }

        .modal-header {
            border-radius: 18px 18px 0 0 !important;
        }

        /* === RESPONSIVE: Hide topbar extras on mobile === */
        @media (max-width: 767px) {
            .topbar-clock-widget,
            .topbar-prayer-pill { display: none !important; }
        }
    </style>

    <!-- Additional CSS -->
    @stack('styles')
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/home') }}">
                <div class="sidebar-brand-icon">
                    <img src="{{ $logoUrl }}"
                        alt="Logo" class="img-fluid" style="max-width: 50px; max-height: 50px; object-fit: contain;"
                        onerror="this.onerror=null; this.src='{{ asset('img/logo.png') }}';">
                </div>
                <div class="sidebar-brand-text mx-2">
                    <small>{{ Str::limit($appName, 15) }}</small>
                </div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('home') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            @auth
            @php
            $rawRole = optional(auth()->user()->role)->name ?? '';
            $roleName = strtolower(trim($rawRole));

            // Fallback jika akun bendahara di database masih terpasang role_id lama
            if ($roleName !== 'bendahara' && $roleName !== 'admin') {
                $userEmail = strtolower(auth()->user()->email ?? '');
                $userName = strtolower(auth()->user()->name ?? '');
                if (str_contains($userEmail, 'bendahara') || str_contains($userName, 'bendahara')) {
                    $roleName = 'bendahara';
                }
            }
            @endphp

            {{-- ============================================
                 SIDEBAR USER PANEL — Islamic Material Design 3
                 ============================================ --}}
            <div class="sidebar-user-panel">
                <div class="sup-avatar">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="sup-info">
                    <div class="sup-name">{{ Auth::user()->name ?? 'Pengguna' }}</div>
                    <div class="sup-role">
                        <span class="sup-role-dot"></span>
                        @if($roleName === 'admin') ⚙ Administrator
                        @elseif($roleName === 'petugas') 👤 Operator
                        @elseif($roleName === 'bendahara') 💰 Bendahara
                        @else {{ ucfirst($roleName ?: 'Pengguna') }}
                        @endif
                    </div>
                </div>
            </div>

            @if ($roleName === 'admin')
            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                <i class="fas fa-mosque me-1"></i> Manajemen Masjid
            </div>

            <!-- Nav Item - Jadwal Sholat -->
            <li class="nav-item {{ request()->routeIs('jadwal_sholat.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('jadwal_sholat.index') }}">
                    <i class="fas fa-fw fa-clock"></i>
                    <span>Jadwal Sholat</span>
                </a>
            </li>

            <!-- Nav Item - Sholat Jumat -->
            <li class="nav-item {{ request()->routeIs('sholat_jumat.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('sholat_jumat.index') }}">
                    <i class="fas fa-fw fa-calendar-alt"></i>
                    <span>Sholat Jumat</span>
                </a>
            </li>

            <!-- Nav Item - Sholat Idul Fitri -->
            <li class="nav-item {{ request()->routeIs('idul-fitri.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('idul-fitri.index') }}">
                    <i class="fas fa-fw fa-moon"></i>
                    <span>Idul Fitri</span>
                </a>
            </li>

            <!-- Nav Item - Sholat Idul Adha -->
            <li class="nav-item {{ request()->routeIs('idul-adha.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('idul-adha.index') }}">
                    <i class="fas fa-fw fa-drumstick-bite"></i>
                    <span>Idul Adha</span>
                </a>
            </li>

            <!-- Nav Item - Pengumuman -->
            <li class="nav-item {{ request()->routeIs('pengumuman.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('pengumuman.index') }}">
                    <i class="fas fa-fw fa-bullhorn"></i>
                    <span>Pengumuman</span>
                </a>
            </li>

            <!-- Nav Item - Agenda Kajian -->
            <li class="nav-item {{ request()->routeIs('agenda_kajian.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('agenda_kajian.index') }}">
                    <i class="fas fa-fw fa-book-open"></i>
                    <span>Agenda Kajian</span>
                </a>
            </li>

            <!-- Nav Item - Slide Informasi -->
            <li class="nav-item {{ request()->routeIs('slides.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('slides.index') }}">
                    <i class="fas fa-fw fa-images"></i>
                    <span>Slide Informasi</span>
                </a>
            </li>

            <!-- Nav Item - Keuangan -->
            <li class="nav-item {{ request()->routeIs('keuangan.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('keuangan.index') }}">
                    <i class="fas fa-fw fa-hand-holding-heart"></i>
                    <span>Keuangan</span>
                </a>
            </li>

            <!-- Nav Item - Kas Ambulance -->
            <li class="nav-item {{ request()->routeIs('ambulance.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('ambulance.index') }}">
                    <i class="fas fa-fw fa-ambulance"></i>
                    <span>Kas Ambulance</span>
                </a>
            </li>

            <!-- Nav Item - Penggalangan Infaq -->
            <li class="nav-item {{ request()->routeIs('program-infaq.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('program-infaq.index') }}">
                    <i class="fas fa-fw fa-donate"></i>
                    <span>Penggalangan Infaq</span>
                </a>
            </li>

            <!-- Nav Item - QRIS -->
            <li class="nav-item {{ request()->routeIs('qris.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('qris.index') }}">
                    <i class="fas fa-fw fa-qrcode"></i>
                    <span>QRIS Donasi</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                <i class="fas fa-cog"></i> Pengaturan Sistem
            </div>

            <!-- Nav Item - Auto Update -->
            <li class="nav-item {{ request()->routeIs('auto_update.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('auto_update.index') }}">
                    <i class="fas fa-fw fa-sync-alt"></i>
                    <span>Auto-Update Jadwal</span>
                    @if($setting->auto_update_jadwal ?? false)
                    <span class="badge badge-success ml-2" style="font-size: 9px;">AKTIF</span>
                    @else
                    <span class="badge badge-secondary ml-2" style="font-size: 9px;">NONAKTIF</span>
                    @endif
                </a>
            </li>

            <!-- Nav Item - Rotasi Halaman -->
            <li class="nav-item {{ request()->routeIs('rotation.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('rotation.index') }}">
                    <i class="fas fa-fw fa-exchange-alt"></i>
                    <span>Rotasi Halaman TV</span>
                    @if($setting->rotation_enabled ?? true)
                    <span class="badge badge-success ml-2" style="font-size: 9px;">AKTIF</span>
                    @else
                    <span class="badge badge-secondary ml-2" style="font-size: 9px;">NONAKTIF</span>
                    @endif
                </a>
            </li>

            <!-- Nav Item - Pengaturan Aplikasi -->
            <li class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('settings.edit') }}">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Pengaturan Aplikasi</span>
                </a>
            </li>

            <!-- Nav Item - Kelola Akun -->
            <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('users.index') }}">
                    <i class="fas fa-fw fa-users-cog"></i>
                    <span>Kelola Akun</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                <i class="fas fa-chart-line"></i> Laporan & Export
            </div>

            <!-- Nav Item - Export Data -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseExport"
                    aria-expanded="true" aria-controls="collapseExport">
                    <i class="fas fa-fw fa-download"></i>
                    <span>Export Data</span>
                </a>
                <div id="collapseExport" class="collapse" aria-labelledby="headingExport"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Pilih Data:</h6>
                        <a class="collapse-item" href="{{ route('export.jadwal-sholat') }}">
                            <i class="fas fa-clock text-primary"></i> Jadwal Sholat
                        </a>
                        <a class="collapse-item" href="{{ route('export.pengumuman') }}">
                            <i class="fas fa-bullhorn text-warning"></i> Pengumuman
                        </a>
                        <a class="collapse-item" href="{{ route('export.keuangan') }}">
                            <i class="fas fa-hand-holding-heart text-success"></i> Keuangan
                        </a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Laporan Keuangan -->
            <li class="nav-item {{ request()->routeIs('laporan.keuangan*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('laporan.keuangan') }}">
                    <i class="fas fa-fw fa-chart-bar"></i>
                    <span>Laporan Keuangan</span>
                </a>
            </li>

            @endif

            @if ($roleName === 'petugas')
            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                <i class="fas fa-tasks"></i> Menu Petugas / Operator
            </div>

            <!-- Nav Item - Jadwal Sholat -->
            <li class="nav-item {{ request()->routeIs('jadwal_sholat.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('jadwal_sholat.index') }}">
                    <i class="fas fa-fw fa-clock"></i>
                    <span>Jadwal Sholat</span>
                </a>
            </li>

            <!-- Nav Item - Sholat Jumat -->
            <li class="nav-item {{ request()->routeIs('sholat_jumat.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('sholat_jumat.index') }}">
                    <i class="fas fa-fw fa-calendar-alt"></i>
                    <span>Sholat Jumat</span>
                </a>
            </li>

            <!-- Nav Item - Sholat Idul Fitri -->
            <li class="nav-item {{ request()->routeIs('idul-fitri.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('idul-fitri.index') }}">
                    <i class="fas fa-fw fa-moon"></i>
                    <span>Idul Fitri</span>
                </a>
            </li>

            <!-- Nav Item - Sholat Idul Adha -->
            <li class="nav-item {{ request()->routeIs('idul-adha.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('idul-adha.index') }}">
                    <i class="fas fa-fw fa-drumstick-bite"></i>
                    <span>Idul Adha</span>
                </a>
            </li>

            <!-- Nav Item - Pengumuman -->
            <li class="nav-item {{ request()->routeIs('pengumuman.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('pengumuman.index') }}">
                    <i class="fas fa-fw fa-bullhorn"></i>
                    <span>Pengumuman</span>
                </a>
            </li>

            <!-- Nav Item - Agenda Kajian -->
            <li class="nav-item {{ request()->routeIs('agenda_kajian.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('agenda_kajian.index') }}">
                    <i class="fas fa-fw fa-book-open"></i>
                    <span>Agenda Kajian</span>
                </a>
            </li>

            <!-- Nav Item - Slide Informasi -->
            <li class="nav-item {{ request()->routeIs('slides.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('slides.index') }}">
                    <i class="fas fa-fw fa-images"></i>
                    <span>Slide Informasi</span>
                </a>
            </li>

            <!-- Nav Item - Rotasi Halaman TV -->
            <li class="nav-item {{ request()->routeIs('rotation.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('rotation.index') }}">
                    <i class="fas fa-fw fa-exchange-alt"></i>
                    <span>Rotasi Halaman TV</span>
                    @if($setting->rotation_enabled ?? true)
                    <span class="badge badge-success ml-2" style="font-size: 9px;">AKTIF</span>
                    @else
                    <span class="badge badge-secondary ml-2" style="font-size: 9px;">NONAKTIF</span>
                    @endif
                </a>
            </li>

            <!-- Nav Item - Teks Berjalan TV (Petugas/Operator) -->
            <li class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('settings.edit') }}">
                    <i class="fas fa-fw fa-bullhorn" style="color: #ffd700;"></i>
                    <span>Teks Berjalan TV</span>
                </a>
            </li>
            @endif

            @if ($roleName === 'bendahara')
            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                <i class="fas fa-wallet"></i> Menu Bendahara
            </div>

            <!-- Nav Item - Keuangan Kas -->
            <li class="nav-item {{ request()->routeIs('keuangan.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('keuangan.index') }}">
                    <i class="fas fa-fw fa-hand-holding-heart"></i>
                    <span>Buku Kas & Transaksi</span>
                </a>
            </li>

            <!-- Nav Item - Buku Kas Ambulance -->
            <li class="nav-item {{ request()->routeIs('ambulance.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('ambulance.index') }}">
                    <i class="fas fa-fw fa-ambulance"></i>
                    <span>Buku Kas Ambulance</span>
                </a>
            </li>

            <!-- Nav Item - Penggalangan Infaq -->
            <li class="nav-item {{ request()->routeIs('program-infaq.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('program-infaq.index') }}">
                    <i class="fas fa-fw fa-donate"></i>
                    <span>Penggalangan Infaq</span>
                </a>
            </li>

            <!-- Nav Item - Laporan Keuangan -->
            <li class="nav-item {{ request()->routeIs('laporan.keuangan*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('laporan.keuangan') }}">
                    <i class="fas fa-fw fa-chart-bar"></i>
                    <span>Laporan & Rekap Kas</span>
                </a>
            </li>

            <!-- Nav Item - Export Data Keuangan -->
            <li class="nav-item {{ request()->routeIs('export.keuangan') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('export.keuangan') }}">
                    <i class="fas fa-fw fa-file-excel"></i>
                    <span>Export Excel Kas</span>
                </a>
            </li>
            @endif

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                <i class="fas fa-user"></i> Akun
            </div>

            <!-- Nav Item - Profile -->
            <li class="nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('profile') }}">
                    <i class="fas fa-fw fa-user"></i>
                    <span>Profil Saya</span>
                </a>
            </li>

            <!-- Nav Item - About -->
            <li class="nav-item {{ request()->routeIs('about') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('about') }}">
                    <i class="fas fa-fw fa-info-circle"></i>
                    <span>Tentang Aplikasi</span>
                </a>
            </li>

            @endauth

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- ==========================================
                         Live Clock Widget — Islamic Material Design 3
                         ========================================== -->
                    <div class="topbar-clock-widget d-none d-sm-flex" id="topbarClockWidget" title="Jam & Tanggal">
                        <div class="topbar-clock-time" id="liveClockTime">--:--:--</div>
                        <div class="topbar-clock-date" id="liveClockDate">Memuat tanggal...</div>
                    </div>

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Cari..."
                                aria-label="Search">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- ==============================
                             Prayer Time Pill (Desktop)
                             ============================== -->
                        <li class="nav-item d-none d-lg-flex align-items-center">
                            <div class="topbar-prayer-pill" id="topbarPrayerPill" title="Waktu Sholat Berikutnya">
                                <span class="pill-icon"><i class="fas fa-mosque"></i></span>
                                <span>
                                    <span class="pill-label">Sholat </span>
                                    <span class="pill-time" id="topbarPrayerName">–</span>
                                </span>
                            </div>
                        </li>

                        <!-- Nav Item - Search Dropdown -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Cari...">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown">
                                <i class="fas fa-bell fa-fw"></i>
                            </a>
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in">
                                <h6 class="dropdown-header bg-gradient-primary text-white">
                                    <i class="fas fa-bell me-2"></i> Notifikasi
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-clock text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">Waktu Sholat</div>
                                        <span class="font-weight-bold">Waktu Dzuhur akan segera masuk</span>
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500 font-weight-bold py-2" href="{{ route('notifications.index') }}"> Lihat Semua</a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown">
                                <span
                                    class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
                                <div class="img-profile rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 32px; height: 32px;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                                <a class="dropdown-item" href="{{ route('profile') }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-primary"></i>
                                    Profil
                                </a>
                                <a class="dropdown-item" href="{{ route('settings.edit') }}">
                                    <i class="fas fa-cog fa-sm fa-fw mr-2 text-primary"></i>
                                    Pengaturan
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-danger"></i>
                                    Keluar
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid main-content">
                    @yield('main-content')
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <div class="mb-1">
                            <i class="fas fa-mosque me-1"></i>
                            <i class="fas fa-star-and-crescent me-1"></i>
                            <i class="fas fa-quran me-1"></i>
                        </div>
                        @if(isset($setting['footer']))
                        {!! $setting['footer'] !!}
                        @else
                        <span>
                            <i class="far fa-copyright"></i> {{ now()->year }}
                            <a href="https://wa.me/628179851011" target="_blank" style="text-decoration: none;">
                                Copyright &copy; 2026 Masjid Al-Jihad Dev. System
                            </a>
                        </span>
                        <div class="mt-1">
                            <small class="opacity-75">
                                <i class="fas fa-code-branch"></i> Versi 3.0 |
                                <i class="fas fa-sync-alt"></i> Auto-Update Aktif |
                                <i class="fas fa-tv"></i> TV Masjid Digital
                            </small>
                        </div>
                        @endif
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Islamic Corner Decoration -->
    <div class="islamic-corner corner-br"></div>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-sign-out-alt me-2"></i> Konfirmasi Keluar
                    </h5>
                    <button class="close" type="button" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-question-circle fa-3x text-warning mb-3"></i>
                    <p>Apakah Anda yakin ingin keluar dari sistem?</p>
                    <small class="text-muted">Sesi Anda akan berakhir</small>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <a class="btn btn-danger" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Keluar
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom Scripts -->
    <script>
        /* ================================================
           🕌 LIVE CLOCK — Topbar Widget
           ================================================ */
        (function() {
            var DAYS   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            var MONTHS = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

            function updateClock() {
                var now = new Date();
                var hh  = String(now.getHours()).padStart(2, '0');
                var mm  = String(now.getMinutes()).padStart(2, '0');
                var ss  = String(now.getSeconds()).padStart(2, '0');
                var timeEl = document.getElementById('liveClockTime');
                var dateEl = document.getElementById('liveClockDate');
                if (timeEl) timeEl.textContent = hh + ':' + mm + ':' + ss;
                if (dateEl) dateEl.textContent  = DAYS[now.getDay()] + ', ' + now.getDate() + ' ' + MONTHS[now.getMonth()] + ' ' + now.getFullYear();
            }

            updateClock();
            setInterval(updateClock, 1000);
        })();

        /* ================================================
           🕌 ICON CHIP — Auto-Wrap Sidebar Icons
           Membungkus <i> dalam .nav-icon-chip secara otomatis
           ================================================ */
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.sidebar .nav-item .nav-link').forEach(function(link) {
                var icon = link.querySelector(':scope > i.fas, :scope > i.far, :scope > i.fab');
                if (icon) {
                    var chip = document.createElement('span');
                    chip.className = 'nav-icon-chip';
                    link.insertBefore(chip, icon);
                    chip.appendChild(icon);
                }
            });
        });

        /* ================================================
           🕌 PRAYER PILL — Fetch sholat berikutnya
           ================================================ */
        (function() {
            function updatePrayerPill() {
                if (typeof $ === 'undefined') return;
                $.ajax({
                    url: '/prayer-mode/status',
                    method: 'GET',
                    timeout: 5000,
                    success: function(data) {
                        var nameEl = document.getElementById('topbarPrayerName');
                        if (!nameEl || !data) return;
                        // Support berbagai format response
                        var name = (data.next_prayer  && data.next_prayer.name)
                                || (data.nextPrayer   && data.nextPrayer.name)
                                || null;
                        if (name) nameEl.textContent = name;
                    },
                    error: function() { /* silent fail — tidak ganggu UX */ }
                });
            }

            // Delay 4 detik agar tidak ganggu page load awal
            setTimeout(function() {
                if (document.getElementById('topbarPrayerName')) {
                    updatePrayerPill();
                    setInterval(updatePrayerPill, 60000);
                }
            }, 4000);
        })();

        // Auto-hide alert setelah 5 detik
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Add fade animation to cards
            $('.card').addClass('fade-in');
        });

        // Konfirmasi sebelum menghapus dengan SweetAlert
        function confirmDelete(event, formId, itemName = 'data') {
            event.preventDefault();
            Swal.fire({
                title: 'Hapus ' + itemName + '?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                background: '#fff',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }

        // Format Rupiah
        function formatRupiah(angka) {
            if (!angka) return 'Rp 0';
            var number_string = angka.toString().replace(/[^,\d]/g, ''),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return 'Rp ' + rupiah;
        }

        // Show success notification
        function showSuccess(message) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: message,
                timer: 3000,
                showConfirmButton: false,
                background: '#fff'
            });
        }

        // Show error notification
        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: message,
                confirmButtonColor: '#1e5a3a'
            });
        }

        // Update waktu sholat real-time
        function updatePrayerTimes() {
            $.ajax({
                url: '/api/prayer-times',
                method: 'GET',
                success: function(data) {
                    if (data.nextPrayer) {
                        $('#nextPrayerName').text(data.nextPrayer.name);
                        $('#nextPrayerTime').text(data.nextPrayer.time);
                        $('#countdown').text(data.countdown);
                    }
                }
            });
        }

        // Update every minute
        if (typeof updatePrayerTimes === 'function') {
            setInterval(updatePrayerTimes, 60000);
        }
    </script>

    <!-- Flatpickr JS (Sistem 24 Jam) -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof flatpickr !== 'undefined') {
                flatpickr(".timepicker-24, input[type=time], #waktu", {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    allowInput: true,
                    minuteIncrement: 1
                });
            }
        });
    </script>

    <!-- Stack untuk scripts tambahan -->
    @stack('scripts')
</body>

</html>