<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$is_premium = false;

// Get user data
$query_premium = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
if ($query_premium) {
    $user_data = mysqli_fetch_assoc($query_premium);
    $is_premium = ($user_data['account_type'] === 'premium');
}

// Get random questions
$query = mysqli_query($conn, "SELECT * FROM tryout_questions ORDER BY RAND() LIMIT 100") or die('Query failed');
$questions = [];
while ($row = mysqli_fetch_assoc($query)) {
    $questions[] = $row;
}
$total_questions = count($questions);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tryout | GACORPTN</title>
    <link rel="stylesheet" href="css/styledash.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    /* Enhanced Tryout Styles */
    .tryout-container {
        background-color: white;
        border-radius: 15px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        padding: 30px;
        margin: 30px auto;
        max-width: 900px;
    }

    .tryout-header {
        border-bottom: 2px solid #4361ee;
        padding-bottom: 15px;
        margin-bottom: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .tryout-premium .tryout-header {
        border-bottom-color: gold;
    }

    .tryout-header h2 {
        color: #4361ee;
        margin: 0;
        font-size: 1.8rem;
    }

    .tryout-premium .tryout-header h2 {
        color: #b8860b;
    }

    .timer-container {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        text-align: center;
        font-weight: bold;
        font-size: 1.2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .tryout-premium .timer-container {
        background: linear-gradient(135deg, gold 0%, #FFD700 100%);
        color: #333;
    }

    .question-container {
        margin-bottom: 30px;
        padding: 25px;
        border-radius: 12px;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }

    .tryout-premium .question-container {
        background-color: #fffaf0;
        border-left: 4px solid gold;
    }

    .question-number {
        font-weight: bold;
        color: #4361ee;
        margin-bottom: 15px;
        font-size: 1.1rem;
    }

    .tryout-premium .question-number {
        color: #b8860b;
    }

    .question-text {
        font-size: 1.15rem;
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .option-list {
        list-style-type: none;
        padding: 0;
    }

    .option-item {
        padding: 15px;
        margin-bottom: 12px;
        border-radius: 8px;
        background-color: white;
        border: 2px solid #e9ecef;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .option-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-color: #4361ee;
    }

    .tryout-premium .option-item:hover {
        border-color: gold;
    }

    .option-item.selected {
        background-color: #4361ee;
        color: white;
        border-color: #4361ee;
    }

    .tryout-premium .option-item.selected {
        background-color: gold;
        color: #333;
        border-color: gold;
    }

    .option-item label {
        display: flex;
        align-items: center;
        cursor: pointer;
        width: 100%;
    }

    .option-item input[type="radio"] {
        margin-right: 12px;
        transform: scale(1.2);
    }

    .btn-submit {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        color: white;
        padding: 15px 30px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-weight: bold;
        font-size: 1.1rem;
        margin-top: 30px;
        display: block;
        width: 100%;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
    }

    .btn-submit i {
        margin-right: 8px;
    }

    .progress-container {
        margin-bottom: 25px;
    }

    .progress-bar {
        height: 8px;
        background-color: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        width: 0%;
        transition: width 0.5s ease;
    }

    .tryout-premium .progress-fill {
        background: linear-gradient(135deg, gold 0%, #FFD700 100%);
    }

    .progress-text {
        text-align: right;
        font-size: 0.9rem;
        color: #6c757d;
        margin-top: 5px;
    }

    @media (max-width: 768px) {
        .tryout-container {
            padding: 20px;
            margin: 20px 10px;
        }

        .question-container {
            padding: 18px;
        }

        .question-text {
            font-size: 1rem;
        }

        .option-item {
            padding: 12px;
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
                <a href="#">User <i class="fas fa-chevron-down dropdown-icon"></i></a>
                <ul class="dropdown-menu">
                    <li><a href="change.php">Switch User</a></li>
                    <li><a href="logout.php">Log Out</a></li>
                    <li><a href="delete.php" class="btn btn-danger">Hapus Akun</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero <?php echo $is_premium ? 'hero-premium' : 'hero-normal'; ?>">
        <div class="hero-content">
            <h1>Tryout UTBK <span><?php echo htmlspecialchars($user_data['email'] ?? ''); ?></span>!</h1>
            <div class="status-box <?php echo $is_premium ? 'status-premium' : 'status-normal'; ?>">
                <p>
                    <?php echo $is_premium ? '✨ Akses Penuh Soal Premium!' : '✅ Akun Standard - Akses terbatas'; ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Tryout Questions Section -->
    <section class="services">
        <div class="section-header">
            <h2>Tryout Simulasi UTBK</h2>
            <p class="section-subtitle">Latih kemampuanmu dengan soal-soal terbaru</p>
        </div>

        <div class="tryout-container <?php echo $is_premium ? 'tryout-premium' : ''; ?>">
            <form id="tryout-form" action="submit_tryout.php" method="POST">
                <div class="tryout-header">
                    <h2><i class="fas fa-clipboard-list"></i> Tryout UTBK</h2>
                    <div class="timer-container">
                        <i class="fas fa-clock"></i> <span id="timer">300:00</span>
                    </div>
                </div>

                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" id="progress-fill"></div>
                    </div>
                    <div class="progress-text">
                        <span id="current-question">1</span>/<?php echo $total_questions; ?> Soal
                    </div>
                </div>

                <div id="questions-wrapper">
                    <?php foreach ($questions as $index => $question): ?>
                    <div class="question-container" data-question="<?php echo $index + 1; ?>"
                        style="<?php echo $index > 0 ? 'display:none;' : ''; ?>">
                        <div class="question-number">Soal <?php echo $index + 1; ?></div>
                        <div class="question-text">
                            <?php echo htmlspecialchars($question['question']); ?>
                        </div>

                        <ul class="option-list">
                            <li class="option-item">
                                <label>
                                    <input type="radio" name="answer_<?php echo $question['id']; ?>" value="A" required>
                                    <span>A. <?php echo htmlspecialchars($question['option_a']); ?></span>
                                </label>
                            </li>
                            <li class="option-item">
                                <label>
                                    <input type="radio" name="answer_<?php echo $question['id']; ?>" value="B">
                                    <span>B. <?php echo htmlspecialchars($question['option_b']); ?></span>
                                </label>
                            </li>
                            <li class="option-item">
                                <label>
                                    <input type="radio" name="answer_<?php echo $question['id']; ?>" value="C">
                                    <span>C. <?php echo htmlspecialchars($question['option_c']); ?></span>
                                </label>
                            </li>
                            <li class="option-item">
                                <label>
                                    <input type="radio" name="answer_<?php echo $question['id']; ?>" value="D">
                                    <span>D. <?php echo htmlspecialchars($question['option_d']); ?></span>
                                </label>
                            </li>
                        </ul>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="navigation-buttons" style="display: flex; justify-content: space-between; gap: 10px;">
                    <button type="button" class="btn-prev" onclick="prevQuestion()" disabled>
                        <i class="fas fa-arrow-left"></i> Sebelumnya
                    </button>
                    <button type="button" class="btn-next" onclick="nextQuestion()">
                        Selanjutnya <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
                <button type="button" class="btn-submit" id="submit-btn" style="display:none;"
                    onclick="confirmSubmit()">
                    <i class="fas fa-paper-plane"></i> Kirim Jawaban
                </button>

            </form>
        </div>
    </section>

    <script>
    // Timer functionality
    let timerInterval;

    function startTimer(duration, display) {
        let timer = duration,
            minutes, seconds;
        timerInterval = setInterval(function() {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;
            display.textContent = minutes + ":" + seconds;

            if (--timer < 0) {
                clearInterval(timerInterval);
                alert("Waktu tryout telah habis!");
                document.getElementById('tryout-form').submit();
            }
        }, 1000);
    }

    // Question navigation
    let currentQuestion = 0;
    const totalQuestions = <?php echo $total_questions; ?>;
    const progressFill = document.getElementById('progress-fill');
    const currentQuestionDisplay = document.getElementById('current-question');
    const submitBtn = document.getElementById('submit-btn');
    const prevBtn = document.querySelector('.btn-prev');
    const nextBtn = document.querySelector('.btn-next');

    function updateProgress() {
        const progress = ((currentQuestion + 1) / totalQuestions) * 100;
        progressFill.style.width = progress + '%';
        currentQuestionDisplay.textContent = currentQuestion + 1;

        // Show/hide navigation buttons
        prevBtn.disabled = currentQuestion === 0;
        nextBtn.style.display = currentQuestion === totalQuestions - 1 ? 'none' : 'block';
        submitBtn.style.display = currentQuestion === totalQuestions - 1 ? 'block' : 'none';
    }

    function showQuestion(index) {
        document.querySelectorAll('.question-container').forEach((q, i) => {
            q.style.display = i === index ? 'block' : 'none';
        });
        currentQuestion = index;
        updateProgress();
    }

    function nextQuestion() {
        if (currentQuestion < totalQuestions - 1) {
            showQuestion(currentQuestion + 1);
        }
    }

    function prevQuestion() {
        if (currentQuestion > 0) {
            showQuestion(currentQuestion - 1);
        }
    }

    // Answer selection
    document.querySelectorAll('.option-item').forEach(item => {
        item.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;

            // Update UI
            document.querySelectorAll('.option-item').forEach(i => {
                i.classList.remove('selected');
            });
            this.classList.add('selected');
        });
    });

    window.onload = function() {
        const totalTime = 300 * 60;
        const display = document.querySelector('#timer');
        startTimer(totalTime, display);
        updateProgress();

        // Initialize first question
        showQuestion(0);
    };

    function confirmSubmit() {
        if (confirm("Apakah Anda yakin ingin mengirim jawaban sekarang?")) {
            document.getElementById('tryout-form').submit();
        }
    }
    </script>
</body>

</html>