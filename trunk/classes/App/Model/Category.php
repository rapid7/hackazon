<?php

namespace App\Model;

class Category extends \PHPixie\ORM\Model {

    public $table = 'tbl_categories';
    public $id_field = 'categoryID';

    public function getRootCategories(){
        return $this->pixie->db->query('select')
                            ->table('tbl_categories')
                            ->where('parent', 0)
                            ->execute();    
    
    }

    public function getRootCategoriesSidebar(){
        $sidebar = array();
        $categories = $this->pixie->orm->get('Category')->where('parent',0)->order_by('name','asc')->find_all();
        if($categories){
            foreach ($categories as $category){
                $sidebar[] = array(
                    'categoryID' => $category->categoryID,
                    'name' => $category->name,
                    'parent' => null,
                    'child' => $this->getChild($category->categoryID)
                );
            }
        }
        return $sidebar;
    }

    protected  function getChild($parent){
        $child = array();
        $subcategories = $this->pixie->orm->get('Category')->where('parent',$parent)->order_by('name','asc')->find_all();
        if($subcategories){
            foreach ($subcategories as $subcategory){
                $child[] = array(
                    'categoryID' => $subcategory->categoryID,
                    'name' => $subcategory->name,
                    'parent' => $parent,
                    'child' => $this->getChild($subcategory->categoryID)
                );
            }
        }
        if(empty($child))
            return null;
        else
            return $child;
    }

    public  function getPageTitle($categoryID){
        $category =$this->loadCategory($categoryID);
        if($category)
            return $category->name;
    }

    public function checkCategoryChild($categoryID){
        $category =$this->loadCategory($categoryID);
        if($category && $category){
            $child = $this->pixie->orm->get('Category')->where('parent', $category->categoryID)->find();
            if($child->loaded())
                return true;
        }
        return false;
    }

    protected function loadCategory($categoryID){
        $category = $this->pixie->orm->get('Category')->where('categoryID', $categoryID)->find();
        if($category->loaded())
            return $category;
    }

    public function getSubCategories($parent){
        $subcategories = array();
        $sub= $this->pixie->orm->get('Category')->where('parent',$parent)->order_by('name','asc')->find_all();
        if($sub){
            foreach ($sub as $category){
                $subcategories[] = array(
                    'categoryID' => $category->categoryID,
                    'name' => $category->name,
                    'products_count' => $category->products_count,
                    'description' => $category->description,
                    'picture' => $category->picture
                );
            }
        }
        return $subcategories;
    }

}