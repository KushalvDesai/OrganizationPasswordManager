<?php
session_start();
require_once '../dbconnect.php';

// Serve JSON if it's an AJAX call for usernames
if (isset($_GET['get_usernames_for']) && isset($_SESSION['cid'])) {
    header('Content-Type: application/json');
    $app = $_GET['get_usernames_for'];
    $cid = $_SESSION['cid'];
    
    $stmt = $conn->prepare("SELECT `USERNAME` FROM passwd WHERE `APP-NAME` = ? AND `CID` = ?");
    $stmt->bind_param("si", $app, $cid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $usernames = [];
    while ($row = $result->fetch_assoc()) {
        $usernames[] = $row['USERNAME'];
    }
    echo json_encode($usernames);
    exit;
}

// Normal page load continues
$cid = $_SESSION['cid'] ?? null;

if (!$cid) {
    die("Session expired or not logged in.");
}

$stmtAppNames = $conn->prepare("SELECT DISTINCT `APP-NAME` FROM passwd WHERE `CID` = ?");
$stmtAppNames->bind_param("i", $cid);
$stmtAppNames->execute();
$resultAppNames = $stmtAppNames->get_result();

$appNames = [];
while ($row = $resultAppNames->fetch_assoc()) {
    $appNames[] = $row['APP-NAME'];
}
$stmtAppNames->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              glassDark: "rgba(0, 0, 0, 0.7)",
              glassBorder: "rgba(255, 255, 255, 0.2)",
            },
            boxShadow: {
              glass: "0 8px 30px rgba(0, 0, 0, 0.5)",
            }
          }
        }
      }
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintainer Dashboard</title>

    <script>
        async function fetchUsernames(appName) {
            try {
                const response = await fetch(`index.php?get_usernames_for=${encodeURIComponent(appName)}`);
                if (!response.ok) throw new Error("Network error");
                const usernames = await response.json();
                const dropdown = document.getElementById("USERNAME");
                dropdown.innerHTML = '<option disabled selected>Select Username</option>';
                usernames.forEach(user => {
                    const option = document.createElement("option");
                    option.value = user;
                    option.textContent = user;
                    dropdown.appendChild(option);
                });
            } catch (error) {
                console.error("Failed to load usernames:", error);
            }
        }
    </script>
</head>
<body class="bg-black min-h-screen">
<?php include '../assets/navbar.php'; ?>

<div class="pl-60 pt-16 pb-10 px-4 flex flex-col items-center gap-10 min-h-screen bg-cover bg-center" style="background-image: url('../assets/wallpaper1.webp');">

    <!-- Add New Password Card -->
    <div class="w-full max-w-md transition-transform duration-500 ease-out group hover:scale-[0.98]">
        <div class="p-6 rounded-xl bg-glassDark backdrop-blur-md border border-glassBorder shadow-glass">
            <div class="text-center text-xl font-semibold text-white mb-4">Add New Password</div>
            <form class="flex flex-col gap-4" action="add.php" method="POST">
                <input type="text" name="appname" placeholder="App Name" class="rounded-lg p-3 bg-white/10 border border-glassBorder text-white" required />
                <input type="text" name="username" placeholder="Username" class="rounded-lg p-3 bg-white/10 border border-glassBorder text-white" required />
                <input type="password" name="password" placeholder="Password" class="rounded-lg p-3 bg-white/10 border border-glassBorder text-white" required />
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 transition rounded-lg py-2 text-white font-medium">Add Password</button>
            </form>
        </div>
    </div>

    <!-- Edit Password Card -->
    <div class="w-full max-w-md transition-transform duration-500 ease-out group hover:scale-[0.98]">
        <div class="p-6 rounded-xl bg-glassDark backdrop-blur-md border border-glassBorder shadow-glass">
            <div class="text-center text-xl font-semibold text-white mb-4">Edit Password</div>
            <form class="flex flex-col gap-4" action="edit.php" method="POST">
                <select name="appname" onchange="fetchUsernames(this.value)" class="rounded-lg p-3 bg-white/10 border border-glassBorder text-white" required>
                    <option value="" disabled selected>Select App</option>
                    <?php foreach ($appNames as $appName): ?>
                        <option value="<?= htmlspecialchars($appName) ?>"><?= htmlspecialchars($appName) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="username" id="USERNAME" class="rounded-lg p-3 bg-white/10 border border-glassBorder text-white" required>
                    <option value="" disabled selected>Select Username</option>
                </select>
                <input type="password" name="new_password" placeholder="New Password" class="rounded-lg p-3 bg-white/10 border border-glassBorder text-white" required />
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 transition rounded-lg py-2 text-white font-medium">Update Password</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
