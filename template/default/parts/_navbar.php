<style>
    #navbar-custom { display: flex; align-items: center; }
    #navbar-custom ul { 
        display: flex !important; 
        flex-direction: row !important; 
        list-style: none !important; 
        margin: 0; padding: 0; 
    }
    #navbar-custom ul li { position: relative; padding: 0 15px; }
    #navbar-custom ul li::before { content: none !important; }
    #navbar-custom a {
        text-decoration: none !important;
        color: var(--primary-color) !important;
        font-size: 15px;
        font-weight: 600;
        transition: 0.3s;
    }
    #navbar-custom a:hover { color: var(--accent-color) !important; }

    /* Dropdown Styling */
    #navbar-custom .dropdown ul {
        display: none !important;
        position: absolute;
        top: 100%; left: 0;
        background: #fff;
        box-shadow: 0px 8px 30px rgba(0,0,0,0.1);
        flex-direction: column !important;
        min-width: 200px;
        border-radius: 4px;
        padding: 10px 0 !important;
    }
    #navbar-custom .dropdown:hover > ul { display: flex !important; }
    #navbar-custom .dropdown ul li { padding: 5px 20px; }
</style>

<header id="main-header">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="index.php" class="logo">
            <img src="template/default/assets/one/img/logo.png" alt="Logo" class="logo-img">
        </a>

        <nav id="navbar-custom">
            <ul>
                <li><a href="index.php#hero">Beranda</a></li>
                <li class="dropdown"><a href="#">Profil <i class="bi bi-chevron-down"></i></a>
                    <ul>
                        <li><a href="index.php#profile">Visi & Misi</a></li>
                        <li><a href="index.php#struktur_organisasi">Struktur Organisasi</a></li>
                    </ul>
                </li>
                <li class="dropdown"><a href="#">Informasi <i class="bi bi-chevron-down"></i></a>
                    <ul>
                        <li><a href="index.php#waktu_layanan">Waktu Layanan</a></li>
                        <li><a href="index.php#tata_tertib">Tata Tertib</a></li>
                        <li><a href="index.php#peraturan">Peraturan</a></li>
                    </ul>
                </li>
                <li><a href="http://lib.litbang.kemendagri.go.id/news/">Berita</a></li>
                <li><a href="index.php#contact">Kontak</a></li>
            </ul>
        </nav>
    </div>
</header>