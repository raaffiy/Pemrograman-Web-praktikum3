<?php
session_start();

// login
if (isset($_POST['login'])) {

    $username = "admin";
    $password = "123";

    if ($_POST['username'] == $username && $_POST['password'] == $password) {
        $_SESSION['login'] = true;
    } else {
        $error = "Login gagal";
    }
}

// logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// tambah produk
if (isset($_POST['tambah'])) {

    $data = [
        "nama"  => $_POST['nama'],
        "harga" => $_POST['harga']
    ];

    $_SESSION['produk'][] = $data;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Aplikasi Sederhana</title>
</head>

<body>

    <?php if (!isset($_SESSION['login'])) { ?>
    <h2>Login</h2>
    
        <?php
        if (isset($error)) {
            echo $error;
        }
        ?>

        <form method="POST">
            Username <br>
            <input type="text" name="username"><br><br>
            password <br>
            <input type="password" name="password"><br><br>

            <button type="submit" name="login">Login</button>
        </form>

    <?php } else { ?>

        <h2>Dashboard</h2>
        <a href="?logout=true">Logout</a>
        <hr><br>
        
        <h3>Tambah Produk</h3>
        <form method="POST">
            Nama Produk <br>
            <input type="text" name="nama"><br><br>
            Harga <br>
            <input type="text" name="harga"><br><br>
            
            <button type="submit" name="tambah">Tambah</button>
        </form>
        
        <hr><br>
        <h3>Daftar Produk</h3>
        
        <table border="1">
            <tr>
                <th>Nama Produk</th>
                <th>Harga</th>
            </tr>
            
            <?php
            if (isset($_SESSION['produk'])) {
                foreach ($_SESSION['produk'] as $p) {
                    echo "<tr>";
                    echo "<td>".$p['nama']."</td>";
                    echo "<td>".$p['harga']."</td>";
                    echo "</tr>";
                }
            }
            ?>
        </table>
    <?php } ?>
</body>
</html>