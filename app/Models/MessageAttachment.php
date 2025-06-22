<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageAttachment extends Model {
    protected $table = 'message_attachments';
    public $timestamps = false;

    // 附件屬於一則訊息
    public function message() {
        return $this->belongsTo(Message::class);
    }
}
