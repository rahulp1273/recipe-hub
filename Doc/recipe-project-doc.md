
# 📘 Laravel Project Documentation: Recipe Sharing

## 1. 🧾 Project Overview

This Laravel-based REST API project allows users to:

- Register and log in
- Create, update, delete, and view **recipes**
- Like/unlike recipes
- Track how many times each recipe has been viewed

The system is token-authenticated and built with scalability in mind.

---

## 2. 🛢️ Database Design

### ER Diagram (text version)

+--------+       +----------+       +---------------+
| users  | 1   M | recipes  | 1   M | recipe_likes  |
+--------+       +----------+       +---------------+
     |                             |
     |                             |
     |                             +---------------+
     |                                             |
     |                         1   M               |
     |                     +-----------------+     |
     |                     | recipe_views    | <---+
     |                     +-----------------+

### Tables and Fields

#### `users`

| Column     | Type      | Description             |
|------------|-----------|-------------------------|
| id         | BIGINT    | Primary key             |
| name       | STRING    | User name               |
| email      | STRING    | Unique user email       |
| password   | STRING    | Hashed password         |
| avatar     | STRING    | Avatar file path (nullable) |
| timestamps | DATETIME  | created_at / updated_at |

#### `recipes`

| Column     | Type      | Description              |
|------------|-----------|--------------------------|
| id         | BIGINT    | Primary key              |
| user_id    | BIGINT    | Foreign key to `users`   |
| title      | STRING    | Title of the recipe      |
| description| TEXT      | Description of the recipe|
| timestamps | DATETIME  | created_at / updated_at  |

#### `recipe_likes`

| Column     | Type      | Description               |
|------------|-----------|---------------------------|
| id         | BIGINT    | Primary key               |
| user_id    | BIGINT    | User who liked the recipe |
| recipe_id  | BIGINT    | Liked recipe              |
| timestamps | DATETIME  | created_at / updated_at   |

#### `recipe_views`

| Column     | Type      | Description               |
|------------|-----------|---------------------------|
| id         | BIGINT    | Primary key               |
| user_id    | BIGINT    | Viewer user ID            |
| recipe_id  | BIGINT    | Viewed recipe             |
| timestamps | DATETIME  | created_at / updated_at   |

---

## 3. 🔁 Model Relationships

### `User.php`

```php
public function recipes() {
    return $this->hasMany(Recipe::class);
}

public function likes() {
    return $this->hasMany(RecipeLike::class);
}

public function views() {
    return $this->hasMany(RecipeView::class);
}
```

### `Recipe.php`

```php
public function user() {
    return $this->belongsTo(User::class);
}

public function likes() {
    return $this->hasMany(RecipeLike::class);
}

public function views() {
    return $this->hasMany(RecipeView::class);
}
```

### `RecipeLike.php`

```php
public function user() {
    return $this->belongsTo(User::class);
}

public function recipe() {
    return $this->belongsTo(Recipe::class);
}
```

### `RecipeView.php`

```php
public function user() {
    return $this->belongsTo(User::class);
}

public function recipe() {
    return $this->belongsTo(Recipe::class);
}
```

---

## 4. 🧩 Controller Logic

### `AuthController`

- `register()`: Register new users
- `login()`: Issue JWT token for valid credentials
- `logout()`: Revoke current token
- `refresh()`: Refresh token
- `userProfile()`: Return authenticated user info

### `RecipeController`

- `index()`: Get all recipes
- `store()`: Add new recipe
- `show($id)`: Show single recipe
- `update($id)`: Update a recipe (user must be owner)
- `destroy($id)`: Delete a recipe (user must be owner)

### `SocialController`

- `likeRecipe($id)`: Toggle like/unlike on a recipe
- `viewRecipe($id)`: Record a view for a recipe
- `recipeLikes($id)`: Get all likes for a recipe
- `recipeViews($id)`: Get all views for a recipe

---

## 5. 🔌 API Endpoints

### Authentication

| Method | URI               | Description     |
|--------|-------------------|-----------------|
| POST   | `/api/register`   | Register a user |
| POST   | `/api/login`      | Login user      |
| POST   | `/api/logout`     | Logout user     |
| POST   | `/api/refresh`    | Refresh token   |
| GET    | `/api/user-profile` | Get user profile |

### Recipe

| Method | URI               | Description           |
|--------|-------------------|-----------------------|
| GET    | `/api/recipes`    | List all recipes      |
| POST   | `/api/recipes`    | Create new recipe     |
| GET    | `/api/recipes/{id}` | Show single recipe  |
| PUT    | `/api/recipes/{id}` | Update recipe       |
| DELETE | `/api/recipes/{id}` | Delete recipe       |

### Social

| Method | URI                                 | Description                    |
|--------|-------------------------------------|--------------------------------|
| POST   | `/api/recipes/{id}/like`            | Like/unlike recipe             |
| POST   | `/api/recipes/{id}/view`            | Register recipe view           |
| GET    | `/api/recipes/{id}/likes`           | Get all likes for a recipe     |
| GET    | `/api/recipes/{id}/views`           | Get all views for a recipe     |

---

## 6. 🔮 Future Improvements

- Add Comments model for recipe discussions
- Add search/filter functionality for recipes (by ingredients, likes, etc.)
- Add pagination and sorting in `index()` responses
- Allow media uploads (images, videos) with recipes
- Rate limiting and analytics (most liked/viewed recipes)
- Cache frequent queries using Redis or Laravel Cache
