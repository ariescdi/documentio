PATH := $(PWD)/bin:$(PATH)

all:
	@echo 'Available tasks:'
	@echo
	@echo '`cs-diff`  Check the code style and print a diff of the code'
	@echo '           to be changed.'
	@echo '`cs-fix`   Fix the code (same changes you see with `cs-diff`).'
	@echo '`refresh`  Refresh the dependencies, cache, assets.'
	@echo '`run`      Run the built-in server.

cs-diff:
	php-cs-fixer fix src --dry-run --diff

cs-fix:
	-php-cs-fixer fix src

refresh:
	app/console doctrine:schema:update --force
	app/console doctrine:fixtures:load
	app/console assets:install --symlink --relative
	app/console assetic:dump

run:
	app/console server:run
