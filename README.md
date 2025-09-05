# Blog Management System - Kurulum ve Çalıştırma

Bu dökümantasyon, Laravel 12 ile geliştirdiğim Blog Yönetim Sistemi projesini adım adım çalıştırmak için hazırlanmıştır.

1-) İlk olarak laravel projelerimde Laravel 10 kullandığım için sürümüm 8.1.25' ti
Laravel 12 yi kullanabilmek için sürümü 8.2 yaptım.

2-)C dizinim çok dolu olduğu yani bellek yetersizliğinden dolayı D dizinide projemin adı olacak olan 
"blog_management_system" diye bir dosya oluşturdum.

3-)Komut istemcisini açıp oluşturduğum dosya yoluna giderek "composer create-project laravel/laravel . "12.*" 
" komutunu çalıştırarak projemi yaratmış oldum.

4-)Oluşturmuş olduğum projenin doğru çalışıp çalışmadığını kontrol etmek için kullandığım IDE(phpstorm) den 
php artisan serve komutunu çalıştırdım ve terminal çıktısı olarak IDE de hala 8.1.25 sürümü görünüyordu 
sürüm hatası verdi biraz araştırdım. IDE de ayarlar>PHP yi seçip CLI Interpreter kısmına 8.2 sürümünü ekleyerek
aktifleştirdim ve projemi tekrardan çalıştırdığımda projem hatasız bir şekilde çalıştı.

5-)Daha sonra veritabanı ile proje bağlantısı yapmak için XAMPP i açtığımda XAMPP sürümümde 8.1 
olduğundan apache kısmı çalışmadı. XAMPP i silerek 8.2 versiyonunu yÜkledim ve kurulumu yaptım.

6-)XAMPP imi sorunsuz bir şekilde çalıştırıp "blog_management_system" adında bir veritabanı oluşturdum.
Daha sonra projemdeki .env dosyasına gelip mysql veritabanı bağlantılarımı yaptım.

7-)php artisan migrate komutunu çalıştırarak veritabanıma tablolarımı migrate ettim. 

8-)Terminalde composer update ve npm run build komutlarını çalıştırarak gerekli bağımlılıkları yükledim.

9-)Son olarak terminalde php artsan serve komutunu çalıştırarak projemi başlattım ve eksiksiz bir şekilde projem
kodlanmaya hazır hale gelerek ayağa kalktı:)

