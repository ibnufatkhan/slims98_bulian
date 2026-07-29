<?php
# @Author: Waris Agung Widodo <user>
# @Date:   2018-01-23T11:27:04+07:00
# @Email:  ido.alit@gmail.com
# @Filename: _home.php
# Customized for Perpustakaan Soepardjo Roestam (BSKDN)
?>

<style>
    /* Global & Hero */
    #hero-section { background: #f8fbfe; padding: 80px 0 60px 0; text-align: center; }
    .hero-logo-main { max-width: 400px; margin-bottom: 20px; }
    .hero-title { color: #124265; font-weight: 700; font-size: 2.5rem; margin-bottom: 50px; }
    
    /* Icon Box Grid */
    .icon-box-wrapper {
        background: #fff; padding: 30px 20px; transition: 0.3s; border-radius: 12px;
        box-shadow: 0px 5px 25px rgba(0, 0, 0, 0.08); height: 100%;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        border-bottom: 4px solid transparent;
    }
    .icon-box-wrapper:hover { transform: translateY(-10px); border-color: #2487ce; }
    .icon-box-wrapper .icon i { font-size: 40px; color: #2487ce; line-height: 1; }
    .icon-box-wrapper .title { margin-top: 15px; font-weight: 700; font-size: 16px; }
    .icon-box-wrapper .title a { color: #124265; text-decoration: none; }

    /* Section Styling */
    .section-bg-white { background-color: #ffffff; padding: 60px 0; }
    .section-bg-light { background-color: #f8fbfe; padding: 60px 0; }
    .section-title h2 { font-size: 32px; font-weight: bold; color: #124265; text-align: center; margin-bottom: 40px; text-transform: uppercase; }
    .subtitle-section { color: #777; font-size: 14px; display: block; margin-top: 5px; }

    /* Struktur Organisasi (Team) Original Style */
    .member { margin-bottom: 20px; background: #fff; border-radius: 5px; overflow: hidden; box-shadow: 0px 2px 15px rgba(0, 0, 0, 0.1); width: 100%; }
    .member .member-img { position: relative; overflow: hidden; }
    .member .member-img img { width: 100%; transition: 0.5s; }
    .member .member-info { padding: 25px 15px; text-align: center; }
    .member .member-info h4 { font-weight: 700; margin-bottom: 5px; font-size: 18px; color: #124265; }
    .member .member-info span { display: block; font-size: 13px; font-weight: 400; color: #aaaaaa; }
    .member .social {
        position: absolute; left: 0; bottom: 30px; right: 0; opacity: 0; transition: ease-in-out 0.3s; text-align: center;
    }
    .member:hover .social { opacity: 1; bottom: 15px; }
    .member .social a {
        transition: color 0.3s; color: #124265; margin: 0 3px; border-radius: 50px;
        width: 36px; height: 36px; background: rgba(36, 135, 206, 0.8); display: inline-flex;
        align-items: center; justify-content: center; color: #fff;
    }
    .member .social a:hover { background: #2487ce; }
    
   

    /* 7 hero icons in one neat row */
    .hero-icon-row {
        display: flex;
        flex-wrap: nowrap;
        gap: 12px;
        justify-content: center;
        align-items: stretch;
    }
    .hero-icon-row > .hero-icon-col {
        flex: 1 1 0;
        min-width: 0;
        max-width: none;
    }
    .hero-icon-row .icon-box-wrapper {
        padding: 22px 10px;
    }
    .hero-icon-row .icon-box-wrapper .icon i { font-size: 34px; }
    .hero-icon-row .icon-box-wrapper .title { font-size: 13px; margin-top: 12px; }
    @media (max-width: 991px) {
        .hero-icon-row { flex-wrap: wrap; }
        .hero-icon-row > .hero-icon-col { flex: 1 1 22%; max-width: 25%; }
    }
    @media (max-width: 575px) {
        .hero-icon-row > .hero-icon-col { flex: 1 1 40%; max-width: 50%; }
    }

    /* Popular & new collections: force single row */
    .collection.collection-single-row {
        display: flex !important;
        flex-wrap: nowrap !important;
        overflow-x: auto;
        gap: 0;
        -webkit-overflow-scrolling: touch;
    }
    .collection.collection-single-row > div {
        flex: 1 1 0;
        min-width: 140px;
        max-width: none;
        width: auto !important;
        padding-right: 1rem;
        padding-bottom: 0.5rem;
    }

</style>


<section id="hero-section">
    <div class="container" data-aos="fade-up">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <img src="template/default/assets/one/img/logo.png" alt="Logo BSKDN" class="hero-logo-main img-fluid">
                <h1 class="hero-title">Perpustakaan Soepardjo Roestam</h1>
            </div>
        </div>

        <div class="hero-icon-row">
            <div class="hero-icon-col">
                <div class="icon-box-wrapper">
                    <div class="icon"><i class="ri-book-2-fill"></i></div>
                    <h4 class="title"><a href="#cari">Buku</a></h4>
                </div>
            </div>
            <div class="hero-icon-col">
                <div class="icon-box-wrapper">
                    <div class="icon"><i class="ri-git-repository-private-fill"></i></div>
                    <h4 class="title"><a href="https://bskdnlib.perpustakaandigital.com/" target="_blank" rel="noopener">E-Book</a></h4>
                </div>
            </div>
            <div class="hero-icon-col">
                <div class="icon-box-wrapper">
                    <div class="icon"><i class="ri-book-open-line"></i></div>
                    <h4 class="title"><a href="http://jurnal.kemendagri.go.id/" target="_blank" rel="noopener">Jurnal</a></h4>
                </div>
            </div>
            <div class="hero-icon-col">
                <div class="icon-box-wrapper">
                    <div class="icon"><i class="ri-camera-2-fill"></i></div>
                    <h4 class="title"><a href="https://www.youtube.com/results?search_query=bskdn" target="_blank" rel="noopener">Media</a></h4>
                </div>
            </div>
            <div class="hero-icon-col">
                <div class="icon-box-wrapper">
                    <div class="icon"><i class="ri-headphone-fill"></i></div>
                    <h4 class="title"><a href="https://www.youtube.com/@AudiobookBSKDNKemendagri" target="_blank" rel="noopener">Audiobook</a></h4>
                </div>
            </div>
            <div class="hero-icon-col">
                <div class="icon-box-wrapper">
                    <div class="icon"><i class="ri-git-repository-fill"></i></div>
                    <h4 class="title"><a href="https://perpustakaan.kemendagri.go.id/arsip/" target="_blank" rel="noopener">Repository</a></h4>
                </div>
            </div>
            <div class="hero-icon-col">
                <div class="icon-box-wrapper">
                    <div class="icon"><i class="ri-newspaper-fill"></i></div>
                    <h4 class="title"><a href="https://pubhtml5.com/homepage/wvfob/" target="_blank" rel="noopener">ePaper</a></h4>
                </div>
            </div>
        </div>

    </div>
</section>



<main id="main">
    <section class="section-bg-white" id="cari">
        <div class="container">
            <div class="section-title"><h2>Cari Koleksi</h2></div>
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <?php include '_search-form.php'; ?>
                </div>
            </div>
        </div>
    </section>

<section class="section-bg-light border-top" id="subject-selection">
        <div class="container" data-aos="fade-up">
            <div class="section-title">
                <h2 style="font-size: 24px; font-weight: 300; color: #777; text-transform: none;">Pilih subjek yang menarik bagi Anda</h2>
            </div>
            
            <div class="row g-4 justify-content-center">
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="index.php?callnumber=8&search=search" class="text-decoration-none">
                        <div class="icon-box-wrapper py-5">
                            <div class="icon mb-3">
                                <img src="template/default/assets/images/buku.jpg" alt="Kesusastraan" style="width: 60px;">
                            </div>
                            <span class="text-secondary">Kesusastraan</span>
                        </div>
                    </a>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <a href="index.php?callnumber=3&search=search" class="text-decoration-none">
                        <div class="icon-box-wrapper py-5">
                            <div class="icon mb-3">
                                <img src="template/default/assets/images/sosial.png" alt="Ilmu-ilmu Sosial" style="width: 60px;">
                            </div>
                            <span class="text-secondary">Ilmu-ilmu Sosial</span>
                        </div>
                    </a>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <a href="index.php?callnumber=6&search=search" class="text-decoration-none">
                        <div class="icon-box-wrapper py-5">
                            <div class="icon mb-3">
                                <img src="template/default/assets/images/sains.png" alt="Ilmu Fotoliasi" style="width: 60px;">
                            </div>
                            <span class="text-secondary">Ilmu Fotoliasi</span>
                        </div>
                    </a>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <a href="index.php?callnumber=7&search=search" class="text-decoration-none">
                        <div class="icon-box-wrapper py-5">
                            <div class="icon mb-3">
                                <img src="template/default/assets/images/kesenian.png" alt="Kesenian" style="width: 60px;">
                            </div>
                            <span class="text-center text-secondary" style="font-size: 14px;">Kesenian, Hiburan, dan Olahraga</span>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="javascript:void(0)" class="d-flex flex-column" data-toggle="modal" data-target="#exampleModal">
                        <div class="icon-box-wrapper py-5">
                            <div class="icon mb-3">
                                <i class="ri-grid-fill" style="font-size: 50px; color: #333;"></i>
                            </div>
                            <span class="text-secondary">lihat lainnya..</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <div id="slims-home">
                <section class="section-bg-light border-top">
            <div class="container">
                <h4 class="mb-4 text-primary font-weight-bold">
                    Yang populer di antara koleksi kami                    <small class="subtitle-section">Our library's line of collection that have been favoured by our users.</small>
                </h4>
                <slims-group-subject url="index.php?p=api/subject/popular"></slims-group-subject>
                <slims-collection url="index.php?p=api/biblio/popular"></slims-collection>
            </div>
        </section>
        
                <section class="section-bg-white border-top">
            <div class="container">
                <h4 class="mb-4 text-primary font-weight-bold">
                    Koleksi baru dan diperbarui                    <small class="subtitle-section">These are new collections list fresh from our processing oven.</small>
                </h4>
                <slims-group-subject url="index.php?p=api/subject/latest"></slims-group-subject>
                <slims-collection url="index.php?p=api/biblio/latest"></slims-collection>
            </div>
        </section>
            </div>

    <section id="struktur_organisasi" class="section-bg-light border-top">
        <div class="container" data-aos="fade-up">
            <div class="section-title"><h2>Struktur Organisasi</h2></div>
            
            <div class="row justify-content-center mb-4">
                <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="100">
                    <div class="member">
                        <div class="member-img">
                            <img src="template/default/assets/one/img/photo/ajiNurCahyo2.jpg" class="img-fluid" alt="">
                            <div class="social"><a href=""><i class="bi bi-instagram"></i></a><a href=""><i class="bi bi-linkedin"></i></a></div>
                        </div>
                        <div class="member-info">
                            <h4>Aji Nur Cahyo</h4>
                            <span>Kepala Subbagian Perpustakaan, Informasi Dan Dokumentasi</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                                <div class="col-lg-2 col-md-4 d-flex align-items-stretch mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="member">
                        <div class="member-img">
                            <img src="template/default/assets/one/img/photo/Bungaran Damanik - Pustakawan2.jpg" class="img-fluid" alt="">
                        </div>
                        <div class="member-info">
                            <h4>Bungaran D</h4>
                            <span>Pustakawan</span>
                        </div>
                    </div>
                </div>
                                <div class="col-lg-2 col-md-4 d-flex align-items-stretch mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="member">
                        <div class="member-img">
                            <img src="template/default/assets/one/img/photo/Moh. Andi Azhar - Pustakawan2.jpg" class="img-fluid" alt="">
                        </div>
                        <div class="member-info">
                            <h4>Moh. Andi Azhar</h4>
                            <span>Pustakawan</span>
                        </div>
                    </div>
                </div>
                                <div class="col-lg-2 col-md-4 d-flex align-items-stretch mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="member">
                        <div class="member-img">
                            <img src="template/default/assets/one/img/photo/Nyimas Aisyah Yuniati - Pustakawan2.jpg" class="img-fluid" alt="">
                        </div>
                        <div class="member-info">
                            <h4>Nyimas A Y</h4>
                            <span>Pustakawan</span>
                        </div>
                    </div>
                </div>
                                <div class="col-lg-2 col-md-4 d-flex align-items-stretch mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="member">
                        <div class="member-img">
                            <img src="template/default/assets/one/img/photo/Frisca Natalia - Pengelola Bahan Pustaka2.jpeg" class="img-fluid" alt="">
                        </div>
                        <div class="member-info">
                            <h4>Frisca N H</h4>
                            <span>Penyusun Basan Informasi dan Publikasi</span>
                        </div>
                    </div>
                </div>
                                <div class="col-lg-2 col-md-4 d-flex align-items-stretch mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="member">
                        <div class="member-img">
                            <img src="template/default/assets/one/img/photo/Shinta Silviana - Pustakawan2.jpg" class="img-fluid" alt="">
                        </div>
                        <div class="member-info">
                            <h4>Shinta S</h4>
                            <span>Pustakawan</span>
                        </div>
                    </div>
                </div>
                            </div>

            <div class="row justify-content-center">
                <div class="col-lg-2 col-md-4 d-flex align-items-stretch mb-4">
                    <div class="member">
                        <div class="member-img"><img src="template/default/assets/one/img/photo/Sandy Wahyu Febryanto - Tenaga Publikasi Ilmiah3.jpg" class="img-fluid"></div>
                        <div class="member-info"><h4>Sandy W.F</h4><span>Tenaga Publikasi Ilmiah</span></div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 d-flex align-items-stretch mb-4">
                    <div class="member">
                        <div class="member-img"><img src="template/default/assets/one/img/photo/Fajar Haramukti - Tenaga Pendukung Publikasi.jpg" class="img-fluid"></div>
                        <div class="member-info"><h4>Fajar N H</h4><span>Tenaga Pendukung Trampil</span></div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 d-flex align-items-stretch mb-4">
                    <div class="member">
                        <div class="member-img"><img src="template/default/assets/one/img/photo/elpino.jpg" class="img-fluid"></div>
                        <div class="member-info"><h4>Elpino W</h4><span>Tenaga Pendukung Trampil</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="section-bg-white border-top">
        <div class="container">
            <div class="section-title"><h2>Lokasi & Kontak</h2></div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="rounded shadow-sm overflow-hidden mb-4" style="height: 350px;">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.5771!2d106.843!3d-6.18!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMTAnNDguMCJTIDEwNsKwNTAnMzQuOCJF!5e0!3m2!1sid!2sid!4v1620000000000" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="p-4 bg-light rounded h-100">
                        <h5><i class="bi bi-geo-alt"></i> Alamat</h5>
                        <p>Jl. Kramat Raya No.132, Jakarta Pusat, DKI Jakarta 10450</p>
                        <hr>
                        <h5><i class="bi bi-clock"></i> Waktu Layanan</h5>
                        <p>Senin - Kamis: 08:30 - 15:30<br>Jumat: 08:30 - 16:00<br>Sabtu - Minggu: Libur(TUTUP)</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
