Getting Started

You need to have installed:

PHP latest version
Composer
Node.js (>= 14.x)
npm

1. Clone the Repository
git clone https://github.com/TamiELB/IpCalc.git

cd IpCalc

2. Set Up the Laravel Backend
Install PHP Dependencies

`composer install`

Set Up the Environment File

Copy the example environment file to create your own .env file:

`cp .env.example .env`

Generate the Application Key

`php artisan key:generate`

Configure Your Database

Edit the .env file to set up your database connection and other environment variables.

Run Migrations

`php artisan migrate`

Install Node.js Dependencies

`npm install`
or
`yarn install`

3. Run the Development Servers
Start the Laravel Development Server

`php artisan serve`

By default, it will run on http://localhost:8000.

Start the Vue.js Development Server

npm run dev

Visit [http://127.0.0.1:8000] in your browser to see the application in action.
