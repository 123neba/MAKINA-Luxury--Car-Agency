<?php
class AdminController {
    public function __construct() {
        if ($_SESSION['user_role'] !== 'admin') {
            header('Location: index.php');
            exit;
        }
    }

    public function users() {
        $userModel = new User();
        $users = $userModel->getAllUsers();
        include 'views/admin/users.php';
    }

    public function blockUser() {
        $id = $_GET['id'];
        (new User())->updateStatus($id, 'blocked');
        header('Location: index.php?action=adminUsers');
        exit;
    }

    public function unblockUser() {
        $id = $_GET['id'];
        (new User())->updateStatus($id, 'active');
        header('Location: index.php?action=adminUsers');
        exit;
    }

    public function deleteUser() {
        $id = $_GET['id'];
        (new User())->delete($id);
        header('Location: index.php?action=adminUsers');
        exit;
    }
}
