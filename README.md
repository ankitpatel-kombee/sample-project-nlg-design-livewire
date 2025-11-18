# Project Setup
Follow these steps to set up and run the project:

## 1. Install Dependencies

Update the project dependencies:
composer update

Install Node dependencies:
npm install

2. Compile Assets
npm run build

3. Install and Publish Telescope & Pulse

Install Laravel Telescope:
php artisan telescope:install

Publish the Laravel Pulse configuration:
php artisan vendor:publish --provider="Laravel\Pulse\PulseServiceProvider"

4. Storage and Database Setup
Create a symbolic link for storage:
php artisan storage:link

5. Run migrations:
php artisan migrate

6. Seed the database:
php artisan db:seed

7. Run the Pint command below to fix code style issues using the Pint binary located in your project's vendor/bin directory:
./vendor/bin/pint

8. Run the PHPStan command below, review the output, and if any errors appear, manually fix them in your code
vendor/bin/phpstan analyse

9. Environment Variables
Update the .env file with the following keys:
GOOGLE_RECAPTCHA_KEY=
GOOGLE_RECAPTCHA_SECRET=
TINIFY_API_KEY=

--------------------------------------------------------------------------------------------
# If you’re running the project using Docker Desktop, please follow the steps below:

1. If Docker Desktop is not installed on your Windows system, download it from the link below:
https://docs.docker.com/desktop/setup/install/windows-install

2. Open a terminal inside the project directory and run the following command:
docker-compose up -d --build

3. Open Docker Desktop and check the following tabs to verify everything is running properly:
  - Containers
  - Images
  - Builds

4. Visit the app in browser → http://localhost:8000
5. Visit phpMyAdmin         → http://localhost:8080
