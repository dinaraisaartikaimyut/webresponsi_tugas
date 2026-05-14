<?php
session_start();

if (!isset($_SESSION['nama']) || $_SESSION['nama'] !== 'admin' || !isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

require 'koneksi.php';

$id = (int) $_GET['id'];
$pesan = "";

$stmt_get = $conn->prepare("SELECT nama FROM users WHERE id = ?");
$stmt_get->bind_param("i", $id);
$stmt_get->execute();
$stmt_get->store_result();

if ($stmt_get->num_rows === 0) {
    header("Location: dashboard.php");
    exit();
}

$stmt_get->bind_result($nama_lama);
$stmt_get->fetch();
$stmt_get->close();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $nama_baru = trim($_POST['nama']);
    $password_baru = $_POST['password'];

    if (empty($nama_baru) || empty($password_baru)) {
        $pesan = "Nama dan password baru wajib diisi.";
    } else {
        $hashed_password = password_hash($password_baru, PASSWORD_BCRYPT);
        $stmt_update = $conn->prepare("UPDATE users SET nama = ?, password = ? WHERE id = ?");
        $stmt_update->bind_param("ssi", $nama_baru, $hashed_password, $id);

        if ($stmt_update->execute()) {
            $stmt_update->close();
            header("Location: dashboard.php");
            exit();
        } else {
            $pesan = "Kesalahan Server: " . $stmt_update->error;
            $stmt_update->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Pengguna</title>
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
        h2 { margin-bottom: 20px; color: #333; }
        .pesan {
            background: #ffe0e0;
            color: #c0392b;
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        label {
            display: block;
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        .btn-simpan {
            width: 100%;
            padding: 10px;
            background: #c8f135;
            color: #111;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 10px;
        }
        .btn-simpan:hover { background: #b0d620; }
        .btn-batal {
            display: block;
            text-align: center;
            padding: 10px;
            background: #eee;
            color: #333;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-batal:hover { background: #ddd; }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Data Pengguna</h2>

    <?php if ($pesan != ""): ?>
        <div class="pesan"><?php echo htmlspecialchars($pesan); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Nama Pengguna:</label>
        <input type="text" name="nama" value="<?php echo htmlspecialchars($nama_lama); ?>" required>

        <label>Password Baru:</label>
        <input type="password" name="password" placeholder="Masukkan password baru" required>

        <button type="submit" name="update" class="btn-simpan">Simpan Perubahan</button>
    </form>
    <a class="btn-batal" href="dashboard.php">Batal</a>
</div>
</body>
</html>
