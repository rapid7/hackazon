Nested Set
==========

Using PHPPixies Nested Set requires being careful.
 
1. You can only add new (not "loaded()") categories into set.
    For this reason I've added `setIsLoaded` method to the `BaseModel` class to be able to temporarily mark model as new.
    `Category` in turn is also derived from `BaseModel`

2. If you operate on many categories, always refresh them before processing, because `lpos`, `rpos` and `depth` fields can be obsolete. 
    PHPixie doesn't provides this capability, so the `refresh()` method was added. 