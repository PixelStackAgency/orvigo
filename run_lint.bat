@echo off
REM --- run_lint.bat ---
REM Recursively run php -l on all PHP files. Requires PHP in PATH.
echo Running PHP lint across project...
for /R %%f in (*.php) do (
  echo Linting %%f
  php -l "%%f"
)
echo Done.
pause
