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
require_once '../classes/Subscriber.php';
require_once '../classes/Activity.php';

$database = new Database();
$pdo = $database->connect();
$subscriber = new Subscriber($pdo);
$activity = new Activity($pdo);

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : null;

try {
    switch ($method) {
        case 'GET':
            if ($action === 'status') {
                // الحصول على المشتركين حسب الحالة
                $status = isset($_GET['status']) ? $_GET['status'] : 'all';
                
                if ($status === 'all') {
                    $subscribers = $subscriber->getAll();
                } else {
                    $subscribers = $subscriber->getByStatus($status);
                }
                
                echo json_encode(['success' => true, 'data' => $subscribers]);
            } elseif ($id) {
                // الحصول على مشترك واحد
                $data = $subscriber->getById($id);
                
                if ($data) {
                    echo json_encode(['success' => true, 'data' => $data]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'المشترك غير موجود']);
                }
            } else {
                // الحصول على جميع المشتركين
                $subscribers = $subscriber->getAll();
                echo json_encode(['success' => true, 'data' => $subscribers]);
            }
            break;

        case 'POST':
            // إضافة مشترك جديد
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['name']) || !isset($data['phone']) || !isset($data['service']) || !isset($data['price'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'بيانات غير كاملة']);
                exit();
            }

            $newId = $subscriber->create($data, $data['userId'] ?? null);
            
            if ($newId) {
                echo json_encode(['success' => true, 'message' => 'تم إضافة المشترك بنجاح', 'id' => $newId]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'خطأ في إضافة المشترك']);
            }
            break;

        case 'PUT':
            // تحديث بيانات المشترك
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'معرف المشترك مفقود']);
                exit();
            }

            if ($subscriber->update($id, $data)) {
                echo json_encode(['success' => true, 'message' => 'تم تحديث بيانات المشترك']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'خطأ في تحديث البيانات']);
            }
            break;

        case 'DELETE':
            // حذف مشترك
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'معرف المشترك مفقود']);
                exit();
            }

            if ($subscriber->delete($id)) {
                echo json_encode(['success' => true, 'message' => 'تم حذف المشترك']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'خطأ في حذف المشترك']);
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
