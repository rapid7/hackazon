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

AMF
===

Hackazon is self-contained regarding AMF. That means you just have install hackazon and just use AMF service included in it.
But to develop or maintain AMF functionality you have to complete several steps.

1. Clone https://github.com/silexlabs/amfphp-2.0 repository.

2. Create VirtualHost (e.g. backoffice.dev) pointing to the amfphp-2.0/BackOffice as a web root.

3. Modify class Amfphp_BackOffice_Config. Set `$amfphpEntryPointPath` to your local VHost for hackazon (with path) and credentials: 
```php
//...
public $amfphpEntryPointPath = 'http://hackazon.dev/amf';
//...

public function __construct() {
    // ...
    $this->backOfficeCredentials['admin'] = 'admin';
    // ...
}
```

4. Run backoffice.dev (or whatever you named it) in browser.

5. In Service Browser you can test your backend methods using many connection types (JSON, Flash, etc.)

6. When you've finished developing and testing your features, go to Client Generator and generate test client in needed format.

7. For JS service format modify web/js/amf/services.js file with generated data (do NOT replace completely).

8. For flash or any other format modifications are up to you.

