# How to run application

React App + Symfony PHP + MySQL Tournament table

## Getting started

## React
### 1. Dependecies
  - In the React project directory, open a terminal and install dependecies:
### `npm install`

### 2. Run the React Application:
  - After installing the dependencies, you can start the React application with:
### `npm start`
\
Open [http://localhost:3000](http://localhost:3000) to view it in your browser.

## MySQL
### 1. Create new database:
  ### `CREATE DATABASE your_db_name;`
### 2. Grant privileges to your db user:
  ### `GRANT ALL PRIVILEGES ON your_db_name.* TO 'your_mysql_user'@'localhost';`

## Symfony
### Dependecies
### 1. Install Dependencies:
 - Navigate to your Symfony project directory and run the command to install all necessary dependencies:
### `composer install`
### 1.1 Update DB credentials:
- Navigate to .env file in your Symfony project folder, and update DATABASE_URL:
  ### `DATABASE_URL="mysql://your_db_user:your_db_pass@127.0.0.1:3306/your_db_name"`
### 2. Start the Symfony Server:
  - Start the Symfony built-in server to ensure everything is working correctly:
### `symfony server:start`
### 3. Start the MySQL Server:
### `mysqld --console`
### 4. Check MySQL Connection:
### `mysql -u root -p`
### 5. Run Migrations and Doctrine Setup:
### `php bin/console make:migration`
### `php bin/console doctrine:migrations:migrate`
### `php bin/console doctrine:fixtures:load`

## How to test:
### Run from React APP:
Open [http://localhost:3000](http://localhost:3000) to view it in your browser.

### Run APIs from Symfony:
- Generate all matches at once:
http://127.0.0.1:8000/api/generate-match-data
- Generate all playoff matches at once:
http://127.0.0.1:8000/api/generate-playoff-data


- Generate Single match
 http://127.0.0.1:8000/api/create-single-match
- Generate Single plaoff match
  http://127.0.0.1:8000/api/generate-playoff-single-data

- Generate Result:
http://127.0.0.1:8000/api/get-match-data
