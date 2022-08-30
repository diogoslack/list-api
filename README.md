# list-api
An API to import a xls file, process it and set the data available.

## Set up
* Install dependencies:
```sh
composer install
```
* _Optional_: Run the docker images for databases (production and tests). Is possible also to set the environment variables to access a external database.
```sh
docker-compose up -d
```
* Create the directory which will receive the xls file(s):
```sh
mkdir uploads
```
* Change the directory owner for your webserver user/group, i.e.: _www-data_
```sh
sudo chown www-data:www-data public/uploads/
```
```
Change the .env file with the database connection (DATABASE_URL)
```
* For the first time, create the schema:
```sh
php bin/console doctrine:schema:create
```

## Unit and Functional Tests
For the first time, is necessary to create the schema and load the fixtures:
```sh
php bin/console --env=test doctrine:schema:create
```
```sh
php bin/console --env=test doctrine:fixtures:load
```
Then is possible to run all tests from the project directory with:
```sh
php bin/phpunit
```
It's already available a test database inside the ```docker-compose.yml``` file. Is possible also to use a different database, changing the ```.env.test``` file on the DATABASE_URL param.
