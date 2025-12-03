<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// معالجة طلبات OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/Database.php';
require_once '../classes/User.php';
require_once '../classes/Activity.php';

$database = new Database();
$pdo = $database->connect();
$user = new User($pdo);
$activity = new Activity($pdo);

$method = $_SERVER['REQUEST_METHOD'];
$request = isset($_GET['action']) ? $_GET['action'] : '';

try {
    switch ($method) {
        case 'GET':
            if ($request === 'login') {
                // تسجيل الدخول
                $data = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($data['username']) || !isset($data['password'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'بيانات غير كاملة']);
                    exit();
                }

                $loggedInUser = $user->login($data['username'], $data['password']);
                
                if ($loggedInUser) {
                    // تسجيل نشاط الدخول
                    $activity->log($loggedInUser['id'], $loggedInUser['fullName'], $loggedInUser['role'], 'login', 'تسجيل دخول');
                    
                    // إزالة كلمة المرور من الرد
                    unset($loggedInUser['password']);
                    
                    echo json_encode(['success' => true, 'data' => $loggedInUser]);
                } else {
                    http_response_code(401);
                    echo json_encode(['success' => false, 'message' => 'بيانات الدخول غير صحيحة']);
                }
            } else {
                // الحصول على جميع المستخدمين
                $users = $user->getAll();
                echo json_encode(['success' => true, 'data' => $users]);
            }
            break;

        case 'POST':
            // إضافة مستخدم جديد
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['username']) || !isset($data['password']) || !isset($data['fullName']) || !isset($data['email'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'بيانات غير كاملة']);
                exit();
            }

            $newUserId = $user->create($data);
            
            if ($newUserId) {
                echo json_encode(['success' => true, 'message' => 'تم إضافة المستخدم بنجاح', 'id' => $newUserId]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'خطأ في إضافة المستخدم']);
            }
            break;

        case 'PUT':
            // تحديث بيانات المستخدم
            $data = json_decode(file_get_contents('php://input'), true);
            $id = isset($_GET['id']) ? $_GET['id'] : null;
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'معرف المستخدم مفقود']);
                exit();
            }

            if ($user->update($id, $data)) {
                echo json_encode(['success' => true, 'message' => 'تم تحديث بيانات المستخدم']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'خطأ في تحديث البيانات']);
            }
            break;

        case 'DELETE':
            // حذف مستخدم
            $id = isset($_GET['id']) ? $_GET['id'] : null;
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'معرف المستخدم مفقود']);
                exit();
            }

            if ($user->delete($id)) {
                echo json_encode(['success' => true, 'message' => 'تم حذف المستخدم']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'خطأ في حذف المستخدم']);
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
