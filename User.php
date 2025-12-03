<?php
/**
 * فئة المستخدم
 * User Class
 */

class User {
    private $pdo;
    private $table = 'users';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * تسجيل الدخول
     */
    public function login($username, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE username = :username AND isActive = TRUE";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // تحديث وقت آخر دخول
            $updateQuery = "UPDATE " . $this->table . " SET lastLogin = NOW(), ipAddress = :ip WHERE id = :id";
            $updateStmt = $this->pdo->prepare($updateQuery);
            $updateStmt->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
            $updateStmt->bindParam(':id', $user['id']);
            $updateStmt->execute();

            return $user;
        }

        return false;
    }

    /**
     * الحصول على جميع المستخدمين
     */
    public function getAll() {
        $query = "SELECT id, username, fullName, email, role, created, lastLogin FROM " . $this->table . " WHERE isActive = TRUE";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * الحصول على مستخدم بواسطة ID
     */
    public function getById($id) {
        $query = "SELECT id, username, fullName, email, role, created, lastLogin FROM " . $this->table . " WHERE id = :id AND isActive = TRUE";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * إضافة مستخدم جديد
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (username, password, fullName, email, role) 
                  VALUES (:username, :password, :fullName, :email, :role)";

        $stmt = $this->pdo->prepare($query);

        // تشفير كلمة المرور
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':fullName', $data['fullName']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':role', $data['role']);

        if ($stmt->execute()) {
            return $this->pdo->lastInsertId();
        }

        return false;
    }

    /**
     * تحديث بيانات المستخدم
     */
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " SET 
                  fullName = :fullName, 
                  email = :email, 
                  role = :role 
                  WHERE id = :id";

        $stmt = $this->pdo->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':fullName', $data['fullName']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':role', $data['role']);

        return $stmt->execute();
    }

    /**
     * حذف مستخدم
     */
    public function delete($id) {
        $query = "UPDATE " . $this->table . " SET isActive = FALSE WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
