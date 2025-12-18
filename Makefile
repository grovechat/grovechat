export CGO_CFLAGS := $(shell php-config --includes)
export CGO_LDFLAGS := $(shell php-config --ldflags) $(shell php-config --libs)

.PHONY: run
run:
	go run -mod=mod ./cmd/grovechat
