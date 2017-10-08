# Test Developer PHP
This is a project was develop using the Zend Framework MVC layer, Guzzle HTTP and other components.


# Install
-------

```bash
$ git clone https://github.com/vasconce7os/indt_test.git
```

## Calling dependences using Composer

The easiest way to get this project is to use
[Composer](https://getcomposer.org/).  If you don't have it already installed,
then please install as per the [documentation](https://getcomposer.org/doc/00-intro.md).


```bash
#  cd  path/root/project
$ composer update
```

## Permission 
Give recursive permission for directory 

```bash
$ path/to/project/data/
```

## Development mode

The project ships with [zf-development-mode](https://github.com/zfcampus/zf-development-mode)
by default, and provides three aliases for consuming the script it ships with:

```bash
$ composer development-enable  # enable development mode   Recommended
$ composer development-disable # enable development mode
$ composer development-status  # whether or not development mode is enabled
```

You can also run composer from the image. The container environment is named
"zf", so you will pass that value to `docker-compose run`:
### NOT TESTED

```bash
$ docker-compose run zf composer install
```

## Web server setup

### Apache setup

To setup apache, setup a virtual host to point to the public/ directory of the
project and you should be ready to go! It should look something like below:

```apache
<VirtualHost *:80>
    ServerName indt.localhost
    DocumentRoot /path/to/indt/public
    <Directory /path/to/indt/public>
        DirectoryIndex index.php
        AllowOverride All
        Order allow,deny
        Allow from all
        <IfModule mod_authz_core.c>
        Require all granted
        </IfModule>
    </Directory>
</VirtualHost>
```

### Nginx setup
##  NO TESTED, but following the instructions of zf3 this should work

To setup nginx, open your `/path/to/nginx/nginx.conf` and add an
[include directive](http://nginx.org/en/docs/ngx_core_module.html#include) below
into `http` block if it does not already exist:

```nginx
http {
    # ...
    include sites-enabled/*.conf;
}
```

Create a virtual host configuration file for your project under `/path/to/nginx/sites-enabled/indt.localhost.conf`
it should look something like below:

```nginx
server {
    listen       80;
    server_name  indt.localhost;
    root         /path/to/project/public;

    location / {
        index index.php;
        try_files $uri $uri/ @php;
    }

    location @php {
        # Pass the PHP requests to FastCGI server (php-fpm) on 127.0.0.1:9000
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_param  SCRIPT_FILENAME /path/to/project/public/index.php;
        include fastcgi_params;
    }
}
```

Restart the nginx, now you should be ready to go!
