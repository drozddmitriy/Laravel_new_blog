<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use Sluggable;

    const IS_DRAFT = 0;
    const IS_PUBLIC = 1;

    protected $fillable = ['title', 'content', 'date', 'description'];

    /**
     * Relations
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relations
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relations
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            'post_tags',
            'post_id',
            'tag_id'
        );
    }

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    /**
     * Add post
     * @param $fields
     * @return Post
     */
    public static function add($fields)
    {
        $post = new static;
        $post->fill($fields);
        $post->user_id = 1;
        $post->save();

        return $post;
    }

    /**
     * Edit post
     * @param $fields
     */
    public function edit($fields)
    {
        $this->fill($fields);
        $this->save();
    }

    /**
     * Remove post
     */
    public function remove()
    {
        ///delete image
        $this->removeImage();
        $this->delete();
    }


    public function removeImage()
    {
        if ($this->image != null) {
            Storage::delete('uploads/' . $this->image);
        }
    }

    /**
     * Upload image
     * @param $image
     */
    public function uploadImage($image)
    {

        if ($image == null) {
            return;
        }

        $this->removeImage();
        $filename = str_random(10) . '.' . $image->extension();
        $image->storeAs('uploads', $filename);
        $this->image = $filename;
        $this->save();
    }

    /**
     * Get Image
     * @return string
     */
    public function getImage()
    {
        if ($this->image == null) {
            return '/img/no-image.png';
        }
        return '/uploads/' . $this->image;
    }

    /**
     * Set category
     * @param $id
     */
    public function setCategory($id)
    {
        if ($id == null) {
            return;
        }
        $this->category_id = $id;
        $this->save();
    }

    /**
     * Set Tags
     * @param $ids
     */
    public function setTags($ids)
    {
        if ($ids == null) {
            return;
        }
        $this->tags()->sync($ids);
    }

    /**
     * Set status
     */
    public function setDraft()
    {
        $this->status = Post::IS_DRAFT;
        $this->save();
    }

    /**
     * Set status
     */
    public function setPublic()
    {
        $this->status = Post::IS_PUBLIC;
        $this->save();
    }

    public function taggleStatus($value)
    {
        if ($value == null) {
            return $this->setDraft();
        }
        return $this->setPublic();
    }

    /**
     * Set Featured
     */
    public function setFeatured()
    {
        $this->is_featured = 1;
        $this->save();
    }

    /**
     * Set Featured
     */
    public function setStandart()
    {
        $this->is_featured = 0;
        $this->save();
    }

    public function taggleFeatured($value)
    {
        if ($value == null) {
            return $this->setStandart();
        }
        return $this->setFeatured();
    }

    public function setDateAttribute($value)
    {
        $date = Carbon::createFromFormat('d/m/y', $value)->format('Y-m-d');
        $this->attributes['date'] = $date;
    }

    public function getCategoryTitle()
    {
        if ($this->category != null) {
            return $this->category->title;
        }
        return 'Нет категории';
    }

    public function getTagsTitles()
    {
        if (!$this->tags->isEmpty()) {
            return implode(', ', $this->tags->pluck('title')->all());
        }
        return 'Нет Тегов';
    }


    public function getDateAttribute($value)
    {
        $date = Carbon::createFromFormat('Y-m-d', $value)->format('d/m/y');
        return $date;
    }

    public function getCategoryID()
    {
        return $this->category != null ? $this->category->id : null;
    }

    public function getDate()
    {
        return Carbon::createFromFormat('d/m/y', $this->date)->format('F d, Y');
    }

    public function hasPrevious()
    {
        return self::where('id', '<', $this->id)->max('id');
    }

    public function getPrevious()
    {
        $postID = $this->hasPrevious(); //ID
        return self::find($postID);
    }

    public function hasNext()
    {
        return self::where('id', '>', $this->id)->min('id');
    }

    public function getNext()
    {
        $postID = $this->hasNext();
        return self::find($postID);
    }
    public function related()
    {
        ///Vse krome tekychego
        return self::all()->except($this->id);
    }

    public function hasCategory()
    {
        return $this->category != null ? true : false;
    }

    public static function getPopularPosts()
    {
        return self::orderBy('views','desc')->take(3)->get();
    }
}
