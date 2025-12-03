# نظام عراق ستار - نظام الإدارة الديناميكي
## Iraq Star System - Dynamic Management System

---

## نظرة عامة

**نظام عراق ستار** هو نظام إدارة ديناميكي متكامل يجمع بين واجهة أمامية حديثة وخلفية قوية. يوفر النظام إمكانيات إدارة شاملة للمستخدمين والمشتركين والخدمات والبيانات المالية مع تتبع كامل للنشاطات.

---

## هيكل المشروع

```
iraq_star_system_project/
├── backend/                          # الخلفية (Backend)
│   ├── api/                          # ملفات API
│   │   ├── users.php                 # API إدارة المستخدمين
│   │   ├── subscribers.php           # API إدارة المشتركين
│   │   ├── services.php              # API إدارة الخدمات
│   │   ├── activity.php              # API تتبع النشاطات
│   │   └── financial.php             # API البيانات المالية
│   ├── classes/                      # الفئات والكلاسات
│   │   ├── User.php                  # فئة إدارة المستخدمين
│   │   ├── Subscriber.php            # فئة إدارة المشتركين
│   │   ├── Service.php               # فئة إدارة الخدمات
│   │   └── Activity.php              # فئة تتبع النشاطات
│   ├── config/                       # ملفات الإعدادات
│   │   └── Database.php              # إعدادات الاتصال بقاعدة البيانات
│   └── db/                           # ملفات قاعدة البيانات
│       └── create_database.sql       # سكريبت إنشاء قاعدة البيانات
├── frontend/                         # الواجهة الأمامية
│   └── index.html                    # الواجهة الرئيسية
└── README.md                         # هذا الملف
```

---

## المتطلبات

- **PHP**: الإصدار 7.4 أو أحدث
- **MySQL**: الإصدار 5.7 أو أحدث
- **خادم ويب**: Apache أو Nginx
- **المتصفح**: أي متصفح حديث يدعم ES6 JavaScript

---

## التثبيت والإعداد

### 1. إنشاء قاعدة البيانات

قم بتنفيذ السكريبت التالي على خادم MySQL:

```bash
mysql -u root -p < backend/db/create_database.sql
```

أو استخدم أداة مثل phpMyAdmin لتنفيذ الأوامر الموجودة في الملف `backend/db/create_database.sql`.

### 2. تعديل إعدادات قاعدة البيانات

قم بتعديل ملف `backend/config/Database.php` وأدخل بيانات الاتصال بقاعدة البيانات:

```php
private $host = 'localhost';      // عنوان الخادم
private $db_name = 'iraq_star_system';  // اسم قاعدة البيانات
private $user = 'root';            // اسم المستخدم
private $password = '';            // كلمة المرور
```

### 3. نشر الملفات

ضع جميع ملفات المشروع في مجلد جذر الويب (Web Root):

```bash
cp -r iraq_star_system_project/* /var/www/html/
```

### 4. التحقق من الأذونات

تأكد من أن خادم الويب له أذونات القراءة والكتابة على جميع المجلدات:

```bash
chmod -R 755 /var/www/html/iraq_star_system_project/
chmod -R 777 /var/www/html/iraq_star_system_project/backend/
```

---

## استخدام النظام

### بيانات الدخول الافتراضية

| الحقل | القيمة |
|------|--------|
| اسم المستخدم | admin |
| كلمة المرور | admin123 |

### الأدوار والصلاحيات

| الدور | الصلاحيات |
|------|----------|
| **Admin** | إدارة كاملة للنظام، إدارة المستخدمين، عرض جميع البيانات |
| **Editor** | إضافة وتعديل المشتركين والخدمات، عرض النشاطات |
| **Viewer** | عرض البيانات فقط، لا يمكن الإضافة أو التعديل |

---

## واجهات API

### 1. API المستخدمين (`/backend/api/users.php`)

#### تسجيل الدخول
```http
GET /backend/api/users.php?action=login
Content-Type: application/json

{
  "username": "admin",
  "password": "admin123"
}
```

#### الحصول على جميع المستخدمين
```http
GET /backend/api/users.php
```

#### إضافة مستخدم جديد
```http
POST /backend/api/users.php
Content-Type: application/json

{
  "username": "newuser",
  "password": "password123",
  "fullName": "اسم المستخدم",
  "email": "user@example.com",
  "role": "editor"
}
```

#### تحديث بيانات المستخدم
```http
PUT /backend/api/users.php?id=1
Content-Type: application/json

{
  "fullName": "الاسم الجديد",
  "email": "newemail@example.com",
  "role": "viewer"
}
```

#### حذف مستخدم
```http
DELETE /backend/api/users.php?id=1
```

### 2. API المشتركين (`/backend/api/subscribers.php`)

#### الحصول على جميع المشتركين
```http
GET /backend/api/subscribers.php
```

#### الحصول على المشتركين حسب الحالة
```http
GET /backend/api/subscribers.php?action=status&status=active
```

#### إضافة مشترك جديد
```http
POST /backend/api/subscribers.php
Content-Type: application/json

{
  "name": "اسم المشترك",
  "phone": "07901234567",
  "email": "subscriber@example.com",
  "service": "برمجة مواقع",
  "price": 500000,
  "status": "active",
  "notes": "ملاحظات إضافية"
}
```

#### تحديث بيانات المشترك
```http
PUT /backend/api/subscribers.php?id=1
Content-Type: application/json

{
  "name": "الاسم الجديد",
  "status": "inactive"
}
```

#### حذف مشترك
```http
DELETE /backend/api/subscribers.php?id=1
```

### 3. API الخدمات (`/backend/api/services.php`)

#### الحصول على جميع الخدمات
```http
GET /backend/api/services.php
```

#### الحصول على الخدمات حسب الفئة
```http
GET /backend/api/services.php?action=category&category=تطوير
```

#### إضافة خدمة جديدة
```http
POST /backend/api/services.php
Content-Type: application/json

{
  "name": "اسم الخدمة",
  "category": "تطوير",
  "minPrice": 200000,
  "maxPrice": 1000000,
  "description": "وصف الخدمة"
}
```

### 4. API النشاطات (`/backend/api/activity.php`)

#### الحصول على جميع النشاطات
```http
GET /backend/api/activity.php
```

#### الحصول على نشاطات مستخدم معين
```http
GET /backend/api/activity.php?action=user&userId=1
```

#### تسجيل نشاط جديد
```http
POST /backend/api/activity.php
Content-Type: application/json

{
  "userId": 1,
  "userName": "اسم المستخدم",
  "userRole": "admin",
  "type": "login",
  "action": "تسجيل دخول",
  "details": {}
}
```

### 5. API البيانات المالية (`/backend/api/financial.php`)

#### الحصول على ملخص البيانات المالية
```http
GET /backend/api/financial.php?action=summary
```

#### الحصول على البيانات المالية الشهرية
```http
GET /backend/api/financial.php?action=monthly
```

#### إضافة سجل مالي جديد
```http
POST /backend/api/financial.php
Content-Type: application/json

{
  "month": "يناير 2024",
  "revenue": 5000000,
  "expenses": 2000000,
  "notes": "ملاحظات"
}
```

---

## ميزات النظام

### 1. إدارة المستخدمين
- إضافة وتعديل وحذف المستخدمين
- تحديد الأدوار والصلاحيات
- تتبع آخر دخول لكل مستخدم
- تشفير كلمات المرور بـ bcrypt

### 2. إدارة المشتركين
- إضافة وتعديل وحذف المشتركين
- تصنيف المشتركين حسب الحالة (نشط، معلق، متوقف)
- تتبع خدمات المشتركين والأسعار
- البحث والفرز حسب معايير مختلفة

### 3. إدارة الخدمات
- إضافة وتعديل وحذف الخدمات
- تصنيف الخدمات حسب الفئة
- تحديد نطاقات الأسعار
- إضافة وصف مفصل لكل خدمة

### 4. تتبع النشاطات
- تسجيل جميع عمليات النظام
- تتبع نشاطات المستخدمين
- حفظ معلومات الجلسة والـ IP والمتصفح
- إمكانية البحث والفرز حسب الفترة الزمنية

### 5. البيانات المالية
- تسجيل الإيرادات والنفقات
- حساب الأرباح تلقائياً
- عرض ملخصات مالية شهرية
- تقارير مالية شاملة

---

## الأمان

### تدابير الأمان المطبقة

1. **تشفير كلمات المرور**: استخدام bcrypt لتشفير كلمات المرور
2. **Prepared Statements**: استخدام PDO مع Prepared Statements لمنع SQL Injection
3. **CORS Headers**: السماح بالطلبات من نفس الأصل
4. **Input Validation**: التحقق من صحة المدخلات
5. **Error Handling**: معالجة الأخطاء بشكل آمن

### التوصيات الأمنية

- قم بتغيير كلمة مرور المسؤول الافتراضية فوراً
- استخدم HTTPS في بيئة الإنتاج
- قم بتحديث PHP و MySQL بانتظام
- قم بعمل نسخ احتياطية منتظمة من قاعدة البيانات
- قم بتقييد الوصول إلى ملفات الإعدادات

---

## استكشاف الأخطاء

### مشكلة: خطأ في الاتصال بقاعدة البيانات

**الحل**: تحقق من إعدادات الاتصال في `backend/config/Database.php` وتأكد من أن خادم MySQL يعمل.

### مشكلة: خطأ 404 عند الوصول إلى API

**الحل**: تأكد من أن الملفات موجودة في المسار الصحيح وأن خادم الويب يدعم إعادة الكتابة (Rewrite).

### مشكلة: لا يمكن تحديث البيانات

**الحل**: تحقق من أذونات الكتابة على مجلد `backend/` وتأكد من أن قاعدة البيانات تعمل بشكل صحيح.

---

## الدعم والتطوير المستقبلي

يمكن توسيع النظام بإضافة الميزات التالية:

- نظام الإشعارات
- تقارير متقدمة وتحليلات
- نظام الأذونات المتقدم
- واجهة برمجية للجوال (Mobile API)
- نظام النسخ الاحتياطي التلقائي
- تكامل مع بوابات الدفع

---

## الترخيص

هذا المشروع مرخص تحت رخصة MIT.

---

## المؤلف

تم تطوير هذا النظام بواسطة **Manus AI**

---

## آخر تحديث

تم آخر تحديث للنظام في: **2025-12-03**

