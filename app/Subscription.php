<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /**
     * Add Subscription
     * @param $email
     * @return Subscription
     */
    public static function add($email){
        $sub = new static;
        $sub->email = $email;
        $sub->token = str_random(100);
        $sub->save();

        return $sub;
    }

    /**
     * Remove Subscription
     */
    public function remove(){
        $this->delete();
    }
}
