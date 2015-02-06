<?php

namespace App\Model;

class ContactMessages extends BaseModel {
    public $table = 'tbl_contact_messages';
    public $id_field = 'id';

    public function create($post)
    {
        $this->name = $post->contact_name;
        $this->email = $post->contact_email;
        $this->phone = $post->contact_phone;
        $this->message = $post->contact_message;
        if (!is_null($this->pixie->auth->user())) {
            $this->customer_id = $this->pixie->auth->user()->id();
        }
        $this->save();
    }
}