<?php

declare(strict_types=1);

namespace DeadMansSwitch\Responder\Service\Responder;

use DeadMansSwitch\Responder\Exception\SerializationException;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\AcceptHeaderItem;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\UidNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final class JsonResponder implements ResponderInterface
{
    public function supports(AcceptHeaderItem $header): bool
    {
        return $header->getValue() === self::getContentType();
    }

    /**
     * @throws Exception
     */
    public function respond(mixed $data, int $status): Response
    {
        if (is_scalar($data)) {
            throw new SerializationException('Only arrays and objects can be serialized into valid JSON');
        }

        try {
            $json = $this
                ->getSerializer()
                ->serialize(
                    data: $data,
                    format: 'json',
                    context: [
                        'json_encode_options' => JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
                    ],
                );
        } catch (Exception $exception) {
            throw new SerializationException('Failed to serialize data into JSON', previous: $exception);
        }

        return new Response(
            content: $json,
            status: $status,
            headers: ['Content-Type' => self::getContentType()],
        );
    }

    public static function getContentType(): string
    {
        return 'application/json';
    }

    private function getSerializer(): SerializerInterface
    {
        $attributeLoader       = new AttributeLoader();
        $classMetadataFactory  = new ClassMetadataFactory(loader: $attributeLoader);
        $nameConverter         = new MetadataAwareNameConverter(metadataFactory: $classMetadataFactory);

        return new Serializer(
            normalizers: [
                new UidNormalizer(),
                new BackedEnumNormalizer(),
                new DateTimeNormalizer(),
                new ObjectNormalizer(nameConverter: $nameConverter),
            ],
            encoders: [
                new JsonEncoder(),
            ],
        );
    }
}