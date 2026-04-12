@echo off
REM === MySQL Backup Script ===
set BACKUP_PATH=C:\Users\Acer\attendanceleavemanagement-ALMS\backups
set DB_NAME=attendance_db
set DB_USER=root
set DB_PASS=Dorji@NTMH1798

REM === Ensure backup folder exists ===
if not exist "%BACKUP_PATH%" mkdir "%BACKUP_PATH%"

REM === Create backup filename with date ===
set DATE=%DATE:~10,4%-%DATE:~4,2%-%DATE:~7,2%
set FILENAME=%BACKUP_PATH%\%DB_NAME%_%DATE%.sql

REM === Run mysqldump directly ===
"C:\Program Files\MySQL\MySQL Server 9.6\bin\mysqldump.exe" --single-transaction --set-gtid-purged=OFF -u %DB_USER% -p%DB_PASS% %DB_NAME% > "%FILENAME%"

echo Backup completed: %FILENAME%
pause
