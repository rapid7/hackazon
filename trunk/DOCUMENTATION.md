Nested Set
==========

Using PHPPixies Nested Set requires being careful.
 
1. You can only add new (not "loaded()") categories into set.
    For this reason I've added `setIsLoaded` method to the `BaseModel` class to be able to temporarily mark model as new.
    `Category` in turn is also derived from `BaseModel`
    
```php
// $category - existing category in DB
$category->setIsLoaded(false);
$category->nested->prepare_append($parent);
$category->setIsLoaded(true);
$category->save();
```

2. If you operate on many categories, always refresh them before processing, because `lpos`, `rpos` and `depth` fields can be obsolete. 
    PHPixie doesn't provides this capability, so the `refresh()` method was added. 
    
```php
// ... Do something with other categories ....

$rootCategory->refresh(); // !!! Important !!!
$rootCategory->parent = 0;
$rootCategory->save();
```