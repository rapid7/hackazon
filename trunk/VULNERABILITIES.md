Vulnerabilities Config
======================

Here you can find description of how to set up vulnerability config.

The app allows to turn on/off following vulnerabilities:
 
* XSS
    - reflected (immediately outputted in the response after user request)
    - stored (in DB or other storage)
 
* CSRF
    - referer
    - token
    
* SQL Injections
    - usual (with error output)
    - blind (when there is no error output, or when the behavior 
    of the app slightly changes after injecting SQL)
    
* Referrer check on/off

* OS Command Injection

* Arbitrary File Upload

* Remote File Include

* XML External Entity
    

## XSS

XSS Injection is a malicious JS code, sent in the request in URL params, 
request body, headers and so on. 

### Reflected XSS

Reflected XSS injections occur while outputting passed by user unescaped 
malicious HTML and JS code. It plays a one-time role, because is shown 
only once for single link click or other user operation.

To make it possible, enable them for certain fields:

```php
return [
    'fields' => [
        'q' => ['xss']
    ],
    'vulnerabilities' => [
        'xss' => [
            'enabled' => true
        ]
    ]
];
```

In code you can allow vulnerability by using `$_` function:

```php
<p><?php $_($request->question, 'q'); ?></p>
```

You need to indicate `q` parameter from config, because property names in view
can differ from names in requests, and that's why we can't escape all vars 
before the rendering.

### Stored XSS

Stored XSS Injections behave the same as ordinary ones, but they are more 
dangerous, because they are stored in persistent storage (DB). By default
app will filter script tags in variables before storing in the DB.

To enable them you should use the next config:

```php
return [
    'fields' => [
        'q' => ['xss']
    ],
    'vulnerabilities' => [
        'xss' => [
            'enabled' => true,
            'stored' => true
        ]
    ]
];
```

If this vulnerability is enabled for a field, the field will **NOT** be filtered for tags
before storing in the database (for INSERT and UPDATE operations only for insert values, not in WHERE clause, or anything else). 
And after this it will be outputted without escaping like Reflected XSS. Other fields will be filtered always.


You can also filter for **Stored XSS** in controller using following code (in case DB field and request params have 
different names):
 
```php
$this->filterStoredXSS($value, 'field_name_in_config');
```


## CSRF

CSRF attack allows hacker to perform a visibly valid action, such as create a user, 
post a message, transfer money, etc. on behalf of the user. To perform this type
of attack, user must be authenticated. Usually, a session based token can prevent 
this kind of attacks.
To enable this vulnerability for certain context (controller, or form), use next config:

```php
return [
   'fields' => [
       'q' => ['csrf']
   ],
   'vulnerabilities' => [
       'csrf' => [
           'enabled' => true,
       ]
   ]
];
```

Csrf is configured for entire context, so it is not needed to be added to single fields.

### Referrer

For some controllers it's necessary to filter referrer. For others not.
For example, we want almost all pages via GET-request to be available from 
Google and other searchers. But for some POST request we'd like to be sure 
that client made this request from pages of our website. It generally won't help
against hackers, because of ease of tampering the Referrer header, but as
a first line of defence it's worth to be implemented.

Here's how you can enable it in config:

```php
return [
   'fields' => [
       'q' => ['csrf']
   ],
   'vulnerabilities' => [
       'referrer' => [
           'enabled' => false,
           'hosts' => [$_SERVER['HTTP_HOST']],
           'protocols' => ['http', 'https'],
           'methods' => ['POST'],
           'paths' => ['/']     // from which to  
       ]
   ]
];
```

If request has protocol and method as in the config, and in such circumstances 
Referrer is missing, or host or path is wrong, the exception is rising.  

## SQL Injections

SQL Injections are specially prepared values for legal fields that brake the original
query by splitting it, commenting out parts of it and so on. 

### Usual SQL Injections

Usual injections can be performed by many methods, but the base is the same.
Hacker splits original query by two parts using variable values like this:

```php
$name = $_POST['name'];  // $_POST['name'] == "a'='a' OR 1=1 #"
$query = "SELECT name, password FROM user WHERE name='" . $name . "' AND role = 'user'";
```

So, part `' AND role = 'user'` is commented out and query fetches all users.
Escaping values and using prepared statements successfully helps to prevent this kind of attacks.


### Blind SQL Injections

Blind injections behave are the same as usual ones, but the observable output is different.
Hacker doesn't see any error output. He can be sure whether the injection was successful 
only by observing the results of request. If the injection was successful hacker only sees
blank page or page with some changes depending on particular website.

Here is how you can enable blindness for SQL Injections:

```php
return [
   'fields' => [
       'q' => ['sql']
   ],
   'vulnerabilities' => [
       'sql' => [
            'blind' => true
        ]
    ]
];
```

## OS Command Injection

To hack the page on Windows you can use: 
```
http://hackazon.dev/page/show?page=terms.html%20%26%26%20dir%20c:\
```

On Linux the same should be as follows:
```
http://hackazon.dev/page/show?page=terms.html%20%2620ls%20%2F
```

## Arbitrary File Upload

Allows to enable or disable the possibility to upload either only allowed file types or arbitrary ones.
In this app it is implemented on the user photo upload page:
```
http://hackazon.dev/account/profile/edit
```

It is enabled in account controller config:
```php
return [
    'fields' => [
        //....
        'photo' => [
            'ArbitraryFileUpload',
            'db_field' => 'user.photo',
        ]
        //....
    ],
];
```

## Remote File Include
RFI Injection allows to use an app logic where the app includes some file based on user input.
In our app it's implemented in the Help Articles section:
```
http://hackazon.dev/account/help_articles?page=add_product_to_cart
```

Vulnerability can be used as such:
```
http://hackazon.dev/account/help_articles?page=/etc/passwd%00
```

It is enabled in account controller config:
```php
return [
    'actions' => [
        'help_articles' => [
            'fields' => [
                'page' => [
                    'RemoteFileInclude'
                ]
            ]
        ]
    ],
];
```

## XML External Entity
This vulnerability uses the capability of XML to link itself to external files.
XML parsers usually include content from these files when parse XML. If the app and http server 
are not protected against this vulnerability, important files can leak:
```xml
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE roottag [<!ENTITY goodies SYSTEM "file:///etc/fstab">]>
<roottag>&goodies;</roottag> 
```

In our app this vulnerability is implemented in REST service when the XML format is used.
You can enable/disable it in rest config:
```php
return array(
    //.....
    'vulnerabilities' => [
        //....
        'XMLExternalEntity' => true
    ]
);
```


Vulnerability Module Structure
==============================

The module consists of two parts: the config, and the mechanisms that use it.

Config contains a tree of contexts, beginning at the root context (default.php config file),
and then controller, action (in the `actions` section of the config), and, possibly, custom, contexts.

At the moment configs are merged in such way, that fields and vulnerabilities are recursively
merged using next algorithm:
* If array is associative its values are merged with parent values
* Else current values override parent ones.

TODO: Maybe it makes sense to add some marks in config to help algorithm to choose the merge strategy.
  For example, add '_inherit_' value in array to merge with parent, and '_override_' to override.
  
  
## Vulnerability Processing

For each vulnerability type it is supposed that you have enabled it in the corresponding config.

#### XSS Reflected

To filter XSS reflected you must output it using `$_()` method in views. If vulnerability is enabled, it will
`htmlspecialchars` all characters.

```php
<?php $_($obj->question, 'userQuestion'); ?>
```

You must add field name from config, because variables can have different names and also object fields, or array values can be used. 
And that's why it is not possible for this king of vulnerability to escape automatically.

#### XSS Stored

If stored XSS is enabled for field (field in DB table, mentioned in vulnerability config), the field is cleaned 
  of `<script>` tags, `on-`-attributes, and `href="javascript:..."` attributes just before storing in the DB. 
  Pretty simple filtering, but it saves us from malicious JS in DB.
   
#### SQL Injections

If injection is enabled for field, it just becomes vulnerable for it. The requirement is that field name 
 must match the DB field. By default (if error displaying is enabled), sql errors are shown to the client.
 
Blind SQL injection differs from usual one just in that it won't show error messages on error.
  
#### CSRF 

Each form should contain CSRF-token to prevent such attacks. And on request a controller must check its correctness.


To add named token use next code: 
```php
<?php echo $_token('faq'); ?>  
```

To check it (with thrown Exception and without one):

```php
// throws Exception, and removes current token, good for not-ajax calls
$this->checkCsrfToken('faq');
 
// Does not throw 
if ($this->isTokenValid($tokenId)) {
    
}

// To get new token (for example for ajax forms)
$tokenValue = $this->pixie->vulnService->getToken($tokenId);

// To refresh
$newValue = $this->pixie->vulnService->refreshToken($tokenId);
```

When CSRF injection is enabled for context, checking token always returns true
   and token is not rendered on the page. 
   
#### Referrer check

By default referrer check is performed on all POST requests and allowed host and paths are
`Referrer` host equal to current host and paths starting at `/`. If this requirement is not met, 
 Exception is thrown. 
If referrer check vulnerability is enabled for context, the check always passes successfully for all
 `Referrer` headers.
   

#### XMLExternalEntity vuln

If you enable this vulnerability in rest.php config, it will be possible to use it by such requests (with XML bodies - for example POST, PUT,...):
```xml
<!DOCTYPE roottag [<!ENTITY goodies SYSTEM "file:///d:/script.txt">]>
<roottag>&goodies;</roottag>
```

If Accept header is `application/json`, the result could be as follows:
```json
{"message":"Remove excess fields: goodies","code":400,"invalidFields":{"goodies":{"goodies":"The Content of \n the hidden file\n"},"customer_id":"1"}}
```

Without this vuln response body will be:
```json
{"message":"Remove excess fields: goodies","code":400}
```

If the vulnerability is off, PHP just switches off the ability to load external dependencies in libxml, so the code 
is fully protected from this vulnerability. 

