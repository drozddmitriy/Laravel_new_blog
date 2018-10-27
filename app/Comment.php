<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * Relations
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
   public function post(){
       return $this->belongsTo(Post::class);
   }

    /**
     * Relations
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
   public function author(){
       return $this->belongsTo(User::class, 'user_id');
   }

    /**
     * Set status
     */
    public function allow(){
        $this->status = 1;
        $this->save();
    }

    /**
     * Set status
     */
    public function disallow(){
        $this->status = 0;
        $this->save();
    }

    public function taggleStatus(){
        if ($this->status == 0){
            return $this->allow();
        }
        return $this->disallow();
    }

    /**
     * remove comment
     */
    public function remove(){
        $this->delete();
    }
}
