# دليل النشر والتأمين - نظام Dusty المحاسبي

## 📋 نظرة عامة

تم تأمين النظام بالكامل مع إخفاء المسارات ومنع الأرشفة وحماية قوية ضد الهجمات. هذا الدليل يشرح كيفية نشر النظام على عدة دومينات بشكل آمن.

---

## 🔒 التحسينات الأمنية المطبقة

### 1. إخفاء المسارات (URL Obfuscation)
- ✅ جميع مسارات التطبيق مخفية خلف prefix عشوائي
- ✅ الدومين الرئيسي يعرض صفحة بيضاء فقط
- ✅ المسار الافتراضي: `b75/n95uk`
- ✅ يمكن تغييره لأي كود عشوائي آخر

**مثال:**
```
قبل: https://example.com/login
بعد: https://example.com/b75/n95uk/login
```

### 2. حظر الأرشفة (Anti-Indexing)
- ✅ ملف `robots.txt` محدّث لمنع جميع محركات البحث
- ✅ منع أرشيف Wayback Machine
- ✅ حظر bots مثل AhrefsBot, SemrushBot

### 3. حظر IP بعد محاولات فاشلة
- ✅ حظر تلقائي بعد 3 محاولات تسجيل دخول فاشلة
- ✅ مدة الحظر: 60 دقيقة
- ✅ تسجيل جميع المحاولات في logs

### 4. Security Headers
- ✅ X-Content-Type-Options: nosniff
- ✅ X-Frame-Options: SAMEORIGIN
- ✅ X-XSS-Protection
- ✅ Content-Security-Policy
- ✅ Referrer-Policy

---

## 🚀 خطوات النشر على الاستضافة

### الخطوة 1: رفع الملفات

```bash
# نقل جميع ملفات المشروع إلى الاستضافة
# تأكد من رفع:
- المجلد الكامل
- ملف .env
- ملف composer.json
- مجلد vendor (أو قم بتشغيل composer install)
```

### الخطوة 2: إعداد قاعدة البيانات

```sql
-- إنشاء قاعدة بيانات جديدة لكل دومين
CREATE DATABASE domain1_db;
CREATE DATABASE domain2_db;
CREATE DATABASE domain3_db;

-- إنشاء مستخدم
CREATE USER 'db_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON domain1_db.* TO 'db_user'@'localhost';
FLUSH PRIVILEGES;
```

### الخطوة 3: تعديل ملف .env لكل دومين

قم بإنشاء نسخة منفصلة لكل دومين مع التعديلات التالية:

**للدومين الأول:**
```env
APP_NAME="نظام المحاسبة - فرع 1"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain1.com
APP_URL_PREFIX=x7k9/m2p5q    # كود مختلف لكل دومين

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=domain1_db
DB_USERNAME=db_user
DB_PASSWORD=strong_password_here

SESSION_DRIVER=database
CACHE_STORE=database
```

**للدومين الثاني:**
```env
APP_NAME="نظام المحاسبة - فرع 2"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain2.com
APP_URL_PREFIX=r3v8/n1t4w    # كود مختلف

DB_DATABASE=domain2_db
# ... باقي الإعدادات
```

**للدومين الثالث:**
```env
APP_NAME="نظام المحاسبة - فرع 3"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain3.com
APP_URL_PREFIX=b6h2/k9m7s    # كود مختلف

DB_DATABASE=domain3_db
# ... باقي الإعدادات
```

### الخطوة 4: تشغيل Migrations

```bash
# لكل دومين، قم بتشغيل:
cd /path/to/domain1
php artisan migrate --force

cd /path/to/domain2
php artisan migrate --force

cd /path/to/domain3
php artisan migrate --force
```

### الخطوة 5: ضبط الصلاحيات

```bash
# لكل نسخة من النظام
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### الخطوة 6: إعداد Web Server

#### Apache (.htaccess)

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # منع الوصول للملفات الحساسة
    RewriteRule ^\.env$ - [F,L]
    RewriteRule ^composer\.(json|lock)$ - [F,L]
    
    # إعادة توجيه للـ public folder
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>

# منع عرض محتوى المجلدات
Options -Indexes

# حماية إضافية
<FilesMatch "\.(env|json|config.js|md|gitignore|gitattributes|lock)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

#### Nginx

```nginx
server {
    listen 80;
    server_name domain1.com;
    root /var/www/domain1/public;

    # منع الوصول للملفات الحساسة
    location ~ /\.(env|git) {
        deny all;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
}
```

---

## 🔐 إعدادات الأمان المتقدمة

### 1. تفعيل HTTPS

```bash
# استخدام Let's Encrypt
sudo certbot --apache -d domain1.com -d www.domain1.com
sudo certbot --apache -d domain2.com -d www.domain2.com
sudo certbot --apache -d domain3.com -d www.domain3.com
```

### 2. تفعيل Firewall

```bash
# تثبيت وتفعيل UFW
sudo ufw enable
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 22/tcp

# حظر بعد محاولات فاشلة
sudo apt install fail2ban
sudo systemctl start fail2ban
sudo systemctl enable fail2ban
```

### 3. إعداد Fail2Ban للوحة التحكم

قم بإنشاء ملف `/etc/fail2ban/filter.d/laravel-login.conf`:

```ini
[Definition]
failregex = ^.*Blocked IP attempted access: <HOST>.*$
            ^.*IP blocked due to failed login attempts: <HOST>.*$
ignoreregex =
```

ثم أضف إلى `/etc/fail2ban/jail.local`:

```ini
[laravel-login]
enabled = true
port = http,https
filter = laravel-login
logpath = /var/www/*/storage/logs/laravel.log
maxretry = 3
bantime = 3600
findtime = 600
```

### 4. تقييد الوصول بالـ IP (اختياري)

إذا أردت السماح فقط لـ IPs محددة بالوصول للوحة التحكم:

```apache
# في .htaccess
<Location "/b75/n95uk">
    Order Deny,Allow
    Deny from all
    Allow from 123.123.123.123
    Allow from 234.234.234.234
</Location>
```

---

## 📝 تغيير الـ URL Prefix

لتغيير المسار المخفي لكل دومين:

1. عدّل في ملف `.env`:
```env
APP_URL_PREFIX=your-custom-code/here
```

2. أمثلة مقترحة:
```
APP_URL_PREFIX=m8k3/n2v7x
APP_URL_PREFIX=assets/img-cache
APP_URL_PREFIX=cdn/resources
APP_URL_PREFIX=static/v2
```

3. بعد التعديل، قم بـ:
```bash
php artisan config:cache
php artisan route:cache
```

---

## 🧪 اختبار النظام

### 1. اختبار الصفحة الرئيسية
```
زيارة: https://yourdomain.com
النتيجة المتوقعة: صفحة بيضاء فارغة
```

### 2. اختبار تسجيل الدخول
```
زيارة: https://yourdomain.com/b75/n95uk/login
النتيجة المتوقعة: صفحة تسجيل الدخول
```

### 3. اختبار حظر IP
```
1. حاول تسجيل الدخول 3 مرات ببيانات خاطئة
2. في المحاولة الرابعة يجب أن تحصل على رسالة حظر
3. تحقق من الـ logs: storage/logs/laravel.log
```

### 4. اختبار robots.txt
```
زيارة: https://yourdomain.com/robots.txt
النتيجة المتوقعة: حظر جميع محركات البحث
```

---

## 🔄 نشر تحديثات مستقبلية

```bash
# 1. عمل backup لقاعدة البيانات
php artisan backup:database

# 2. تحديث الملفات
git pull origin main
composer install --no-dev

# 3. تشغيل migrations جديدة
php artisan migrate --force

# 4. مسح الـ cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. إعادة تشغيل services
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx
```

---

## 🆘 استكشاف الأخطاء

### المشكلة: صفحة 500 Error
```bash
# تحقق من الـ logs
tail -f storage/logs/laravel.log

# تحقق من الصلاحيات
sudo chown -R www-data:www-data storage bootstrap/cache
```

### المشكلة: المسارات لا تعمل
```bash
# مسح الـ cache
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### المشكلة: لا يمكن الوصول للتطبيق
```bash
# تحقق من APP_URL_PREFIX في .env
# تحقق من أنك تستخدم المسار الصحيح
```

---

## 📞 معلومات مهمة

### روابط الوصول لكل دومين:

**الدومين الأول:**
- الرئيسية: `https://domain1.com`
- لوحة التحكم: `https://domain1.com/x7k9/m2p5q/login`

**الدومين الثاني:**
- الرئيسية: `https://domain2.com`
- لوحة التحكم: `https://domain2.com/r3v8/n1t4w/login`

**الدومين الثالث:**
- الرئيسية: `https://domain3.com`
- لوحة التحكم: `https://domain3.com/b6h2/k9m7s/login`

### ⚠️ ملاحظات هامة:

1. **لا تشارك الـ URL Prefix مع أي شخص غير مصرح له**
2. **احتفظ بنسخة احتياطية من ملف .env**
3. **قم بتغيير APP_KEY لكل دومين**
4. **استخدم كلمات مرور قوية لقاعدة البيانات**
5. **فعّل HTTPS على جميع الدومينات**
6. **راقب ملفات الـ logs بشكل دوري**

---

## 📊 مراقبة الأمان

### تحقق من محاولات الاختراق:

```bash
# عرض محاولات تسجيل الدخول الفاشلة
grep "Blocked IP" storage/logs/laravel.log

# عرض IPs المحظورة
grep "blocked due to failed login" storage/logs/laravel.log
```

### تقرير يومي:

```bash
# إنشاء تقرير أمان يومي
cat > /usr/local/bin/security-report.sh << 'EOF'
#!/bin/bash
echo "=== Security Report $(date) ==="
echo "Failed Login Attempts:"
grep "Blocked IP" /var/www/*/storage/logs/laravel.log | wc -l
echo "Blocked IPs:"
grep "blocked due to" /var/www/*/storage/logs/laravel.log | tail -10
EOF

chmod +x /usr/local/bin/security-report.sh

# جدولة التقرير اليومي
echo "0 8 * * * /usr/local/bin/security-report.sh | mail -s 'Daily Security Report' admin@yourdomain.com" | crontab -
```

---

## ✅ Checklist قبل النشر

- [ ] تم رفع جميع الملفات
- [ ] تم إنشاء قواعد بيانات منفصلة لكل دومين
- [ ] تم تعديل ملف .env لكل دومين
- [ ] تم تشغيل migrations
- [ ] تم ضبط الصلاحيات (755 for directories, 644 for files)
- [ ] تم تثبيت SSL certificates
- [ ] تم اختبار تسجيل الدخول
- [ ] تم اختبار حظر IP
- [ ] تم التحقق من robots.txt
- [ ] تم تفعيل Fail2Ban
- [ ] تم إنشاء backup من قاعدة البيانات
- [ ] تم توثيق الـ URL Prefix لكل دومين
- [ ] تم اختبار جميع المسارات

---

## 🎯 الخلاصة

النظام الآن محمي بالكامل مع:
- ✅ إخفاء كامل للمسارات
- ✅ صفحة رئيسية بيضاء
- ✅ منع الأرشفة الكاملة
- ✅ حظر IP تلقائي
- ✅ Security headers قوية
- ✅ إمكانية نشر نسخ متعددة على دومينات مختلفة

**تاريخ الإعداد:** 3 يناير 2026  
**الإصدار:** 1.0.0
