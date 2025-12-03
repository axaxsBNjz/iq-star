<?php
/**
 * فئة الخدمة
 * Service Class
 */

class Service {
    private $pdo;
    private $table = 'services';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * الحصول على جميع الخدمات
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " WHERE isActive = TRUE ORDER BY created DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * الحصول على خدمة بواسطة ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id AND isActive = TRUE";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * إضافة خدمة جديدة
     */
    public function create($data, $userId = null) {
        $query = "INSERT INTO " . $this->table . " 
                  (name, category, minPrice, maxPrice, priceRange, description, createdBy) 
                  VALUES (:name, :category, :minPrice, :maxPrice, :priceRange, :description, :createdBy)";

        $stmt = $this->pdo->prepare($query);

        // بناء priceRange
        $priceRange = $data['minPrice'] . ' - ' . $data['maxPrice'];

        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':minPrice', $data['minPrice']);
        $stmt->bindParam(':maxPrice', $data['maxPrice']);
        $stmt->bindParam(':priceRange', $priceRange);
        $stmt->bindParam(':description', $data['description'] ?? null);
        $stmt->bindParam(':createdBy', $userId);

        if ($stmt->execute()) {
            return $this->pdo->lastInsertId();
        }

        return false;
    }

    /**
     * تحديث بيانات الخدمة
     */
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " SET 
                  name = :name, 
                  category = :category, 
                  minPrice = :minPrice, 
                  maxPrice = :maxPrice, 
                  priceRange = :priceRange, 
                  description = :description 
                  WHERE id = :id";

        $stmt = $this->pdo->prepare($query);

        $priceRange = $data['minPrice'] . ' - ' . $data['maxPrice'];

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':minPrice', $data['minPrice']);
        $stmt->bindParam(':maxPrice', $data['maxPrice']);
        $stmt->bindParam(':priceRange', $priceRange);
        $stmt->bindParam(':description', $data['description'] ?? null);

        return $stmt->execute();
    }

    /**
     * حذف خدمة
     */
    public function delete($id) {
        $query = "UPDATE " . $this->table . " SET isActive = FALSE WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * الحصول على الخدمات حسب الفئة
     */
    public function getByCategory($category) {
        $query = "SELECT * FROM " . $this->table . " WHERE category = :category AND isActive = TRUE ORDER BY created DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':category', $category);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
