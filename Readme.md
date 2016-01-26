# Px_DrushOptionValidator

Validating inputs is boring! This is a quick wrapper to standardize validation procedures. 
It was made for drush commands, but you could use it anywhere

You can add your own Validation routines and pass them to the factory by using the fully qualified namespace as the name. 
Otherwise it will assume its part of this class.

If you have standard validation classes to contribute that would be awesome.

You can call any function and pass it data by using a CallableConstraint type.

See the unit tests for examples of these

    $options = [];

    $constraints = [];
    $constraints[] = [
      'class' => 'Numeric\\GreaterThan',
      'args' => array(5),
    ];

    $constraints[] = [
      'class' => 'Numeric\\IsNumeric',
      'args' => array(),
    ];

    $constraints[] = [
      'class' => 'Broken\\Will Not Return',
      'args' => array(7,11),
    ];

    $constraints[] = [
      'class' => 'Numeric\\Between',
      'args' => array(7,11),
    ];

    $options[] = new Option('field_1',ConstraintFactory::makeConstraints($constraints));

    $constraints = [];
    $constraints[] = [
      'class' => 'CallableConstraint',
      'args' => array(
        function($data){
          return($data < 10000000)?TRUE:FALSE;
        }
      ),
    ];

    $options[] = new Option('field_2',ConstraintFactory::makeConstraints($constraints));
    $options[] = new Option('field_3',ConstraintFactory::makeConstraints($constraints));


    $vali = new DrushOptionValidator($options);

    $data = [];
    $data['field_1'] = 9;
    $data['field_2'] = 1000000;

    $passes = $vali->validate($data);//TRUE

    $data['field_3'] = 100000000;

    $fails = $vali->validate($data);//FALSE
 


## Roadmap

Integrate with forthcoming px_cache module
Error Messages
Logging Interface