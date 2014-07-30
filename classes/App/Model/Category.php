<?php

namespace App\Model;

/**
 * Class Category
 * @property int categoryID
 * @property string name
 * @property string description
 * @property int parent
 * @property int products_count
 * @property int products_count_admin
 * @property string picture
 * @property string about
 * @property string meta_title
 * @property string meta_keywords
 * @property string meta_desc
 * @property string hurl
 * @property string canonical
 * @property string h1
 * @property int enabled
 * @property int hidden
 * @property Category parentCategory
 * @property Category children
 * @package App\Model
 */
class Category extends \PHPixie\ORM\Model {

    public $table = 'tbl_categories';
    public $id_field = 'categoryID';

    protected $belongs_to = array(
        'parentCategory' => array(
            'model' => 'category',
            'key' => 'parent'
        )
    );

    protected $has_many = array(
        'children' => array(
            'model' => 'category',
            'key' => 'parent'
        )
    );

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

    public  function getChild($parent){
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
        return '';
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
        return null;
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

    /**
     * Builds array representation of category tree.
     *
     * @return array|null
     */
    public function getCategoryTreeArray()
    {
        $res = $this->pixie->orm->get('Category')->order_by('name','asc')->find_all()->as_array();
        return $this->buildCaregoryTree($res);
    }

    /**
     * Recoursively builds category tree from plain array.
     *
     * @param array $categories
     * @param int $parentId
     * @return array|null
     */
    protected function buildCaregoryTree(array &$categories = array(), $parentId = 0)
    {
        if (!count($categories)) {
            return null;
        }
        $resultCategories = array();

        /** @var Category $category */
        foreach ($categories as $key => $category) {
            if ($category->parent == $parentId) {
                $resultCategories[] = array(
                    'categoryID' => $category->categoryID,
                    'name' => $category->name,
                    'parent' => $category->parent
                );
                unset($categories[$key]);
            }
        }

        foreach ($resultCategories as $key => $category) {
            $resultCategories[$key]['child'] = $this->buildCaregoryTree($categories, $category['categoryID']);
        }

        return $resultCategories;
    }
}