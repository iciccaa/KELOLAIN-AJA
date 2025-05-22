<?php
// Cek jika session belum dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "./koneksi/db.php";

// Ambil pesan sukses atau error dari session jika ada
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $available_room = $_POST['available_room'];
    $tenant_room = $_POST['tenant_room'];
    $fasilitas = $_POST['fasilitas'];
    $deskripsi = $_POST['deskripsi'];

    $gambar = $_FILES['gambar']['name'];
    $tmp_name = $_FILES['gambar']['tmp_name'];

    // Validasi sederhana
    if (empty($name) || empty($price) || empty($available_room) || empty($tenant_room) || empty($fasilitas) || empty($gambar)) {
        $error = "Harap isi semua kolom yang diperlukan.";
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = "Harga harus berupa angka lebih dari 0.";
    } elseif (!is_numeric($available_room) || !is_numeric($tenant_room)) {
        $error = "Jumlah kamar harus berupa angka.";
    } else {

        $path_gambar = basename($_FILES['gambar']['name']);
        $tmp_name = $_FILES['gambar']['tmp_name'];

        // Path tujuan upload (ke folder "uploads")
        $target_path = "uploads/" . $path_gambar;

        if (move_uploaded_file($tmp_name, $target_path)) {
            // Simpan hanya nama file ke database, bukan path lengkap
            $stmt = $conn->prepare("INSERT INTO rooms (name, price, available_room, tenant_room, fasilitas, deskripsi, gambar) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("siiisss", $name, $price, $available_room, $tenant_room, $fasilitas, $deskripsi, $path_gambar);

            if ($stmt->execute()) {
                $success = "Data kosan berhasil ditambahkan!";
            } else {
                $error = "Gagal menyimpan data: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $error = "Gagal mengupload gambar.";
        }
    }

    // Redirect ke halaman ini agar form tidak dikirim ulang saat refresh
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Kos Pelita Harapan</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">

    <?php include "./Components/admin/sidebar.php"; ?>
    <div class="flex-1 p-10 ml-[16rem]">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Product Information</h1>

        <!-- Notifikasi -->
        <?php if ($error): ?>
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded"><?= $error ?></div>
        <?php elseif ($success): ?>
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded"><?= $success ?></div>
        <?php endif; ?>

        <?php
        $result = $conn->query("SELECT * FROM rooms");
        ?>

        <div class="grid grid-cols-1 gap-6">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="flex justify-between bg-white rounded-xl shadow-md p-6 gap-x-12 relative">
            <!-- Gambar -->
            <div>
                <img src="../uploads/<?php echo $row['gambar']; ?>" alt="gambar" class="rounded-xl" width="200">
            </div>

            <!-- Data kosan -->
            <div class="flex-1">
                <h2 class="text-lg font-bold"><?= htmlspecialchars($row['name']) ?></h2>
                <br><br>
                <p class="text-gray-700 mb-1">Harga: Rp<?= number_format($row['price'], 0, ',', '.') ?></p>
                <p class="text-gray-700 mb-1">Kamar Kosong: <?= $row['available_room'] ?></p>
                <p class="text-gray-700 mb-1">Kamar Terisi: <?= $row['tenant_room'] ?></p>
            </div>

            <!-- Tombol Edit & Hapus -->
            <div class="flex items-center gap-2 absolute top-4 right-4">
                <!-- Tombol Edit -->
                <a href="edit.php?id=<?= $row['id'] ?>" class="bg-blue-500 text-white p-2 rounded-full hover:bg-blue-600" title="Edit">
                    ✏️
                </a>

                <!-- Tombol Hapus -->
                <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')" class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600" title="Hapus">
                    🗑️
                </a>
            </div>
        </div>
    <?php endwhile; ?>
</div>


        <!-- Div pemberitahuan, selalu di bawah -->
        <div class="flex items-center justify-center mt-10">


            <div class="flex flex-col gap-4 items-center text-center">
                <a href="#" onclick="document.getElementById('modalKamar').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800">
                    Tambah Kamar
                </a>
            </div>


        </div>
    </div>


    <!-- Modal -->
    <div id="modalKamar" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class=" w-full max-w-sm p-6 bg-white border rounded-lg shadow-md relative">
            <button onclick="document.getElementById('modalKamar').classList.add('hidden')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
            <form class="space-y-4" action="product" method="POST" enctype="multipart/form-data">
                <h5 class="text-xl font-medium text-gray-900">Input Data Kamar</h5>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="name" class="block text-sm font-medium">Nama Kamar</label>
                        <input type="text" name="name" required class="input-field" placeholder="Kamar A1" />
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium">Harga</label>
                        <input type="number" name="price" required class="input-field" placeholder="1200000" />
                    </div>

                    <div>
                        <label for="available_room" class="block text-sm font-medium">Kamar Kosong</label>
                        <input type="number" name="available_room" required class="input-field" placeholder="3" />
                    </div>
                </div>

                <div>
                    <label for="tenant_room" class="block text-sm font-medium">Kamar Terisi</label>
                    <input type="number" name="tenant_room" required class="input-field" placeholder="2" />
                </div>

                <div>
                    <label for="fasilitas" class="block text-sm font-medium">Fasilitas</label>
                    <input type="text" name="fasilitas" required class="input-field" placeholder="WiFi, AC, Lemari" />
                </div>

                <div>
                    <label for="deskripsi" class="block text-sm font-medium">Deskripsi</label>
                    <textarea name="deskripsi" required class="input-field" placeholder="Tulis deskripsi kamar..."></textarea>
                </div>

                <div>
                    <label for="gambar" class="block text-sm font-medium">Upload Gambar</label>
                    <input type="file" name="gambar" required class="block w-full text-sm text-gray-500" />
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded">
                    Simpan
                </button>
            </form>
        </div>
    </div>

    <style>
        .input-field {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            color: #111827;
            padding: 0.5rem;
            border-radius: 0.375rem;
            width: 100%;
        }
    </style>

</body>

</html>