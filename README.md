# Task Management System Setup Guide

## Prerequisites
- PHP >= 8.1
- Composer
- Node.js >= 16
- MySQL or SQLite

## Backend Setup (Laravel API)

1. Clone the repository and install dependencies:
```bash
cd laravel_api
composer install
cp .env.example .env
php artisan key:generate
```

2. Configure your database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

3. Run migrations and seed the database:
```bash
php artisan migrate --seed
```

4. Generate API documentation:
```bash
php artisan l5-swagger:generate
```

## Frontend Setup (Vue.js)

1. Install dependencies:
```bash
cd ../frontend
npm install
```

2. Configure API endpoint in `.env`:
```env
VITE_API_URL=http://localhost:8000/api
```

3. Start development server:
```bash
npm run dev
```

## API Documentation

The API documentation is available in three formats:

### 1. Swagger UI (Interactive)
- Start the Laravel server: `php artisan serve`
- Visit: `http://localhost:8000/api/documentation`

### 2. Postman Collection
- Import the collection file: `Task_Management_API.postman_collection.json`
- Located in: `/docs/postman/`

### 3. Markdown Documentation
- View the complete API documentation in: `/docs/API.md`

## Default Admin Account
```
Email: admin@example.com
Password: password123
```

## Testing

### Backend Tests
```bash
cd laravel_api
php artisan test
```

### Frontend Tests
```bash
cd frontend
npm run test
```

## Common Issues

### CORS Issues:
- Check `config/cors.php` settings
- Verify allowed origins in `.env`

### Authentication Errors:
- Clear cache: `php artisan cache:clear`
- Reset tokens: `php artisan sanctum:prune-expired`

### Swagger Generation Fails:
- Clear cache: `php artisan config:clear`
- Regenerate: `php artisan l5-swagger:generate`

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org/)
- [PrimeVue Components](https://primevue.org/)
- [Swagger/OpenAPI Specification](https://swagger.io/specification/)

For detailed API endpoints and examples, visit the Swagger UI documentation or refer to the markdown documentation in `/docs/API.md`.

## API Reference

### Authentication Endpoints

#### Login
```http
POST /api/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

#### Register
```http
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "user@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

### Task Endpoints

#### Get All Tasks
```http
GET /api/tasks
Authorization: Bearer {token}
```

#### Create Task
```http
POST /api/tasks
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Task Title",
    "description": "Task Description",
    "priority": "high",
    "status": "pending"
}
```

### Admin Endpoints

#### Get User Statistics
```http
GET /api/admin/user-statistics
Authorization: Bearer {token}
```

#### Get User Tasks
```http
GET /api/admin/users/{user_id}/tasks
Authorization: Bearer {token}
```

## Error Responses

```json
{
    "message": "Error message",
    "errors": {
        "field": [
            "Validation error message"
        ]
    }
}
```
