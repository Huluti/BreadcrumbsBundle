rector:
	./vendor/bin/rector

lint:
	./vendor/bin/php-cs-fixer fix

lint_ci:
	./vendor/bin/php-cs-fixer fix --dry-run --diff

analyze:
	./vendor/bin/phpstan analyse

analyze_ci:
	./vendor/bin/phpstan analyse --error-format github
