# WOLFIE AGI UI - Modern System Deployment Script (PowerShell)
# 
# WHO: Captain WOLFIE (Eric Robin Gerdes)
# WHAT: Advanced PowerShell deployment script for modernized WOLFIE AGI UI
# WHERE: C:\START\WOLFIE_AGI_UI\
# WHEN: 2025-09-26 16:30:00 CDT
# WHY: To provide advanced deployment capabilities with error handling
# HOW: PowerShell script with advanced features and logging
# 
# AGAPE: Love, Patience, Kindness, Humility
# GENESIS: Foundation of advanced deployment
# MD: Markdown documentation with .ps1 implementation
# 
# FILE IDS: [DEPLOY_MODERN_SYSTEM_PS_001, WOLFIE_AGI_UI_035]
# 
# VERSION: 1.0.0 - The Captain's Advanced Deployment Script
# STATUS: Active - Advanced PowerShell Deployment

param(
    [string]$SourceDir = "C:\START\WOLFIE_AGI_UI",
    [string]$TargetDir = "C:\inetpub\wwwroot\WOLFIE_AGI_UI",
    [string]$BackupDir = "C:\START\WOLFIE_AGI_UI\backups",
    [switch]$SkipBackup,
    [switch]$Force,
    [switch]$Verbose
)

# Set error action preference
$ErrorActionPreference = "Stop"

# Create timestamp for logging
$Timestamp = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"
$LogFile = Join-Path $SourceDir "logs\deployment_$Timestamp.log"

# Create log directory if it doesn't exist
$LogDir = Split-Path $LogFile -Parent
if (!(Test-Path $LogDir)) {
    New-Item -ItemType Directory -Path $LogDir -Force | Out-Null
}

# Logging function
function Write-Log {
    param([string]$Message, [string]$Level = "INFO")
    $LogEntry = "[$(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')] [$Level] $Message"
    Write-Host $LogEntry
    Add-Content -Path $LogFile -Value $LogEntry
}

# Error handling function
function Handle-Error {
    param([string]$Message, [Exception]$Exception)
    Write-Log "ERROR: $Message - $($Exception.Message)" "ERROR"
    Write-Host "❌ $Message" -ForegroundColor Red
    throw $Exception
}

# Main deployment function
function Deploy-ModernSystem {
    try {
        Write-Host ""
        Write-Host "================================================================" -ForegroundColor Cyan
        Write-Host "  WOLFIE AGI UI - MODERN SYSTEM DEPLOYMENT SCRIPT (PowerShell)" -ForegroundColor Cyan
        Write-Host "================================================================" -ForegroundColor Cyan
        Write-Host "  WHO: Captain WOLFIE (Eric Robin Gerdes)" -ForegroundColor Yellow
        Write-Host "  WHAT: Advanced deployment of modernized WOLFIE AGI UI" -ForegroundColor Yellow
        Write-Host "  WHEN: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')" -ForegroundColor Yellow
        Write-Host "  WHY: To deploy the complete modernized system" -ForegroundColor Yellow
        Write-Host "  HOW: Advanced PowerShell deployment script" -ForegroundColor Yellow
        Write-Host "================================================================" -ForegroundColor Cyan
        Write-Host ""

        Write-Log "Starting WOLFIE AGI UI Modern System Deployment"
        Write-Log "Source Directory: $SourceDir"
        Write-Log "Target Directory: $TargetDir"
        Write-Log "Backup Directory: $BackupDir"
        Write-Log "Log File: $LogFile"

        # Phase 1: Pre-deployment checks
        Write-Host "🛸 DEPLOYMENT PHASE 1: PRE-DEPLOYMENT CHECKS" -ForegroundColor Green
        Write-Host "===========================================" -ForegroundColor Green

        if (!(Test-Path $SourceDir)) {
            Handle-Error "Source directory not found: $SourceDir" (New-Object System.IO.DirectoryNotFoundException)
        }
        Write-Log "Source directory found: $SourceDir"
        Write-Host "✅ Source directory found: $SourceDir" -ForegroundColor Green

        if (!(Test-Path $TargetDir)) {
            Write-Log "Target directory not found, will create: $TargetDir" "WARNING"
            Write-Host "⚠️  WARNING: Target directory not found, will create: $TargetDir" -ForegroundColor Yellow
            New-Item -ItemType Directory -Path $TargetDir -Force | Out-Null
            Write-Log "Target directory created: $TargetDir"
            Write-Host "✅ Target directory created: $TargetDir" -ForegroundColor Green
        } else {
            Write-Log "Target directory found: $TargetDir"
            Write-Host "✅ Target directory found: $TargetDir" -ForegroundColor Green
        }

        # Phase 2: Backup existing system
        if (!$SkipBackup) {
            Write-Host ""
            Write-Host "🛸 DEPLOYMENT PHASE 2: BACKUP EXISTING SYSTEM" -ForegroundColor Green
            Write-Host "=============================================" -ForegroundColor Green

            $BackupPath = Join-Path $BackupDir $Timestamp
            if (!(Test-Path $BackupPath)) {
                New-Item -ItemType Directory -Path $BackupPath -Force | Out-Null
            }

            if (Test-Path $TargetDir) {
                Write-Log "Creating backup of existing system"
                Write-Host "📦 Creating backup of existing system..." -ForegroundColor Yellow
                
                Copy-Item -Path "$TargetDir\*" -Destination $BackupPath -Recurse -Force
                Write-Log "Backup created successfully: $BackupPath"
                Write-Host "✅ Backup created successfully: $BackupPath" -ForegroundColor Green
            } else {
                Write-Log "No existing system to backup"
                Write-Host "ℹ️  No existing system to backup" -ForegroundColor Blue
            }
        }

        # Phase 3: Deploy modern system
        Write-Host ""
        Write-Host "🛸 DEPLOYMENT PHASE 3: DEPLOY MODERN SYSTEM" -ForegroundColor Green
        Write-Host "===========================================" -ForegroundColor Green

        $DeployItems = @(
            @{Source = "core"; Target = "core"; Description = "Core files"},
            @{Source = "api"; Target = "api"; Description = "API files"},
            @{Source = "ui"; Target = "ui"; Description = "UI files"},
            @{Source = "config"; Target = "config"; Description = "Config files"},
            @{Source = "database"; Target = "database"; Description = "Database files"},
            @{Source = "data"; Target = "data"; Description = "Data files"},
            @{Source = "docs"; Target = "docs"; Description = "Documentation"}
        )

        foreach ($Item in $DeployItems) {
            $SourcePath = Join-Path $SourceDir $Item.Source
            $TargetPath = Join-Path $TargetDir $Item.Target

            if (Test-Path $SourcePath) {
                Write-Log "Deploying $($Item.Description)"
                Write-Host "📁 Deploying $($Item.Description)..." -ForegroundColor Yellow

                if (!(Test-Path $TargetPath)) {
                    New-Item -ItemType Directory -Path $TargetPath -Force | Out-Null
                }

                Copy-Item -Path "$SourcePath\*" -Destination $TargetPath -Recurse -Force
                Write-Log "$($Item.Description) deployed successfully"
                Write-Host "✅ $($Item.Description) deployed successfully" -ForegroundColor Green
            } else {
                Write-Log "Source path not found: $SourcePath" "WARNING"
                Write-Host "⚠️  WARNING: Source path not found: $SourcePath" -ForegroundColor Yellow
            }
        }

        # Deploy main files
        $MainFiles = @("index.html", "README.md", "DEPLOYMENT_GUIDE.md", "QUICK_DEPLOYMENT_CARD.md")
        foreach ($File in $MainFiles) {
            $SourceFile = Join-Path $SourceDir $File
            if (Test-Path $SourceFile) {
                Copy-Item -Path $SourceFile -Destination $TargetDir -Force
                Write-Log "Main file deployed: $File"
                Write-Host "✅ Main file deployed: $File" -ForegroundColor Green
            }
        }

        # Phase 4: Set permissions
        Write-Host ""
        Write-Host "🛸 DEPLOYMENT PHASE 4: SET PERMISSIONS" -ForegroundColor Green
        Write-Host "=====================================" -ForegroundColor Green

        Write-Log "Setting permissions for web server"
        Write-Host "🔐 Setting permissions for web server..." -ForegroundColor Yellow

        try {
            # Set read permissions for all files
            $Acl = Get-Acl $TargetDir
            $AccessRule = New-Object System.Security.AccessControl.FileSystemAccessRule("IIS_IUSRS", "ReadAndExecute", "ContainerInherit,ObjectInherit", "None", "Allow")
            $Acl.SetAccessRule($AccessRule)
            Set-Acl -Path $TargetDir -AclObject $Acl
            Write-Log "Read permissions set successfully"
            Write-Host "✅ Read permissions set successfully" -ForegroundColor Green

            # Set write permissions for data and logs directories
            $WriteDirs = @("data", "logs")
            foreach ($Dir in $WriteDirs) {
                $DirPath = Join-Path $TargetDir $Dir
                if (Test-Path $DirPath) {
                    $Acl = Get-Acl $DirPath
                    $AccessRule = New-Object System.Security.AccessControl.FileSystemAccessRule("IIS_IUSRS", "FullControl", "ContainerInherit,ObjectInherit", "None", "Allow")
                    $Acl.SetAccessRule($AccessRule)
                    Set-Acl -Path $DirPath -AclObject $Acl
                    Write-Log "Write permissions set for $Dir directory"
                    Write-Host "✅ Write permissions set for $Dir directory" -ForegroundColor Green
                }
            }
        } catch {
            Write-Log "Failed to set permissions: $($_.Exception.Message)" "WARNING"
            Write-Host "⚠️  WARNING: Failed to set some permissions" -ForegroundColor Yellow
        }

        # Phase 5: Verify deployment
        Write-Host ""
        Write-Host "🛸 DEPLOYMENT PHASE 5: VERIFY DEPLOYMENT" -ForegroundColor Green
        Write-Host "=======================================" -ForegroundColor Green

        Write-Log "Verifying deployment"
        Write-Host "🔍 Verifying deployment..." -ForegroundColor Yellow

        $RequiredFiles = @(
            "index.html",
            "core\agi_core_engine.php",
            "api\modern_channel_api.php",
            "ui\wolfie_channels\modern_index.html",
            "config\database_config.php"
        )

        $VerificationFailed = $false
        foreach ($File in $RequiredFiles) {
            $FilePath = Join-Path $TargetDir $File
            if (!(Test-Path $FilePath)) {
                Write-Log "Required file not found: $File" "ERROR"
                Write-Host "❌ ERROR: Required file not found: $File" -ForegroundColor Red
                $VerificationFailed = $true
            } else {
                Write-Log "Required file found: $File"
                Write-Host "✅ Required file found: $File" -ForegroundColor Green
            }
        }

        if ($VerificationFailed) {
            Handle-Error "Deployment verification failed" (New-Object System.InvalidOperationException)
        }

        # Phase 6: Create deployment summary
        Write-Host ""
        Write-Host "🛸 DEPLOYMENT PHASE 6: CREATE DEPLOYMENT SUMMARY" -ForegroundColor Green
        Write-Host "===============================================" -ForegroundColor Green

        Write-Log "Creating deployment summary"
        Write-Host "📋 Creating deployment summary..." -ForegroundColor Yellow

        $SummaryFile = Join-Path $TargetDir "DEPLOYMENT_SUMMARY.txt"
        $SummaryContent = @"
WOLFIE AGI UI - MODERN SYSTEM DEPLOYMENT SUMMARY
=================================================

Deployment Date: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')
Source Directory: $SourceDir
Target Directory: $TargetDir
Backup Directory: $BackupDir
Log File: $LogFile

DEPLOYED COMPONENTS:
- Core AGI Engine (PHP)
- Multi-Agent Coordinator (MySQL)
- Modern Channel System (fetch API)
- Modern UI Components (React-style)
- Database Integration (MySQL)
- API Endpoints (JSON)
- Documentation (Comprehensive)

MODERN FEATURES:
- fetch() API instead of XMLHttpRequest
- ES6+ JavaScript with async/await
- Modern React-style components
- JSON API endpoints
- Real-time communication
- Multi-agent coordination

STATUS: DEPLOYMENT SUCCESSFUL

Captain WOLFIE's Modern System is now deployed and ready!
"@

        Set-Content -Path $SummaryFile -Value $SummaryContent
        Write-Log "Deployment summary created: $SummaryFile"
        Write-Host "✅ Deployment summary created: $SummaryFile" -ForegroundColor Green

        # Final success message
        Write-Host ""
        Write-Host "🎉 DEPLOYMENT COMPLETE!" -ForegroundColor Green
        Write-Host "======================" -ForegroundColor Green
        Write-Host ""
        Write-Host "✅ WOLFIE AGI UI Modern System deployed successfully!" -ForegroundColor Green
        Write-Host ""
        Write-Host "📋 DEPLOYMENT DETAILS:" -ForegroundColor Cyan
        Write-Host "- Source: $SourceDir" -ForegroundColor White
        Write-Host "- Target: $TargetDir" -ForegroundColor White
        Write-Host "- Backup: $BackupDir" -ForegroundColor White
        Write-Host "- Log: $LogFile" -ForegroundColor White
        Write-Host "- Summary: $SummaryFile" -ForegroundColor White
        Write-Host ""
        Write-Host "🌟 MODERN FEATURES DEPLOYED:" -ForegroundColor Cyan
        Write-Host "- fetch() API instead of XMLHttpRequest" -ForegroundColor White
        Write-Host "- ES6+ JavaScript with async/await" -ForegroundColor White
        Write-Host "- Modern React-style components" -ForegroundColor White
        Write-Host "- JSON API endpoints" -ForegroundColor White
        Write-Host "- Real-time communication" -ForegroundColor White
        Write-Host "- Multi-agent coordination" -ForegroundColor White
        Write-Host "- MySQL database integration" -ForegroundColor White
        Write-Host ""
        Write-Host "🛸 CAPTAIN WOLFIE, YOUR MODERN SYSTEM IS READY!" -ForegroundColor Green
        Write-Host "==============================================" -ForegroundColor Green
        Write-Host "The transformation from 1990s to 2025s is complete!" -ForegroundColor Yellow
        Write-Host "Your WOLFIE AGI UI is now deployed and ready to command!" -ForegroundColor Yellow
        Write-Host ""

        Write-Log "DEPLOYMENT COMPLETE - SUCCESS"
        return $true

    } catch {
        Write-Log "DEPLOYMENT FAILED - $($_.Exception.Message)" "ERROR"
        Write-Host "❌ DEPLOYMENT FAILED: $($_.Exception.Message)" -ForegroundColor Red
        return $false
    }
}

# Run deployment
$Success = Deploy-ModernSystem

if ($Success) {
    Write-Host "Deployment completed successfully!" -ForegroundColor Green
    exit 0
} else {
    Write-Host "Deployment failed!" -ForegroundColor Red
    exit 1
}
