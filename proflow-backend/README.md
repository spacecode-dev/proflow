# Proflow Platform

This project contains the core laravel for Proflow.
 
## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

What things you need to install the software and how to install them

* PHP 7
* MySQL
* Node

### Installing

* Clone the repo
* Run ```composer install```
* Create a .env frile from env.example to your local MySQL credentials and database
* Run ```php artisan key:generate```
* Run ```php artisan migrate```
* Run
```
mkdir -p storage/framework/{sessions,views,cache}
```

* Run ```php artisan serve```

## Running the tests

No tests yet. 

## Deployment

Add additional notes about how to deploy this on a live system

## Built With

* Laravel

## Contributing


## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/your/project/tags). 

## License

This project is private.
