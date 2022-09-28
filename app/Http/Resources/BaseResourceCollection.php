<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/** @property bool $preserveKeys */
class BaseResourceCollection extends AnonymousResourceCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * Set to null on a base class to prevent wrapping when the resource
     * is a non-paginated collection.
     *
     * @var null|string
     */
    public static $wrap;

    /**
     * Customize the response for a request.
     *
     * This overriden method will tap into the response content and
     * remove the meta.links field, which is not needed in our case.
     *
     * @param \Illuminate\Http\Request      $request
     * @param \Illuminate\Http\JsonResponse $response
     *
     * @return void
     */
    public function withResponse($request, $response): void
    {
        $jsonResponse = json_decode(
            $response->getContent() ?: '[]',
            true
        );

        if (is_array($jsonResponse) && isset($jsonResponse['meta']['links'])) {
            unset($jsonResponse['meta']['links']);
        }

        $content = json_encode($jsonResponse);

        if ($content) {
            $response->setContent(
                $content
            );
        }
    }
}
