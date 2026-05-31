<?php
class Car {
    private $db;

    public function __construct() {
        $this->db = (new Database())->conn;
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO cars (seller_id, model, year, price, brand, license_plate, summary, image_front, image_back, image_interior) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['seller_id'], $data['model'], $data['year'], $data['price'],
            $data['brand'], $data['license_plate'], $data['summary'],
            $data['image_front'], $data['image_back'], $data['image_interior']
        ]);
    }

    public function getBySeller($seller_id) {
        $stmt = $this->db->prepare("SELECT * FROM cars WHERE seller_id = ? ORDER BY id DESC");
        $stmt->execute([$seller_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM cars ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM cars WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id, $seller_id) {
        $stmt = $this->db->prepare("DELETE FROM cars WHERE id = ? AND seller_id = ?");
        return $stmt->execute([$id, $seller_id]);
    }

    public function getByIdAndSeller($id, $seller_id) {
        $stmt = $this->db->prepare("SELECT * FROM cars WHERE id = ? AND seller_id = ?");
        $stmt->execute([$id, $seller_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $seller_id, $data) {
        // Build query — only replace images if new ones were uploaded
        $fields = "brand=?, model=?, year=?, price=?, license_plate=?, summary=?";
        $params = [
            $data['brand'], $data['model'], $data['year'],
            $data['price'], $data['license_plate'], $data['summary'],
        ];
        if (!empty($data['image_front'])) {
            $fields .= ", image_front=?";
            $params[] = $data['image_front'];
        }
        if (!empty($data['image_back'])) {
            $fields .= ", image_back=?";
            $params[] = $data['image_back'];
        }
        if (!empty($data['image_interior'])) {
            $fields .= ", image_interior=?";
            $params[] = $data['image_interior'];
        }
        $params[] = $id;
        $params[] = $seller_id;
        $stmt = $this->db->prepare("UPDATE cars SET $fields WHERE id=? AND seller_id=?");
        return $stmt->execute($params);
    }
}
