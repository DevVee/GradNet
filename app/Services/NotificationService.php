<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\PushSubscription;
use App\Models\User;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class NotificationService
{
    /**
     * Create an in-app notification (and optionally send a push).
     *
     * @param int         $userId    Recipient user ID
     * @param int         $actorId   User who triggered the action
     * @param string      $type      reaction|comment|connection|news|event
     * @param string      $message   Human-readable message
     * @param int|null    $postId    Related post (optional)
     * @param bool        $sendPush  Whether to also send a Web Push
     */
    public static function dispatch(
        int $userId,
        int $actorId,
        string $type,
        string $message,
        ?int $postId = null,
        bool $sendPush = true
    ): void {
        // Do not notify yourself
        if ($userId === $actorId) return;

        $notif = AppNotification::create([
            'user_id'  => $userId,
            'actor_id' => $actorId,
            'type'     => $type,
            'message'  => $message,
            'post_id'  => $postId,
            'is_read'  => false,
        ]);

        if ($sendPush) {
            static::sendPush($userId, $message, $type);
        }
    }

    /**
     * Send a Web Push notification to all of a user's active subscriptions.
     */
    public static function sendPush(int $userId, string $body, string $type = 'alert'): void
    {
        $vapidPublic  = config('app.vapid_public_key');
        $vapidPrivate = config('app.vapid_private_key');
        $subject      = config('app.vapid_subject', 'mailto:noreply@iccbi.edu.ph');

        if (empty($vapidPublic) || empty($vapidPrivate)) return;

        $subscriptions = PushSubscription::where('user_id', $userId)->get();
        if ($subscriptions->isEmpty()) return;

        try {
            $webPush = new WebPush([
                'VAPID' => [
                    'subject'    => $subject,
                    'publicKey'  => $vapidPublic,
                    'privateKey' => $vapidPrivate,
                ],
            ]);

            $payload = json_encode([
                'title' => 'ICCBI Alumni',
                'body'  => $body,
                'icon'  => '/images/ICCLOGO.png',
                'type'  => $type,
            ]);

            foreach ($subscriptions as $sub) {
                $subscription = Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'keys'     => [
                        'p256dh' => $sub->public_key,
                        'auth'   => $sub->auth_token,
                    ],
                ]);

                $webPush->queueNotification($subscription, $payload);
            }

            // Flush and remove expired subscriptions
            foreach ($webPush->flush() as $report) {
                if ($report->isSubscriptionExpired()) {
                    PushSubscription::where('endpoint', $report->getRequest()->getUri()->__toString())->delete();
                }
            }
        } catch (\Throwable $e) {
            // Log but do not break the request
            logger()->error('Push send failed: ' . $e->getMessage());
        }
    }
}
