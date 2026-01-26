@echo off
echo Starting Obertrack Queue Worker...
cd /d "%~dp0"
php artisan queue:work --sleep=3 --tries=3 --timeout=90
