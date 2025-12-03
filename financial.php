<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/Database.php';

$database = new Database();
$pdo = $database->connect();

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : null;

try {
    switch ($method) {
        case 'GET':
            if ($action === 'summary') {
                // الحصول على ملخص البيانات المالية
                $query = "SELECT 
                            SUM(revenue) as totalRevenue,
                            SUM(expenses) as totalExpenses,
                            SUM(profit) as totalProfit,
                            COUNT(*) as recordCount
                          FROM financial_data";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                $summary = $stmt->fetch();
                
                echo json_encode(['success' => true, 'data' => $summary]);
            } elseif ($action === 'monthly') {
                // الحصول على البيانات المالية الشهرية
                $query = "SELECT month, revenue, expenses, profit 
                         FROM financial_data 
                         ORDER BY month DESC 
                         LIMIT 12";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                $data = $stmt->fetchAll();
                
                echo json_encode(['success' => true, 'data' => $data]);
            } elseif ($id) {
                // الحصول على سجل مالي واحد
                $query = "SELECT * FROM financial_data WHERE id = :id";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $data = $stmt->fetch();
                
                if ($data) {
                    echo json_encode(['success' => true, 'data' => $data]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'السجل غير موجود']);
                }
            } else {
                // الحصول على جميع البيانات المالية
                $query = "SELECT * FROM financial_data ORDER BY created DESC";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                $data = $stmt->fetchAll();
                
                echo json_encode(['success' => true, 'data' => $data]);
            }
            break;

        case 'POST':
            // إضافة سجل مالي جديد
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['month']) || !isset($data['revenue']) || !isset($data['expenses'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'بيانات غير كاملة']);
                exit();
            }

            $profit = $data['revenue'] - $data['expenses'];
            
            $query = "INSERT INTO financial_data (subscriberId, month, revenue, expenses, profit, notes) 
                     VALUES (:subscriberId, :month, :revenue, :expenses, :profit, :notes)";
            $stmt = $pdo->prepare($query);
            
            $stmt->bindParam(':subscriberId', $data['subscriberId'] ?? null);
            $stmt->bindParam(':month', $data['month']);
            $stmt->bindParam(':revenue', $data['revenue']);
            $stmt->bindParam(':expenses', $data['expenses']);
            $stmt->bindParam(':profit', $profit);
            $stmt->bindParam(':notes', $data['notes'] ?? null);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'تم إضافة السجل المالي', 'id' => $pdo->lastInsertId()]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'خطأ في إضافة السجل']);
            }
            break;

        case 'PUT':
            // تحديث سجل مالي
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'معرف السجل مفقود']);
                exit();
            }

            $profit = isset($data['revenue']) && isset($data['expenses']) ? 
                     $data['revenue'] - $data['expenses'] : null;
            
            $query = "UPDATE financial_data SET 
                     month = COALESCE(:month, month),
                     revenue = COALESCE(:revenue, revenue),
                     expenses = COALESCE(:expenses, expenses),
                     profit = COALESCE(:profit, profit),
                     notes = COALESCE(:notes, notes)
                     WHERE id = :id";
            $stmt = $pdo->prepare($query);
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':month', $data['month'] ?? null);
            $stmt->bindParam(':revenue', $data['revenue'] ?? null);
            $stmt->bindParam(':expenses', $data['expenses'] ?? null);
            $stmt->bindParam(':profit', $profit);
            $stmt->bindParam(':notes', $data['notes'] ?? null);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'تم تحديث السجل المالي']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'خطأ في تحديث السجل']);
            }
            break;

        case 'DELETE':
            // حذف سجل مالي
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'معرف السجل مفقود']);
                exit();
            }

            $query = "DELETE FROM financial_data WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'تم حذف السجل المالي']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'خطأ في حذف السجل']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'طريقة الطلب غير مدعومة']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'خطأ في الخادم: ' . $e->getMessage()]);
}
?>
