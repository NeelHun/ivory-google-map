<?php

/*
 * This file is part of the Ivory Google Map package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\GoogleMap\Service\Geocoder;

use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use Ivory\GoogleMap\Service\AbstractSerializableService;
use Ivory\GoogleMap\Service\Geocoder\Request\GeocoderRequestInterface;
use Ivory\GoogleMap\Service\Geocoder\Response\GeocoderResponse;
use Ivory\Serializer\Context\Context;
use Ivory\Serializer\Naming\SnakeCaseNamingStrategy;
use Ivory\Serializer\SerializerInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GeocoderService extends AbstractSerializableService
{
	private $requestParams;
	private $responseBody;

    /**
     * @param HttpClient               $client
     * @param MessageFactory           $messageFactory
     * @param SerializerInterface|null $serializer
     */
    public function __construct(
        HttpClient $client,
        MessageFactory $messageFactory,
        SerializerInterface $serializer = null
    ) {
        parent::__construct(
            'https://maps.googleapis.com/maps/api/geocode',
            $client,
            $messageFactory,
            $serializer
        );
    }

    /**
     * @param GeocoderRequestInterface $request
     *
     * @return GeocoderResponse
     */
    public function geocode(GeocoderRequestInterface $request)
    {
        $httpRequest = $this->createRequest($request);
        $this->requestParams = $httpRequest->getUri()->getQuery();
        $httpResponse = $this->getClient()->sendRequest($httpRequest);
        $this->responseBody = $httpResponse->getBody()->getContents();

        $response = $this->deserialize(
            $httpResponse,
            GeocoderResponse::class,
            (new Context())->setNamingStrategy(new SnakeCaseNamingStrategy())
        );

        $response->setRequest($request);

        return $response;
    }

	public function getRequestParams()
	{
		return $this->requestParams;
	}

	public function getResponseBody()
	{
		return $this->responseBody;
	}
}
