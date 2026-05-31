<?php
session_start();

require_once 'config/Database.php';
$db = new Database();

require_once 'models/User.php';
require_once 'models/Car.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/AdminController.php';
require_once 'controllers/SellerController.php';
require_once 'controllers/CustomerController.php';
require_once 'controllers/PageController.php';

$action = $_GET['action'] ?? 'login';

if (!isset($_SESSION['user_id'])) {
    if (!in_array($action, ['login', 'signup', 'postLogin', 'postSignup', 'about'])) {
        $action = 'login';
    }
} else {
    if (in_array($action, ['login', 'signup', 'postLogin', 'postSignup'])) {
        $role = $_SESSION['user_role'];
        if ($role === 'admin') {
            $action = 'adminUsers';
        } else if ($role === 'seller') {
            $action = 'sellerProfile';
        } else {
            $action = 'customerCars';
        }
    }
}

switch ($action) {
    case 'login': (new AuthController())->login(); break;
    case 'signup': (new AuthController())->signup(); break;
    case 'postLogin': (new AuthController())->postLogin(); break;
    case 'postSignup': (new AuthController())->postSignup(); break;
    case 'logout': (new AuthController())->logout(); break;
    case 'adminUsers': (new AdminController())->users(); break;
    case 'adminBlockUser': (new AdminController())->blockUser(); break;
    case 'adminUnblockUser': (new AdminController())->unblockUser(); break;
    case 'adminDeleteUser': (new AdminController())->deleteUser(); break;
    case 'sellerAddCar': (new SellerController())->addCar(); break;
    case 'sellerPostAddCar': (new SellerController())->postAddCar(); break;
    case 'sellerProfile': (new SellerController())->profile(); break;
    case 'sellerDeleteCar': (new SellerController())->deleteCar(); break;
    case 'sellerEditCar': (new SellerController())->editCar(); break;
    case 'sellerPostEditCar': (new SellerController())->postEditCar(); break;
    case 'customerCars': (new CustomerController())->cars(); break;
    case 'carDetails': (new CustomerController())->carDetails(); break;
    case 'about': (new PageController())->about(); break;
    default: (new AuthController())->login(); break;
}
