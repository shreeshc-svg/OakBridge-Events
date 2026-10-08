<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function edition()
    {
        return $this->belongsTo(Edition::class);
    }

    /** YouTube id from watch?v=, youtu.be/, /shorts/, /embed/ or /live/ links. */
    public function youtubeId(): ?string
    {
        $url = trim((string) $this->video);
        if (preg_match('~(?:youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/|v/)|youtu\.be/)([A-Za-z0-9_-]{11})~', $url, $m)) {
            return $m[1];
        }

        return preg_match('~^[A-Za-z0-9_-]{11}$~', $url) ? $url : null;
    }

    public function embedUrl(): ?string
    {
        return ($id = $this->youtubeId()) ? 'https://www.youtube.com/embed/' . $id : null;
    }

    public function thumbnailUrl(): ?string
    {
        return ($id = $this->youtubeId()) ? 'https://i.ytimg.com/vi/' . $id . '/mqdefault.jpg' : null;
    }
}
