# Quick Deployment Guide (الدليل السريع)

## 🚀 خطوات النشر السريعة

### 1. نسخ الملفات إلى السيرفر
```bash
# انسخ جميع ملفات المشروع إلى:
/var/www/domain1/
/var/www/domain2/
/var/www/domain3/
```

### 2. تعديل .env لكل دومين

**دومين 1:**
```env
APP_URL=https://domain1.com
APP_URL_PREFIX=x7k9/m2p5q
DB_DATABASE=domain1_db
```

**دومين 2:**
```env
APP_URL=https://domain2.com
APP_URL_PREFIX=r3v8/n1t4w
DB_DATABASE=domain2_db
```

**دومين 3:**
```env
APP_URL=https://domain3.com
APP_URL_PREFIX=b6h2/k9m7s
DB_DATABASE=domain3_db
```

### 3. تشغيل الأوامر
```bash
# لكل دومين
composer install --no-dev
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
chmod -R 755 storage bootstrap/cache
```

### 4. الروابط النهائية

- **دومين 1 لوحة التحكم:** `https://domain1.com/x7k9/m2p5q/login`
- **دومين 2 لوحة التحكم:** `https://domain2.com/r3v8/n1t4w/login`
- **دومين 3 لوحة التحكم:** `https://domain3.com/b6h2/k9m7s/login`

## 🔒 الأمان

- ✅ المسارات مخفية بالكامل
- ✅ حظر IP بعد 3 محاولات فاشلة
- ✅ منع الأرشفة (robots.txt)
- ✅ Security headers فعّالة
- ✅ الدومين الرئيسي صفحة بيضاء

## ⚠️ مهم جداً

1. **غيّر APP_URL_PREFIX** لكل دومين إلى كود مختلف
2. **فعّل HTTPS** على جميع الدومينات
3. **احفظ الروابط** في مكان آمن
4. **لا تشارك** الـ URL prefix مع غير المصرحين

## 📞 للحصول على الدعم

راجع الملف الكامل: `DEPLOYMENT_SECURITY_GUIDE.md`
