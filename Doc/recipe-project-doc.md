
# 📘 RecipeHub Project Documentation

## 1. 🧾 Project Overview

RecipeHub is a Laravel + Vue.js application for:

- User registration/login with **email OTP verification** (register + login)
- Creating, updating, deleting, and browsing **recipes**
- Social features: likes, views, ratings, comments, and public feed
- Organising recipes into **collections** (private + public)
- AI-assisted recipe generation
- Profile management (avatar, bio, location, password)
- Seller features: create a **store**, add recipe-based products, and receive **orders**

The backend is a token-authenticated REST API using Laravel Sanctum. The frontend is a Vue SPA using `vue-router` and Axios.

---

## 2. 🛢️ Database Design (high level)

Key tables (simplified):

- `users`: core user profile, authentication fields, avatar, bio, location, etc.
- `recipes`: user-authored and AI-generated recipes (title, description, category, timings, ingredients, instructions, image, flags).
- `recipe_likes` / aggregated `likes_count` on `recipes`.
- `recipe_views` / aggregated `views_count` on `recipes`.
- `recipe_comments`: comments + optional rating per recipe.
- `recipe_collections` + pivot `collection_recipes`: user collections and recipes they contain.
- `stores`: seller storefront linked to a user.
- `store_products`: recipes offered for sale by a store (price, quantity, availability).
- `orders`: orders placed by customers for store products.
- `otp_verifications`: email OTPs for login/registration (hashed code, attempts, expiry, type).

Refer to individual migrations in `database/migrations` for exact columns.

---

## 3. 🔁 Core Models & Relationships

### `User`

- `recipes()`: hasMany `Recipe`
- `views()`: hasMany `RecipeView`
- `comments()`: hasMany `RecipeComment`
- `collections()`: hasMany `RecipeCollection`
- `store()`: hasOne `Store`
- `orders()`: hasMany `Order` (as customer)

### `Recipe`

- `user()`: belongsTo `User`
- `likes()`: hasMany `RecipeLike`
- `views()`: hasMany `RecipeView`
- `comments()`: hasMany `RecipeComment`
- `collections()`: belongsToMany `RecipeCollection`

### Collections & Social

- `RecipeCollection` hasMany `recipes` via pivot table.
- `RecipeLike`/`RecipeView` belong to both `User` and `Recipe`.
- `RecipeComment` belongs to `User` and `Recipe`.

### Stores & Orders

- `Store` belongsTo `User`, hasMany `StoreProduct` and `Order`.
- `StoreProduct` belongsTo `Store` and `Recipe`.
- `Order` belongsTo `Store`, `StoreProduct`, and `User` (customer).

### OTP

- `OtpVerification` belongsTo `User` (optional, can be null for pre-registration).

---

## 4. 🧩 Controllers & CRUD Capabilities

Below is what each controller exposes and the effective CRUD for each domain.

### 4.1 Authentication & OTP

**Controller:** `AuthController`  
**Routes:**

- `POST /api/register`  
  - Validates name/email/password.  
  - Creates user (email initially unverified).  
  - Triggers OTP for `register`.  
  - Response: `{ success, requires_otp: true, email, type: "register" }`

- `POST /api/login`  
  - Validates email/password.  
  - Triggers OTP for `login`.  
  - Response: `{ success, requires_otp: true, email, type: "login" }`

- `POST /api/logout` (auth) – revoke current Sanctum token.
- `GET /api/user` (auth) – get authenticated user JSON (used in dashboard).

**Controller:** `OtpController`  
**Routes:**

- `POST /api/verify-otp`
  - Request: `{ email, otp, type: "register"|"login" }`
  - Validates record exists, not expired, attempts < 5, OTP hash match.
  - For `register`: sets `email_verified_at`.
  - Deletes OTP + issues Sanctum token.

- `POST /api/resend-otp`
  - Request: `{ email, type }`
  - Regenerates + sends OTP email.

**Service:** `OtpService`

- Centralises OTP generation, hashing, expiry (5 minutes), attempt limiting, and verification logic.

---

### 4.2 Recipes

**Controller:** `Api\RecipeController`  
**Routes (auth):**

- `GET /api/recipes` – list all recipes (paginated, includes author).
- `POST /api/recipes` – **Create** a recipe with:
  - title, description, category, timings, servings,
  - `ingredients[]`, `instructions[]`,
  - optional image upload.
- `GET /api/recipes/{recipe}` – **Read** single recipe (with user, comment count, average rating, like status).
- `PUT/PATCH /api/recipes/{recipe}` – **Update** recipe (owner only, image add/remove).
- `DELETE /api/recipes/{recipe}` – **Delete** recipe (owner only).
- `GET /api/my-recipes` – list recipes owned by current user.

**Front‑end pages:**

- `recipes/RecipeList.vue` – list & filter user recipes.
- `recipes/CreateRecipe.vue` – create recipe form.
- `recipes/RecipeEdit.vue` – edit recipe form.
- `recipes/RecipeView.vue` – detailed recipe view (likes, comments, collections).
- `recipes/PublicRecipeView.vue` – public read-only view.

---

### 4.3 Social (Likes, Views, Public Feed)

**Controller:** `Api\SocialController`

- `GET /api/feed` (public)
  - Public recipe feed (only `is_public = true` recipes).
  - Filters: search, category, type (veg/non-veg), `min_rating`, `max_prep_time`.
  - Response includes like status and counts for the current user if authenticated.

- `POST /api/social/recipes/{recipe}/like` (auth)
  - Toggle like/unlike a recipe.
  - Adjusts `likes_count` on recipe.

- `POST /api/recipes/{recipe}/view` (public)
  - Records daily unique view by (user or IP).
  - Adjusts `views_count` on recipe.

- `GET /api/social/recipes/{recipe}/stats` (auth)
  - Returns `{ likes_count, views_count, is_liked }`.

**Front‑end components/pages:**

- `components/recipe/LikeButton.vue`
- `components/recipe/CommentSection.vue`
- `Pages/social/HomeFeed.vue`

---

### 4.4 Comments

**Controller:** `Api\CommentController`

- `GET /api/recipes/{recipe}/comments` (public) – list all comments for a recipe.
- `POST /api/recipes/{recipe}/comments` (auth) – add comment + optional rating (one per user per recipe).
- `PUT /api/recipes/{recipe}/comments/{comment}` (auth) – update own comment.
- `DELETE /api/recipes/{recipe}/comments/{comment}` (auth) – delete own comment.

---

### 4.5 Collections

**Controller:** `Api\CollectionController`

Authenticated user collections:

- `GET /api/collections` – list collections with recipe counts.
- `POST /api/collections` – **Create** a collection (`name`, `description`, `is_public`, `color`, `icon`).
- `GET /api/collections/{collection}` – **Read** collection + recipes + comments.
- `PUT /api/collections/{collection}` – **Update**.
- `DELETE /api/collections/{collection}` – **Delete**.
- `POST /api/collections/{collection}/recipes` – attach recipe to collection.
- `DELETE /api/collections/{collection}/recipes` – detach recipe from collection.

Public collections:

- `GET /api/public/collections` – paginated list of public collections with recipe counts.

Front‑end:

- `Pages/Collections.vue`, `CollectionView.vue`, and `PublicCollections.vue`.

---

### 4.6 AI Recipe Generation

**Controller:** `Api\AiRecipeController`  
See `Doc/AI.md` for detailed docs.

Key endpoints:

- `POST /api/ai/generate-recipe` (auth) – generate and persist an AI recipe.
- `GET /api/ai/generated-recipes` (auth) – list user’s AI‑generated recipes.

Internally it:

- Optionally calls Hugging Face (if API key present) or falls back to template recipes.
- Writes structured `Recipe` records marked `is_ai_generated = true`.

Front‑end:

- `components/AiRecipeGenerator.vue` (embedded in `Dashboard.vue`).

---

### 4.7 User Profile

**Controller:** `Api\UserProfileController`  
Routes under `/api/user/*` (auth):

- `GET /api/user/profile` – profile data + stats + avatar URL.
- `PUT /api/user/profile` – update `name`, `email`, `phone`, `bio`, `location`.
- `POST /api/user/profile/avatar` – upload new avatar image.
- `DELETE /api/user/profile/avatar` – delete current avatar.
- `POST /api/user/change-password` – change password (`current_password`, `new_password`, `new_password_confirmation`).

Front‑end:

- `Pages/UserProfile.vue` + `components/modals/ChangePasswordModal.vue`.

---

### 4.8 Stores & Products

**Controller:** `Api\StoreController` (auth)

- `GET /api/stores` – list nearby active stores (optional `latitude`/`longitude` filter by distance).
- `POST /api/stores` – create a store for the current user (1 per user).
- `GET /api/stores/my-store` – show current user’s store with products.
- `GET /api/stores/{store}` – show a specific active store.
- `PUT /api/stores/{store}` – update own store (details, location, active flag).
- `GET /api/stores/{store}/orders` – all orders for that store (for owner).

**Controller:** `Api\StoreProductController` (auth)

- `GET /api/store-products` – list available products; optional `store_id` filter.
- `POST /api/store-products` – add a recipe as a product (must own the recipe and a store).
- `GET /api/store-products/{product}` – view available product details.
- `PUT /api/store-products/{product}` – update own product (price, quantity, availability).
- `DELETE /api/store-products/{product}` – remove own product from store.
- `GET /api/store-products/my-products` – list all products for current user’s store.

Front‑end:

- `Pages/stores/MyStore.vue`, `BrowseStores.vue`, `components/StoreCard.vue`.

---

### 4.9 Orders

**Controller:** `Api\OrderController` (auth)

Customer‑side:

- `GET /api/orders` – list current user’s orders.
- `POST /api/orders` – place an order for a `store_product`:
  - Validates quantity, ensures product availability and max distance (10km).
- `GET /api/orders/{order}` – view a specific order (owned by customer).
- `POST /api/orders/{order}/cancel` – cancel pending order (restores quantity).

Store‑side:

- `PUT /api/orders/{order}` – update status (`pending`, `confirmed`, `preparing`, `ready`, `delivered`, `cancelled`) and optional `delivery_time` (only for store owner).

Front‑end:

- `Pages/orders/OrderTracking.vue`.

---

## 5. 🔌 Authentication & Frontend Flow

- Frontend uses Axios with a base URL of `window.location.origin` (web) and `http://10.0.2.2:8001` (Android emulator).
- Auth token (`auth_token`) is stored in `localStorage` after `/api/verify-otp`.
- `resources/js/router/index.js` has a global guard:
  - Routes with `meta.requiresAuth = true` require a token; otherwise redirect to `/login`.
  - OTP flow:
    - `Login.vue` / `Register.vue` send credentials → on `requires_otp`, store `{email,type}` as `pending_otp` and route to `/verify-otp`.
    - `VerifyOtp.vue` posts to `/api/verify-otp` and persists the issued token on success.

---

## 6. 🔮 Future Improvements

- Add full OpenAPI/Swagger spec for all endpoints.
- Add pagination, sorting, and filtering docs per list endpoint.
- Extend AI docs to cover prompt engineering and error states.
- Add role-based permissions (admin vs user vs seller).
- Document mobile integration patterns (Capacitor).

---

**Last Updated:** Feb 25, 2026  
**Status:** ✅ In sync with current codebase
