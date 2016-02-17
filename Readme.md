# Fluent Validator

[ ![Codeship Status for twhiston/fluent-validator](https://codeship.com/projects/d4cbc4a0-b7ed-0133-4102-6ef29f71ac4a/status?branch=master)](https://codeship.com/projects/134961)

Validating inputs is boring! This is a quick wrapper to standardize validation procedures. 
It was made for drupal form input, but you could use it anywhere.
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

See the unit tests for examples of these.

For creating sets of rules easily use the TreeFactory class, see tests for how to use this, but something like the following

```
        $tf = new TreeFactory();
        $tf->startRule('submitted')
            ->startRule('number')->addConstraint( new CallableConstraint('is_numeric'))->endRule()
            ->startRule('street')->addConstraint( $iss )->addConstraint( new CallableConstraint('is_null' , NULL, ['false' => TRUE]))->endRule()
            ->startRule('postcode')
                ->addConstraint( new CallableConstraint(
                    function($data){
                        //At least 2 numbers && last 3 characters are a number and a letter
                        if(preg_match('/^(?=.*\d.*\d)/',$data) && preg_match('/.*[0-9]+[a-zA-Z]+[a-zA-Z]$/',$data)){
                         return new ValidationResult(TRUE);
                        }
                        return new ValidationResult(FALSE);
                    }
                ))
            ->endRule()
            ->startRule('town')->addConstraint( $iss )->endRule()
            ->startRule('country')->addConstraint( new CallableConstraint('strcmp', 'uk', [0 => TRUE ] ))->endRule()
          ->endRule()
          ->startRule('details')
            ->startRule('nid')->addConstraint( new CallableConstraint('is_numeric'))->endRule()
            ->startRule('sid')->endRule()
            ->startRule('uid')->addConstraint( new CallableConstraint('is_numeric'))->endRule()
            ->startRule('page_num')->addConstraint( new CallableConstraint('is_numeric'))->endRule()
            ->startRule('page_count')->addConstraint( new CallableConstraint('is_numeric'))->endRule()
            ->startRule('finished')->addConstraint( new CallableConstraint('is_numeric'))->endRule()
          ->endRule()
          ->startRule('op')->addConstraint(new CallableConstraint('strcmp','Submit',[0 => TRUE ]))->endRule()
          ->startRule('form_token')->addConstraint(new CallableConstraint('is_null',NULL,['false' => TRUE]))->endRule();
```
 


## Roadmap

Integrate with forthcoming px_cache module