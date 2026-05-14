<?php
session_start();

if (isset($_SESSION['nama'])) {
    header("Location: dashboard.php");
    exit();
}

require 'koneksi.php';
$pesan = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['register'])) {
        $nama = trim($_POST['nama']);
        $password = $_POST['password'];

        if (empty($nama) || empty($password)) {
            $pesan = "Validasi Gagal: Nama dan password wajib diisi.";
        } elseif (strlen($password) < 6) {
            $pesan = "Validasi Gagal: Password minimal 6 karakter.";
        } else {
            $stmt_check = $conn->prepare("SELECT id FROM users WHERE nama = ?");
            $stmt_check->bind_param("s", $nama);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $pesan = "Registrasi Gagal: Nama sudah terdaftar.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("INSERT INTO users (nama, password) VALUES (?, ?)");
                $stmt->bind_param("ss", $nama, $hashed_password);

                if ($stmt->execute()) {
                    $pesan = "Registrasi Berhasil. Silakan login.";
                } else {
                    $pesan = "Kesalahan Server: " . $stmt->error;
                }
                $stmt->close();
            }
            $stmt_check->close();
        }
    }

    if (isset($_POST['login'])) {
        $nama = trim($_POST['nama']);
        $password = $_POST['password'];

        if (empty($nama) || empty($password)) {
            $pesan = "Validasi Gagal: Nama dan password wajib diisi.";
        } else {
            $stmt = $conn->prepare("SELECT password FROM users WHERE nama = ?");
            $stmt->bind_param("s", $nama);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($hashed_password);
                $stmt->fetch();

                if (password_verify($password, $hashed_password)) {
                    $_SESSION['nama'] = $nama;
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $pesan = "Login Gagal: Password salah.";
                }
            } else {
                $pesan = "Login Gagal: Pengguna tidak ditemukan.";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Login & Register</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            margin-bottom: 15px;
            color: #333;
            font-size: 20px;
        }
        .pesan {
            background: #ffe0e0;
            color: #c0392b;
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .pesan.sukses {
            background: #d4edda;
            color: #155724;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        button[type="submit"] {
            width: 100%;
            padding: 10px;
            background: #c8f135;
            color: #111;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }
        button[type="submit"]:hover { background: #b0d620; }
        hr { margin: 25px 0; border: none; border-top: 1px solid #ddd; }
    </style>
</head>
<body>
<div class="container">
    <?php if ($pesan != "") {
        $kelas = (strpos($pesan, 'Berhasil') !== false) ? 'pesan sukses' : 'pesan';
        echo "<div class='$kelas'>$pesan</div>";
    } ?>

    <h2>Registrasi</h2>
    <form method="POST" action="">
        <input type="text" name="nama" placeholder="Nama Pengguna" required><br>
        <input type="password" name="password" placeholder="Password (Min. 6 Karakter)" required><br>
        <button type="submit" name="register">Register</button>
    </form>

    <hr>

    <h2>Login</h2>
    <form method="POST" action="">
        <input type="text" name="nama" placeholder="Nama Pengguna" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit" name="login">Login</button>
    </form>
</div>
</body>
</html>
