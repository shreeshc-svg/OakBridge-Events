<?php

namespace App\Models;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'image',
        'video',
        'excerpt',
        'body',
        'user_id',
        'meta_title',
        'meta_description',
        'meta_keyword',
        'featured',
        'published',
        'disable_comment',
        'views',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
