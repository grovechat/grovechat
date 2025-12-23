package native

import (
	"encoding/json"
	"grovechat/internal/app/config"
	"grovechat/internal/app/phpbridge"
	"log"
	"net/http"
)

// ExampleHandler 示例 Native 调用处理器
// 访问: http://localhost:80/native/example
func ExampleHandler(cfg *config.Config) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		// 示例：调用 PHP 的 App\Services\ExampleService 类的 hello 方法
		result, err := phpbridge.CallNative(
			cfg.NativeWorkers,
			"App\\Services\\ExampleService",
			"hello",
			"World", // 参数
		)

		if err != nil {
			log.Printf("Native 调用失败: %v", err)
			http.Error(w, err.Error(), http.StatusInternalServerError)
			return
		}

		// 返回 JSON 响应
		w.Header().Set("Content-Type", "application/json")
		json.NewEncoder(w).Encode(result)
	}
}

// CustomHandler 自定义 Native 调用处理器（通过 URL 参数指定类和方法）
// 访问: http://localhost:80/native/call?class=App\\Services\\ExampleService&method=hello&params=["World"]
func CustomHandler(cfg *config.Config) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		class := r.URL.Query().Get("class")
		method := r.URL.Query().Get("method")

		if class == "" || method == "" {
			http.Error(w, "Missing class or method parameter", http.StatusBadRequest)
			return
		}

		// 解析 params（JSON 数组）
		var params []any
		if paramsStr := r.URL.Query().Get("params"); paramsStr != "" {
			if err := json.Unmarshal([]byte(paramsStr), &params); err != nil {
				http.Error(w, "Invalid params format: "+err.Error(), http.StatusBadRequest)
				return
			}
		}

		// 调用 Native
		result, err := phpbridge.CallNative(cfg.NativeWorkers, class, method, params...)
		if err != nil {
			log.Printf("Native 调用失败: %v", err)
			http.Error(w, err.Error(), http.StatusInternalServerError)
			return
		}

		// 返回 JSON 响应
		w.Header().Set("Content-Type", "application/json")
		json.NewEncoder(w).Encode(result)
	}
}
