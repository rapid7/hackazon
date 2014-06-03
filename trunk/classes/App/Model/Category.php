<?php

namespace App\Model;

class Category extends Model {

    public function getRootCategories(){
        return $this->pixie->db->query('select')
                            ->table('tbl_categories')
                            ->where('parent', 0)
                            ->execute();    
    
    }

}