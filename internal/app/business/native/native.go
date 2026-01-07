package native

import (
	"encoding/json"
	"grovechat/internal/app/config"
	"grovechat/internal/app/phpbridge"
	"log"
	"net/http"
)

// ExampleHandler 示例 Native 调用处理器
func ExampleHandler(cfg *config.Config) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		result, err := phpbridge.CallNative(
			cfg.NativeWorkers,
			"App\\Domain\\SystemSettings\\Actions\\GetSettingAction",
			"execute",
			nil, // 参数
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
