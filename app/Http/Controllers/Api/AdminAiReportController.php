<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminAiReportController extends Controller
{
    public function generate(Request $request)
    {
        // 前端丟進來的統計資料（summary / items_by_category / items_per_day / hot_keywords / ranking）
        $payload = $request->all();

        // 安全檢查：沒有 key 就直接回錯誤
        $apiKey = env('OPENAI_API_KEY');
        if (!$apiKey) {
            return response()->json([
                'error' => '環境變數 OPENAI_API_KEY 未設定',
            ], 500);
        }

        // 把資料壓成一段 prompt
        $prompt = <<<PROMPT
你是一位熟悉大學生二手交易平台營運的數據分析師。

底下是後台統計資料（JSON），請產生一份「可以直接貼到簡報裡」的中文分析報告，格式請用條列式，包含：

1. 平台整體概況（總商品數、上架比例、最近幾天的上架趨勢）
2. 熱門商品分類分析（哪幾類佔比高、可能原因）
3. 熱門關鍵字與學生需求推測
4. 前 3 名熱門商品的行銷建議（如何在首頁或活動中曝光）
5. 接下來一個月可以執行的營運建議（例如主題活動、推薦位、推播文案方向）

請直接用繁體中文輸出報告，不要再出現 JSON，只要分析文字。

以下為 JSON 資料：

PROMPT;

        $prompt .= "\n\n" . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        // 呼叫 OpenAI Chat Completions API
        $response = Http::withToken($apiKey)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini', // 也可以改成 gpt-4.1-mini 等
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => '你是一位擅長做商業簡報的數據分析顧問，回答要精簡有重點，以條列式為主。'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => 0.7,
            ]);

        if ($response->failed()) {
            return response()->json([
                'error'  => 'OpenAI API 呼叫失敗',
                'status' => $response->status(),
                'body'   => $response->body(),
            ], 500);
        }

        $content = $response->json('choices.0.message.content');

        return response()->json([
            'text' => $content,
        ]);
    }
}
