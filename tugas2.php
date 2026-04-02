<?php
session_start();
// koneksi database
$conn = mysqli_connect("localhost", "root", "", "AgriLens");
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// LOGIN 
if (isset($_POST['login'])) {
    if ($_POST['username'] == "admin" && $_POST['password'] == "123") {
        $_SESSION['login'] = true;
    } else {
        $error = "Login gagal";
    }
}

// LOGOUT 
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// SIMPAN (CRUD) 
if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];

    // upload file
    $fileName = $_FILES['file']['name'];
    $tmpName = $_FILES['file']['tmp_name'];

    if ($fileName != "") {
        $uploadPath = "uploads/" . $fileName;
        move_uploaded_file($tmpName, $uploadPath);
    }

    if ($id == "") {
        // CREATE
        $date = date("Y-m-d H:i:s");
        mysqli_query($conn, "INSERT INTO news (judul, deskripsi, file, date)
        VALUES ('$judul', '$deskripsi', '$fileName', '$date')");
    } else {
        // UPDATE
        if ($fileName != "") {
            mysqli_query($conn, "UPDATE news 
            SET judul='$judul', deskripsi='$deskripsi', file='$fileName' 
            WHERE id=$id");
        } else {
            mysqli_query($conn, "UPDATE news 
            SET judul='$judul', deskripsi='$deskripsi' 
            WHERE id=$id");
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// DELETE 
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    mysqli_query($conn, "DELETE FROM news WHERE id=$id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


// EDIT (AMBIL DATA) 
$editData = null;

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM news WHERE id=$id");
    $editData = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Aplikasi CRUD + Upload</title>
</head>

<body>
<?php if (!isset($_SESSION['login'])) { ?>

    <h2>Login</h2>
    <?php if (isset($error)) echo $error; ?>

    <form method="POST">
        Username <br>
        <input type="text" name="username"><br><br>
        Password <br>
        <input type="password" name="password"><br><br>
        <button type="submit" name="login">Login</button>
    </form>

<?php } else { ?>

    <h2>Dashboard</h2>
    <a href="?logout=true">Logout</a>
    <hr><br>

    <!-- FORM -->
    <h3><?= $editData ? "Edit News" : "Tambah News" ?></h3>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $editData['id'] ?? '' ?>">
        Judul <br>
        <input type="text" name="judul" value="<?= $editData['judul'] ?? '' ?>"><br><br>
        Deskripsi <br>
        <textarea name="deskripsi"><?= $editData['deskripsi'] ?? '' ?></textarea><br><br>
        Upload File <br>
        <input type="file" name="file"><br><br>

        <?php if (!empty($editData['file'])) { ?>
            File lama: <br>
            <img src="uploads/<?= $editData['file'] ?>" width="100"><br><br>
        <?php } ?>

        <button type="submit" name="simpan">Simpan</button>
    </form>

    <hr><br>

    <!-- DATA -->
    <h3>Daftar News</h3>
    <table border="1" cellpadding="10">
        <tr>
            <th>Judul</th>
            <th>Deskripsi</th>
            <th>File</th>
            <th>Date</th>
            <th>Aksi</th>
        </tr>

        <?php
        $result = mysqli_query($conn, "SELECT * FROM news");
        while ($p = mysqli_fetch_assoc($result)) {
        ?>
            <tr>
                <td><?= $p['judul'] ?></td>
                <td><?= $p['deskripsi'] ?></td>
                <td>
                    <?php if ($p['file']) { ?>
                        <img src="uploads/<?= $p['file'] ?>" width="80">
                    <?php } ?>
                </td>
                <td><?= $p['date'] ?></td>
                <td>
                    <a href="?edit=<?= $p['id'] ?>">Edit</a> |
                    <a href="?delete=<?= $p['id'] ?>" onclick="return confirm('Yakin hapus?')">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>

<?php } ?>
</body>
</html>