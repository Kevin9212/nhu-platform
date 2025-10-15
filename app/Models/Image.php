<?php
// app/Models/Image.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Image extends Model
{
    protected $appends = ['url'];

    public function getUrlAttribute(): ?string
    {
        // 依序嘗試各種欄位名，你實際用哪個就會被吃到
        $p = (string) ($this->image_path ?? $this->path ?? $this->image_url ?? '');

        $p = trim($p);
        if ($p === '') return null;                // ★ 避免回 /storage/ 目錄

        $p = str_replace('\\', '/', $p);
        $p = ltrim($p, '/');
        if (Str::startsWith($p, 'public/'))  $p = Str::replaceFirst('public/', '', $p);
        if (Str::startsWith($p, 'storage/')) $p = Str::replaceFirst('storage/', '', $p);

        return asset('storage/'.$p);
    }
}
