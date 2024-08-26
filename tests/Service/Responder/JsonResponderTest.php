<?php

declare(strict_types=1);

use DeadMansSwitch\Responder\Exception\SerializationException;
use DeadMansSwitch\Responder\Service\Responder\JsonResponder;
use Symfony\Component\HttpFoundation\AcceptHeaderItem;
use Symfony\Component\Uid\Uuid;

test('Supports method returns true for application/json content type', function (string $value, bool $expected) {
    $header    = new AcceptHeaderItem($value);
    $responder = new JsonResponder();

    expect($responder->supports($header))->toBe($expected);
})->with([
    ['application/json', true],
    ['application/xml', false],
]);

test('Simple object can be properly converted into json response', function () {
    $data = new class {
        public string $name = 'John Doe';
        public int $age = 30;
    };

    $responder = new JsonResponder();
    $response  = $responder->respond($data, 200);

    expect($response->getContent())->toBeJson()
        ->and($response->getContent())->json()
            ->toHaveCount(2)
            ->name->toBe('John Doe', 'Name property should be John Doe')
            ->age->toBe(30, 'Age property should be 30')
        ->and($response->getStatusCode())->toBe(200)
    ;
});

test('Simple array can be properly converted into json response', function () {
    $data = [
        'name' => 'John Doe',
        'age' => 30,
    ];

    $responder = new JsonResponder();
    $response  = $responder->respond($data, 200);

    expect($response->getContent())->toBeJson()
        ->and($response->getContent())->json()
            ->toHaveCount(2)
            ->name->toBe('John Doe', 'Name property should be John Doe')
            ->name->toBeString()
            ->age->toBe(30, 'Age property should be 30')
            ->age->toBeInt()
        ->and($response->getStatusCode())->toBe(200)
    ;
});

test('Array of simple objects can be properly converted into json response', function () {
    $data = [
        new class {
            public string $name = 'John Doe';
            public int $age = 30;
        },
        new class {
            public string $name = 'Jane Doe';
            public int $age = 25;
        },
    ];

    $responder = new JsonResponder();
    $response  = $responder->respond($data, 200);
    $json      = $response->getContent();

    expect($json)->toBeJson()
        ->and($json)
            ->json()
            ->toBeArray()
            ->toHaveLength(2)
            // TODO: find a way to assert structure of json elements
        ->and($response->getStatusCode())->toBe(200);
});

test('Scalar value can not be converted into JSON and will throw an exception', function () {
    $data = 'foobarbaz';

    $responder = new JsonResponder();
    $responder->respond($data, 200);
})->expectException(SerializationException::class);

test('Null can be converted into JSON response', function () {
    $data = null;

    $responder = new JsonResponder();
    $response  = $responder->respond($data, 200);

    expect($response->getContent())->toBeJson()
        ->and($response->getContent())->json()->toBeNull()
        ->and($response->getStatusCode())->toBe(200)
    ;
});

test('UUID object will be properly converted in UUID-string', function () {
    $uuid = Uuid::v7();
    $data = (object) [
        'uuid' => $uuid,
        'name' => 'John Doe',
    ];

    expect($data->uuid)->toBeInstanceOf(Uuid::class);

    $responder = new JsonResponder();
    $response  = $responder->respond($data, 200);
    $json      = $response->getContent();

    expect($json)
        ->toBeJson()
        ->and($json)
            ->json()
            ->uuid->toBeString()
            ->uuid->toBe($uuid->toRfc4122())
            ->name->toBe('John Doe')
        ->and($response->getStatusCode())
            ->toBe(200);
});

test('DateTimeInterface will be properly converted in RFC 3339 format', function () {
    $date = new DateTimeImmutable('now');

    $data = (object) [
        'date' => $date,
        'name' => 'John Doe',
    ];

    expect($data->date)->toBeInstanceOf(DateTimeInterface::class);

    $responder = new JsonResponder();
    $response  = $responder->respond($data, 200);
    $json      = $response->getContent();

    expect($json)
        ->toBeJson()
        ->and($json)
            ->json()
            ->date->toBeString()
            ->date->toBe($date->format(DateTimeInterface::RFC3339))
            ->name->toBe('John Doe')
        ->and($response->getStatusCode())
            ->toBe(200);
});

test('Non-backed enum will be serialized same to object', function () {
    enum NonBackedEnum {
        case BAR;
    }

    $data = (object) [
        'foo' => NonBackedEnum::BAR,
        'name' => 'John Doe',
    ];

    expect($data->foo)->toBeInstanceOf(NonBackedEnum::class);

    $responder = new JsonResponder();
    $response  = $responder->respond($data, 200);
    $json      = $response->getContent();

    expect($json)
        ->toBeJson()
        ->and($json)
            ->json()
            ->foo->toBeArray()
            ->foo->toHaveCount(1)
            ->foo->name->toBe('BAR')
            ->name->toBe('John Doe')
        ->and($response->getStatusCode())
            ->toBe(200);
});

test('Backed enum will be converted properly in scalar representation', function () {
    enum ItsBackedEnum: string {
        case BAR = 'bar';
    }

    $data = (object) [
        'foo' => ItsBackedEnum::BAR,
        'name' => 'John Doe',
    ];

    expect($data->foo)->toBeInstanceOf(ItsBackedEnum::class);

    $responder = new JsonResponder();
    $response  = $responder->respond($data, 200);
    $json      = $response->getContent();

    expect($json)
        ->toBeJson()
        ->and($json)
            ->json()
            ->foo->toBe('bar')
            ->name->toBe('John Doe')
        ->and($response->getStatusCode())
            ->toBe(200);
});

test('Nested object will be properly serialized', function () {
    $data = (object) [
        'name' => 'John Doe',
        'address' => (object) [
            'street' => 'Main Street',
            'city' => 'New York',
        ],
    ];

    $responder = new JsonResponder();
    $response  = $responder->respond($data, 200);
    $json      = $response->getContent();

    expect($json)
        ->toBeJson()
        ->and($json)
            ->json()
            ->name->toBe('John Doe')
            ->address->toBeArray()
            ->address->toHaveCount(2)
            ->address->street->toBe('Main Street')
            ->address->city->toBe('New York')
        ->and($response->getStatusCode())
            ->toBe(200);
});

test('Invalid payload will cause SerializationException', function () {
    $data = fopen('php://memory', 'r');

    $responder = new JsonResponder();
    $responder->respond($data, 200);
})->expectException(SerializationException::class);