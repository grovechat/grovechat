package config

import (
	"github.com/dunglas/frankenphp"
)

type Config struct {
	PhpProjectRoot  string
	WebPort         int
	WatchPaths      []string
	LambdaWorkers   frankenphp.Workers
	ScheduleWorkers frankenphp.Workers
	QueueWorkers    frankenphp.Workers
}

func New() *Config {
	cfg := &Config{
		PhpProjectRoot: ".",
		WebPort:        80,
	}
	if frankenphp.EmbeddedAppPath != "" {
		cfg.PhpProjectRoot = frankenphp.EmbeddedAppPath
	}
	cfg.WatchPaths = []string{
		cfg.PhpProjectRoot + "/routes",
		cfg.PhpProjectRoot + "/app",
		cfg.PhpProjectRoot + "/config",
	}
	return cfg
}
