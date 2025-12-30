export CGO_CFLAGS := $(shell php-config --includes)
export CGO_LDFLAGS := $(shell php-config --ldflags) $(shell php-config --libs)

.PHONY: run
run:
	go run -mod=mod ./cmd/grovechat

.PHONY: docker-push
docker-push:
	docker buildx bake -f docker-compose.yaml --set "*.platform=linux/amd64,linux/arm64" --push dev

.PHONY: docker-build
docker-build:
	docker buildx bake -f docker-compose.yaml --set "*.platform=linux/arm64" --load dev
