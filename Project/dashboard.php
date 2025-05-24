<?php
session_start();
include 'db.php';

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

// If it's an AJAX request, return JSON and exit
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header("Content-Type: application/json");
    echo json_encode($_SESSION["user"]);
    exit();
}

$user = $_SESSION["user"];
$is_premium = ($user["account_type"] === "premium");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | GACORPTN</title>
    <link rel="stylesheet" href="css/styledash.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    /* Additional CSS for hover effects and modals */
    .service-card {
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .normal-card:hover {
        border: 2px solid #4361ee;
    }

    .premium-card:hover {
        border: 2px solid gold;
    }

    /* Modal styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        overflow-y: auto;
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 30px;
        border-radius: 10px;
        width: 80%;
        max-width: 800px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        position: relative;
        animation: modalFadeIn 0.3s;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }

    .modal-premium .modal-header {
        background: linear-gradient(90deg, gold, #ffd700);
        color: #333;
        padding: 15px 20px;
        margin: -30px -30px 20px -30px;
        border-radius: 10px 10px 0 0;
    }

    .modal-premium .modal-header h2 {
        color: #333;
    }

    .modal-normal .modal-header {
        background: linear-gradient(90deg, #4361ee, #3a56d4);
        color: white;
        padding: 15px 20px;
        margin: -30px -30px 20px -30px;
        border-radius: 10px 10px 0 0;
    }

    .close {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover {
        color: #333;
    }

    .modal-body {
        line-height: 1.6;
    }

    .modal-premium .modal-body {
        background-color: #fffaf0;
        padding: 20px;
        border-radius: 5px;
    }

    .premium-badge-modal {
        background-color: gold;
        color: #333;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        display: inline-block;
        margin-left: 10px;
    }

    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Material content styles */
    .material-section {
        margin-bottom: 20px;
    }

    .material-section h3 {
        color: #4361ee;
        margin-bottom: 10px;
    }

    .modal-premium .material-section h3 {
        color: #b8860b;
    }

    .material-content {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 15px;
    }

    .material-image {
        max-width: 100%;
        height: auto;
        border-radius: 5px;
        margin: 10px 0;
        display: block;
    }

    .material-video {
        width: 100%;
        aspect-ratio: 16/9;
        border-radius: 5px;
        margin: 10px 0;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .modal-content {
            width: 95%;
            margin: 10% auto;
            padding: 20px;
        }
    }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <span class="logo-primary">GACOR</span><span class="logo-accent">PTN</span>
        </div>
        <ul class="nav-links">
            <li><a href="dashboard.php">Beranda</a></li>
            <li><a href="upgrade.php">Upgrade</a></li>
            <li><a href="profil.php">Profil</a></li>
            <li class="dropdown">
                <a href="#services">User <i class="fas fa-chevron-down dropdown-icon"></i></a>
                <ul class="dropdown-menu">
                    <li><a href="change.php">Swith User</a></li>
                    <li><a href="logout.php">Log Out</a></li>
                    <li><a href="delete.php" class="btn btn-danger">Hapus Akun</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <!-- Hero Section (Tambahan Status Akun) -->
    <section id="home" class="hero <?php echo $is_premium ? 'hero-premium' : 'hero-normal'; ?>">
        <div class="hero-content">
            <h1>Selamat Datang, <span id="user-email"><?php echo htmlspecialchars($user["email"]); ?></span>!</h1>
            <div id="account-status" class="status-box <?php echo $is_premium ? 'status-premium' : 'status-normal'; ?>">
                <p id="status-message">
                    <?php echo $is_premium ? '✨ Anda memiliki akses penuh ke fitur Premium!' : '✅ Akun Normal - Upgrade untuk fitur eksklusif!'; ?>
                </p>
            </div>
        </div>
    </section>

    <section id="services" class="services">
        <div class="section-header">
            <h2>Materi</h2>
            <p class="section-subtitle">Akses materi sesuai dengan jenis akun Anda</p>
        </div>
        <div class="service-grid">
            <!-- Kartu untuk semua akun -->
            <div class="service-card normal-card" onclick="openModal('normal1')">
                <div class="card-icon">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <h3>Pengetahuan dan Pemahaman Umum</h3>
                <ul>
                    <li>✔ Pemahaman konsep dasar akademik</li>
                    <li>✔ Prinsip-prinsip fundamental berbagai bidang ilmu</li>
                </ul>
            </div>

            <div class="service-card normal-card" onclick="openModal('normal2')">
                <div class="card-icon">
                    <i class="fas fa-brain"></i>
                </div>
                <h3>Pengetahuan Kognitif</h3>
                <ul>
                    <li>✔ Kemampuan berpikir kritis dan analitis</li>
                    <li>✔ Pengolahan informasi dan penyelesaian masalah</li>
                </ul>
            </div>

            <div class="service-card normal-card" onclick="openModal('normal3')">
                <div class="card-icon">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <h3>Penalaran Umum</h3>
                <ul>
                    <li>✔ Mengasah logika berpikir</li>
                    <li>✔ Menyelesaikan soal dengan pendekatan sistematis</li>
                </ul>
            </div>

            <!-- Kartu eksklusif untuk Premium -->
            <?php if ($is_premium): ?>
            <div class="service-card premium-card" onclick="openModal('premium1')">
                <div class="card-icon">
                    <i class="fas fa-book-reader"></i>
                </div>
                <h3>Kemampuan Memahami Bacaan dan Menulis</h3>
                <ul>
                    <li>✔ Meningkatkan pemahaman teks akademik</li>
                    <li>✔ Teknik menulis yang efektif dan sistematis</li>
                </ul>
            </div>

            <div class="service-card premium-card" onclick="openModal('premium2')">
                <div class="card-icon">
                    <i class="fas fa-language"></i>
                </div>
                <h3>Literasi Bahasa Indonesia</h3>
                <ul>
                    <li>✔ Struktur dan tata bahasa yang benar</li>
                    <li>✔ Memahami teks kompleks dalam bahasa Indonesia</li>
                </ul>
            </div>

            <div class="service-card premium-card" onclick="openModal('premium3')">
                <div class="card-icon">
                    <i class="fas fa-globe"></i>
                </div>
                <h3>Literasi Bahasa Inggris</h3>
                <ul>
                    <li>✔ Membaca teks akademik dalam bahasa Inggris</li>
                    <li>✔ Strategi menjawab soal dalam bahasa Inggris</li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Modal Windows -->
    <div id="normal1" class="modal">
        <div class="modal-content modal-normal">
            <div class="modal-header">
                <h2>Pengetahuan dan Pemahaman Umum <span class="premium-badge-modal">STANDARD</span></h2>
                <span class="close" onclick="closeModal('normal1')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="material-section">
                    <h3>Konsep Dasar Akademik</h3>
                    <div class="material-content">
                        <p>Materi ini membahas konsep-konsep dasar yang perlu dipahami untuk menghadapi tes akademik:
                        </p>
                        <ul>
                            <li>Pengenalan struktur tes akademik</li>
                            <li>Teknik membaca cepat dan efektif</li>
                            <li>Strategi menjawab pertanyaan konseptual</li>
                        </ul>
                        <img src="https://via.placeholder.com/600x300?text=Contoh+Materi+Standard" alt="Materi Standard"
                            class="material-image">
                    </div>
                </div>

                <div class="material-section">
                    <h3>Prinsip Fundamental</h3>
                    <div class="material-content">
                        <p>Pemahaman prinsip-prinsip dasar berbagai bidang ilmu pengetahuan:</p>
                        <ul>
                            <li>Matematika dasar dan logika</li>
                            <li>Konsep sains umum</li>
                            <li>Pengetahuan sosial dasar</li>
                        </ul>
                        <p>Contoh soal:</p>
                        <div style="background-color: #e9ecef; padding: 10px; border-radius: 5px;">
                            <p><strong>Soal:</strong> Jika x + 5 = 12, maka nilai x adalah...</p>
                            <p><strong>Pembahasan:</strong> x = 12 - 5 = 7</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="normal2" class="modal">
        <div class="modal-content modal-normal">
            <div class="modal-header">
                <h2>Pengetahuan Kognitif <span class="premium-badge-modal">STANDARD</span></h2>
                <span class="close" onclick="closeModal('normal2')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="material-section">
                    <h3>Berpikir Kritis dan Analitis</h3>
                    <div class="material-content">
                        <p>Materi ini mengajarkan teknik berpikir kritis untuk menganalisis masalah:</p>
                        <ul>
                            <li>Identifikasi masalah utama</li>
                            <li>Analisis argumen dan bukti</li>
                            <li>Evaluasi solusi alternatif</li>
                        </ul>
                        <img src="https://via.placeholder.com/600x300?text=Berpikir+Kritis" alt="Berpikir Kritis"
                            class="material-image">
                    </div>
                </div>

                <div class="material-section">
                    <h3>Penyelesaian Masalah</h3>
                    <div class="material-content">
                        <p>Strategi sistematis untuk menyelesaikan berbagai jenis masalah:</p>
                        <ol>
                            <li>Definisikan masalah dengan jelas</li>
                            <li>Kumpulkan informasi relevan</li>
                            <li>Buat hipotesis solusi</li>
                            <li>Uji solusi yang mungkin</li>
                            <li>Implementasi solusi terbaik</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="normal3" class="modal">
        <div class="modal-content modal-normal">
            <div class="modal-header">
                <h2>Penalaran Umum <span class="premium-badge-modal">STANDARD</span></h2>
                <span class="close" onclick="closeModal('normal3')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="material-section">
                    <h3>Logika Berpikir</h3>
                    <div class="material-content">
                        <p>Materi pengembangan kemampuan logika:</p>
                        <ul>
                            <li>Deduksi dan induksi</li>
                            <li>Analogi dan hubungan logis</li>
                            <li>Pola berpikir sistematis</li>
                        </ul>
                        <div style="background-color: #e9ecef; padding: 10px; border-radius: 5px; margin-top: 10px;">
                            <p><strong>Contoh:</strong> Jika semua A adalah B, dan beberapa B adalah C, maka...</p>
                            <p><strong>Pembahasan:</strong> Beberapa A mungkin adalah C</p>
                        </div>
                    </div>
                </div>

                <div class="material-section">
                    <h3>Pendekatan Sistematis</h3>
                    <div class="material-content">
                        <p>Teknik menjawab soal dengan pendekatan terstruktur:</p>
                        <ol>
                            <li>Baca soal dengan teliti</li>
                            <li>Identifikasi kata kunci</li>
                            <li>Eliminasi jawaban yang jelas salah</li>
                            <li>Bandinkan opsi yang tersisa</li>
                            <li>Pilih jawaban terbaik</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($is_premium): ?>
    <div id="premium1" class="modal">
        <div class="modal-content modal-premium">
            <div class="modal-header">
                <h2>Kemampuan Memahami Bacaan dan Menulis <span class="premium-badge-modal">PREMIUM</span></h2>
                <span class="close" onclick="closeModal('premium1')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="material-section">
                    <h3>Pemahaman Teks Akademik</h3>
                    <div class="material-content">
                        <p><strong>Fitur Premium:</strong> Akses penuh ke teknik memahami teks kompleks</p>
                        <ul>
                            <li>Identifikasi struktur teks akademik</li>
                            <li>Teknik skimming dan scanning tingkat lanjut</li>
                            <li>Analisis argumen dalam teks</li>
                        </ul>
                        <img src="https://via.placeholder.com/600x300?text=Contoh+Materi+Premium" alt="Materi Premium"
                            class="material-image">
                        <p><em>Contoh teks akademik premium dengan analisis mendalam:</em></p>
                        <div style="background-color: #fff8e1; padding: 10px; border-radius: 5px;">
                            <p>Analisis teks jurnal ilmiah dengan pendekatan kritis...</p>
                        </div>
                    </div>
                </div>

                <div class="material-section">
                    <h3>Teknik Menulis Efektif</h3>
                    <div class="material-content">
                        <p>Materi eksklusif untuk anggota premium:</p>
                        <ul>
                            <li>Struktur esai akademik</li>
                            <li>Penggunaan referensi yang tepat</li>
                            <li>Gaya penulisan formal</li>
                            <li>Template penulisan premium</li>
                        </ul>
                        <iframe class="material-video" src="https://www.youtube.com/embed/dQw4w9WgXcQ"
                            title="Contoh video premium" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="premium2" class="modal">
        <div class="modal-content modal-premium">
            <div class="modal-header">
                <h2>Literasi Bahasa Indonesia <span class="premium-badge-modal">PREMIUM</span></h2>
                <span class="close" onclick="closeModal('premium2')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="material-section">
                    <h3>Tata Bahasa Lanjutan</h3>
                    <div class="material-content">
                        <p>Materi premium untuk penguasaan bahasa Indonesia:</p>
                        <ul>
                            <li>Struktur kalimat kompleks</li>
                            <li>Penggunaan tanda baca tingkat lanjut</li>
                            <li>Gaya bahasa sastra</li>
                        </ul>
                        <div style="background-color: #fff8e1; padding: 10px; border-radius: 5px; margin-top: 10px;">
                            <p><strong>Contoh Analisis Premium:</strong> Perbedaan antara "yang mana" dan "di mana"
                                dalam konteks formal</p>
                        </div>
                    </div>
                </div>

                <div class="material-section">
                    <h3>Teks Kompleks</h3>
                    <div class="material-content">
                        <p>Teknik memahami teks sastra dan akademik kompleks:</p>
                        <ol>
                            <li>Identifikasi tema dan amanat</li>
                            <li>Analisis unsur intrinsik</li>
                            <li>Interpretasi makna tersirat</li>
                        </ol>
                        <p><em>Contoh teks sastra dengan analisis mendalam hanya tersedia untuk anggota premium.</em>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="premium3" class="modal">
        <div class="modal-content modal-premium">
            <div class="modal-header">
                <h2>Literasi Bahasa Inggris <span class="premium-badge-modal">PREMIUM</span></h2>
                <span class="close" onclick="closeModal('premium3')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="material-section">
                    <h3>Academic English</h3>
                    <div class="material-content">
                        <p>Materi eksklusif untuk penguasaan bahasa Inggris akademik:</p>
                        <ul>
                            <li>Vocabulary for academic purposes</li>
                            <li>Complex sentence structures</li>
                            <li>Academic writing conventions</li>
                        </ul>
                        <div style="background-color: #fff8e1; padding: 10px; border-radius: 5px; margin-top: 10px;">
                            <p><strong>Premium Example:</strong> Analysis of scientific journal abstracts with key
                                terminology highlighting</p>
                        </div>
                    </div>
                </div>

                <div class="material-section">
                    <h3>Test Strategies</h3>
                    <div class="material-content">
                        <p>Advanced strategies for English proficiency tests:</p>
                        <ul>
                            <li>Time management techniques</li>
                            <li>Question pattern recognition</li>
                            <li>Elimination strategies for multiple choice</li>
                        </ul>
                        <iframe class="material-video" src="https://www.youtube.com/embed/dQw4w9WgXcQ"
                            title="English premium video" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
    // Modal functions
    function openModal(modalId) {
        document.getElementById(modalId).style.display = "block";
        document.body.style.overflow = "hidden"; // Prevent scrolling
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = "none";
        document.body.style.overflow = "auto"; // Re-enable scrolling
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        document.querySelectorAll('.modal').forEach(modal => {
            if (event.target == modal) {
                modal.style.display = "none";
                document.body.style.overflow = "auto";
            }
        });
    }

    // Check for premium status changes
    function checkPremiumStatus() {
        fetch("dashboard.php")
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    window.location.href = "login.php";
                } else if (data.account_type === "premium" && !document.querySelector('.premium-card')) {
                    // If user upgraded to premium, reload the page
                    window.location.reload();
                }
            })
            .catch(error => console.error("Error:", error));
    }

    // Check status every 30 seconds
    setInterval(checkPremiumStatus, 30000);
    </script>

</body>

</html>