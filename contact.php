<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config/database.php';

$success_msg = '';
$error_msg = '';
$form_data = [
    'nama' => '',
    'instansi' => '',
    'email' => '',
    'telepon' => '',
    'topik' => '',
    'pesan' => ''
];

if (isset($_GET['sent']) && $_GET['sent'] === '1') {
    $success_msg = 'Pesan Anda berhasil dikirim. Tim kami akan menghubungi Anda segera.';
}

$create_contact_sql = "CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(120) NOT NULL,
    instansi VARCHAR(160),
    email VARCHAR(160) NOT NULL,
    telepon VARCHAR(60) NOT NULL,
    topik VARCHAR(80) NOT NULL,
    pesan TEXT NOT NULL,
    ip_address VARCHAR(64),
    user_agent VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    KEY idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
@$conn->query($create_contact_sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $nama = trim($_POST['nama'] ?? '');
    $instansi = trim($_POST['instansi'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $topik = trim($_POST['topik'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');

    $form_data = [
        'nama' => $nama,
        'instansi' => $instansi,
        'email' => $email,
        'telepon' => $telepon,
        'topik' => $topik,
        'pesan' => $pesan
    ];

    if ($nama === '' || $email === '' || $telepon === '' || $topik === '' || $pesan === '') {
        $error_msg = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = 'Format email tidak valid.';
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $stmt = $conn->prepare("INSERT INTO contact_messages (nama, instansi, email, telepon, topik, pesan, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param('ssssssss', $nama, $instansi, $email, $telepon, $topik, $pesan, $ip, $ua);
            if ($stmt->execute()) {
                header('Location: contact.php?sent=1');
                exit;
            } else {
                $error_msg = 'Gagal menyimpan pesan. Silakan coba lagi.';
            }
            $stmt->close();
        } else {
            $error_msg = 'Gagal menyiapkan penyimpanan. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungi Kami - Diskominfo Karanganyar</title>
    <?php $asset_ver = @filemtime(__DIR__ . '/css/style.css') ?: time(); ?>
    <link rel="stylesheet" href="css/style.css?v=<?php echo $asset_ver; ?>">
    <link rel="stylesheet" href="css/social-icons.css">
    <style>
        .contact-topbar {
            background: #16245c;
            border-bottom: 3px solid #f58220;
            padding: 0;
            position: sticky;
            top: 0;
            z-index: 120;
        }

        .contact-topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            min-height: 44px;
        }

        .contact-socials {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .contact-top-search {
            display: flex;
            align-items: center;
            flex: 0 0 auto;
        }

        .contact-main-header {
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            border-bottom: 2px solid #f58220;
        }

        .contact-main-header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 6px 0;
            flex-wrap: wrap;
        }

        .contact-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: max-content;
        }

        .contact-hero {
            background: linear-gradient(135deg, #193a7b 0%, #2563a8 55%, #2d74bf 100%);
            color: #fff;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 24px;
            box-shadow: 0 12px 30px rgba(25,58,123,0.24);
            position: relative;
            overflow: hidden;
        }

        .contact-hero::after {
            content: '';
            position: absolute;
            right: -40px;
            top: -40px;
            width: 180px;
            height: 180px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 22px;
        }

        .contact-card {
            background: #fff;
            border-radius: 18px;
            padding: 22px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            border: 1px solid #e8f4ff;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .contact-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.11);
        }

        .contact-info-list {
            display: grid;
            gap: 12px;
            margin-top: 12px;
        }

        .contact-info-item {
            background: #f8fbff;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid #e0ecff;
        }

        .contact-form h3 {
            color: #193a7b;
            font-size: 1.28rem;
            margin-bottom: 6px;
        }

        .contact-subtitle {
            color: #5e6a85;
            margin: 0 0 14px;
            font-size: 0.95rem;
        }

        .contact-field-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .contact-form label {
            font-weight: 700;
            font-size: 0.9rem;
            color: #193a7b;
            display: block;
            margin-bottom: 8px;
        }

        .contact-form input,
        .contact-form select,
        .contact-form textarea {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #dbe5ff;
            font-size: 0.95rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .contact-form input:focus,
        .contact-form select:focus,
        .contact-form textarea:focus {
            border-color: #2d74bf;
            box-shadow: 0 0 0 3px rgba(45,116,191,0.16);
            outline: none;
        }

        .contact-form textarea {
            min-height: 140px;
            resize: vertical;
        }

        .contact-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            margin-top: 16px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #193a7b 0%, #2563a8 100%);
            color: #fff;
            border: none;
            padding: 11px 20px;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(25,58,123,0.25);
        }

        .btn-outline {
            border: 2px solid #f58220;
            color: #f58220;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-outline:hover {
            background: #f58220;
            color: #fff;
        }

        .alert-success {
            background: #e7f6ed;
            border: 1px solid #bfe7cf;
            color: #1a7f45;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 16px;
        }

        .alert-error {
            background: #fdecea;
            border: 1px solid #f5c2c7;
            color: #b4232a;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 16px;
        }

        .contact-main {
            padding: 24px 0 40px;
        }

        .contact-nav {
            background: #fff;
            border-bottom: 2px solid #f58220;
            box-shadow: 0 1px 4px rgba(0,0,0,0.03);
            position: sticky;
            top: 44px;
            z-index: 110;
        }

        .contact-nav-list {
            display: flex;
            gap: 14px;
            margin: 0;
            padding: 0;
            list-style: none;
            font-size: 0.9rem;
            font-weight: 700;
            align-items: center;
            min-height: 28px;
        }

        .contact-nav-list > li > a {
            color: #193a7b;
            text-decoration: none;
            padding: 6px 0 4px 0;
            display: inline-block;
        }

        .contact-nav-list > li > a:hover {
            color: #f58220;
        }

        .contact-top-search input {
            border: none;
            border-radius: 22px 0 0 22px;
            padding: 8px 18px;
            font-size: 1em;
            outline: none;
            width: 220px;
        }

        .contact-top-search button {
            background: #f58220;
            border: none;
            border-radius: 0 22px 22px 0;
            padding: 8px 16px;
            color: #fff;
            font-size: 1.2em;
            cursor: pointer;
        }

        .contact-main-action {
            background: linear-gradient(135deg,#f58220 0%,#fbb040 100%);
            color: #fff;
            border: none;
            padding: 7px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            font-size: 0.9rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            white-space: nowrap;
        }

        @media (max-width: 960px) {
            .contact-grid {
                grid-template-columns: 1fr;
            }

            .contact-topbar-inner {
                min-height: auto;
                padding: 8px 0;
            }

            .contact-socials {
                display: none;
            }

            .contact-top-search {
                width: 100%;
            }

            .contact-top-search form {
                width: 100%;
            }

            .contact-top-search input {
                width: 100%;
            }

            .contact-nav {
                top: 52px;
            }

            .contact-main-header-inner {
                justify-content: center;
            }

            .contact-main {
                padding-top: 18px;
            }
        }

        @media (max-width: 720px) {
            .contact-nav .container {
                overflow-x: auto;
            }

            .contact-nav .container ul {
                overflow-x: auto;
                white-space: nowrap;
                padding-bottom: 4px;
            }

            .contact-nav {
                top: 52px;
            }

            .contact-hero {
                padding: 20px;
                border-radius: 16px;
            }

            .contact-hero h1 {
                font-size: 1.55rem !important;
            }

            .contact-card {
                padding: 16px;
            }

            .contact-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-primary,
            .btn-outline,
            .contact-main-action {
                text-align: center;
                justify-content: center;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="contact-topbar">
        <div class="container contact-topbar-inner">
            <div class="contact-socials" aria-label="Media sosial">
                <a href="https://www.instagram.com/diskominfo_karanganyar?igsh=dGV2N3FqMnNjMDJq" target="_blank" rel="noopener" class="social-icon social-icon-instagram" aria-label="Instagram" title="Instagram">
                    <img src="img/instagram.png" alt="Instagram">
                </a>
                <a href="https://x.com/karanganyarkab" target="_blank" rel="noopener" class="social-icon social-icon-x" aria-label="X" title="X">
                    <img src="img/twitter.png" alt="X">
                </a>
                <a href="https://www.facebook.com/diskominfo.kra/" target="_blank" rel="noopener" class="social-icon social-icon-facebook" aria-label="Facebook" title="Facebook">
                    <img src="img/facebook.png" alt="Facebook">
                </a>
                <a href="https://www.youtube.com/@KabKaranganyar" target="_blank" rel="noopener" class="social-icon social-icon-youtube" aria-label="YouTube" title="YouTube">
                    <img src="img/youtube.png" alt="YouTube">
                </a>
                <a href="https://www.tiktok.com/@diskominfokaranganyar" target="_blank" rel="noopener" class="social-icon social-icon-tiktok" aria-label="TikTok" title="TikTok">
                    <img src="img/tiktok.png" alt="TikTok">
                </a>
            </div>

            <div class="contact-top-search">
                <form action="index.php" method="get" style="display:flex;align-items:center;width:100%;">
                    <input type="text" name="q" placeholder="Cari berita atau wisata...">
                    <button type="submit"><span style="font-size:1.1em;">&#128269;</span></button>
                </form>
            </div>
        </div>
    </div>

    <header class="contact-main-header">
        <div class="container contact-main-header-inner">
            <div class="contact-brand">
                <img src="img/kominfo.png" alt="Logo Kominfo" style="height:40px;width:auto;display:block;object-fit:contain;background:transparent;border-radius:0;padding:0;box-shadow:none;">
                <div>
                    <span style="font-size:1.55rem;font-weight:900;color:#193a7b;letter-spacing:0.4px;">Berita Karanganyar</span><br>
                    <span style="font-size:0.82rem;color:#f58220;font-weight:600;">Portal Berita Resmi Pemerintah Daerah</span>
                </div>
            </div>
            <a href="index.php" class="contact-main-action"><span style="font-size:1.1em;">🏠</span> Beranda</a>
        </div>
    </header>

    <nav class="contact-nav">
        <div class="container">
            <ul class="contact-nav-list">
                <li><a href="index.php">Beranda</a></li>
                <li><a href="arsip.php">Arsip Berita</a></li>
                <li><a href="wisata.php">Wisata</a></li>
                <li style="position:relative;">
                    <a href="#">Profile &#9662;</a>
                    <ul style="display:none;position:absolute;left:0;top:100%;background:#fff;color:#193a7b;min-width:210px;box-shadow:0 4px 16px rgba(0,0,0,0.10);border-radius:0 0 8px 8px;z-index:10;list-style:none;padding:8px 0;">
                        <li><a href="https://diskominfo.karanganyarkab.go.id/struktur-organisasi/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Struktur Organisasi</a></li>
                        <li><a href="https://diskominfo.karanganyarkab.go.id/tugas-dan-fungsi/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Tugas & Fungsi</a></li>
                        <li><a href="https://diskominfo.karanganyarkab.go.id/visi-misi/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Visi & Misi</a></li>
                        <li><a href="https://diskominfo.karanganyarkab.go.id/pejabat-struktural/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Pejabat Struktural</a></li>
                        <li><a href="admin.php" style="color:#193a7b;display:block;padding:8px 18px;">Admin</a></li>
                    </ul>
                </li>
                <li style="position:relative;">
                    <a href="#">Galeri &#9662;</a>
                    <ul style="display:none;position:absolute;left:0;top:100%;background:#fff;color:#193a7b;min-width:180px;box-shadow:0 4px 16px rgba(0,0,0,0.10);border-radius:0 0 8px 8px;z-index:10;list-style:none;padding:8px 0;">
                        <li><a href="https://diskominfo.karanganyarkab.go.id/galeri/foto/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Foto</a></li>
                        <li><a href="https://diskominfo.karanganyarkab.go.id/galeri/video/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Video</a></li>
                        <li><a href="https://diskominfo.karanganyarkab.go.id/galeri/infografis/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Infografis</a></li>
                    </ul>
                </li>
                <li style="position:relative;">
                    <a href="#">Informasi &#9662;</a>
                    <ul style="display:none;position:absolute;left:0;top:100%;background:#fff;color:#193a7b;min-width:180px;box-shadow:0 4px 16px rgba(0,0,0,0.10);border-radius:0 0 8px 8px;z-index:10;list-style:none;padding:8px 0;">
                        <li><a href="https://diskominfo.karanganyarkab.go.id/informasi/pengumuman/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Pengumuman</a></li>
                        <li><a href="https://diskominfo.karanganyarkab.go.id/informasi/artikel/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Artikel</a></li>
                        <li><a href="https://diskominfo.karanganyarkab.go.id/informasi/agenda/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Agenda</a></li>
                        <li><a href="https://diskominfo.karanganyarkab.go.id/informasi/siaran-pers/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Siaran Pers</a></li>
                    </ul>
                </li>
                <li style="position:relative;">
                    <a href="#">PPID &#9662;</a>
                    <ul style="display:none;position:absolute;left:0;top:100%;background:#fff;color:#193a7b;min-width:180px;box-shadow:0 4px 16px rgba(0,0,0,0.10);border-radius:0 0 8px 8px;z-index:10;list-style:none;padding:8px 0;">
                        <li><a href="https://diskominfo.karanganyarkab.go.id/ppid/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">PPID Utama</a></li>
                        <li><a href="https://diskominfo.karanganyarkab.go.id/ppid/daftar-informasi-publik/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Daftar Informasi Publik</a></li>
                        <li><a href="https://diskominfo.karanganyarkab.go.id/ppid/regulasi/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Regulasi</a></li>
                        <li><a href="https://diskominfo.karanganyarkab.go.id/ppid/permohonan-informasi/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Permohonan Informasi</a></li>
                    </ul>
                </li>
                <li style="position:relative;">
                    <a href="#">Layanan &#9662;</a>
                    <ul style="display:none;position:absolute;left:0;top:100%;background:#fff;color:#193a7b;min-width:180px;box-shadow:0 4px 16px rgba(0,0,0,0.10);border-radius:0 0 8px 8px;z-index:10;list-style:none;padding:8px 0;">
                        <li><a href="https://diskominfo.karanganyarkab.go.id/layanan/permohonan-informasi/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Permohonan Informasi</a></li>
                        <li><a href="https://diskominfo.karanganyarkab.go.id/layanan/permohonan-ppid/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Permohonan PPID</a></li>
                        <li><a href="https://diskominfo.karanganyarkab.go.id/layanan/permohonan-ikp/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Permohonan IKP</a></li>
                    </ul>
                </li>
                <li style="position:relative;">
                    <a href="#">Pengaduan &#9662;</a>
                    <ul style="display:none;position:absolute;left:0;top:100%;background:#fff;color:#193a7b;min-width:180px;box-shadow:0 4px 16px rgba(0,0,0,0.10);border-radius:0 0 8px 8px;z-index:10;list-style:none;padding:8px 0;">
                        <li><a href="https://diskominfo.karanganyarkab.go.id/aduan-aspirasi-publik/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Aduan/Aspirasi Publik</a></li>
                        <li><a href="https://diskominfo.karanganyarkab.go.id/faq/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">FAQ</a></li>
                        <li><a href="https://diskominfo.karanganyarkab.go.id/whistleblowing-system/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Whistleblowing System</a></li>
                    </ul>
                </li>
                <li><a href="contact.php" class="nav-active" style="color:#f58220;text-decoration:none;padding:5px 0 3px 0;display:inline-block;border-bottom:2px solid #f58220;">Hubungi Kami</a></li>
                <li><a href="admin.php">👤 Admin</a></li>
            </ul>
        </div>
    </nav>
    <script>
    (function() {
        var navRoot = document.querySelector('.contact-nav');
        if (!navRoot) return;

        var menuItems = navRoot.querySelectorAll('ul > li');
        menuItems.forEach(function(li) {
            var dropdown = li.querySelector(':scope > ul');
            if (!dropdown) return;

            li.addEventListener('mouseenter', function() {
                if (window.innerWidth > 720) dropdown.style.display = 'block';
            });
            li.addEventListener('mouseleave', function() {
                if (window.innerWidth > 720) dropdown.style.display = 'none';
            });

            var trigger = li.querySelector(':scope > a');
            if (trigger) {
                trigger.addEventListener('click', function(e) {
                    if (window.innerWidth > 720) return;
                    e.preventDefault();
                    var isOpen = dropdown.style.display === 'block';
                    menuItems.forEach(function(otherLi) {
                        var otherDrop = otherLi.querySelector(':scope > ul');
                        if (otherDrop && otherDrop !== dropdown) otherDrop.style.display = 'none';
                    });
                    dropdown.style.display = isOpen ? 'none' : 'block';
                });
            }
        });

        document.addEventListener('click', function(e) {
            if (!navRoot.contains(e.target) || window.innerWidth > 720) {
                menuItems.forEach(function(li) {
                    var dropdown = li.querySelector(':scope > ul');
                    if (dropdown) dropdown.style.display = 'none';
                });
            }
        });
    })();
    </script>

    <main class="container contact-main">
        <div class="contact-hero">
            <div style="position:relative;z-index:1;">
                <h1 style="margin:0 0 10px 0;font-size:2rem;font-weight:900;">Hubungi Kami</h1>
                <p style="margin:0;font-size:1.02rem;opacity:0.95;font-weight:500;max-width:720px;">
                    Layanan informasi publik dan koordinasi komunikasi resmi Diskominfo Karanganyar.
                    Sampaikan kebutuhan Anda agar tim kami dapat merespons dengan cepat dan tepat.
                </p>
            </div>
        </div>

        <div class="contact-grid">
            <div class="contact-card">
                <h3 style="margin-top:0;">Informasi Kontak</h3>
                <div class="contact-info-list">
                    <div class="contact-info-item"><strong>Alamat:</strong> Jl. Lawu, Karanganyar, Jawa Tengah</div>
                    <div class="contact-info-item"><strong>Telepon:</strong> 0821-7173-8467</div>
                    <div class="contact-info-item"><strong>Email:</strong> diskominfo@karanganyar.go.id</div>
                    <div class="contact-info-item"><strong>Jam Layanan:</strong> Senin - Jumat, 08.00 - 15.30 WIB</div>
                </div>
                <div style="margin-top:16px;">
                    <a class="btn-outline" href="https://diskominfo.karanganyarkab.go.id/ppid/" target="_blank" rel="noopener">Kanal PPID</a>
                </div>
            </div>

            <div class="contact-card contact-form">
                <h3 style="margin-top:0;">Formulir Hubungi Kami</h3>
                <p class="contact-subtitle">Pesan Anda akan tersimpan otomatis dan ditinjau melalui dashboard admin.</p>
                <?php if ($success_msg !== ''): ?>
                    <div class="alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
                <?php endif; ?>
                <?php if ($error_msg !== ''): ?>
                    <div class="alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
                <?php endif; ?>
                <form method="post" action="contact.php">
                    <div class="contact-field-row">
                        <div>
                            <label for="nama">Nama Lengkap</label>
                            <input id="nama" name="nama" type="text" placeholder="Nama lengkap" value="<?php echo htmlspecialchars($form_data['nama']); ?>" required>
                        </div>
                        <div>
                            <label for="instansi">Instansi</label>
                            <input id="instansi" name="instansi" type="text" placeholder="Instansi atau komunitas" value="<?php echo htmlspecialchars($form_data['instansi']); ?>">
                        </div>
                        <div>
                            <label for="email">Email</label>
                            <input id="email" name="email" type="email" placeholder="email@contoh.go.id" value="<?php echo htmlspecialchars($form_data['email']); ?>" required>
                        </div>
                        <div>
                            <label for="telepon">Nomor Telepon</label>
                            <input id="telepon" name="telepon" type="tel" placeholder="08xx-xxxx-xxxx" value="<?php echo htmlspecialchars($form_data['telepon']); ?>" required>
                        </div>
                    </div>
                    <div style="margin-top:16px;">
                        <label for="topik">Topik Layanan</label>
                        <select id="topik" name="topik" required>
                            <option value="">Pilih topik</option>
                            <option value="informasi" <?php echo $form_data['topik'] === 'informasi' ? 'selected' : ''; ?>>Permintaan Informasi Publik</option>
                            <option value="kerjasama" <?php echo $form_data['topik'] === 'kerjasama' ? 'selected' : ''; ?>>Kerja Sama / Media</option>
                            <option value="digital" <?php echo $form_data['topik'] === 'digital' ? 'selected' : ''; ?>>Layanan Digital</option>
                            <option value="lainnya" <?php echo $form_data['topik'] === 'lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                        </select>
                    </div>
                    <div style="margin-top:16px;">
                        <label for="pesan">Pesan</label>
                        <textarea id="pesan" name="pesan" placeholder="Tuliskan pesan dengan jelas" required><?php echo htmlspecialchars($form_data['pesan']); ?></textarea>
                    </div>
                    <div class="contact-actions">
                        <button class="btn-primary" type="submit" name="contact_submit" value="1">Kirim Pesan</button>
                        <a class="btn-outline" href="https://diskominfo.karanganyarkab.go.id/aduan-aspirasi-publik/" target="_blank" rel="noopener">Aduan Publik</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php
        $footer_show_map = true;
        $footer_year = date('Y');
        include __DIR__ . '/partials/site_footer.php';
    ?>

    <?php include __DIR__ . '/partials/floating_widget.php'; ?>
</body>
</html>
