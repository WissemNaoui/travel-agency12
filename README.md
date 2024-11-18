# Travel Agency Application

A modern travel agency web application built with Symfony 6.4, featuring flight bookings, hotel reservations, and travel package management.

## 🚀 Features

- User authentication and registration
- Admin dashboard for managing content
- Flight management and bookings
- Hotel listings and reservations
- Travel package offerings
- User profiles
- Multi-language support

## 🔧 Prerequisites

Before you begin, ensure you have the following installed:
- PHP 8.1 or higher
- Composer
- Node.js and npm
- Docker and Docker Compose
- Git

## 🛠️ Installation

1. Clone the repository
```bash
git clone [your-repository-url]
cd travel-agency
```

2. Install PHP dependencies
```bash
composer install
```

3. Install JavaScript dependencies
```bash
npm install
```

4. Copy the environment file and configure it
```bash
cp .env .env.local
```
Edit `.env.local` and configure your database connection and other parameters.

5. Start the Docker containers
```bash
docker compose up -d
```

6. Create the database and run migrations
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

7. Load fixtures (optional - for test data)
```bash
php bin/console doctrine:fixtures:load
```

8. Build assets
```bash
npm run build
```

## 🚀 Running the Application

1. Start the Symfony development server
```bash
symfony server:start
```

2. Build assets in development mode (with watch)
```bash
npm run watch
```

The application will be available at `http://localhost:8000`

## 🧪 Running Tests

Execute the test suite:
```bash
php bin/phpunit
```

For detailed testing information, refer to [TESTING.md](TESTING.md).

## 👥 Admin Access

To set up an admin user:

1. Create a new user through the registration form
2. Use the following command to promote the user to admin:
```bash
php bin/console app:make-user-admin user@example.com
```
Replace `user@example.com` with the email of the user you want to promote.

Note: Make sure to create and promote an admin user before loading fixtures to ensure proper data relationships.

## 📚 Documentation

Additional documentation:
- [Manual Testing Guide](MANUAL_TESTING.md)
- [Testing Documentation](TESTING.md)

## 🔄 Development Workflow

1. Create a new branch for your feature
```bash
git checkout -b feature/your-feature-name
```

2. Make your changes and commit them
```bash
git add .
git commit -m "Description of your changes"
```

3. Push your changes and create a pull request
```bash
git push origin feature/your-feature-name
```

## 🛠️ Built With

- [Symfony 6.4](https://symfony.com/) - The PHP framework used
- [Doctrine ORM](https://www.doctrine-project.org/) - Database management
- [EasyAdmin](https://github.com/EasyCorp/EasyAdminBundle) - Admin panel
- [Webpack Encore](https://symfony.com/doc/current/frontend.html) - Asset management
- [Stimulus](https://stimulus.hotwired.dev/) - JavaScript framework
- [PostgreSQL](https://www.postgresql.org/) - Database

## 📝 License

This project is licensed under the proprietary license - see the LICENSE file for details.

## 🤝 Contributing

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## ✨ Support

For support, please open an issue in the GitHub repository or contact the development team.
