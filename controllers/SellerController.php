<?php
class SellerController {
    public function __construct() {
        if ($_SESSION['user_role'] !== 'seller') {
            header('Location: index.php');
            exit;
        }
    }

    public function addCar() {
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        include 'views/seller/add_car.php';
    }

    public function postAddCar() {
        if (!is_dir('assets/uploads')) {
            mkdir('assets/uploads', 0777, true);
        }

        $slots = [
            'image_front' => 'Front',
            'image_back' => 'Back',
            'image_interior' => 'Interior',
        ];
        $paths = [];
        $hashes = [];

        foreach ($slots as $field => $label) {
            $path = $this->uploadImage($field);
            if (!$path) {
                $_SESSION['error'] = "Please upload a separate {$label} photo (JPG, PNG, or WEBP, max 5MB).";
                header('Location: index.php?action=sellerAddCar');
                exit;
            }
            $hash = md5_file($path);
            if (in_array($hash, $hashes, true)) {
                foreach ($paths as $uploadedPath) {
                    if (file_exists($uploadedPath)) {
                        unlink($uploadedPath);
                    }
                }
                if (file_exists($path)) {
                    unlink($path);
                }
                $_SESSION['error'] = 'Each photo must be different. Upload one Front, one Back, and one Interior image.';
                header('Location: index.php?action=sellerAddCar');
                exit;
            }
            $hashes[] = $hash;
            $paths[$field] = $path;
        }

        $data = [
            'seller_id' => $_SESSION['user_id'],
            'model' => $_POST['model'],
            'year' => $_POST['year'],
            'price' => $_POST['price'],
            'brand' => $_POST['brand'],
            'license_plate' => $_POST['lp_char1'] . '-' . $_POST['lp_num'] . '-' . $_POST['lp_char2'],
            'summary' => $_POST['summary'],
            'image_front' => $paths['image_front'],
            'image_back' => $paths['image_back'],
            'image_interior' => $paths['image_interior'],
        ];

        (new Car())->create($data);
        header('Location: index.php?action=sellerProfile');
        exit;
    }

    private function uploadImage($inputName) {
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES[$inputName];
        if ($file['size'] > 5 * 1024 * 1024) {
            return null;
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, $allowed, true)) {
            return null;
        }

        $extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $fileName = $inputName . '_' . uniqid('', true) . '.' . $extMap[$mime];
        $path = 'assets/uploads/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $path)) {
            return null;
        }

        return $path;
    }

    public function profile() {
        $carModel = new Car();
        $cars = $carModel->getBySeller($_SESSION['user_id']);
        include 'views/seller/profile.php';
    }

    public function deleteCar() {
        $id = $_GET['id'];
        (new Car())->delete($id, $_SESSION['user_id']);
        header('Location: index.php?action=sellerProfile');
        exit;
    }

    public function editCar() {
        $carModel = new Car();
        $car = $carModel->getByIdAndSeller($_GET['id'], $_SESSION['user_id']);
        if (!$car) {
            header('Location: index.php?action=sellerProfile');
            exit;
        }
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        include 'views/seller/edit_car.php';
    }

    public function postEditCar() {
        $id        = $_POST['car_id'] ?? 0;
        $sellerId  = $_SESSION['user_id'];

        $carModel  = new Car();
        $existing  = $carModel->getByIdAndSeller($id, $sellerId);
        if (!$existing) {
            header('Location: index.php?action=sellerProfile');
            exit;
        }

        if (!is_dir('assets/uploads')) {
            mkdir('assets/uploads', 0777, true);
        }

        $data = [
            'brand'         => $_POST['brand'],
            'model'         => $_POST['model'],
            'year'          => $_POST['year'],
            'price'         => $_POST['price'],
            'license_plate' => $_POST['lp_char1'] . '-' . $_POST['lp_num'] . '-' . $_POST['lp_char2'],
            'summary'       => $_POST['summary'],
            'image_front'   => '',
            'image_back'    => '',
            'image_interior'=> '',
        ];

        $slots = [
            'image_front'    => 'Front',
            'image_back'     => 'Back',
            'image_interior' => 'Interior',
        ];

        foreach ($slots as $field => $label) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                $newPath = $this->uploadImage($field);
                if (!$newPath) {
                    $_SESSION['error'] = "Invalid {$label} image. Use JPG, PNG, or WEBP under 5MB.";
                    header("Location: index.php?action=sellerEditCar&id={$id}");
                    exit;
                }
                // Delete old local file if it exists
                if (!empty($existing[$field]) && strpos($existing[$field], 'http') === false && file_exists($existing[$field])) {
                    unlink($existing[$field]);
                }
                $data[$field] = $newPath;
            }
        }

        $carModel->update($id, $sellerId, $data);
        header('Location: index.php?action=sellerProfile');
        exit;
    }
}
