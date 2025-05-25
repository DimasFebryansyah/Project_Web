<?php
session_start();
include 'db.php';

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];

if ($user["account_type"] == "premium") {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Proses pembayaran
    $payment_method = $_POST["payment_method"];
    $card_number = isset($_POST["card_number"]) ? $_POST["card_number"] : '';
    
    // Validasi sederhana
    if (empty($payment_method)) {
        $error = "Pilih metode pembayaran!";
    } elseif ($payment_method == "credit_card" && (empty($card_number) || strlen($card_number) < 16)) {
        $error = "Nomor kartu tidak valid!";
    } else {
        // Simpan data pembayaran (dalam real app, ini akan ke payment gateway)
        $email = $user["email"];
        $stmt = $conn->prepare("UPDATE users SET account_type = 'premium' WHERE email = ?");
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            $_SESSION["user"]["account_type"] = "premium";
            header("Location: upgrade_success.php");
            exit();
        } else {
            $error = "Terjadi kesalahan. Coba lagi!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Pembayaran Premium</title>
    <style>
    :root {
        --primary: #4361ee;
        --primary-dark: #3a56d4;
        --secondary: #3f37c9;
        --success: #4cc9f0;
        --danger: #f72585;
        --light: #f8f9fa;
        --dark: #212529;
        --white: #ffffff;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .payment-container {
        background-color: var(--white);
        border-radius: 10px;
        box-shadow: var(--shadow);
        padding: 40px;
        width: 100%;
        max-width: 500px;
        animation: fadeIn 0.5s ease-in-out;
    }

    h2 {
        color: var(--primary);
        margin-bottom: 20px;
        font-size: 28px;
        text-align: center;
    }

    .price {
        font-size: 24px;
        font-weight: bold;
        color: var(--primary);
        text-align: center;
        margin: 20px 0;
    }

    .payment-methods {
        margin: 30px 0;
    }

    .payment-option {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .payment-option:hover {
        border-color: var(--primary);
    }

    .payment-option input {
        margin-right: 15px;
    }

    .payment-option label {
        flex-grow: 1;
        cursor: pointer;
    }

    .payment-details {
        display: none;
        padding: 15px;
        background-color: var(--light);
        border-radius: 5px;
        margin-top: 10px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
    }

    .form-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
    }

    .btn-pay {
        background-color: var(--primary);
        color: var(--white);
        border: none;
        padding: 12px 30px;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 20px;
        width: 100%;
    }

    .btn-pay:hover {
        background-color: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(67, 97, 238, 0.2);
    }

    .error-message {
        color: var(--danger);
        font-weight: bold;
        margin: 15px 0;
        text-align: center;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>
</head>

<body>
    <div class="payment-container">
        <h2>Pembayaran Premium</h2>
        <div class="price">Rp 99.000/bulan</div>

        <?php if (isset($error)): ?>
        <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="payment.php" method="POST">
            <div class="payment-methods">
                <h3>Pilih Metode Pembayaran:</h3>

                <div class="payment-option">
                    <input type="radio" id="credit_card" name="payment_method" value="credit_card" required>
                    <label for="credit_card">Kartu Kredit/Debit</label>
                </div>
                <div class="payment-details" id="credit_card_details">
                    <div class="form-group">
                        <label for="card_number">Nomor Kartu</label>
                        <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456">
                    </div>
                    <div class="form-group">
                        <label for="card_name">Nama di Kartu</label>
                        <input type="text" id="card_name" name="card_name" placeholder="Nama Pemilik Kartu">
                    </div>
                    <div style="display: flex; gap: 15px;">
                        <div class="form-group" style="flex: 1;">
                            <label for="expiry_date">Tanggal Kadaluarsa</label>
                            <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label for="cvv">CVV</label>
                            <input type="text" id="cvv" name="cvv" placeholder="123">
                        </div>
                    </div>
                </div>

                <div class="payment-option">
                    <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer">
                    <label for="bank_transfer">Transfer Bank</label>
                </div>
                <div class="payment-details" id="bank_transfer_details">
                    <p>Anda akan diarahkan ke halaman transfer bank setelah mengklik tombol Bayar.</p>
                    <p>Bank yang didukung: BCA, Mandiri, BRI, BNI</p>
                </div>

                <div class="payment-option">
                    <input type="radio" id="ewallet" name="payment_method" value="ewallet">
                    <label for="ewallet">E-Wallet</label>
                </div>
                <div class="payment-details" id="ewallet_details">
                    <p>E-Wallet yang didukung: OVO, GoPay, DANA, LinkAja</p>
                </div>
            </div>

            <button type="submit" class="btn-pay">Bayar Sekarang</button>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentOptions = document.querySelectorAll('input[name="payment_method"]');

        paymentOptions.forEach(option => {
            option.addEventListener('change', function() {
                // Sembunyikan semua detail pembayaran
                document.querySelectorAll('.payment-details').forEach(detail => {
                    detail.style.display = 'none';
                });

                // Tampilkan detail yang dipilih
                const selectedDetails = document.getElementById(this.id + '_details');
                if (selectedDetails) {
                    selectedDetails.style.display = 'block';
                }
            });
        });
    });
    </script>
</body>

</html>