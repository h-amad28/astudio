# Astudio

Completed a test by Astudio.

## Prerequisite

- To run application through docker some installations are required
- Postman 
- Docker desktop (this will install Docker and Docker compose)
- After installation goto the project directory and execute
- update host file `/etc/hosts` add 127.0.0.1 htdocs_7.net
(the docker is php 7.4 version so I have named it as htdocs_7)
```bash
docker compose up
```
- this will setup nginX container
- this will setup PHP 7.4 container
- this will also setup MySql container `user = root & No password`
## Documentation
Documentation is available in `public/apidoc` folder.
it can be accessed using `http://htdocs_7.net/aStudioTest/public/apidoc/` or if we are running localhost `localhost/Folder_name/public/apidoc`
## Running application
1. Open Docker desktop 
- Select the PHP container
- Select EXEC Tab
- Run followings
- composer install `if the project is pulled from gitHub it will not contains the vendor, so we need to install the dependencies`
- php artisan migrate
- php artisan cache:clear
- php artisan passport:install `if database is new`
- This will setup database
2. Open postman
3. Import postman collection from folder
4. API's documentation is available under `Application_Folder/public/apidoc`
- We can verify the available API calls in the documentation
- Postman collection contains all the API calls with parameters

## DataBase
Application folder also contains the database `astudio`.
Either we can import the database using MySQL UI tool or can run the migration process