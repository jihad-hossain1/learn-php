<?php


declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\User;

$user = new User(name: 'Jihad');

echo $user->name;