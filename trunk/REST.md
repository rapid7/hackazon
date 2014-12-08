REST Service
============

REST service (will be named "service" in the text) provides the possibility to operate on resources from any kind of application.
To use it one must authenticate first. Service supports two types of authentication: basic and token.

When user is authenticated, he is able to perform CRUD operations on resources by using the following methods:
GET, POST, PUT, PATCH, DELETE.

Also, the service supports other HTTP methods: OPTIONS, TRACE, HEAD.

The service understands the several input and output data formats.
Input: standard HTTP url-encoded, XML and JSON bodies.
Output: XML and JSON.


## Authorization

### Basic

To use basic authorization just use base64-encoded `username:password` string in `Authorization` header like this:

`Authorization: Basic dGVzdF91c2VyOjEyMzQ1Ng==`

End of story.

### Token

Token authorization is a little more complicated than basic. First, you have to make a usual
Basic-Authorization request, and in response you will receive the token.
Then just send this token in every request in `Authorization` header or as a request parameter `_token`.
The **former is better, because it will not be cached** (in GET requests).

Step 1:
`Authorization: Basic dGVzdF91c2VyOjEyMzQ1Ng==`
On every basic authorization request without `_token` parameter new token will be generated.
You can have only one token, so if you use it in several places, do not call basic authorization requests,
do it only once, and then use received token.

Step 2 (and consequent):
`Authorization: Token 1af538baa9045a84c0e889f672baf83ff24`
or
`http://hackazon.dev/api/?_token=1af538baa9045a84c0e889f672baf83ff24&other_params...`

## Content Type

### Request

Content type in request can be standard HTTP url-encoded, XML and JSON bodies.
**Important:** `Content-Type` header in request and actual content type of the request body must be equal.
 Otherwise parsing error occur.

### Response

The Service reads `Accept` header in request to determine output format. But this value can be
overridden by using url parameter `_format` (accepts `xml` and `json`).

`Accept` header supports `application/json`, `application\xml` and `application/x-www-form-urlencoded` formats.
There may be more complicated `Accept` strings, but they are canonicalised to these forms.

If no correct `Accept` or `_format` values provided, `json` is used.

## URL format

URLs have the following format:

```

    /api(/resource(/id(/property)))
```

Each consequent parameter is optional, the mandatory is only `/api` part, as an entry point to the Service

`property` value is used to access certain sub-collections (prepared beforehand):

```

    GET http://hackazon.dev/api/order/1/addresses HTTP/1.1
```

## Methods

Some of methods are forbidden by default in many http servers, o you must enable them in order
to fully use the service.

In Apache you should make the following changes:

```

    <Directory "....">
        # ....
        <LimitExcept GET POST HEAD PUT OPTIONS DELETE PATCH>
            Order deny,allow
            Deny from all
        </LimitExcept>
        # ....
    </Directory>
```

And in Nginx the next ones (if this constraint is used in your config):

```

    if ($request_method !~* ^(GET|HEAD|POST|DELETE|OPTIONS|PUT|PATCH)$ ){return 403;}
```

### TRACE

Research has shown that in PHP (and even in other languages) we don't have
a control over TRACE method. Apache uses its own logic and sends the request content
back. And Nginx just sends 405 Method Not Allowed status.

### OPTIONS

This method just allows to fetch allowed methods for a given resource in `Allow` header.

### GET

Incorrect response just raises an error message (with corresponding status):

Request:

```
    http://hackazon.dev/api/incorrect_path/10
```

Response:

```json
    {"message":"Not Found","code":404}
```

Fetches the collection or a resource(sub-resources):

Request:

```
    http://hackazon.dev/api/order?_format=xml
```

Response:

```xml

    <?xml version="1.0"?>
    <order_address>
        <item0>
            <id>1</id>
            <full_name>asc</full_name>
            <address_line_1>asdasd</address_line_1>
            <address_line_2>asdasdasd</address_line_2>
            <city>asdasd</city>
            <region>asdasdasd</region>
            <zip>215325235</zip>
            <country_id>RU</country_id>
            <phone>1016186</phone>
            <customer_id>1</customer_id>
            <address_type>shipping</address_type>
            <order_id>1</order_id>
        </item0>
        <item1>
            ....
            ....
        </item1>
        ....
        ....
    </order_address>
```

Collections are fetched in controller action `get_collection`.
Additional information is provided in response header `Link` (as on GitHub):

```

    Link: </api/category?page=1>; rel="current",</api/category?page=1>; rel="first",</api/category?page=4>; rel="last",</api/category?page=2>; rel="next"
```


Fetch single item:

Request:

```

    http://hackazon.dev/api/order/1
```

Response:

```json

    {
        "id": "1",
        "created_at": "2014-08-22 09:57:52",
        "updated_at": "2014-08-22 12:57:52",
        "customer_firstname": "test_user",
        "customer_lastname": null,
        "....": "......",
        "....": "......",
        "orderAddress": [
            {
                 "id": "1",
                 "full_name": "asc",
                 "address_line_1": "asdasd",
                 "address_line_2": "asdasdasd",
                 "city": "asdasd",
                 "region": "asdasdasd",
                 "....": "......"
            },
            {
                 "id": "2",
                 "full_name": "asc",
                 "address_line_1": "asdasd",
                 "....": "......"
            }
        ]
    }
```


Fetch (predefined) collection of order addresses.

```

    http://hackazon.dev/api/order/1/addresses
```

All operations with addresses are possible if you are its owner (It's checked in Order controller).


### HEAD request

Does the same as GET, but doesn't return the body of the response. So you can for example check whether
you have an access to the resource, but without additional payload.

### POST, PUT and PATCH methods

These methods are similar in the way they are processed. The difference is in their semantics.
**POST** request must contain all fields of object being created. If some fields are missing, or
there are excess fields, the exception will be thrown.
If everything is OK, new resource is created and returned in the body of the response.
**POST** method adds new object to the collection:

```

    POST http://hackazon.dev/api/order HTTP/1.1
    ... headers...
    Content-Type: application/xml
    Authorization: Token ...............

    <?xml version="1.0"?>
    <order>
        <created_at>2014-08-20 15:00:57</created_at>
        <updated_at>2014-08-20 18:00:57</updated_at>
        <customer_firstname>test_user</customer_firstname>
        <customer_lastname></customer_lastname>
        <customer_email>test_user@example.com</customer_email>
        <status>complete</status>
        <comment></comment>
        <customer_id>1</customer_id>
        <payment_method>wire transfer</payment_method>
        <shipping_method>mail</shipping_method>
    </order>
```

**PUT** method behaves similar to POST, but it updates existing object. It also must have ALL fields
of the object.

```

    PUT http://hackazon.dev/api/order/1 HTTP/1.1
```

**PATCH** method also updates a resource like PUT, but its body do not have to hold all fields.
It can send just one (or another needed count) field.

```

    PATCH http://hackazon.dev/api/order/1 HTTP/1.1
```

All these methods return modified or created resource in the response.

### DELETE method

This method is very simple. It just deletes the resource and returns no content.

```

    DELETE http://hackazon.dev/api/order/1 HTTP/1.1
```

## Config

```php

    <?php
    return array(
       'excluded_models' => [     // Exclude models, that we do not want to expose
           'BaseModel',
           'Model',
           'OrderAddress'         // Or ones, which names we want to hide by using special REST Controller (e.g. OrderAddressesCollection)
       ],
       'auth' => [
           'type' => 'token',  // Type of authorization. default == 'basic'
       ]
    );
```




