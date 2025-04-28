# TroyWeb Book Management

This is a Laravel-based application for managing books. It provides features for creating, updating, and managing book records alias CRUD for Trow Web

## Features

- Add, edit, and delete books.
- Search and filter books.
- RESTful API for book management.

## Requirements

- PHP >= 8.0
- Composer
- Laravel >= 10.x
- MySQL or any supported database
- Vue 3 Composition API
- InertiaJS

## Installation

1. Clone the repository:
    ```bash
    git clone git@github.com:frederickgzmn/troyweb-bookmanagement.git
    cd troyweb-bookmanagement
    ```
    
2. Install dependencies:
    ```bash
    composer install
    ```

3. Copy the `.env.example` file to `.env` and configure your environment variables:
    ```bash
    cp .env.example .env
    ```

4. Generate the application key:
    ```bash
    php artisan key:generate
    ```

5. Generate the jwt key:
    ```bash
    php artisan jwt:secret
    ```

5. Run migrations and seed the database:
    ```bash
    php artisan migrate --seed
    ```

6. Run npm for the dependencies:
    ```bash
    npm install
    ```
7. Run start vue and inertia:
    ```bash
    npm run dev
    ```

8. Start the development server:
    ```bash
    php artisan serve
    ```

9. Enjoy!!..

## Usage

- Access the application at `http://localhost:8000`.
- Use the provided API endpoints for programmatic access.

## Contributing

Contributions are welcome! Please fork the repository and submit a pull request.

## License

This project is licensed under the [MIT License](LICENSE).