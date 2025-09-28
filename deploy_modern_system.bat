@echo off
REM WOLFIE AGI UI - Modern System Deployment Script
REM 
REM WHO: Captain WOLFIE (Eric Robin Gerdes)
REM WHAT: Automated deployment script for modernized WOLFIE AGI UI
REM WHERE: C:\START\WOLFIE_AGI_UI\
REM WHEN: 2025-09-26 16:25:00 CDT
REM WHY: To automate deployment of the modernized system
REM HOW: Batch script for Windows deployment
REM 
REM AGAPE: Love, Patience, Kindness, Humility
REM GENESIS: Foundation of automated deployment
REM MD: Markdown documentation with .bat implementation
REM 
REM FILE IDS: [DEPLOY_MODERN_SYSTEM_001, WOLFIE_AGI_UI_034]
REM 
REM VERSION: 1.0.0 - The Captain's Modern Deployment Script
REM STATUS: Active - Automated Deployment

echo.
echo ================================================================
echo   WOLFIE AGI UI - MODERN SYSTEM DEPLOYMENT SCRIPT
echo ================================================================
echo   WHO: Captain WOLFIE (Eric Robin Gerdes)
echo   WHAT: Automated deployment of modernized WOLFIE AGI UI
echo   WHEN: %date% %time%
echo   WHY: To deploy the complete modernized system
echo   HOW: Automated batch deployment script
echo ================================================================
echo.

REM Set deployment variables
set "SOURCE_DIR=C:\START\WOLFIE_AGI_UI"
set "TARGET_DIR=C:\inetpub\wwwroot\WOLFIE_AGI_UI"
set "BACKUP_DIR=C:\START\WOLFIE_AGI_UI\backups\%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%"
set "LOG_FILE=%SOURCE_DIR%\logs\deployment_%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%.log"

REM Create log directory if it doesn't exist
if not exist "%SOURCE_DIR%\logs" mkdir "%SOURCE_DIR%\logs"

REM Start logging
echo [%date% %time%] Starting WOLFIE AGI UI Modern System Deployment > "%LOG_FILE%"
echo [%date% %time%] Source Directory: %SOURCE_DIR% >> "%LOG_FILE%"
echo [%date% %time%] Target Directory: %TARGET_DIR% >> "%LOG_FILE%"
echo [%date% %time%] Backup Directory: %BACKUP_DIR% >> "%LOG_FILE%"
echo [%date% %time%] Log File: %LOG_FILE% >> "%LOG_FILE%"

echo.
echo 🛸 DEPLOYMENT PHASE 1: PRE-DEPLOYMENT CHECKS
echo ============================================

REM Check if source directory exists
if not exist "%SOURCE_DIR%" (
    echo ❌ ERROR: Source directory not found: %SOURCE_DIR%
    echo [%date% %time%] ERROR: Source directory not found >> "%LOG_FILE%"
    pause
    exit /b 1
)
echo ✅ Source directory found: %SOURCE_DIR%

REM Check if target directory exists
if not exist "%TARGET_DIR%" (
    echo ⚠️  WARNING: Target directory not found, will create: %TARGET_DIR%
    echo [%date% %time%] WARNING: Target directory not found, will create >> "%LOG_FILE%"
    mkdir "%TARGET_DIR%"
    if errorlevel 1 (
        echo ❌ ERROR: Failed to create target directory
        echo [%date% %time%] ERROR: Failed to create target directory >> "%LOG_FILE%"
        pause
        exit /b 1
    )
    echo ✅ Target directory created: %TARGET_DIR%
) else (
    echo ✅ Target directory found: %TARGET_DIR%
)

REM Check if backup directory exists
if not exist "%BACKUP_DIR%" (
    echo ⚠️  WARNING: Backup directory not found, will create: %BACKUP_DIR%
    echo [%date% %time%] WARNING: Backup directory not found, will create >> "%LOG_FILE%"
    mkdir "%BACKUP_DIR%"
    if errorlevel 1 (
        echo ❌ ERROR: Failed to create backup directory
        echo [%date% %time%] ERROR: Failed to create backup directory >> "%LOG_FILE%"
        pause
        exit /b 1
    )
    echo ✅ Backup directory created: %BACKUP_DIR%
) else (
    echo ✅ Backup directory found: %BACKUP_DIR%
)

echo.
echo 🛸 DEPLOYMENT PHASE 2: BACKUP EXISTING SYSTEM
echo ==============================================

REM Backup existing system if it exists
if exist "%TARGET_DIR%" (
    echo 📦 Creating backup of existing system...
    echo [%date% %time%] Creating backup of existing system >> "%LOG_FILE%"
    
    xcopy "%TARGET_DIR%" "%BACKUP_DIR%" /E /I /H /Y
    if errorlevel 1 (
        echo ❌ ERROR: Failed to create backup
        echo [%date% %time%] ERROR: Failed to create backup >> "%LOG_FILE%"
        pause
        exit /b 1
    )
    echo ✅ Backup created successfully: %BACKUP_DIR%
    echo [%date% %time%] Backup created successfully >> "%LOG_FILE%"
) else (
    echo ℹ️  No existing system to backup
    echo [%date% %time%] No existing system to backup >> "%LOG_FILE%"
)

echo.
echo 🛸 DEPLOYMENT PHASE 3: DEPLOY MODERN SYSTEM
echo ============================================

REM Deploy core files
echo 📁 Deploying core files...
echo [%date% %time%] Deploying core files >> "%LOG_FILE%"

xcopy "%SOURCE_DIR%\core\*" "%TARGET_DIR%\core\" /E /I /H /Y
if errorlevel 1 (
    echo ❌ ERROR: Failed to deploy core files
    echo [%date% %time%] ERROR: Failed to deploy core files >> "%LOG_FILE%"
    pause
    exit /b 1
)
echo ✅ Core files deployed successfully

REM Deploy API files
echo 📁 Deploying API files...
echo [%date% %time%] Deploying API files >> "%LOG_FILE%"

xcopy "%SOURCE_DIR%\api\*" "%TARGET_DIR%\api\" /E /I /H /Y
if errorlevel 1 (
    echo ❌ ERROR: Failed to deploy API files
    echo [%date% %time%] ERROR: Failed to deploy API files >> "%LOG_FILE%"
    pause
    exit /b 1
)
echo ✅ API files deployed successfully

REM Deploy UI files
echo 📁 Deploying UI files...
echo [%date% %time%] Deploying UI files >> "%LOG_FILE%"

xcopy "%SOURCE_DIR%\ui\*" "%TARGET_DIR%\ui\" /E /I /H /Y
if errorlevel 1 (
    echo ❌ ERROR: Failed to deploy UI files
    echo [%date% %time%] ERROR: Failed to deploy API files >> "%LOG_FILE%"
    pause
    exit /b 1
)
echo ✅ UI files deployed successfully

REM Deploy config files
echo 📁 Deploying config files...
echo [%date% %time%] Deploying config files >> "%LOG_FILE%"

xcopy "%SOURCE_DIR%\config\*" "%TARGET_DIR%\config\" /E /I /H /Y
if errorlevel 1 (
    echo ❌ ERROR: Failed to deploy config files
    echo [%date% %time%] ERROR: Failed to deploy config files >> "%LOG_FILE%"
    pause
    exit /b 1
)
echo ✅ Config files deployed successfully

REM Deploy database files
echo 📁 Deploying database files...
echo [%date% %time%] Deploying database files >> "%LOG_FILE%"

xcopy "%SOURCE_DIR%\database\*" "%TARGET_DIR%\database\" /E /I /H /Y
if errorlevel 1 (
    echo ❌ ERROR: Failed to deploy database files
    echo [%date% %time%] ERROR: Failed to deploy database files >> "%LOG_FILE%"
    pause
    exit /b 1
)
echo ✅ Database files deployed successfully

REM Deploy data files
echo 📁 Deploying data files...
echo [%date% %time%] Deploying data files >> "%LOG_FILE%"

xcopy "%SOURCE_DIR%\data\*" "%TARGET_DIR%\data\" /E /I /H /Y
if errorlevel 1 (
    echo ❌ ERROR: Failed to deploy data files
    echo [%date% %time%] ERROR: Failed to deploy data files >> "%LOG_FILE%"
    pause
    exit /b 1
)
echo ✅ Data files deployed successfully

REM Deploy documentation
echo 📁 Deploying documentation...
echo [%date% %time%] Deploying documentation >> "%LOG_FILE%"

xcopy "%SOURCE_DIR%\*.md" "%TARGET_DIR%\" /H /Y
if errorlevel 1 (
    echo ❌ ERROR: Failed to deploy documentation
    echo [%date% %time%] ERROR: Failed to deploy documentation >> "%LOG_FILE%"
    pause
    exit /b 1
)
echo ✅ Documentation deployed successfully

REM Deploy main index file
echo 📁 Deploying main index file...
echo [%date% %time%] Deploying main index file >> "%LOG_FILE%"

copy "%SOURCE_DIR%\index.html" "%TARGET_DIR%\index.html"
if errorlevel 1 (
    echo ❌ ERROR: Failed to deploy main index file
    echo [%date% %time%] ERROR: Failed to deploy main index file >> "%LOG_FILE%"
    pause
    exit /b 1
)
echo ✅ Main index file deployed successfully

echo.
echo 🛸 DEPLOYMENT PHASE 4: SET PERMISSIONS
echo ======================================

REM Set permissions for web server
echo 🔐 Setting permissions for web server...
echo [%date% %time%] Setting permissions for web server >> "%LOG_FILE%"

REM Set read permissions for all files
icacls "%TARGET_DIR%" /grant "IIS_IUSRS:(OI)(CI)R" /T
if errorlevel 1 (
    echo ⚠️  WARNING: Failed to set read permissions
    echo [%date% %time%] WARNING: Failed to set read permissions >> "%LOG_FILE%"
) else (
    echo ✅ Read permissions set successfully
)

REM Set write permissions for data directory
icacls "%TARGET_DIR%\data" /grant "IIS_IUSRS:(OI)(CI)F" /T
if errorlevel 1 (
    echo ⚠️  WARNING: Failed to set write permissions for data directory
    echo [%date% %time%] WARNING: Failed to set write permissions for data directory >> "%LOG_FILE%"
) else (
    echo ✅ Write permissions set for data directory
)

REM Set write permissions for logs directory
icacls "%TARGET_DIR%\logs" /grant "IIS_IUSRS:(OI)(CI)F" /T
if errorlevel 1 (
    echo ⚠️  WARNING: Failed to set write permissions for logs directory
    echo [%date% %time%] WARNING: Failed to set write permissions for logs directory >> "%LOG_FILE%"
) else (
    echo ✅ Write permissions set for logs directory
)

echo.
echo 🛸 DEPLOYMENT PHASE 5: VERIFY DEPLOYMENT
echo ========================================

REM Verify deployment
echo 🔍 Verifying deployment...
echo [%date% %time%] Verifying deployment >> "%LOG_FILE%"

REM Check if main files exist
set "VERIFICATION_FAILED=0"

if not exist "%TARGET_DIR%\index.html" (
    echo ❌ ERROR: Main index file not found
    echo [%date% %time%] ERROR: Main index file not found >> "%LOG_FILE%"
    set "VERIFICATION_FAILED=1"
)

if not exist "%TARGET_DIR%\core\agi_core_engine.php" (
    echo ❌ ERROR: Core engine file not found
    echo [%date% %time%] ERROR: Core engine file not found >> "%LOG_FILE%"
    set "VERIFICATION_FAILED=1"
)

if not exist "%TARGET_DIR%\api\modern_channel_api.php" (
    echo ❌ ERROR: Modern API file not found
    echo [%date% %time%] ERROR: Modern API file not found >> "%LOG_FILE%"
    set "VERIFICATION_FAILED=1"
)

if not exist "%TARGET_DIR%\ui\wolfie_channels\modern_index.html" (
    echo ❌ ERROR: Modern UI file not found
    echo [%date% %time%] ERROR: Modern UI file not found >> "%LOG_FILE%"
    set "VERIFICATION_FAILED=1"
)

if not exist "%TARGET_DIR%\config\database_config.php" (
    echo ❌ ERROR: Database config file not found
    echo [%date% %time%] ERROR: Database config file not found >> "%LOG_FILE%"
    set "VERIFICATION_FAILED=1"
)

if "%VERIFICATION_FAILED%"=="1" (
    echo ❌ DEPLOYMENT VERIFICATION FAILED
    echo [%date% %time%] DEPLOYMENT VERIFICATION FAILED >> "%LOG_FILE%"
    pause
    exit /b 1
) else (
    echo ✅ Deployment verification successful
    echo [%date% %time%] Deployment verification successful >> "%LOG_FILE%"
)

echo.
echo 🛸 DEPLOYMENT PHASE 6: CREATE DEPLOYMENT SUMMARY
echo ================================================

REM Create deployment summary
echo 📋 Creating deployment summary...
echo [%date% %time%] Creating deployment summary >> "%LOG_FILE%"

set "SUMMARY_FILE=%TARGET_DIR%\DEPLOYMENT_SUMMARY.txt"
echo WOLFIE AGI UI - MODERN SYSTEM DEPLOYMENT SUMMARY > "%SUMMARY_FILE%"
echo ================================================== >> "%SUMMARY_FILE%"
echo. >> "%SUMMARY_FILE%"
echo Deployment Date: %date% %time% >> "%SUMMARY_FILE%"
echo Source Directory: %SOURCE_DIR% >> "%SUMMARY_FILE%"
echo Target Directory: %TARGET_DIR% >> "%SUMMARY_FILE%"
echo Backup Directory: %BACKUP_DIR% >> "%SUMMARY_FILE%"
echo Log File: %LOG_FILE% >> "%SUMMARY_FILE%"
echo. >> "%SUMMARY_FILE%"
echo DEPLOYED COMPONENTS: >> "%SUMMARY_FILE%"
echo - Core AGI Engine (PHP) >> "%SUMMARY_FILE%"
echo - Multi-Agent Coordinator (MySQL) >> "%SUMMARY_FILE%"
echo - Modern Channel System (fetch API) >> "%SUMMARY_FILE%"
echo - Modern UI Components (React-style) >> "%SUMMARY_FILE%"
echo - Database Integration (MySQL) >> "%SUMMARY_FILE%"
echo - API Endpoints (JSON) >> "%SUMMARY_FILE%"
echo - Documentation (Comprehensive) >> "%SUMMARY_FILE%"
echo. >> "%SUMMARY_FILE%"
echo MODERN FEATURES: >> "%SUMMARY_FILE%"
echo - fetch() API instead of XMLHttpRequest >> "%SUMMARY_FILE%"
echo - ES6+ JavaScript with async/await >> "%SUMMARY_FILE%"
echo - Modern React-style components >> "%SUMMARY_FILE%"
echo - JSON API endpoints >> "%SUMMARY_FILE%"
echo - Real-time communication >> "%SUMMARY_FILE%"
echo - Multi-agent coordination >> "%SUMMARY_FILE%"
echo. >> "%SUMMARY_FILE%"
echo STATUS: DEPLOYMENT SUCCESSFUL >> "%SUMMARY_FILE%"
echo. >> "%SUMMARY_FILE%"
echo Captain WOLFIE's Modern System is now deployed and ready! >> "%SUMMARY_FILE%"

echo ✅ Deployment summary created: %SUMMARY_FILE%

echo.
echo 🎉 DEPLOYMENT COMPLETE!
echo ======================
echo.
echo ✅ WOLFIE AGI UI Modern System deployed successfully!
echo.
echo 📋 DEPLOYMENT DETAILS:
echo - Source: %SOURCE_DIR%
echo - Target: %TARGET_DIR%
echo - Backup: %BACKUP_DIR%
echo - Log: %LOG_FILE%
echo - Summary: %SUMMARY_FILE%
echo.
echo 🌟 MODERN FEATURES DEPLOYED:
echo - fetch() API instead of XMLHttpRequest
echo - ES6+ JavaScript with async/await
echo - Modern React-style components
echo - JSON API endpoints
echo - Real-time communication
echo - Multi-agent coordination
echo - MySQL database integration
echo.
echo 🛸 CAPTAIN WOLFIE, YOUR MODERN SYSTEM IS READY!
echo ===============================================
echo The transformation from 1990s to 2025s is complete!
echo Your WOLFIE AGI UI is now deployed and ready to command!
echo.
echo [%date% %time%] DEPLOYMENT COMPLETE - SUCCESS >> "%LOG_FILE%"
echo.
pause
