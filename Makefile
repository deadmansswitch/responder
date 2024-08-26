.PHONY: test
test:
	./vendor/bin/pest

.PHONY: coverage
coverage:
	./vendor/bin/pest --coverage