.PHONY: dev
dev:
	docker compose build && \
	docker compose up -d && \
	docker compose exec php composer install

.PHONY: test
test:
	./vendor/bin/pest

.PHONY: coverage
coverage:
	./vendor/bin/pest --coverage