# Piranha Frontend

We are using SlimPHP, a micro framework from php where we are creating the services. No thirdparty auth toolkit is used. Integrated eloquent as ORM and Phinx for migration.

## Getting Started
Install packages from composer.json

```
composer install
```

Change DB Setting in following files for Local:

```
piranha-api/phinx.yml
```

```
piranha-api/src/settings.php
```


Start the project

```
php composer.phar start
```

### For docker

First Change DB Setting in following files for Docker:


```
/docker-compose.yml
```

```
piranha-api/phinx.yml
```

```
piranha-api/src/settings.php
```


we have to give

```
docker-compose up --build
```

then stop the docker and run 


```
docker-compose up
```

### Prerequisites
  
PHP - 7.2 or later, Thread Safe, PThreads, Docker, Eloquent, Phinx
  
Documentation and Issue tickets found [here](https://git.dev.netstax.io/piranha/piranha-project)  
  
Assets and Libraries found [here](https://git.dev.netstax.io/piranha/piranha-assets)
