<?php
/**
 * فئة النشاط
 * Activity Class
 */

class Activity {
    private $pdo;
    private $table = 'activity_log';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * تسجيل نشاط
     */
    public function log($userId, $userName, $userRole, $type, $action, $details = null) {
        $query = "INSERT INTO " . $this->table . " 
                  (userId, userName, userRole, type, action, details, ip, browser, page) 
                  VALUES (:userId, :userName, :userRole, :type, :action, :details, :ip, :browser, :page)";

        $stmt = $this->pdo->prepare($query);

        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $browser = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $page = $_SERVER['REQUEST_URI'] ?? 'Unknown';
        $detailsJson = $details ? json_encode($details) : null;

        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':userName', $userName);
        $stmt->bindParam(':userRole', $userRole);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':details', $detailsJson);
        $stmt->bindParam(':ip', $ip);
        $stmt->bindParam(':browser', $browser);
        $stmt->bindParam(':page', $page);

        return $stmt->execute();
    }

    /**
     * الحصول على جميع النشاطات
     */
    public function getAll($limit = 1000) {
        $query = "SELECT * FROM " . $this->table . " ORDER BY timestamp DESC LIMIT :limit";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * الحصول على نشاطات مستخدم معين
     */
    public function getByUserId($userId, $limit = 100) {
        $query = "SELECT * FROM " . $this->table . " WHERE userId = :userId ORDER BY timestamp DESC LIMIT :limit";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * الحصول على نشاطات حسب النوع
     */
    public function getByType($type, $limit = 100) {
        $query = "SELECT * FROM " . $this->table . " WHERE type = :type ORDER BY timestamp DESC LIMIT :limit";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * الحصول على نشاطات في فترة زمنية معينة
     */
    public function getByDateRange($startDate, $endDate, $limit = 100) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE timestamp BETWEEN :startDate AND :endDate 
                  ORDER BY timestamp DESC 
                  LIMIT :limit";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * حذف النشاطات القديمة
     */
    public function deleteOld($days = 90) {
        $query = "DELETE FROM " . $this->table . " WHERE timestamp < DATE_SUB(NOW(), INTERVAL :days DAY)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
