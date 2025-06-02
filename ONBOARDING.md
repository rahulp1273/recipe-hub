# 🍳 RecipeHub - Quick Setup Guide

## 📥 Step 1: Clone Project

```bash
git clone https://github.com/your-username/RecipeHub.git
cd RecipeHub
```

## ⚙️ Step 2: Install Everything

```bash
# Install PHP packages
composer install

# Install JavaScript packages  
npm install

# Copy environment file
cp .env.example .env
```

## 🗄️ Step 3: Setup Database

```bash
# Create database in MySQL
mysql -u root -p
CREATE DATABASE recipehub;
exit

# Generate app key
php artisan key:generate

# Run database setup
php artisan migrate
```

## 🔧 Step 4: Configure .env File

Open `.env` file and update:

```env
DB_DATABASE=recipehub
DB_USERNAME=your_mysql_username
DB_PASSWORD=your_mysql_password
```

## 🚀 Step 5: Start Servers

**Open 2 terminals:**

**Terminal 1:**

```bash
php artisan serve
```

*(Backend runs on http://localhost:8000)*

**Terminal 2:**

```bash

npm run dev
```

*(Frontend runs on http://localhost:5173)*

## ✅ Test Setup

- Open browser: `http://localhost:5173`
- Register new account
- Create a recipe
- If everything works = Setup Complete! 🎉

---

## 🌿 Working with Git (Team Rules)

### Create New Feature

```bash
# Get latest code
git pull origin main

# Create your branch
git checkout -b feature/your-feature-name

# Work on your code...
# When done:
git add .
git commit -m "add your feature description"
git push origin feature/your-feature-name
```

### Create Pull Request

1. Go to GitHub
2. Click "New Pull Request"
3. Select your branch
4. Add description
5. Wait for review & merge

### Get Latest Updates

```bash
git checkout main
git pull origin main
```

---

## 🆘 Common Problems

**Problem 1: Database error**

```bash
# Make sure MySQL is running
# Check .env database settings
```

**Problem 2: Permission error**

```bash
chmod -R 775 storage bootstrap/cache
```

**Problem 3: npm error**

```bash
rm -rf node_modules
npm install
```

---

## 📞 Need Help?

- Ask team lead
- Check GitHub issues
- Google the error message

**That's it! Keep it simple! 😊**
