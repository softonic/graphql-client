.PHONY: tests
tests:
	docker-compose run composer run test

.PHONY: debug
debug:
	docker-compose run --entrypoint=bash composer

.PHONY: update-dependencies
update-dependencies:
	docker-compose run composer update

.PHONY: checkstyle
checkstyle:
	docker-compose run composer run checkstyle

.PHONY: fix-checkstyle
fix-checkstyle:
	docker-compose run composer run fix-checkstyle
