# Task Management System

## API Documentation

The API documentation is available at `/api/documentation` when running the application locally.

### Generate Documentation
```bash
php artisan l5-swagger:generate
```

### View Documentation
1. Start your local server
2. Visit: http://localhost:8000/api/documentation

### Available Endpoints

#### Authentication
- POST `/api/login` - User login
- POST `/api/register` - Register new user
- POST `/api/logout` - Logout user
- GET  `/api/me` - Get user

#### Tasks
- GET `/api/tasks` - Get all tasks
- POST `/api/tasks` - Create new task
- PUT `/api/tasks/{id}` - Update task
- DELETE `/api/tasks/{id}` - Delete task

#### Admin
- GET `/api/admin/user-statistics` - Get user statistics
- GET `/api/admin/users/{id}/tasks` - Get user's tasks
