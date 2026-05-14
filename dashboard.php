<?php
session_start();

if (!isset($_SESSION['nama'])) {
    header("Location: auth.php");
    exit();
}

require 'koneksi.php';

$nama_sesi = $_SESSION['nama'];

if (isset($_GET['hapus']) && $nama_sesi === 'admin') {
    $id_hapus = (int) $_GET['hapus'];
    $stmt_hapus = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt_hapus->bind_param("i", $id_hapus);
    $stmt_hapus->execute();
    $stmt_hapus->close();
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            padding: 30px 20px;
        }
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 700px;
            margin: 0 auto;
        }
        h2 { color: #333; margin-bottom: 10px; }
        p { color: #555; margin-bottom: 15px; }
        a.btn-logout {
            display: inline-block;
            padding: 8px 16px;
            background: #333;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 25px;
        }
        a.btn-logout:hover { background: #555; }
        h3 { margin-bottom: 15px; color: #333; }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        th, td {
            padding: 10px 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th { background: #f5f5f5; font-weight: bold; }
        tr:hover { background: #fafafa; }
        .btn-edit {
            display: inline-block;
            padding: 5px 10px;
            background: #c8f135;
            color: #111;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
        }
        .btn-edit:hover { background: #b0d620; }
        .btn-hapus {
            display: inline-block;
            padding: 5px 10px;
            background: #e74c3c;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
        }
        .btn-hapus:hover { background: #c0392b; }
        .user-box {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }
        .user-box p { font-size: 16px; color: #444; }
    </style>
</head>
<body>
<div class="container">
    <h2>Selamat Datang di Dashboard</h2>
    <p>Halo, <strong><?php echo htmlspecialchars($nama_sesi); ?></strong>!</p>
    <a class="btn-logout" href="logout.php">Logout</a>

    <?php if ($nama_sesi === 'admin'): ?>
        <h3>Menu Admin: Kelola Pengguna</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT id, nama FROM users ORDER BY id DESC");
                while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                    <td>
                        <a class="btn-edit" href="edit.php?id=<?php echo $row['id']; ?>">Edit</a>
                        <a class="btn-hapus" href="dashboard.php?hapus=<?php echo $row['id']; ?>"
                           onclick="return confirm('Yakin ingin menghapus pengguna ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    <?php else: ?>
        <div class="user-box">
            <p>Anda login sebagai pengguna reguler.<br>Tidak ada akses ke menu admin.</p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
