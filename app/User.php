<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use Notifiable;

    const IS_BAN = 1;
    const IS_ACTIVE = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Relations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts(){
        return $this->hasMany(Post::class);
    }

    /**
     * Relations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(){
        return $this->hasMany(Comment::class);
    }

    /**
     * Add user
     * @param $fields
     * @return User
     */
    public static function add($fields){
        $user = new static;
        $user->fill($fields);
        $user->password = bcrypt($fields['password']);
        $user->save();

        return $user;
    }

    /**
     * Edit user
     * @param $fields
     */
    public function edit($fields){
        $this->fill($fields);
        $this->password = bcrypt($fields['password']);
        $this->save();
    }

    /**
     * Delete user
     */
    public function remove(){
        Storage::delete('uploads/'. $this->image);
        $this->delete();
    }

    /**
     * Upload Avatarka
     * @param $image
     */
    public function uploadAvatar($image){

        if($image == null){return;}

        Storage::delete('uploads/'. $this->image);
        $filename = str_random(10). '.' . $image->extension();
        $image->saveAs('uploads', $filename);
        $this->image = $filename;
        $this->save();
    }

    /**
     * Get Avatarka
     * @return string
     */
    public function getAvatar(){
        if($this->image == null) {
            return '/img/no-user-image.png';
        }
        return '/uploads/' . $this->image;
    }

    /**
     * Set admin
     */
    public function makeAdmin(){
        $this->is_admin = 1;
        $this->save();
    }

    /**
     * Set normal user
     */
    public function makeNormal(){
        $this->is_admin = 0;
        $this->save();
    }

    public function taggleAdmin($value){
        if ($value == null){
            return $this->makeNormal();
        }
        return $this->makeAdmin();
    }

    /**
     * Set BAN
     */
    public function ban(){
        $this->status = User::IS_BAN;
        $this->save();
    }

    /**
     * Set UNBAN
     */
    public function unban(){
        $this->status = User::IS_ACTIVE;
        $this->save();
    }

    public function taggleBan($value){
        if ($value == null){
            return $this->unban();
        }
        return $this->ban();
    }


}
