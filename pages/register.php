<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $firstName = $_POST["first_name"];
    $lastName = $_POST["last_name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];
    $agree = isset($_POST["agree"]);

    // Validasi sederhana (boleh dikembangkan)
    if ($password !== $confirmPassword) {
        $error = "Password dan konfirmasi tidak cocok.";
    } elseif (!$agree) {
        $error = "Anda harus menyetujui syarat dan ketentuan.";
    } else {
        // Simpan ke database di sini (belum dibuat)
        $success = "Registrasi berhasil!";
    }

    // Validasi
    if ($password !== $confirmPassword) {
        $error = "Password dan konfirmasi tidak cocok.";
    } elseif (!$agree) {
        $error = "Anda harus menyetujui syarat dan ketentuan.";
    } else {
        $name = $firstName . ' ' . $lastName;
        $Password = $password;

        // Query simpan
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $Password);

        if ($stmt->execute()) {
            $success = "Registrasi berhasil!";
        } else {
            $error = "Gagal menyimpan data: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Registrasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</head>

<body class="min-h-screen bg-white flex items-center justify-center grid grid-cols-2">
    <div class="p-10">
        <h2 class="text-3xl font-bold mb-6">Input your information</h2>
        <p class="text-gray-600 text-sm mb-6">
            We need some information for your account. Read our <a href="#" class="text-blue-500">terms and conditions</a>.
        </p>

        <?php if (!empty($error)): ?>
            <div class="text-red-500 mb-4"><?= htmlspecialchars($error) ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="text-green-500 mb-4"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div class="flex gap-4">
                <input type="text" name="first_name" placeholder="First name" required class="w-1/2 border px-3 py-2 rounded">
                <input type="text" name="last_name" placeholder="Last name" required class="w-1/2 border px-3 py-2 rounded">
            </div>
            <div class="flex gap-4">
                <input type="text" name="university" placeholder="University/Company" required class="w-1/2 border px-3 py-2 rounded">
                <input type="email" name="email" placeholder="Email" required class="w-1/2 border px-3 py-2 rounded">
            </div>
            <div class="flex gap-4">
                <select name="umur" required class="w-1/2 border px-3 py-2 rounded">
                    <option value="">Umur</option>
                    <option value="<20">&lt;20</option>
                    <option value="20-25">20-25</option>
                    <option value="25-30">25-30</option>
                    <option value=">35">&gt;35</option>
                </select>
                <input type="text" name="phone" placeholder="Phone number" class="w-1/2 border px-3 py-2 rounded">
            </div>
            <div class="flex gap-4">
                <input type="password" name="password" placeholder="Password" required class="w-1/2 border px-3 py-2 rounded">
                <input type="password" name="confirm_password" placeholder="Confirm password" required class="w-1/2 border px-3 py-2 rounded">
            </div>
            <br>
            <div class="flex flex-wrap">
                <div class="flex items-center me-4">
                    <input id="red-radio" type="radio" value="" name="colored-radio" class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 focus:ring-red-500 dark:focus:ring-red-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="red-radio" class="ms-2 text-sm font-medium ">Pengguna</label>
                </div>
                <div class="flex items-center me-4">
                    <input id="green-radio" type="radio" value="" name="colored-radio" class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="green-radio" class="ms-2 text-sm font-medium">Owner</label>
                </div>
            </div>


            <div class="flex items-center gap-2">
                <input type="checkbox" name="agree" id="agree" required>
                <label for="agree" class="text-sm">I agree with <a href="#" class="text-blue-500">terms and conditions</a>.</label>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Register</button>
        </form>
    </div>
    <div class="bg-blue-50 flex">
        <img src="./assets/register.png" alt="Illustration" class="w-[34.5rem] relative">
    </div>

</body>

</html>