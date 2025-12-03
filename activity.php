<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/Database.php';
require_once '../classes/Activity.php';

$database = new Database();
$pdo = $database->connect();
$activity = new Activity($pdo);

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

try {
    switch ($method) {
        case 'GET':
            if ($action === 'user') {
                // الحصول على نشاطات مستخدم معين
                $userId = isset($_GET['userId']) ? $_GET['userId'] : null;
                $limit = isset($_GET['limit']) ? $_GET['limit'] : 100;
                
                if ($userId) {
                    $activities = $activity->getByUserId($userId, $limit);
                    echo json_encode(['success' => true, 'data' => $activities]);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'معرف المستخدم مفقود']);
                }
            } elseif ($action === 'type') {
                // الحصول على نشاطات حسب النوع
                $type = isset($_GET['type']) ? $_GET['type'] : null;
                $limit = isset($_GET['limit']) ? $_GET['limit'] : 100;
                
                if ($type) {
                    $activities = $activity->getByType($type, $limit);
                    echo json_encode(['success' => true, 'data' => $activities]);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'نوع النشاط مفقود']);
                }
            } elseif ($action === 'range') {
                // الحصول على نشاطات في فترة زمنية معينة
                $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
                $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;
                $limit = isset($_GET['limit']) ? $_GET['limit'] : 100;
                
                if ($startDate && $endDate) {
                    $activities = $activity->getByDateRange($startDate, $endDate, $limit);
                    echo json_encode(['success' => true, 'data' => $activities]);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'تواريخ غير كاملة']);
                }
            } else {
                // الحصول على جميع النشاطات
                $limit = isset($_GET['limit']) ? $_GET['limit'] : 1000;
                $activities = $activity->getAll($limit);
                echo json_encode(['success' => true, 'data' => $activities]);
            }
            break;

        case 'POST':
            // تسجيل نشاط جديد
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['userId']) || !isset($data['userName']) || !isset($data['userRole']) || 
                !isset($data['type']) || !isset($data['action'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'بيانات غير كاملة']);
                exit();
            }

            if ($activity->log($data['userId'], $data['userName'], $data['userRole'], 
                              $data['type'], $data['action'], $data['details'] ?? null)) {
                echo json_encode(['success' => true, 'message' => 'تم تسجيل النشاط']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'خطأ في تسجيل النشاط']);
            }
            break;

        case 'DELETE':
            // حذف النشاطات القديمة
            $days = isset($_GET['days']) ? $_GET['days'] : 90;
            
            if ($activity->deleteOld($days)) {
                echo json_encode(['success' => true, 'message' => 'تم حذف النشاطات القديمة']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'خطأ في حذف النشاطات']);
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
