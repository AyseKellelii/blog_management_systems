# Blog Management System - Kurulum ve Çalıştırma

Bu dökümantasyon, Laravel 12 ile geliştirdiğim Blog Yönetim Sistemi projesini adım adım çalıştırmak için hazırlanmıştır.

1. Gereksinimler

PHP >= 8.2
Composer
Node.js & NPM
MySQL / MariaDB
Postman (API testleri için)

2. Kurulum Adımları

PHP sürümümü kontrol ettim ve Laravel 12 için PHP 8.2’ye yükselttim.
Dizinimde C sürücüsü dolu olduğu için D sürücüsünde blog_management_system adında bir klasör oluşturdum.
Komut istemcisini açıp bu dizine giderek Laravel projesini oluşturdum:
composer create-project laravel/laravel . "12.*" 
Bu komutla laravel projem oluşturulmuş oldu.Daha sonra
PhpStorm’dan php artisan serve komutunu çalıştırdım. Başlangıçta PHP sürümü 8.1.25 olduğu için hata verdi.
PhpStorm ayarlarından CLI Interpreter olarak PHP 8.2’yi ekleyip aktifleştirdim ve tekrar çalıştırdım. Proje hatasız açıldı.

3. Node.js ve NPM Kurulumu

Node.js resmi web sitesinden Node.js 20.x LTS sürümünü indirdim ve yükledim.
Terminalden versiyonları kontrol ettim:

node -v
npm -v

Proje bağımlılıklarını yükledim:

npm install
npm run build

4. Veritabanı ve .env Ayarları

XAMPP’im 8.1v olduğu için laravel 12 yi desteklemiyordu ve kaldırıp PHP 8.2 sürümünü yükledim.
Veritabanımı hem mysql hem de datagrip ide' si üzerinden yönettim.
blog_management_system adında bir veritabanı oluşturdum.
.env dosyasını düzenledim:

APP_NAME=BlogManagementSystem
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_management_system
DB_USERNAME=root
DB_PASSWORD=
SANCTUM_STATEFUL_DOMAINS=127.0.0.1:8000

5. Tabloları migrate ettim:
php artisan migrate

6. Projeyi Çalıştırma:
   
Terminalden proje dizinine giderek:
php artisan serve
komutunu çalıştırdım. Böylece proje eksiksiz çalışır hâle geldi.

7. Postman Kullanımı
   
Postman’i açtım ve BlogManagementSystem API collection’ını içe aktardım.
API endpointlerini test ettim.
Authorization kısmında Bearer Token kullanarak korunan endpointleri test ettim.
BlogManagementSystem/postman/BlogManagementSystem API.postman_collection.json yolunda bulunan dosyamı projeye entegre ettim

8. Ek Notlar

Projede Laravel Sanctum kullanarak API authentication sağlanmıştır.
Spatie MediaLibrary dosya yükleme işlemleri için entegre edilmiştir.
Spatie ActivityLog ile model değişiklikleri loglanmaktadır.
Frontend tarafı Vue 3 Composition API ve Tailwind CSS ile geliştirilmiştir.
Git versiyon kontrolü kullanılmıştır ve Postman collection projeye eklenmiştir.

