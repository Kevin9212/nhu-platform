<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    

    /**
     * G通知要透過哪些管道送
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * 建立要寄出的信件
     */
    public function toMail(object $notifiable): MailMessage
    {
        
        $verificationUrl = $this -> verificationUrl($notifiable);
         // NHU 學校信箱
        if (str_ends_with($notifiable->email, '@nhu.edu.tw')) {
            return (new MailMessage)
                ->subject('南華大學學生信箱驗證')
                ->greeting('您好，' . ($notifiable->nickname ?? '同學'))
                ->line('這是一封來自 **南華大學 NHU 二手交易平台** 的驗證信。')
                ->line('請點擊以下按鈕完成信箱驗證：')
                ->action('立即驗證 (NHU)', $verificationUrl)
                ->line('⚠️ 此連結將於 5 分鐘後失效。');
        }

        // CCU 學校信箱
        if (str_ends_with($notifiable->email, '@ccu.edu.tw')) {
            return (new MailMessage)
                ->subject('中正大學學生信箱驗證')
                ->greeting('哈囉，' . ($notifiable->nickname ?? '同學'))
                ->line('這是一封來自 **國立中正大學 CCU 二手交易平台** 的驗證信。')
                ->line('請點擊以下按鈕完成信箱驗證：')
                ->action('立即驗證 (CCU)', $verificationUrl)
                ->line('⚠️ 請在 5 分鐘內完成驗證，否則需要重新申請。');
        }

        // 預設樣式（理論上不會用到）
        return (new MailMessage)
            ->subject('請驗證您的學生信箱')
            ->greeting('您好，' . ($notifiable->nickname ?? '用戶'))
            ->line('感謝您註冊本平台！')
            ->line('請點擊以下按鈕驗證您的信箱：')
            ->action('驗證信箱', $verificationUrl)
            ->line('⚠️ 注意：此驗證連結將在 5 分鐘後失效。');
    }

    /**
     * 生成驗證鏈接
     */
    protected function verificationUrl($notifiable):string{
        return URL::temporarySignedRoute(
            'verification.verify',//對應 web.php 的 route name
            Carbon::now()->addMinutes(Config::get('auth.verification.expire',5)) // 預設5分鐘
            ,['id'=>$notifiable->getKey(),
            'hash'=> sha1($notifiable->getEmailForVerification()),]
        );
    }

    /**
     * 如果要存成 array 格式
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
