DocumentIO
==========

> [Symfony] website to host and browse documents.

[Symfony]: http://symfony.com/

Description
-----------

**TODO:** write a short summary of the project.

Requirements
------------

Ensure you have [Composer] installed, and your host complies with
[Symfony requirements].

[Composer]: https://getcomposer.org/
[Symfony requirements]: http://symfony.com/doc/current/reference/requirements.html

Installation
------------

```sh
git clone https://github.com/ariescdi/documentio.git
cd documentio
composer install

# When asked, configure parameters according to your setup. It's
# preconfigured to use a SQLite database in `app/data` which is ideal
# for development.

app/console doctrine:schema:update --force # Create/update the database.
app/console doctrine:fixtures:load # Load fixtures (default data).
app/console assets:install --symlink --relative # Symlink assets in `web` directory.
app/console assetic:dump # Compile assets.

# Run a PHP built-in server.
app/console server:run
```

Deployment
----------

```
composer install --no-dev --optimize-autoloader
app/console cache:clear --env=prod --no-debug
app/console assetic:dump --env=prod --no-debug
```

Contributing
------------

See the [contributing guidelines](CONTRIBUTING.md).
