Nestis
======
[![Build Status][]](https://travis-ci.org/webdevvie/nestis)

A simple class to get a nested property or array key from an array or nested property.


Why
---
Having gone through several weeks of is_null($object) checks and not being able to reliably depend on the api's output 
I was using to always have the objects I decided this method needed to be made. Now I can do the following without 
the fear of running into a null or other random object that the api responds with:
 
```
public function getThatThingIWant($apiResponse)
{
    return $nestis->getNestedItem('someObject/someOtherProperty/itemIWant',$apiResponse);
}
``` 

instead of :

```
public function getThatThingIWant($apiResponse)
{
   $someObject = $apiResponse->getSomeObject();
   
   if(!is_null($someObject))
   {
        $someOtherProperty = $someObject->getSomeOtherProperty();
        if(!is_null($someOtherProperty))
        {
            return $someOtherProperty->getItemIWant();
        }
   }
   
   return null;
}
```

It works on arrays,objects,public properties, public methods,get{{propertyname}} and is{{propertyname}} and public static properties

Separator is /
For static properties use ::{yourvarname} (e.g. testItem/::someStaticVar)

Also works great with json objects. 


Using it in your project
------------------------
First add it to your project using composer

```
./composer require webdevvie/nestis
```


In your project use the class.

```
use Webdevvie\Nestis;
```

then try it out!
```

use Webdevvie\Nestis\Nestis;

$nested = (object)["test"=>(object)["layer1"=>['layer2'=>(object)['layer3'=>'downtherabbithole']]]];

print_r($nested);
$nestis = new Nestis();
$item = $nestis->getNestedItem('test/layer1/layer2',$nested,null);

print_r($item);
    
```

This will output:

```
stdClass Object
(
    [test] => stdClass Object
        (
            [layer1] => Array
                (
                    [layer2] => stdClass Object
                        (
                            [layer3] => downtherabbithole
                        )

                )

        )

)
stdClass Object
(
    [layer3] => downtherabbithole
)
```

Author
------
If you like this library. Find me on twitter [@webdevvie](http://twitter.com/webdevvie) or my personal site [johnbakker.name](http://johnbakker.name) and say hello
