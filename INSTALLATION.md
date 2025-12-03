# دليل التثبيت والتشغيل
## Installation and Setup Guide

---

## المتطلبات الأساسية

### 1. خادم الويب
- **Apache** مع تفعيل mod_rewrite
- أو **Nginx**

### 2. PHP
- الإصدار 7.4 أو أحدث
- تفعيل Extension: PDO, PDO_MySQL

### 3. قاعدة البيانات
- **MySQL** الإصدار 5.7 أو أحدث
- أو **MariaDB** الإصدار 10.3 أو أحدث

---

## خطوات التثبيت

### الخطوة 1: نسخ الملفات

```bash
# نسخ جميع ملفات المشروع إلى جذر الويب
cp -r iraq_star_system_project /var/www/html/

# أو إذا كنت تستخدم Windows
xcopy iraq_star_system_project C:\xampp\htdocs\ /E /I
```

### الخطوة 2: إنشاء قاعدة البيانات

#### الطريقة الأولى: استخدام سطر الأوامر

```bash
mysql -u root -p < backend/db/create_database.sql
```

#### الطريقة الثانية: استخدام phpMyAdmin

1. افتح phpMyAdmin في المتصفح
2. انقر على "استيراد" (Import)
3. اختر ملف `backend/db/create_database.sql`
4. انقر على "تنفيذ" (Execute)

### الخطوة 3: تعديل إعدادات قاعدة البيانات

قم بتعديل ملف `backend/config/Database.php`:

```php
<?php
class Database {
    private $host = 'localhost';           // عنوان خادم MySQL
    private $db_name = 'iraq_star_system'; // اسم قاعدة البيانات
    private $user = 'root';                // اسم مستخدم MySQL
    private $password = '';                // كلمة مرور MySQL
    // ...
}
?>
```

### الخطوة 4: ضبط الأذونات

```bash
# منح أذونات القراءة والكتابة
chmod -R 755 /var/www/html/iraq_star_system_project/
chmod -R 777 /var/www/html/iraq_star_system_project/backend/

# في Windows، تأكد من أن خادم الويب له أذونات الكتابة على مجلد backend
```

### الخطوة 5: تفعيل CORS (اختياري)

إذا كنت تشغل الواجهة الأمامية على مجال مختلف، قم بتعديل رؤوس CORS في ملفات API:

```php
header('Access-Control-Allow-Origin: https://yourdomain.com');
```

---

## التشغيل

### الطريقة الأولى: استخدام Apache

1. تأكد من تشغيل خدمة Apache:
```bash
sudo systemctl start apache2
```

2. افتح المتصفح وانتقل إلى:
```
http://localhost/iraq_star_system_project/frontend/
```

### الطريقة الثانية: استخدام PHP Built-in Server

```bash
cd iraq_star_system_project/frontend/
php -S localhost:8000
```

ثم افتح المتصفح على:
```
http://localhost:8000
```

### الطريقة الثالثة: استخدام XAMPP

1. ضع المشروع في مجلد `htdocs`
2. شغل Apache و MySQL من لوحة تحكم XAMPP
3. افتح المتصفح على:
```
http://localhost/iraq_star_system_project/frontend/
```

---

## بيانات الدخول الافتراضية

| الحقل | القيمة |
|------|--------|
| **اسم المستخدم** | admin |
| **كلمة المرور** | admin123 |

---

## اختبار النظام

### 1. اختبار تسجيل الدخول

- افتح الصفحة الرئيسية
- أدخل بيانات الدخول الافتراضية
- يجب أن تظهر لوحة التحكم

### 2. اختبار إضافة مشترك

- انقر على "إدارة المشتركين"
- انقر على "إضافة مشترك جديد"
- أدخل البيانات المطلوبة
- انقر على "حفظ"

### 3. اختبار API مباشرة

```bash
# اختبار جلب المستخدمين
curl http://localhost/iraq_star_system_project/backend/api/users.php

# اختبار جلب المشتركين
curl http://localhost/iraq_star_system_project/backend/api/subscribers.php

# اختبار جلب الخدمات
curl http://localhost/iraq_star_system_project/backend/api/services.php
```

---

## استكشاف الأخطاء

### المشكلة: خطأ "Connection refused"

**الحل:**
- تأكد من تشغيل خادم MySQL
- تحقق من بيانات الاتصال في `Database.php`
- تأكد من أن قاعدة البيانات موجودة

### المشكلة: خطأ 404 عند الوصول إلى API

**الحل:**
- تأكد من أن المسار صحيح
- تحقق من أن ملفات API موجودة في `backend/api/`
- تأكد من أن خادم الويب يدعم إعادة الكتابة

### المشكلة: خطأ "Permission denied"

**الحل:**
```bash
# منح الأذونات الصحيحة
sudo chown -R www-data:www-data /var/www/html/iraq_star_system_project/
sudo chmod -R 755 /var/www/html/iraq_star_system_project/
sudo chmod -R 777 /var/www/html/iraq_star_system_project/backend/
```

### المشكلة: لا تظهر البيانات في الجداول

**الحل:**
- افتح "أدوات المطور" في المتصفح (F12)
- انظر إلى تبويب "Console" للأخطاء
- تحقق من أن API يرد بيانات صحيحة

---

## الخطوات التالية

بعد التثبيت الناجح، يمكنك:

1. **تغيير كلمة مرور المسؤول**
   - اذهب إلى الإعدادات
   - غير كلمة المرور الافتراضية

2. **إضافة مستخدمين جدد**
   - اذهب إلى "إدارة المستخدمين"
   - أضف مستخدمين جدد مع أدوار مختلفة

3. **إضافة خدمات جديدة**
   - اذهب إلى "إدارة الخدمات"
   - أضف الخدمات التي تقدمها شركتك

4. **بدء إضافة المشتركين**
   - اذهب إلى "إدارة المشتركين"
   - ابدأ بإضافة المشتركين والعقود

---

## الدعم

إذا واجهت أي مشاكل:

1. تحقق من ملف السجل (Log) في خادم الويب
2. افتح "أدوات المطور" في المتصفح
3. تحقق من رسائل الخطأ في وحدة التحكم (Console)

---

**آخر تحديث:** 2025-12-03
