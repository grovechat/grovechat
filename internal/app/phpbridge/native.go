package phpbridge

import (
	"context"
	"errors"
	"fmt"
	"time"

	"github.com/dunglas/frankenphp"
)

// CallNative 调用 PHP Native 方法
func CallNative(workers frankenphp.Workers, class string, method string, params ...any) (any, error) {
	ctx, cancel := context.WithTimeout(context.Background(), 30*time.Second)
	defer cancel()

	request := map[string]any{
		"class":  class,
		"method": method,
		"params": params,
	}

	resp, err := workers.SendMessage(ctx, request, nil)
	if err != nil {
		return nil, fmt.Errorf("worker communication failed: %w", err)
	}

	// 将 FrankenPHP 的响应统一转换为干净的 Go 类型 (map, slice, etc.)
	cleanResp := unwrapPHPValue(resp)

	// 校验响应结构并提取 error/data
	// 经过 unwrapPHPValue 处理后，这里一定是 map[string]any
	resMap, ok := cleanResp.(map[string]any)
	if !ok {
		return nil, fmt.Errorf("unexpected response format: %T", cleanResp)
	}

	// 处理 PHP 返回的逻辑错误
	if errMsg, ok := resMap["error"].(string); ok && errMsg != "" {
		return nil, errors.New(errMsg)
	}

	return resMap["data"], nil
}

// unwrapPHPValue 递归地将 FrankenPHP 的 AssociativeArray 转换为标准的 Go 类型
func unwrapPHPValue(v any) any {
	switch val := v.(type) {
	case frankenphp.AssociativeArray[any]:
		// 如果是 PHP 关联数组，转换为 map，并递归处理 Value
		result := make(map[string]any, len(val.Map))
		for k, v := range val.Map {
			result[k] = unwrapPHPValue(v)
		}
		return result

	case []any:
		// 如果是切片，递归处理每一个元素
		for i, item := range val {
			val[i] = unwrapPHPValue(item)
		}
		return val

	default:
		// 基础类型 (int, string, bool, nil) 直接返回
		return val
	}
}
