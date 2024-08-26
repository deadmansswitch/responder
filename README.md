# Symfony Responder

Quick start to convert your traditional MVC symfony app into ADR app

## Features

### Typed responses from your actions

Instead of returning Response objects from your controllers, you can return DTO objects.

Before:

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreateUserInput;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[Route('/users', name: 'create_user', methods: ['POST'])]
final class UserController extends AbstractController
{
    public function __invoke(#[MapRequestPayload] CreateUserInput $input): Response
    {
        $user = new User();
        $user->setLogin($request->get('name'));
        $user->setEmail($request->get('email'));
        $user->setPassword($request->get('email'));
        
        // ... some logic, entity validation, password hashing, persisting etc. (delegate it to service in real app)
        
        return new JsonResponse(['id' => $user->getId()], Response::HTTP_CREATED);
    }
}
```

After:

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreateUserInput;
use App\DTO\CompactUserDTO;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[Route('/users', name: 'create_user', methods: ['POST'])]
final class CreateUser
{
   public function __invoke(#[MapRequestPayload] CreateUserInput $input): CompactUserDTO
   {
       $user = new User();
       $user->setLogin($input->login);
       $user->setEmail($input->email);
       $user->setPassword($input->password);
       
       // ... some logic, entity validation, password hashing, persisting etc. (delegate it to service in real app)
       
       return new CompactUserDTO(
           id: $user->getId(),
           login: $user->getLogin(),
           createdAt: $user->getCreatedAt(), 
       );
   }
}
```

Benefits:
- Slightly lower amount of client code, no injection of serializer into each controller
- Business logic separated from response formating, so action becomes reusable
- Every action now gets a clear contract of object that it should return
- Ability to read actions using reflection and generate API documentation

### Automatic response serialization

Using content-type negotiation, bundle will create proper instance of Responder to serialize your data for client purposes.

Just out ouf the box you've got JsonResponder that will serialize your DTOs into JSON responses.

You can create your own responders for different content type or override default JsonResponder with own implementation.

### HTTP Response Codes

It uses default response codes for every HTTP Request Method, but you can easily override them on action level:

```php
<?php

declare(strict_types=1);

namespace App\Domain\User\Action;

#[HttpResponseCode(status: 202)] // <----------- 202 Accepted instead of 201 Created
final class CreateUser
{
   public function __invoke(CreateUserInput $input): CreateUserOutput
   {
       // ...
   }
}
```
