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
require_once '../classes/Service.php';

$database = new Database();
$pdo = $database->connect();
$service = new Service($pdo);

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : null;

try {
    switch ($method) {
        case 'GET':
            if ($action === 'category') {
                // الحصول على الخدمات حسب الفئة
                $category = isset($_GET['category']) ? $_GET['category'] : null;
                
                if ($category) {
                    $services = $service->getByCategory($category);
                } else {
                    $services = $service->getAll();
                }
                
                echo json_encode(['success' => true, 'data' => $services]);
            } elseif ($id) {
                // الحصول على خدمة واحدة
                $data = $service->getById($id);
                
                if ($data) {
                    echo json_encode(['success' => true, 'data' => $data]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'الخدمة غير موجودة']);
                }
            } else {
                // الحصول على جميع الخدمات
                $services = $service->getAll();
                echo json_encode(['success' => true, 'data' => $services]);
            }
            break;

        case 'POST':
            // إضافة خدمة جديدة
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['name']) || !isset($data['category']) || !isset($data['minPrice']) || !isset($data['maxPrice'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'بيانات غير كاملة']);
                exit();
            }

            $newId = $service->create($data, $data['userId'] ?? null);
            
            if ($newId) {
                echo json_encode(['success' => true, 'message' => 'تم إضافة الخدمة بنجاح', 'id' => $newId]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'خطأ في إضافة الخدمة']);
            }
            break;

        case 'PUT':
            // تحديث بيانات الخدمة
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'معرف الخدمة مفقود']);
                exit();
            }

            if ($service->update($id, $data)) {
                echo json_encode(['success' => true, 'message' => 'تم تحديث بيانات الخدمة']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'خطأ في تحديث البيانات']);
            }
            break;

        case 'DELETE':
            // حذف خدمة
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'معرف الخدمة مفقود']);
                exit();
            }

            if ($service->delete($id)) {
                echo json_encode(['success' => true, 'message' => 'تم حذف الخدمة']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'خطأ في حذف الخدمة']);
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
