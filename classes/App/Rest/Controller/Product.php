<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 01.10.2014
 * Time: 14:41
 */


namespace App\Rest\Controller;


use App\Exception\NotFoundException;
use App\Model\Category;
use App\Rest\Controller;

class Product extends Controller
{
    public function action_get_collection()
    {
        $ids = $this->request->get($this->model->id_field);
        if ($ids) {
            $ids = array_unique(preg_split('/\s*,\s*/', $ids, -1, PREG_SPLIT_NO_EMPTY));
            if (count($ids) > 0) {
                $this->model->where($this->model->id_field, 'IN', $this->pixie->db->expr('(' . implode(',', $ids) . ')'));
            }
        }

        if ($catId = $this->request->get("categoryID")) {
            $categoryIds = [$catId];
            $category = $this->pixie->orm->get('Category', $catId);

            if (!$category->loaded()) {
                throw new NotFoundException("Category $catId Not Found");
            }

            // Find child categories
            $categoryChildren = $category->nested->children()->find_all();
            /** @var Category $child */
            foreach ($categoryChildren as $child) {
                $categoryIds[] = $child->id();
            }

            $this->model->where('categoryID', 'IN', $this->pixie->db->expr('(' . implode(',', $categoryIds) . ')'));
        }
        return parent::action_get_collection();
    }
} 