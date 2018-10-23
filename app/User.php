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
        'name', 'email',
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
        $user->save();

        return $user;
    }

    /**
     * Edit user
     * @param $fields
     */
    public function edit($fields){
        $this->fill($fields);
        $this->save();
    }

    /**
     * Generate password
     * @param $password
     */
    public function generatePassword($password){
        if($password != null) {
            $this->password = bcrypt($password);
            $this->save();
        }
    }


    /**
     * Delete user
     */
    public function remove(){
        $this->removeAvatar();
        $this->delete();
    }

    /**
     * Upload Avatarka
     * @param $image
     */
    public function uploadAvatar($image){

        if($image == null){return;}

        $this->removeAvatar();

        $filename = str_random(10). '.' . $image->extension();
        $image->storeAs('uploads', $filename);
        $this->avatar = $filename;
        $this->save();
    }

    public function removeAvatar(){
        if($this->avatar != null) {
            Storage::delete('uploads/' . $this->avatar);
        }
    }

    /**
     * Get Avatarka
     * @return string
     */
    public function getAvatar(){
        if($this->avatar == null) {
            return '/img/no-image.png';
        }
        return '/uploads/' . $this->avatar;
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
