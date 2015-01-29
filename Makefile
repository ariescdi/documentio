PATH := $(PWD)/bin:$(PATH)

all:
	@echo 'Available tasks:'
	@echo
	@echo '`cs-diff`  Check the code style and print a diff of the code'
	@echo '           to be changed.'
	@echo '`cs-fix`   Fix the code (same changes you see with `cs-diff`).'

cs-diff:
	php-cs-fixer fix src --dry-run --diff

cs-fix:
	php-cs-fixer fix src || :
