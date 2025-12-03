<?php
/**
 * فئة المشترك
 * Subscriber Class
 */

class Subscriber {
    private $pdo;
    private $table = 'subscribers';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * الحصول على جميع المشتركين
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * الحصول على مشترك بواسطة ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * إضافة مشترك جديد
     */
    public function create($data, $userId = null) {
        $query = "INSERT INTO " . $this->table . " 
                  (name, phone, email, service, price, status, createdBy, notes) 
                  VALUES (:name, :phone, :email, :service, :price, :status, :createdBy, :notes)";

        $stmt = $this->pdo->prepare($query);

        $status = $data['status'] ?? 'pending';

        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':email', $data['email'] ?? null);
        $stmt->bindParam(':service', $data['service']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':createdBy', $userId);
        $stmt->bindParam(':notes', $data['notes'] ?? null);

        if ($stmt->execute()) {
            return $this->pdo->lastInsertId();
        }

        return false;
    }

    /**
     * تحديث بيانات المشترك
     */
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " SET 
                  name = :name, 
                  phone = :phone, 
                  email = :email, 
                  service = :service, 
                  price = :price, 
                  status = :status, 
                  notes = :notes 
                  WHERE id = :id";

        $stmt = $this->pdo->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':email', $data['email'] ?? null);
        $stmt->bindParam(':service', $data['service']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':status', $data['status'] ?? 'pending');
        $stmt->bindParam(':notes', $data['notes'] ?? null);

        return $stmt->execute();
    }

    /**
     * حذف مشترك
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * الحصول على المشتركين حسب الحالة
     */
    public function getByStatus($status) {
        $query = "SELECT * FROM " . $this->table . " WHERE status = :status ORDER BY created DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
