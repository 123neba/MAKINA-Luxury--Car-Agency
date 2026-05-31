<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAKINA — Premium Automotive Portal</title>
    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Sleek Remix Icon Library -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="container header-container">
            <div class="logo">
                <h1><a href="index.php"><span>M</span>akina</a></h1>
            </div>
            <nav class="main-nav">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="index.php?action=about" class="<?= ($_GET['action'] ?? '') == 'about' ? 'active' : '' ?>">
                        <i class="ri-information-line"></i> About
                    </a>
                    <?php if($_SESSION['user_role'] == 'admin'): ?>
                        <a href="index.php?action=adminUsers" class="<?= ($_GET['action'] ?? '') == 'adminUsers' ? 'active' : '' ?>">
                            <i class="ri-group-line"></i> All Users
                        </a>
                    <?php elseif($_SESSION['user_role'] == 'seller'): ?>
                        <a href="index.php?action=sellerAddCar" class="<?= ($_GET['action'] ?? '') == 'sellerAddCar' ? 'active' : '' ?>">
                            <i class="ri-add-circle-line"></i> Add Car
                        </a>
                        <a href="index.php?action=sellerProfile" class="<?= ($_GET['action'] ?? '') == 'sellerProfile' ? 'active' : '' ?>">
                            <i class="ri-user-line"></i> My Profile
                        </a>
                    <?php elseif($_SESSION['user_role'] == 'customer'): ?>
                        <a href="index.php?action=customerCars" class="<?= ($_GET['action'] ?? '') == 'customerCars' || ($_GET['action'] ?? '') == 'carDetails' ? 'active' : '' ?>">
                            <i class="ri-car-line"></i> See Cars
                        </a>
                    <?php endif; ?>
                    <a href="index.php?action=logout" class="logout-btn">
                        <i class="ri-logout-box-r-line"></i> Logout (<?= htmlspecialchars($_SESSION['user_name']) ?>)
                    </a>
                <?php else: ?>
                    <a href="index.php?action=about" class="<?= ($_GET['action'] ?? '') == 'about' ? 'active' : '' ?>">
                        <i class="ri-information-line"></i> About
                    </a>
                    <a href="index.php?action=login" class="<?= ($_GET['action'] ?? 'login') == 'login' ? 'active' : '' ?>">
                        <i class="ri-login-box-line"></i> Login
                    </a>
                    <a href="index.php?action=signup" class="<?= ($_GET['action'] ?? '') == 'signup' ? 'active' : '' ?>">
                        <i class="ri-user-add-line"></i> Sign Up
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="main-content container">
