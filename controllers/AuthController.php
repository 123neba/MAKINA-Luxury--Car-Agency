<?php
class AuthController {
    public function login() {
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        include 'views/auth/login.php';
    }

    public function signup() {
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        include 'views/auth/signup.php';
    }

    public function postLogin() {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] == 'blocked') {
                $_SESSION['error'] = 'Your account is blocked.';
                header('Location: index.php?action=login');
                exit;
            }
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['first_name'];
            header('Location: index.php');
            exit;
        } else {
            $_SESSION['error'] = 'Invalid email or password.';
            header('Location: index.php?action=login');
            exit;
        }
    }

    public function postSignup() {
        $data = [
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'dob' => $_POST['dob'],
            'phone_number' => $_POST['phone_number'],
            'email' => $_POST['email'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'role' => $_POST['role']
        ];

        $userModel = new User();
        if ($userModel->findByEmail($data['email'])) {
            $_SESSION['error'] = 'Email already exists.';
            header('Location: index.php?action=signup');
            exit;
        }

        if ($userModel->create($data)) {
            header('Location: index.php?action=login');
            exit;
        } else {
            $_SESSION['error'] = 'Registration failed.';
            header('Location: index.php?action=signup');
            exit;
        }
    }

    public function logout() {
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }
}
