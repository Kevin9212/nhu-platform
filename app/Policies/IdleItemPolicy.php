<?php

namespace App\Policies;

use App\Models\IdleItem;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class IdleItemPolicy
{
    /**
     * 判斷使用者是否可以更新該商品。
     */
    public function update(User $user, IdleItem $idleItem): bool
    {
        // dd(
        //     '目前登入的使用者 (User) 物件:',
        //     $user->toArray(),
        //     '要編輯的商品 (IdleItem) 物件:',
        //     $idleItem->toArray()
        // );
        // dd($user->id, $idleItem->user_id);

        // 只有當商品的 user_id 等於當前登入者的 id 時，才允許更新
        return $user->id == $idleItem->user_id;
    }

    /**
     * 判斷使用者是否可以刪除該商品。
     */
    public function delete(User $user, IdleItem $idleItem): bool
    {
        // 只有當商品的 user_id 等於當前登入者的 id 時，才允許刪除
        return $user->id == $idleItem->user_id;
    }
}
