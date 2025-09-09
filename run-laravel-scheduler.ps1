# Laravel Scheduler'ı çalıştıran PowerShell Script
$projectPath = "D:\blog_management_system"
$phpPath = "C:\xampp\php8.2\php.exe"  # PHP.exe yolu

Set-Location $projectPath
& $phpPath artisan schedule:run
