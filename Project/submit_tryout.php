<?php
session_start();
include 'db.php';

// Pastikan user sudah login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mulai transaksi
    mysqli_begin_transaction($conn);

    // Hitung jumlah soal dalam tryout
    $query_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM tryout_questions");
    $totalQuestions = mysqli_fetch_assoc($query_total)['total'] ?? 0;
    $correctAnswers = 0;

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'answer_') === 0) {
            $questionId = str_replace('answer_', '', $key);

            // Cek jawaban benar
            $query_correct = mysqli_query($conn, "SELECT correct_option FROM tryout_questions WHERE id = '$questionId'");
            $correctOption = mysqli_fetch_assoc($query_correct)['correct_option'] ?? '';

            if ($correctOption && $value === $correctOption) {
                $correctAnswers++;
            }
        }
    }

    // Hitung skor dalam persentase (0-100%)
    $score = ($totalQuestions > 0) ? ($correctAnswers / $totalQuestions) * 100 : 0;

    // Simpan hasil tryout ke database
    $query_insert = mysqli_query($conn, "INSERT INTO tryout_results (user_id, score) VALUES ('$user_id', '$score')");

    if ($query_insert) {
        mysqli_commit($conn);
        
        // Simpan hasil ke session agar bisa diakses di result.php
        $_SESSION['tryout_result'] = [
            'score' => number_format($score, 2),
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers
        ];

        header("Location: result.php");
        exit();
    } else {
        mysqli_rollback($conn);
        die("Terjadi kesalahan dalam menyimpan hasil tryout.");
    }
}

header("Location: tryout.php");
exit();