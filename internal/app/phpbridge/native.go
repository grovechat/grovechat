package phpbridge

import (
	"context"
	"fmt"
	"time"

	"github.com/dunglas/frankenphp"
)

// NativeResult Native 调用结果
type NativeResult struct {
	Success bool   `json:"success"`
	Data    any    `json:"data"`
	Error   string `json:"error"`
	Trace   string `json:"trace"`
}

// CallNative 调用 PHP Native 方法
func CallNative(workers frankenphp.Workers, class string, method string, params ...any) (*NativeResult, error) {
	ctx, cancel := context.WithTimeout(context.Background(), 30*time.Second)
	defer cancel()

	request := map[string]any{
		"class":  class,
		"method": method,
		"params": params,
	}

	resp, err := workers.SendMessage(ctx, request, nil)
	if err != nil {
		return nil, fmt.Errorf("发送消息到 Native Worker 失败: %w", err)
	}

	// 解析响应
	result := &NativeResult{}
	if arr, ok := resp.(frankenphp.AssociativeArray[any]); ok {
		if success, found := arr.Map["success"]; found {
			result.Success, _ = success.(bool)
		}
		if data, found := arr.Map["data"]; found {
			result.Data = data
		}
		if errMsg, found := arr.Map["error"]; found {
			if errMsg != nil {
				result.Error, _ = errMsg.(string)
			}
		}
		if trace, found := arr.Map["trace"]; found {
			if trace != nil {
				result.Trace, _ = trace.(string)
			}
		}
	} else {
		return nil, fmt.Errorf("Native Worker 返回类型解析失败: %T", resp)
	}

	if !result.Success && result.Error != "" {
		return result, fmt.Errorf("Native 调用失败: %s", result.Error)
	}

	return result, nil
}
