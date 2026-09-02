<!-- Top Navbar Partials -->
<div class="top-navbar">
    <ul class="nav-links">
        <li><a href="{{ route('admin.dashboard') }}"
                class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>
        <li><a href="{{ route('admin.control.panel') }}"
                class="{{ request()->routeIs('admin.control.panel') ? 'active' : '' }}">Control Panel</a></li>
        <li><a href="{{ route('admin.logout') }}">Logout</a></li>
    </ul>
</div>

<style>
    .top-navbar {
        box-sizing: border-box;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        max-width: 100vw;
        height: 60px;
        background: rgba(30, 30, 30, 0.8);
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(12px);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding: 0 40px;
        z-index: 20;
    }

    .nav-links {
        list-style: none;
        display: flex;
        align-items: center;
        gap: 30px;
        margin: 0;
        padding: 0;
    }

    .nav-links a {
        text-decoration: none;
        color: #b0b0b0;
        font-weight: 500;
        font-size: 15px;
        white-space: nowrap;
        transition: color 0.2s;
    }

    .nav-links a.active,
    .nav-links a:hover {
        color: #ffca7a;
        font-weight: bold;
        text-shadow: 0 0 10px rgba(255, 202, 122, 0.6);
    }

    /* Responsive: layar sedang */
    @media (max-width: 768px) {
        .top-navbar {
            padding: 0 20px;
            height: 54px;
        }

        .nav-links {
            gap: 18px;
        }

        .nav-links a {
            font-size: 13px;
        }
    }

    /* Responsive: layar kecil banget */
    @media (max-width: 480px) {
        .top-navbar {
            padding: 0 12px;
            justify-content: center;
        }

        .nav-links {
            gap: 12px;
        }

        .nav-links a {
            font-size: 12px;
        }
    }
</style>