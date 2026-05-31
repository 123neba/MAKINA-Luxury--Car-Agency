<?php
class Database {
    private $host = '127.0.0.1';
    private $username = 'root';
    private $password = '';
    private $dbname = 'makina_db';
    public $conn;

    public function __construct() {
        $this->host = '127.0.0.1';
        
        // Attempt default connection (Port 3306)
        try {
            $this->conn = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
        } catch(PDOException $e) {
            // Fallback to Port 3307 if default fails (e.g., due to Workbench)
            try {
                $this->conn = new PDO("mysql:host=" . $this->host . ";port=3307", $this->username, $this->password);
            } catch(PDOException $e2) {
                die("<div style='font-family: sans-serif; text-align: center; margin-top: 50px; color: #e74c3c;'>
                        <h2>Database Connection Error</h2>
                        <p>" . htmlspecialchars($e2->getMessage()) . "</p>
                        <p><b>Troubleshooting:</b> Please ensure MySQL is running in XAMPP.</p>
                     </div>");
            }
        }

        // Proceed with database setup if connection is successful
        try {
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("CREATE DATABASE IF NOT EXISTS `" . $this->dbname . "`");
            $this->conn->exec("USE `" . $this->dbname . "`");
            
            $this->createTables();
            $this->seedAdmin();
            $this->seedSampleDataIfNeeded();
        } catch(PDOException $e) {
            die("<div style='font-family: sans-serif; text-align: center; margin-top: 50px; color: #e74c3c;'>
                    <h2>Database Setup Error</h2>
                    <p>" . htmlspecialchars($e->getMessage()) . "</p>
                 </div>");
        }
    }

    private function createTables() {
        $sqlUsers = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            dob DATE NOT NULL,
            phone_number VARCHAR(20) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'seller', 'customer') NOT NULL DEFAULT 'customer',
            status ENUM('active', 'blocked') NOT NULL DEFAULT 'active'
        )";
        $this->conn->exec($sqlUsers);

        $sqlCars = "CREATE TABLE IF NOT EXISTS cars (
            id INT AUTO_INCREMENT PRIMARY KEY,
            seller_id INT NOT NULL,
            model VARCHAR(100) NOT NULL,
            year INT NOT NULL,
            price DECIMAL(15,2) NOT NULL,
            brand VARCHAR(100) NOT NULL,
            license_plate VARCHAR(20) NOT NULL,
            summary TEXT,
            image_front VARCHAR(255),
            image_back VARCHAR(255),
            image_interior VARCHAR(255),
            FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
        )";
        $this->conn->exec($sqlCars);
    }

    private function seedAdmin() {
        $email = 'mat@gmail.com';
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if($stmt->rowCount() == 0) {
            $password = password_hash('0123', PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (first_name, last_name, dob, phone_number, email, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtInsert = $this->conn->prepare($sql);
            $stmtInsert->execute(['Matt', 'MES', '2003-09-25', '0987279321', $email, $password, 'admin']);
        }
    }

    private const SEED_IMAGE_VERSION = 4;

    private function commonsImage($filename) {
        $encoded = str_replace(' ', '_', $filename);
        return 'https://commons.wikimedia.org/wiki/Special:FilePath/' . rawurlencode($encoded) . '?width=1280';
    }

    private function isValidImageFile($path) {
        if (!is_file($path) || filesize($path) < 5000) {
            return false;
        }
        $info = @getimagesize($path);
        return $info !== false && in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true);
    }

    private function purgeSeedImagesIfNeeded() {
        $versionFile = 'assets/.seed_image_version';
        $current = file_exists($versionFile) ? (int) file_get_contents($versionFile) : 0;
        if ($current >= self::SEED_IMAGE_VERSION) {
            return;
        }
        $dir = 'assets/uploads';
        if (is_dir($dir)) {
            foreach (glob($dir . '/*') as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
        file_put_contents($versionFile, (string) self::SEED_IMAGE_VERSION);
    }

    private function downloadImage($url, $filename) {
        $dir = 'assets/uploads';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $path = $dir . '/' . $filename;

        if ($this->isValidImageFile($path)) {
            return $path;
        }
        if (file_exists($path)) {
            unlink($path);
        }

        // Try PHP file_get_contents
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n",
                'timeout' => 20,
                'follow_location' => 1,
            ],
        ];
        $context = stream_context_create($opts);

        try {
            $img = @file_get_contents($url, false, $context);
            if ($img !== false && strlen($img) > 5000) {
                file_put_contents($path, $img);
                if ($this->isValidImageFile($path)) {
                    return $path;
                }
                unlink($path);
            }
        } catch (Exception $e) {
            // fall through
        }

        // Fallback 1: PHP cURL extension
        if (function_exists('curl_init')) {
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $img = curl_exec($ch);
                curl_close($ch);
                if ($img !== false && strlen($img) > 5000) {
                    file_put_contents($path, $img);
                    if ($this->isValidImageFile($path)) {
                        return $path;
                    }
                    unlink($path);
                }
            } catch (Exception $e) {
                // fall through
            }
        }

        // Fallback 2: System curl.exe command (common on Windows)
        try {
            $escUrl = escapeshellarg($url);
            $escPath = escapeshellarg($path);
            exec("curl.exe -L -k -H \"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\" -o $escPath $escUrl 2>&1");
            if ($this->isValidImageFile($path)) {
                return $path;
            }
            if (file_exists($path)) {
                unlink($path);
            }
        } catch (Exception $e) {
            // fall through
        }

        return $url;
    }

    private function ensureUser($email, $firstName, $lastName, $dob, $phone, $role) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() == 0) {
            $password = password_hash('0123', PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (first_name, last_name, dob, phone_number, email, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtInsert = $this->conn->prepare($sql);
            $stmtInsert->execute([$firstName, $lastName, $dob, $phone, $email, $password, $role]);
            return (int) $this->conn->lastInsertId();
        }
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['id'];
    }

    private function upsertCar($car) {
        $stmt = $this->conn->prepare("SELECT id FROM cars WHERE license_plate = ?");
        $stmt->execute([$car['license_plate']]);

        if ($stmt->rowCount() > 0) {
            $sql = "UPDATE cars SET seller_id = ?, brand = ?, model = ?, year = ?, price = ?, summary = ?,
                    image_front = ?, image_back = ?, image_interior = ? WHERE license_plate = ?";
            $stmtUpdate = $this->conn->prepare($sql);
            $stmtUpdate->execute([
                $car['seller_id'],
                $car['brand'],
                $car['model'],
                $car['year'],
                $car['price'],
                $car['summary'],
                $car['image_front'],
                $car['image_back'],
                $car['image_interior'],
                $car['license_plate'],
            ]);
            return;
        }

        $sql = "INSERT INTO cars (seller_id, brand, model, year, price, license_plate, summary, image_front, image_back, image_interior) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsert = $this->conn->prepare($sql);
        $stmtInsert->execute([
            $car['seller_id'],
            $car['brand'],
            $car['model'],
            $car['year'],
            $car['price'],
            $car['license_plate'],
            $car['summary'],
            $car['image_front'],
            $car['image_back'],
            $car['image_interior'],
        ]);
    }

    private function seedSampleDataIfNeeded() {
        // Only seed when the cars table is empty (first run)
        $count = (int) $this->conn->query('SELECT COUNT(*) FROM cars')->fetchColumn();
        if ($count > 0) {
            return;
        }
        $this->seedSampleData();
    }

    private function seedSampleData() {
        $seller1Id = $this->ensureUser('samuel@gmail.com', 'Samuel', 'Bekele', '1990-06-15', '0911223344', 'seller');
        $seller2Id = $this->ensureUser('meron@gmail.com', 'Meron', 'Tadesse', '1988-03-22', '0922334455', 'seller');
        $seller3Id = $this->ensureUser('daniel@gmail.com', 'Daniel', 'Alemu', '1985-11-08', '0933445566', 'seller');
        $seller4Id = $this->ensureUser('hanna@gmail.com', 'Hanna', 'Girma', '1992-01-14', '0944556677', 'seller');
        $seller5Id = $this->ensureUser('yonas@gmail.com', 'Yonas', 'Kebede', '1987-07-30', '0955667788', 'seller');

        $this->ensureUser('sara@gmail.com', 'Sara', 'Hailu', '1998-04-12', '0966778899', 'customer');
        $this->ensureUser('david@gmail.com', 'David', 'Mekonnen', '1995-09-03', '0977889900', 'customer');
        $this->ensureUser('lydia@gmail.com', 'Lydia', 'Tesfaye', '2000-12-21', '0988990011', 'customer');
        $this->ensureUser('peter@gmail.com', 'Peter', 'Negash', '1993-06-18', '0999001122', 'customer');
        $this->ensureUser('ruth@gmail.com', 'Ruth', 'Worku', '1997-02-27', '0900112233', 'customer');

        $this->purgeSeedImagesIfNeeded();

        // Verified Wikimedia Commons filenames (front / back / interior per model)
        $imageFiles = [
            'byd_front'      => 'BYD Atto 3 IMG 9753.jpg',
            'byd_back'       => 'BYD Atto 3 (Yuan Plus) Rear.jpg',
            'byd_int'        => 'BYD Atto 3 interior.jpg',

            'lc300_front'    => '2022 Toyota Land Cruiser 300 ZX (Japan), front 8.27.22.jpg',
            'lc300_back'     => '2022 Toyota Land Cruiser 300 ZX (Japan), rear 8.27.22.jpg',
            'lc300_int'      => '2022 Toyota Land Cruiser 300 ZX (Japan), interior 8.27.22.jpg',

            'jimny_front'    => '2019 Suzuki Jimny SZ5 AllGrip 1.5.jpg',
            'jimny_back'     => '2019 Suzuki Jimny SZ5 AllGrip 1.5 rear.jpg',
            'jimny_int'      => '2019 Suzuki Jimny SZ5 AllGrip 1.5 interior.jpg',

            'mustang_front'  => '2020 Ford Mustang GT (facelift, grey), front 8.11.20.jpg',
            'mustang_back'   => '2020 Ford Mustang GT (facelift, grey), rear 8.11.20.jpg',
            'mustang_int'    => '2020 Ford Mustang GT (facelift), interior 8.11.20.jpg',

            'tucson_front'   => '2022 Hyundai Tucson (NX4) Elite 2WD (Australia), front 8.16.22.jpg',
            'tucson_back'    => '2022 Hyundai Tucson (NX4) Elite 2WD (Australia), rear 8.16.22.jpg',
            'tucson_int'     => '2022 Hyundai Tucson (NX4) Elite 2WD (Australia), interior 8.16.22.jpg',

            'hilux_front'    => '2021 Toyota Hilux Revo GR Sport 2.8 4WD AT (Thailand), front 9.16.21.jpg',
            'hilux_back'     => '2021 Toyota Hilux Revo GR Sport 2.8 4WD AT (Thailand), rear 9.16.21.jpg',
            'hilux_int'      => '2021 Toyota Hilux Revo GR Sport 2.8 4WD AT (Thailand), interior 9.16.21.jpg',

            'gle_front'      => '2019 Mercedes-Benz GLE 300d AMG Line (W167), front 8.8.19.jpg',
            'gle_back'       => '2019 Mercedes-Benz GLE 300d AMG Line (W167), rear 8.8.19.jpg',
            'gle_int'        => '2019 Mercedes-Benz GLE 300d AMG Line (W167), interior 8.8.19.jpg',

            'sonata_front'   => '2020 Hyundai Sonata (DN8) Luxury 2.0, front 10.15.19.jpg',
            'sonata_back'    => '2020 Hyundai Sonata (DN8) Luxury 2.0, rear 10.15.19.jpg',
            'sonata_int'     => '2020 Hyundai Sonata (DN8) Luxury 2.0, interior 10.15.19.jpg',

            'corolla_front'  => '2020 Toyota Corolla Hatchback XSE in Blizzard Pearl, Front Left, 09-02-2023.jpg',
            'corolla_back'   => '2020 Toyota Corolla Hatchback XSE in Blizzard Pearl, Rear Left, 09-02-2023.jpg',
            'corolla_int'    => '2020 Toyota Corolla Hatchback XSE in Blizzard Pearl, Interior, 09-02-2023.jpg',

            'bmw_front'      => '2019 BMW 330i M Sport 2.0 Front.jpg',
            'bmw_back'       => '2019 BMW 330i M Sport 2.0 Rear.jpg',
            'bmw_int'        => '2019 BMW 320d xDrive M Sport 2.0 Interior.jpg',

            'sportage_front' => '2022 KIA Sportage GT-Line - 1598cc 1.6 (148PS) Petrol - Orange Fusion - 02-2025, Front.jpg',
            'sportage_back'  => '2022 KIA Sportage GT-Line - 1598cc 1.6 (148PS) Petrol - Orange Fusion - 02-2025, Rear.jpg',
            'sportage_int'   => 'Kia Sportage NQ5 Black Interior (1).jpg',

            'xtrail_front'   => '2023 Nissan X-Trail Ti front.jpg',
            'xtrail_back'    => '2023 Nissan X-Trail Ti rear.jpg',
            'xtrail_int'     => 'The interior of Nissan X-TRAIL G e-4ORCE (6AA-SNT33).jpg',

            'rav4_front'     => '2019 Toyota RAV4 XLE AWD, front 12.31.19.jpg',
            'rav4_back'      => '2019 Toyota RAV4 XLE AWD, rear 12.31.19.jpg',
            'rav4_int'       => '2019 Toyota RAV4 Interior.jpg',

            'tiguan_front'   => '2022 Volkswagen Tiguan R-Line TSi S-A 1.5 Front.jpg',
            'tiguan_back'    => '2022 Volkswagen Tiguan R-Line TSi S-A 1.5 Rear.jpg',
            'tiguan_int'     => '2020 Volkswagen Tiguan Life facelift Interior.jpg',

            'q5_front'       => '2018 Audi Q5 S Line TDi Quattro S-A 2.0 Front.jpg',
            'q5_back'        => '2018 Audi Q5 S Line TDi Quattro S-A 2.0 Rear.jpg',
            'q5_int'         => '2018 Audi Q5 TDi Quattro Interior.jpg',

            'lexus_front'    => '2023 Lexus RX 350 Premium Plus in Matador Red Mica, front left.jpg',
            'lexus_back'     => '2023 Lexus RX 350 Premium Plus in Matador Red Mica, rear left.jpg',
            'lexus_int'      => '2016 Lexus RX 200t interior.jpg',

            'crv_front'      => 'Honda CR-V EX・Masterpiece (DBA-RW1) front.jpg',
            'crv_back'       => 'Honda CR-V EX・Masterpiece (DBA-RW1) rear.jpg',
            'crv_int'        => 'Honda CRV 2.4 SX 2013 Interior.jpg',

            'cx5_front'      => '2024 Mazda CX-5 Suna in Zircon Sand Metallic, Front Left, 2024-03-03.jpg',
            'cx5_back'       => '2024 Mazda CX-5 Suna in Zircon Sand Metallic, Rear Left, 2024-03-03.jpg',
            'cx5_int'        => '2014 Mazda CX-5 SE-L LUX NAV Diesel 2.2 Interior.jpg',

            'tesla_front'    => '2019 Tesla Model 3 Performance AWD Front.jpg',
            'tesla_back'     => '2019 Tesla Model 3 Performance AWD Rear.jpg',
            'tesla_int'      => 'Tesla Model 3 (2023), long range, Japan, interior.jpg',

            'evoque_front'   => '2019 Land Rover Range Rover Evoque First Edition D180 Automatic 2.0 Front.jpg',
            'evoque_back'    => '2019 Land Rover Range Rover Evoque First Edition D180 Automatic 2.0 Rear.jpg',
            'evoque_int'     => '2019 Land Rover Range Rover Evoque D180 SE Interior.jpg',

        ];

        $p = [];
        foreach ($imageFiles as $key => $filename) {
            $url = $this->commonsImage($filename);
            $p[$key] = $this->downloadImage($url, $key . '.jpg');
        }

        $sampleCars = [
            [
                'seller_id'     => $seller1Id,
                'brand'         => 'BYD',
                'model'         => 'Atto 3 Electric SUV',
                'year'          => 2025,
                'price'         => 2800000,
                'license_plate' => '3-12345-AA',
                'summary'       => 'Brand new BYD Atto 3 — fully electric, zero emissions. Sleek digital dashboard with a rotatable center screen, premium Dynaudio sound system, lane-keep assist, full active safety suite, and a real-world range of 480km per charge. Perfect for Addis Ababa city driving with low running cost.',
                'image_front'   => $p['byd_front'],
                'image_back'    => $p['byd_back'],
                'image_interior'=> $p['byd_int'],
            ],
            [
                'seller_id'     => $seller1Id,
                'brand'         => 'Toyota',
                'model'         => 'Land Cruiser 300 ZX',
                'year'          => 2022,
                'price'         => 12500000,
                'license_plate' => '3-88990-AM',
                'summary'       => 'Legendary Toyota Land Cruiser 300 ZX in Black Pearl. Loaded with premium black leather interiors, quad-zone climate control, ventilated front seats, Toyota Safety Sense 3.0, e-KDSS suspension, and Multi-Terrain Select. Flawless condition with full service history.',
                'image_front'   => $p['lc300_front'],
                'image_back'    => $p['lc300_back'],
                'image_interior'=> $p['lc300_int'],
            ],
            [
                'seller_id'     => $seller1Id,
                'brand'         => 'Suzuki',
                'model'         => 'Jimny SZ5 AllGrip 4WD',
                'year'          => 2024,
                'price'         => 3200000,
                'license_plate' => '3-44556-AA',
                'summary'       => 'The iconic Suzuki Jimny SZ5 AllGrip 4WD. Body-on-frame off-roader with automatic transmission, hill descent control, leather-wrapped steering wheel, and Apple CarPlay. Fuel-efficient 1.5L petrol engine. Never been off-road — bought new and garage-kept.',
                'image_front'   => $p['jimny_front'],
                'image_back'    => $p['jimny_back'],
                'image_interior'=> $p['jimny_int'],
            ],
            [
                'seller_id'     => $seller1Id,
                'brand'         => 'Ford',
                'model'         => 'Mustang GT 5.0 V8',
                'year'          => 2023,
                'price'         => 7800000,
                'license_plate' => '3-77665-OR',
                'summary'       => 'Iconic Ford Mustang GT 5.0 V8 in Iconic Silver. 450 naturally aspirated horsepower, active valve exhaust (quad modes), Brembo 6-piston brakes, MagneRide suspension, heated/cooled leather seats, 12" digital cluster and SYNC 4 infotainment. A rare and unforgettable machine.',
                'image_front'   => $p['mustang_front'],
                'image_back'    => $p['mustang_back'],
                'image_interior'=> $p['mustang_int'],
            ],
            [
                'seller_id'     => $seller2Id,
                'brand'         => 'Hyundai',
                'model'         => 'Tucson 2.0 Signature AWD',
                'year'          => 2023,
                'price'         => 4200000,
                'license_plate' => '3-55123-AB',
                'summary'       => 'Hyundai Tucson 2.0 Signature AWD in Phantom Black. Parametric grille design, 10.25" infotainment, wireless Apple/Android Auto, panoramic sunroof, ventilated + heated front seats, blind-spot monitoring, and forward collision avoidance. Low mileage and in showroom condition.',
                'image_front'   => $p['tucson_front'],
                'image_back'    => $p['tucson_back'],
                'image_interior'=> $p['tucson_int'],
            ],
            [
                'seller_id'     => $seller2Id,
                'brand'         => 'Toyota',
                'model'         => 'Hilux GR Sport 2.8 4WD',
                'year'          => 2023,
                'price'         => 6500000,
                'license_plate' => '3-34411-AA',
                'summary'       => 'Toyota Hilux GR Sport 2.8L diesel turbo 4WD — the most capable dual-cab pickup on the market. Sports-tuned suspension, GR leather seats, Toyota Safety Sense, adaptive cruise control, and Gazoo Racing aero kit. An extraordinary truck that does everything.',
                'image_front'   => $p['hilux_front'],
                'image_back'    => $p['hilux_back'],
                'image_interior'=> $p['hilux_int'],
            ],
            [
                'seller_id'     => $seller2Id,
                'brand'         => 'Mercedes-Benz',
                'model'         => 'GLE 300d AMG Line 4MATIC',
                'year'          => 2021,
                'price'         => 9800000,
                'license_plate' => '3-90112-OR',
                'summary'       => 'Prestigious Mercedes-Benz GLE 300d AMG Line in Obsidian Black. 2.0L turbodiesel 245hp engine, AMG body kit, 21" AMG wheels, MBUX with augmented reality navigation, Burmester 3D surround sound, air suspension, and 7-seat configuration. A statement vehicle with presence.',
                'image_front'   => $p['gle_front'],
                'image_back'    => $p['gle_back'],
                'image_interior'=> $p['gle_int'],
            ],
            [
                'seller_id'     => $seller2Id,
                'brand'         => 'Hyundai',
                'model'         => 'Sonata 2.0 Luxury',
                'year'          => 2022,
                'price'         => 2200000,
                'license_plate' => '3-76543-AM',
                'summary'       => 'Elegant Hyundai Sonata 2.0 Luxury in Phantom Black. Fastback design with LED headlights, 10.25" widescreen infotainment, Bose premium audio, ventilated front seats, heated rear seats, sunroof, and highway driving assist. The perfect premium family saloon.',
                'image_front'   => $p['sonata_front'],
                'image_back'    => $p['sonata_back'],
                'image_interior'=> $p['sonata_int'],
            ],
            [
                'seller_id'     => $seller3Id,
                'brand'         => 'Toyota',
                'model'         => 'Corolla Hatchback XSE',
                'year'          => 2024,
                'price'         => 1850000,
                'license_plate' => '3-11223-AA',
                'summary'       => 'Toyota Corolla Hatchback XSE in Celestite Grey. Sport-tuned suspension, 18-inch alloys, leather-trimmed seats, Toyota Safety Sense 3.0, wireless charging, and excellent fuel economy. Ideal daily driver for Addis with low maintenance costs.',
                'image_front'   => $p['corolla_front'],
                'image_back'    => $p['corolla_back'],
                'image_interior'=> $p['corolla_int'],
            ],
            [
                'seller_id'     => $seller3Id,
                'brand'         => 'BMW',
                'model'         => '330i M Sport G20',
                'year'          => 2022,
                'price'         => 8900000,
                'license_plate' => '3-33445-OR',
                'summary'       => 'BMW 330i M Sport in Portimao Blue. 258hp turbocharged inline-six feel, M Sport brakes and suspension, Live Cockpit Professional, Harman Kardon audio, and adaptive LED headlights. Executive sedan with sharp handling.',
                'image_front'   => $p['bmw_front'],
                'image_back'    => $p['bmw_back'],
                'image_interior'=> $p['bmw_int'],
            ],
            [
                'seller_id'     => $seller4Id,
                'brand'         => 'Kia',
                'model'         => 'Sportage GT-Line AWD',
                'year'          => 2024,
                'price'         => 5100000,
                'license_plate' => '3-55667-AB',
                'summary'       => 'Kia Sportage GT-Line AWD with bold tiger-nose grille, dual 12.3-inch curved displays, highway driving assist 2, ventilated seats, and panoramic sunroof. Family SUV with premium tech and strong resale value.',
                'image_front'   => $p['sportage_front'],
                'image_back'    => $p['sportage_back'],
                'image_interior'=> $p['sportage_int'],
            ],
            [
                'seller_id'     => $seller4Id,
                'brand'         => 'Nissan',
                'model'         => 'X-Trail Ti e-POWER',
                'year'          => 2023,
                'price'         => 5800000,
                'license_plate' => '3-77889-AM',
                'summary'       => 'Nissan X-Trail Ti with e-POWER hybrid system for smooth, efficient driving. Three-row flexibility, ProPILOT assist, 360° camera, and quilted leather seats. Spacious crossover built for Ethiopian roads and families.',
                'image_front'   => $p['xtrail_front'],
                'image_back'    => $p['xtrail_back'],
                'image_interior'=> $p['xtrail_int'],
            ],
            [
                'seller_id'     => $seller5Id,
                'brand'         => 'Toyota',
                'model'         => 'RAV4 XLE AWD',
                'year'          => 2023,
                'price'         => 4600000,
                'license_plate' => '3-99001-AA',
                'summary'       => 'Toyota RAV4 XLE AWD in Magnetic Grey. Reliable 2.5L engine, all-wheel drive, power liftgate, Toyota Safety Sense 2.5, and excellent ground clearance. One of Ethiopia\'s most trusted compact SUVs.',
                'image_front'   => $p['rav4_front'],
                'image_back'    => $p['rav4_back'],
                'image_interior'=> $p['rav4_int'],
            ],
            [
                'seller_id'     => $seller5Id,
                'brand'         => 'Volkswagen',
                'model'         => 'Tiguan R-Line 4Motion',
                'year'          => 2022,
                'price'         => 7200000,
                'license_plate' => '3-22334-OR',
                'summary'       => 'Volkswagen Tiguan R-Line 4Motion with 2.0 TSI power, DSG gearbox, digital cockpit, adaptive cruise, and R-Line styling package. European build quality with composed ride and premium cabin materials.',
                'image_front'   => $p['tiguan_front'],
                'image_back'    => $p['tiguan_back'],
                'image_interior'=> $p['tiguan_int'],
            ],
            [
                'seller_id'     => $seller1Id,
                'brand'         => 'Tesla',
                'model'         => 'Model 3 Performance AWD',
                'year'          => 2023,
                'price'         => 5200000,
                'license_plate' => '3-14141-AA',
                'summary'       => 'Tesla Model 3 Performance AWD in Pearl White. Dual-motor all-wheel drive, 0–100 km/h in 3.3 seconds, Autopilot hardware, glass roof, and over-the-air updates. Low running cost and premium minimalist cabin with large center touchscreen.',
                'image_front'   => $p['tesla_front'],
                'image_back'    => $p['tesla_back'],
                'image_interior'=> $p['tesla_int'],
            ],
            [
                'seller_id'     => $seller2Id,
                'brand'         => 'Lexus',
                'model'         => 'RX 350 Premium Plus',
                'year'          => 2023,
                'price'         => 11200000,
                'license_plate' => '3-25252-OR',
                'summary'       => '2023 Lexus RX 350 Premium Plus in Matador Red Mica. 275hp turbocharged engine, Lexus Safety System+ 3.0, Mark Levinson audio, ventilated seats, and a refined luxury cabin. Flagship SUV comfort for Ethiopian executives.',
                'image_front'   => $p['lexus_front'],
                'image_back'    => $p['lexus_back'],
                'image_interior'=> $p['lexus_int'],
            ],
            [
                'seller_id'     => $seller2Id,
                'brand'         => 'Land Rover',
                'model'         => 'Range Rover Evoque D180',
                'year'          => 2022,
                'price'         => 8500000,
                'license_plate' => '3-36363-AB',
                'summary'       => 'Range Rover Evoque D180 First Edition with premium cabin, Pivi Pro infotainment, ClearSight rear-view mirror, and confident all-weather capability. Compact luxury SUV with unmistakable Land Rover design.',
                'image_front'   => $p['evoque_front'],
                'image_back'    => $p['evoque_back'],
                'image_interior'=> $p['evoque_int'],
            ],
            [
                'seller_id'     => $seller3Id,
                'brand'         => 'Audi',
                'model'         => 'Q5 S Line Quattro',
                'year'          => 2021,
                'price'         => 7400000,
                'license_plate' => '3-47474-AM',
                'summary'       => 'Audi Q5 S Line TDi Quattro with Virtual Cockpit, MMI navigation, quattro all-wheel drive, and a spacious premium interior. Smooth diesel performance ideal for highway and city use.',
                'image_front'   => $p['q5_front'],
                'image_back'    => $p['q5_back'],
                'image_interior'=> $p['q5_int'],
            ],
            [
                'seller_id'     => $seller4Id,
                'brand'         => 'Honda',
                'model'         => 'CR-V EX Masterpiece',
                'year'          => 2022,
                'price'         => 3900000,
                'license_plate' => '3-58585-AA',
                'summary'       => 'Honda CR-V EX Masterpiece with Honda Sensing, spacious rear seats, efficient 1.5L turbo engine, and excellent reliability. A top family SUV choice in Addis with low maintenance costs.',
                'image_front'   => $p['crv_front'],
                'image_back'    => $p['crv_back'],
                'image_interior'=> $p['crv_int'],
            ],
            [
                'seller_id'     => $seller5Id,
                'brand'         => 'Mazda',
                'model'         => 'CX-5 Suna AWD',
                'year'          => 2024,
                'price'         => 4800000,
                'license_plate' => '3-69696-OR',
                'summary'       => 'Mazda CX-5 Suna AWD in Zircon Sand Metallic. Kodo design language, i-Activsense safety, head-up display, Bose audio, and engaging Skyactiv handling. Premium Japanese SUV with style and practicality.',
                'image_front'   => $p['cx5_front'],
                'image_back'    => $p['cx5_back'],
                'image_interior'=> $p['cx5_int'],
            ],
        ];

        foreach ($sampleCars as $car) {
            $this->upsertCar($car);
        }
    }
}
