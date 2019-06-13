# Annotation Generator
Annotation generator is ```php artisan ```command used to generate annotation for Swagger 3.0 documentation 

#Install

    composer require shortcodes/annotation-generator

# Usage

Register command in ```app/Console/Kernel.php```

    protected $commands = [
        GenerateAnnotations::class
    ];

Then you can use command:

    php artisan make:annotation Product --tag="product"

Option ```--tag``` is responsible for generation of annotation with correct Swagger 3.0 tag and it is mandatory

###Output

Command creates in directory ```app/Swagger/Actions``` files ```Index.php```, ```Show.php```, ```Store.php```, ```Update.php``` and ```Delete.php``` with Swagger 3.0 annotations and creates stub of model in ```app/Swagger/Models``` named ```Product.php``` with example property

It is possible to exclude creation of model using option ```--nomodel```

