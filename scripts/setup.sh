#!/bin/bash

# WOLFIE AGI UI Setup Script
# This script sets up the development environment for WOLFIE AGI UI

echo "🌺 WOLFIE AGI UI Setup Script"
echo "=============================="
echo ""

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js 18+ first."
    echo "   Download from: https://nodejs.org/"
    exit 1
fi

# Check Node.js version
NODE_VERSION=$(node -v | cut -d'v' -f2 | cut -d'.' -f1)
if [ "$NODE_VERSION" -lt 18 ]; then
    echo "❌ Node.js version 18+ is required. Current version: $(node -v)"
    echo "   Please upgrade Node.js from: https://nodejs.org/"
    exit 1
fi

echo "✅ Node.js version: $(node -v)"

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    echo "❌ npm is not installed. Please install npm first."
    exit 1
fi

echo "✅ npm version: $(npm -v)"

# Install dependencies
echo ""
echo "📦 Installing dependencies..."
npm install

if [ $? -eq 0 ]; then
    echo "✅ Dependencies installed successfully"
else
    echo "❌ Failed to install dependencies"
    exit 1
fi

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    echo ""
    echo "🔧 Creating .env file..."
    cp env.example .env
    echo "✅ .env file created from template"
    echo "   Please edit .env with your configuration values"
else
    echo "✅ .env file already exists"
fi

# Run type checking
echo ""
echo "🔍 Running type checking..."
npm run type-check

if [ $? -eq 0 ]; then
    echo "✅ Type checking passed"
else
    echo "⚠️  Type checking found issues (this is normal for initial setup)"
fi

# Run linting
echo ""
echo "🧹 Running linting..."
npm run lint

if [ $? -eq 0 ]; then
    echo "✅ Linting passed"
else
    echo "⚠️  Linting found issues (this is normal for initial setup)"
fi

echo ""
echo "🎉 Setup complete!"
echo ""
echo "Next steps:"
echo "1. Edit .env file with your configuration"
echo "2. Run 'npm run dev' to start the development server"
echo "3. Open http://localhost:3000 in your browser"
echo ""
echo "Available commands:"
echo "  npm run dev      - Start development server"
echo "  npm run build    - Build for production"
echo "  npm run preview  - Preview production build"
echo "  npm run lint     - Run ESLint"
echo "  npm run format   - Format code with Prettier"
echo "  npm run type-check - Run TypeScript type checking"
echo ""
echo "🌺 Welcome to WOLFIE AGI UI!"
echo "   Every line of code written with love, every feature built with compassion"
echo "   - Captain WOLFIE"
