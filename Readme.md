# Px_DrushOptionValidator

Validating inputs is boring! This is a quick wrapper to standardize validation procedures. 
It was made for drupal input, but you could use it anywhere.
If you have standard validation classes to contribute that would be awesome.

You can add your own Validation routines and pass them to the factory by using the fully qualified namespace as the name. 
Otherwise it will assume its part of this class.

The Rules and Validator have fluent interfaces so its not too painful to add multiple constraints.
Rules take a name and an optional default value. The name should match the array key of the data to be validated by this rule
```
$data = [ 'anum' => 25 ];
$r2 = new VRule('anum',23);
        $r2->addConstraint(
          new GreaterThan(10)
        )->addConstraint(
          new LessThan(29)
        );
```


The most powerful constraint type is the CallableConstraint. You can call any function and pass it data by using this type.
The constructor accepts a function name as a string or a lambda/closure

See the unit tests for examples of these
 


## Roadmap

Integrate with forthcoming px_cache module