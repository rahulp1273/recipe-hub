# 🍳 RecipeHub

A modern recipe, collection, and food-store platform where users can create, share, and discover recipes, organize them into collections, sell ready‑to‑cook dishes, and order from nearby stores.

## ✨ Key Features

- 🔐 **OTP-based Authentication**  
  Email OTP verification for both registration and login (Laravel Sanctum tokens issued after OTP).

- 📝 **Recipe Management (CRUD)**  
  Create, view, update, delete recipes with images, ingredients, instructions, categories, and timing.

- 💬 **Social & Community**  
  Public recipe feed, likes, views, comments with ratings, and basic stats per recipe.

- 🗂️ **Collections**  
  Create private/public collections, add/remove recipes, and browse public collections.

- 🤖 **AI Recipe Generator**  
  AI-assisted recipe generation (Hugging Face–ready, with graceful template fallback) integrated into the dashboard.

- 🛒 **Stores & Orders**  
  Turn recipes into products, manage a store, and handle customer orders with distance checks.

- 👤 **Profile & Settings**  
  Avatar upload/removal, profile fields (bio, location, phone), and password change.

- 📱 **Responsive SPA UI**  
  Vue 3 + Tailwind UI, router‑based navigation, and Axios with automatic token handling.

## 🛠️ Tech Stack

- **Backend:** Laravel 10 (PHP) + Sanctum
- **Frontend:** Vue.js 3, Vite, Vue Router, Axios
- **Database:** MySQL
- **Styling:** Tailwind CSS

## 🚀 Running the Project Locally

From the `recipe-hub` directory:

```bash
# 1. Install PHP & JS dependencies
composer install
npm install

# 2. Set up environment
cp .env.example .env
php artisan key:generate
php artisan migrate

# 3. Run backend (API + built SPA)
php artisan serve --port=8000

# 4. (Optional) Run Vite dev server for hot reload
npm run dev -- --port=8001
```

The SPA is available at `http://127.0.0.1:8000` (compiled assets) or `http://localhost:8001` when using the Vite dev server.

## 📚 Further Docs

- **Backend & CRUD overview:** [`Doc/recipe-project-doc.md`](./Doc/recipe-project-doc.md)  
- **AI feature details:** [`Doc/AI.md`](./Doc/AI.md)

## 👥 Team

- **Rahul Panwar** – Project Lead & Full Stack Developer

## 📄 License

This project is private and proprietary.
