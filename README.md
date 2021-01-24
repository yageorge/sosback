1. Install laravel: Please check the official laravel installation guide for server requirements before you start.
(https://laravel.com/docs/8.x/installation)

2. Clone the repository: git clone [your URL]

3. Go to the project directory: composer install

4. Copy the example env file and make the required configuration changes in the .env file:
    Run the command in the terminal: cp .env.example .env
    Create a new database <database-name> and edit the below in the .env accordingly:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=<database-name> 
DB_USERNAME=<mysql-username>
DB_PASSWORD= <mysql-password>

5. Run command in terminal: php artisan migrate (to deploy the database)

6. php artisan passport:install

7. php artisan passport:client --personal 
Name the personal access token: “Personal Access Token” 
For more details on Laravel passport, check the official documentation:
 (Laravel Passport - Laravel - The PHP Framework For Web Artisans)

8. php artisan storage:link 
To create a symbolic link from public/storage to storage/app/public

9. Create Firebase project:
Go to Authentication and enable authentication with email and password
 get the Firebase Credentials file (json file):
From Firebase navigate to Project settings -> Service accounts -> Generate a new private key -> Generate Key.
Open the downloaded file and copy and paste it to the firebase credentials file (json file) in the project
The file should look like:
{
  "type": "",
  "project_id": "",
  "private_key_id": "",
  "private_key": "",
  "client_id": "",
  "auth_uri": "",
  "token_uri": "",
  "auth_provider_x509_cert_url": "",
  "client_x509_cert_url": ""
}

10. Run command in terminal: php artisan serve --host 192.168.x.x
