<?php
namespace App\Repositories;

use App\Models\Contact;

class ContactRepository{

    protected $contact;

    public function __construct(Contact $contact){
        $this->contact = $contact;
    }

    public function store($data){
        return $this->contact::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'query_type' => $data['query_type'],
            'message' => $data['message']
        ]);
    }
}