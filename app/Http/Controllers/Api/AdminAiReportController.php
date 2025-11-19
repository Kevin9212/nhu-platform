<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class AdminAiReportController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        // 前端傳來的統計資料（summary、items_by_category、items_per_day、hotKeywords、hotItems）
        $payload = $request->all();

        // 1. 準備 prompt（系統 + 使用者）
        $systemPrompt = <<<'PROMPT'
            你是 NHU 二手交易平台的營運顧問，請閱讀後端提供的統計資料，產生一份「給系上老師看得懂」的中文分析報告。

            要求：
            1. 使用繁體中文。
            2. 口氣偏正式、條列清楚，適合作為簡報講稿。
            3. 至少包含這幾段：
            - 整體概況（例如總商品數、上架率、上架時間分布）
            - 各分類表現（哪些分類商品數較多，可能原因）
            - 熱門商品與關鍵字觀察（哪些類型或關鍵字特別熱門）
            - 未來優化建議（例如鼓勵哪些商品上架、是否可以辦促銷活動等）
            4. 不要列出完整 JSON，只要用自然語言摘要就好，可以適度引用數字。
            PROMPT;

        $userPrompt = "以下是 NHU 二手交易平台的統計 JSON，請依照系統提示產出分析報告（不要回 JSON，只要文字）：\n\n"
            . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        // 2. 讀取 .env 裡的 API Key 和 Model
        $apiKey = env('OPENAI_API_KEY');
        $model  = env('OPENAI_MODEL', 'gpt-4.1-mini'); // 預設使用 gpt-4.1-mini

        if (!$apiKey) {
            return response()->json([
                'message' => 'OPENAI_API_KEY 尚未設定',
            ], 500);
        }

        try {
            // 3. 呼叫 OpenAI Chat Completions API
            $response = Http::withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'    => $model,
                    'messages' => [
                        [
                            'role'    => 'system',
                            'content' => $systemPrompt,
                        ],
                        [
                            'role'    => 'user',
                            'content' => $userPrompt,
                        ],
                    ],
                    'temperature' => 0.7,
                ]);

            if (!$response->ok()) {
                return response()->json([
                    'message' => 'OpenAI API 呼叫失敗',
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                ], 500);
            }

            $data = $response->json();

            $text = $data['choices'][0]['message']['content'] ?? '';

            if (!$text) {
                return response()->json([
                    'message' => 'OpenAI 回傳格式異常，沒有 content',
                    'raw'     => $data,
                ], 500);
            }

            //  前端期待 { text: "..." }
            return response()->json([
                'text' => $text,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => '呼叫 OpenAI 發生例外',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
