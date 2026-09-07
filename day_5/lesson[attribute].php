<?php

declare(strict_types=1);

#[Attribute]
class Example {}

#[Attribute]
class Route
{
    public function __construct(
        public string $path,
        public string $method = 'GET'
    ) {}
}


class UserController
{
    #[Route(
        path: '/users',
        method: 'POST'
    )]
    public function store(): void {}
}


#[Attribute(Attribute::TARGET_CLASS)]
final class Entity {}

#[Entity]
final class User
{
    public string $name;
}



#[Attribute(
    Attribute::TARGET_CLASS |
        Attribute::TARGET_METHOD |
        Attribute::TARGET_PROPERTY
)]
class Example2 {}

#[Example2]
final class User2
{
    #[Example2]
    public string $name;

    #[Example2]
    public function save(): void
    {
        //
    }
}

#[Attribute(
    Attribute::TARGET_METHOD |
        Attribute::IS_REPEATABLE
)]
final class Permission
{
    public function __construct(
        public string $name
    ) {}
}


final class Repeat
{
    #[Permission('users.read')]
    #[Permission('user.write')]
    public function update(): void
    {
        //
    }
}

#[Attribute(Attribute::TARGET_METHOD)]
final class Cache
{
    public function __construct(
        public int $second
    ) {
        //
    }
}


class ProductService
{
    #[Cache(300)]
    public function getProducts(): void
    {
        //
    }
}

#[Attribute(Attribute::TARGET_PROPERTY)]
class Required 
{
    //
}

class RequiredTest 
{
    #[Required]
    public string $name;

    #[Required]
    public string $email;

}

class UController
{
    #[Route('/users','GET')]
    public function index(): void 
    {
        echo 'List users';
    }
    
    #[Route('/users','POST')]
    public function store(): void 
    {
        echo 'Create user';
    }

}


$reflection = new ReflectionClass(UController::class);

foreach($reflection->getMethods() as $method){
    $attributes = $method->getAttributes(Route::class);

    foreach($attributes as $attribute){
        $route = $attribute->newInstance();

        echo $method->getName() . PHP_EOL;
        echo $route->method . ' ' . $route->path . PHP_EOL;
    }
}


