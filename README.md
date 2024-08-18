## Getting Started

**Ensure you have the following installed:**

- PHP (latest version)
- Composer
- Node.js (version 14.x or later)
- npm

### 1. Clone the Repository

Open your terminal and run the following command to clone the repository:

    git clone https://github.com/TamiELB/IpCalc.git

Then navigate into the project directory:

    cd IpCalc

### 2. Set Up the Laravel Backend

- **Install PHP Dependencies**

  In the project directory, run:

    `composer install`

- **Set Up the Environment File**

  Create your own environment file by copying the example file:

    `cp .env.example .env`

- **Generate the Application Key**

  Generate the application key by running:

    `php artisan key:generate`

- **Run Migrations**

  Set up the database schema by running:

    `php artisan migrate`

- **Install Node.js Dependencies**

  Install the required Node.js packages using npm:

    `npm install`

### 3. Run the Development Servers

- **Start the Laravel Development Server**

  Start the Laravel development server with:

    `php artisan serve`


- **Start the Vue.js Development Server**

  To start the Vue.js development server, run:

    `npm run dev`

  You can now visit [http://127.0.0.1:8000](http://127.0.0.1:8000) in your browser to see the application in action.
