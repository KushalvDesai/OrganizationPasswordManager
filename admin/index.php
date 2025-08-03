<?php
session_start();
require_once '../dbconnect.php';

$cid = $_SESSION['cid'] ?? null;
if (!$cid) {
    die("Session expired. Please log in again.");
}

// Fetch distinct app names for dropdowns
$stmtAppNames = $conn->prepare("SELECT DISTINCT `APP-NAME` FROM passwd WHERE `CID` = ?");
$stmtAppNames->bind_param("i", $cid);
$stmtAppNames->execute();
$resultAppNames = $stmtAppNames->get_result();

$appNames = [];
while ($row = $resultAppNames->fetch_assoc()) {
    $appNames[] = $row['APP-NAME'];
}
$stmtAppNames->close();

// AJAX Handler: Fetch usernames
if (isset($_GET['fetchUsernames']) && isset($_GET['appname'])) {
    $appname = $_GET['appname'];
    $stmt = $conn->prepare("SELECT `USERNAME` FROM passwd WHERE `CID` = ? AND `APP-NAME` = ?");
    $stmt->bind_param("is", $cid, $appname);
    $stmt->execute();
    $res = $stmt->get_result();
    $usernames = [];
    while ($row = $res->fetch_assoc()) {
        $usernames[] = $row['USERNAME'];
    }
    echo json_encode($usernames);
    exit;
}

// AJAX Handler: Fetch passwords
if (isset($_GET['fetchPasswords']) && isset($_GET['appname'])) {
    $appname = $_GET['appname'];
    $stmt = $conn->prepare("SELECT `USERNAME`, `PASS` FROM passwd WHERE `CID` = ? AND `APP-NAME` = ?");
    $stmt->bind_param("is", $cid, $appname);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}
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
    <title>Admin Dashboard</title>
    <script>
        async function fetchUsernames(appName) {
            const response = await fetch(`?fetchUsernames=1&appname=${encodeURIComponent(appName)}`);
            const usernames = await response.json();
            const dropdown = document.getElementById('USERNAME');
            dropdown.innerHTML = '<option value="" disabled selected>Select Username</option>';
            usernames.forEach(user => {
                const option = document.createElement('option');
                option.value = user;
                option.textContent = user;
                dropdown.appendChild(option);
            });
        }

        async function fetchPasswords(appName) {
            const response = await fetch(`?fetchPasswords=1&appname=${encodeURIComponent(appName)}`);
            const data = await response.json();
            const tableBody = document.querySelector("#password-table tbody");
            tableBody.innerHTML = "";

            data.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-4 py-2">${appName}</td>
                    <td class="px-4 py-2">${item.USERNAME}</td>
                    <td class="px-4 py-2 flex items-center gap-2">
                        <input type="text" value="${item.PASS}" readonly class="bg-transparent text-white outline-none" id="pass-${item.USERNAME}">
                        <button onclick="copyPassword('${item.USERNAME}')" class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded text-sm">Copy</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        function copyPassword(username) {
            const input = document.getElementById(`pass-${username}`);
            input.select();
            input.setSelectionRange(0, 99999);
            document.execCommand("copy");
            alert("Password copied to clipboard");
        }
    </script>
</head>
<body class="bg-black min-h-screen">
    <?php include '../assets/navbar.php'; ?>
    <div class="pl-60 pt-16 pb-10 px-4 flex flex-col items-center gap-10 min-h-screen bg-cover bg-center" style="background-image: url('../assets/wallpaper1.webp');">

        <!-- Add New Password Card -->
        <div class="w-full max-w-md transition-transform duration-500 group hover:scale-[0.98]">
            <div class="p-6 rounded-xl bg-glassDark backdrop-blur-md border border-glassBorder shadow-glass">
                <div class="text-center text-xl font-semibold text-white mb-4">Add New Password</div>
                <form class="flex flex-col gap-4" action="add.php" method="POST">
                    <input type="text" name="appname" placeholder="App Name" class="rounded-lg p-3 bg-white/10 border border-glassBorder text-white" required />
                    <input type="text" name="username" placeholder="Username" class="rounded-lg p-3 bg-white/10 border border-glassBorder text-white" required />
                    <input type="password" name="password" placeholder="Password" class="rounded-lg p-3 bg-white/10 border border-glassBorder text-white" required />
                    <label class="flex items-center gap-2 text-white">
                        <input type="checkbox" name="access" value="1" class="accent-blue-500 scale-125" />
                        <span class="text-sm">Allow others to view?</span>
                    </label>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 transition rounded-lg py-2 text-white font-medium">
                        Add Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Edit Password Card -->
        <div class="w-full max-w-md transition-transform duration-500 group hover:scale-[0.98]">
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
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 transition rounded-lg py-2 text-white font-medium">
                        Update Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Search Password Card -->
        <div class="w-full max-w-md transition-transform duration-500 group hover:scale-[0.98]">
            <div class="p-6 rounded-xl bg-glassDark backdrop-blur-md border border-glassBorder shadow-glass">
                <div class="text-center text-xl font-semibold text-white mb-4">Search Passwords</div>
                <div class="flex flex-col gap-4">
                    <label for="search-appname" class="text-white">Select App Name:</label>
                    <select 
                        id="search-appname" 
                        onchange="fetchPasswords(this.value)" 
                        class="rounded-lg p-3 bg-white/10 border border-glassBorder text-white"
                    >
                        <option value="" disabled selected>Select App</option>
                        <?php foreach ($appNames as $appName): ?>
                            <option value="<?= htmlspecialchars($appName) ?>"><?= htmlspecialchars($appName) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <div class="overflow-x-auto">
                        <table id="password-table" class="w-full text-white text-left border-collapse mt-4">
                            <thead class="bg-white/10 border-b border-glassBorder">
                                <tr>
                                    <th class="px-4 py-2">App Name</th>
                                    <th class="px-4 py-2">Username</th>
                                    <th class="px-4 py-2">Password</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Filled by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
