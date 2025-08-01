<?php
session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit;
}

$redirectPage = ($_SESSION['ROLE'] == 0) ? "admin/index.php" : "maintainer/index.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vault Animation</title>
    <style>
        /* (Your existing styles for vault animation) */
        body {
            background-color: #121212;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .vault-door {
            width: 200px;
            height: 200px;
            border: 10px solid #555;
            border-radius: 50%;
            position: relative;
            background: radial-gradient(circle, #333, #000);
        }

        .vault-handle {
            width: 250px;
            height: 250px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(0deg);
            animation: spin-handle 2s ease-out forwards;
        }

        .vault-handle img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        @keyframes spin-handle {
            to {
                transform: translate(-50%, -50%) rotate(150deg);
            }
        }        
    </style>
</head>
<body>
    <div class="vault-door">
        <div class="vault-handle">
            <img src="./assets/vault_handle.png" alt="Vault Handle">
        </div>
    </div>
    <script>
        setTimeout(() => {
            window.location.href = "<?php echo $redirectPage; ?>";
        }, 1500);
    </script>
</body>
</html>
