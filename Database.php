<?php
/**
 * فئة الاتصال بقاعدة البيانات
 * Database Connection Class
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'iraq_star_system';
    private $user = 'root';
    private $password = '';
    private $pdo;

    /**
     * الاتصال بقاعدة البيانات
     */
    public function connect() {
        $this->pdo = null;

        try {
            $this->pdo = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8mb4',
                $this->user,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die('خطأ في الاتصال: ' . $e->getMessage());
        }

        return $this->pdo;
    }

    /**
     * الحصول على الاتصال
     */
    public function getPDO() {
        if (!$this->pdo) {
            $this->connect();
        }
        return $this->pdo;
    }
}
?>
