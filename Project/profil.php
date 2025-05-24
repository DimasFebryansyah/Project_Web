<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION["user"])) {
    header('Location: login.php');
    exit;
}

$email = $_SESSION["user"]["email"];
$message = [];
$edit_mode = isset($_GET['edit']) && $_GET['edit'] == 'true';

// Get user data from database
$stmt = $conn->prepare("SELECT full_name, address, school, target_university, email, account_type FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $fetch = $result->fetch_assoc();
} else {
    die('User data not found');
}

// Handle POST request (update data)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $update_name = mysqli_real_escape_string($conn, $_POST['fullName']);
    $update_address = mysqli_real_escape_string($conn, $_POST['address']);
    $update_school = mysqli_real_escape_string($conn, $_POST['school']);
    $update_university = mysqli_real_escape_string($conn, $_POST['targetUniversity']);

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, address = ?, school = ?, target_university = ? WHERE email = ?");
    $stmt->bind_param("sssss", $update_name, $update_address, $update_school, $update_university, $email);

    if ($stmt->execute()) {
        // Update session data
        $_SESSION["user"]["full_name"] = $update_name;
        $message[] = 'Profile updated successfully!';
        $edit_mode = false; // Switch back to view mode after successful update
        
        // Refresh user data
        $stmt = $conn->prepare("SELECT full_name, address, school, target_university, email, account_type FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $fetch = $result->fetch_assoc();
    } else {
        $message[] = 'Failed to update profile!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    :root {
        --primary-color: #4e73df;
        --secondary-color: #f8f9fc;
        --premium-color: #6f42c1;
        --success-color: #1cc88a;
        --dark-color: #5a5c69;
    }

    body {
        background-color: #f8f9fc;
        font-family: 'Nunito', sans-serif;
    }

    .profile-container {
        max-width: 900px;
        margin: 30px auto;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        border-radius: 15px;
        overflow: hidden;
        background: white;
    }

    .profile-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
        color: white;
        padding: 30px;
        position: relative;
    }

    .profile-header h2 {
        font-weight: 700;
        margin-bottom: 5px;
    }

    .account-badge {
        font-size: 0.9rem;
        padding: 6px 15px;
        border-radius: 20px;
        font-weight: 600;
        position: absolute;
        top: 20px;
        right: 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .premium-badge {
        background-color: var(--premium-color);
        color: white;
    }

    .normal-badge {
        background-color: var(--dark-color);
        color: white;
    }

    .profile-content {
        padding: 30px;
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background-color: var(--secondary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: -70px auto 20px;
        border: 5px solid white;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        font-size: 40px;
        color: var(--primary-color);
    }

    .form-label {
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 8px;
    }

    .form-control {
        border-radius: 8px;
        padding: 12px 15px;
        border: 1px solid #e3e6f0;
        transition: all 0.3s;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    }

    .form-control-plaintext {
        padding: 12px 15px;
        border-radius: 8px;
        background-color: #f8f9fc;
    }

    .btn-primary {
        background-color: var(--primary-color);
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background-color: #2e59d9;
        transform: translateY(-2px);
    }

    .btn-secondary {
        background-color: var(--dark-color);
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-secondary:hover {
        background-color: #42444e;
        transform: translateY(-2px);
    }

    .btn-warning {
        background-color: #f6c23e;
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
        color: white;
    }

    .btn-warning:hover {
        background-color: #dda20a;
        transform: translateY(-2px);
        color: white;
    }

    .alert-success {
        background-color: rgba(28, 200, 138, 0.1);
        color: var(--success-color);
        border: none;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 25px;
    }

    .alert-danger {
        background-color: rgba(231, 74, 59, 0.1);
        color: #e74a3b;
        border: none;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 25px;
    }

    .profile-section {
        margin-bottom: 30px;
    }

    .profile-section-title {
        font-size: 1.2rem;
        color: var(--primary-color);
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f8f9fc;
        font-weight: 700;
    }

    .input-group-text {
        background-color: #f8f9fc;
        border: 1px solid #e3e6f0;
    }

    .view-mode {
        display: <?php echo $edit_mode ? 'none': 'block';
        ?>;
    }

    .edit-mode {
        display: <?php echo $edit_mode ? 'block': 'none';
        ?>;
    }
    </style>
</head>

<body>
    <div class="profile-container">
        <div class="profile-header">
            <h2>Profil Pengguna</h2>
            <span id="accountBadge"
                class="account-badge <?php echo $fetch['account_type'] === "premium" ? "premium-badge" : "normal-badge"; ?>">
                <?php echo $fetch['account_type'] === "premium" ? "Akun Premium" : "Akun Normal"; ?>
                <?php if($fetch['account_type'] === "premium"): ?>
                <i class="fas fa-crown ms-2"></i>
                <?php else: ?>
                <i class="fas fa-user ms-2"></i>
                <?php endif; ?>
            </span>
        </div>

        <div class="profile-avatar">
            <i class="fas fa-user"></i>
        </div>

        <div class="profile-content">
            <?php
            if(!empty($message)){
                foreach($message as $msg){
                    echo '<div class="alert '. (strpos($msg, 'successfully') !== false ? 'alert-success' : 'alert-danger') .'">';
                    echo '<i class="fas '. (strpos($msg, 'successfully') !== false ? 'fa-check-circle' : 'fa-exclamation-circle') .' me-2"></i>';
                    echo $msg;
                    echo '</div>';
                }
            }
            ?>

            <!-- View Mode Form (Read-only) -->
            <div class="view-mode">
                <div class="profile-section">
                    <h4 class="profile-section-title"><i class="fas fa-id-card me-2"></i>Informasi Pribadi</h4>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control-plaintext"
                                    value="<?php echo htmlspecialchars($fetch['full_name'] ?? '-'); ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="text" class="form-control-plaintext"
                                    value="<?php echo htmlspecialchars($fetch['email'] ?? '-'); ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Alamat</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <input type="text" class="form-control-plaintext"
                                value="<?php echo htmlspecialchars($fetch['address'] ?? '-'); ?>" readonly>
                        </div>
                    </div>
                </div>

                <div class="profile-section">
                    <h4 class="profile-section-title"><i class="fas fa-graduation-cap me-2"></i>Informasi Pendidikan
                    </h4>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Asal Sekolah</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-school"></i></span>
                                <input type="text" class="form-control-plaintext"
                                    value="<?php echo htmlspecialchars($fetch['school'] ?? '-'); ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Kampus Tujuan</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-university"></i></span>
                                <input type="text" class="form-control-plaintext"
                                    value="<?php echo htmlspecialchars($fetch['target_university'] ?? '-'); ?>"
                                    readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-5">
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    <a href="profil.php?edit=true" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Profile
                    </a>
                </div>
            </div>

            <!-- Edit Mode Form -->
            <div class="edit-mode">
                <form method="post">
                    <div class="profile-section">
                        <h4 class="profile-section-title"><i class="fas fa-id-card me-2"></i>Informasi Pribadi</h4>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="fullName" class="form-label">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="fullName" name="fullName"
                                        value="<?php echo htmlspecialchars($fetch['full_name'] ?? '-'); ?>">
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control-plaintext" id="email"
                                        value="<?php echo htmlspecialchars($fetch['email'] ?? '-'); ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="address" class="form-label">Alamat</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <input type="text" class="form-control" id="address" name="address"
                                    value="<?php echo htmlspecialchars($fetch['address'] ?? '-'); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="profile-section">
                        <h4 class="profile-section-title"><i class="fas fa-graduation-cap me-2"></i>Informasi Pendidikan
                        </h4>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="school" class="form-label">Asal Sekolah</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-school"></i></span>
                                    <input type="text" class="form-control" id="school" name="school"
                                        value="<?php echo htmlspecialchars($fetch['school'] ?? '-'); ?>">
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="targetUniversity" class="form-label">Kampus Tujuan</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-university"></i></span>
                                    <input type="text" class="form-control" id="targetUniversity"
                                        name="targetUniversity"
                                        value="<?php echo htmlspecialchars($fetch['target_university'] ?? '-'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-5">
                        <a href="profil.php" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>