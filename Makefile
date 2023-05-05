.PHONY: build
build:
	docker-compose build 

stop:
	docker-compose stop 
up:
	docker-compose up 
	
run: build up
