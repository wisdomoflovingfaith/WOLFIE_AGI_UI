@echo off
REM WOLFIE AGI UI Setup Script for Windows
REM This script sets up the development environment for WOLFIE AGI UI

echo 🌺 WOLFIE AGI UI Setup Script
echo ==============================
echo.

REM Check if Node.js is installed
node --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Node.js is not installed. Please install Node.js 18+ first.
    echo    Download from: https://nodejs.org/
    pause
    exit /b 1
)

echo ✅ Node.js version: 
node --version

REM Check if npm is installed
npm --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ npm is not installed. Please install npm first.
    pause
    exit /b 1
)

echo ✅ npm version: 
npm --version

REM Install dependencies
echo.
echo 📦 Installing dependencies...
npm install

if %errorlevel% neq 0 (
    echo ❌ Failed to install dependencies
    pause
    exit /b 1
)

echo ✅ Dependencies installed successfully

REM Create .env file if it doesn't exist
if not exist .env (
    echo.
    echo 🔧 Creating .env file...
    copy env.example .env
    echo ✅ .env file created from template
    echo    Please edit .env with your configuration values
) else (
    echo ✅ .env file already exists
)

REM Run type checking
echo.
echo 🔍 Running type checking...
npm run type-check

if %errorlevel% neq 0 (
    echo ⚠️  Type checking found issues (this is normal for initial setup)
) else (
    echo ✅ Type checking passed
)

REM Run linting
echo.
echo 🧹 Running linting...
npm run lint

if %errorlevel% neq 0 (
    echo ⚠️  Linting found issues (this is normal for initial setup)
) else (
    echo ✅ Linting passed
)

echo.
echo 🎉 Setup complete!
echo.
echo Next steps:
echo 1. Edit .env file with your configuration
echo 2. Run 'npm run dev' to start the development server
echo 3. Open http://localhost:3000 in your browser
echo.
echo Available commands:
echo   npm run dev      - Start development server
echo   npm run build    - Build for production
echo   npm run preview  - Preview production build
echo   npm run lint     - Run ESLint
echo   npm run format   - Format code with Prettier
echo   npm run type-check - Run TypeScript type checking
echo.
echo 🌺 Welcome to WOLFIE AGI UI!
echo    Every line of code written with love, every feature built with compassion
echo    - Captain WOLFIE
echo.
pause
