<?php
/**
 * @var \App\Model\Category $categories
 * @internal param \App\Pixie $pixie
 */
return;

$walker = function (\App\Model\Category $category) use ($pixie, &$walker) {
    $children = $pixie->orm->get('Category')->where('parent', $category->id())->find_all()->as_array();

    if (count($children)) {
        $subCats = [];
        /** @var \App\Model\Category $child */
        foreach ($children as $child) {
            $child->setIsLoaded(false);
            $child->nested->prepare_append($category);
            $child->setIsLoaded(true);
            $child->save();
            $subCats[] = $child;
            $walker($child);
        }

        foreach ($subCats as $child) {

        }
    }
};

$startProcessing = function () use ($walker, $pixie) {
    /** @var \App\Model\Category $rootCategory */
    $rootCategory = $pixie->orm->get('Category');
    $rootCategory->name = '0_ROOT';
    $rootCategory->enabled = 1;
    $rootCategory->values([
        'name' => '0_ROOT',
        'enabled' => 1
    ]);
    $rootCategory->nested->prepare_append();
    $rootCategory->save();

    $pixie->db->query('update')->table('tbl_categories')
        ->where('parent', '=', '0')
        ->data(['parent' => $rootCategory->id()])
        ->execute();

    $walker($rootCategory);

    $rootCategory->refresh();
    $rootCategory->parent = 0;
    $rootCategory->save();
};

$startProcessing();