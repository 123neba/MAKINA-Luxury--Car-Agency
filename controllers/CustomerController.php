<?php
class CustomerController {
    public function __construct() {
        if ($_SESSION['user_role'] !== 'customer') {
            header('Location: index.php');
            exit;
        }
    }

    public function cars() {
        $carModel = new Car();
        $cars = $carModel->getAll();
        include 'views/customer/cars.php';
    }

    public function carDetails() {
        $id = $_GET['id'];
        $carModel = new Car();
        $car = $carModel->getById($id);
        if ($car) {
            $userModel = new User();
            $seller = $userModel->findById($car['seller_id']);
            include 'views/customer/car_details.php';
        } else {
            header('Location: index.php?action=customerCars');
            exit;
        }
    }
}
