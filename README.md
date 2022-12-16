# 3slab Converter Tools


## ShortUUID

Based and converted from 'short-uuid' javascript library (https://www.npmjs.com/package/short-uuid).

You can see this url for original Javascript code : https://github.com/oculus42/short-uuid/blob/bdd83c4a6cae19387796ec1e8fdf36129b819b50/index.js
 
This PHP library can be used with the npm lib 'short-uuid' to transfert some UUID information between backend and frontend.

### usage

``` php

use SuezSmartSolution\ConverterTools\ShortUUID;

...

$uuid = '18a1bbcb-23ad-40b5-9a1d-1f80baa41890';
$uuidConvert = new ShortUUID();
$shortenUUID =  $uuidConvert->fromUUID($uuid)

...

$backUuid = $convert->toUUID($shortenUUID);

```