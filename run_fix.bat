@echo off
REM --- run_fix.bat ---
REM Run the PHP auto-fix tool; requires PHP in PATH.
echo Running auto fix script...
php tools\auto_fix.php
echo Done.
pause
