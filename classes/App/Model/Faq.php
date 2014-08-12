<?php

namespace App\Model;

class Faq extends BaseModel {

    public function getEntries() {
        return $this->pixie->db->query('select')
            ->table('tbl_faq')
            ->execute();
    }
    
    public function addEntry($params){
        $this->pixie->db->query('insert')->table('tbl_faq')
            ->data(array('email' => $params['userEmail'], 'question' => $params['userQuestion']))
            ->execute();
        return $this->pixie->db->insert_id();  
    }
}