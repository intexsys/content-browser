<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException;
use Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ValueSearchAdapter;
use Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    /**
     * Performs the search for values by using the specified text.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException If search text is empty
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function search(Request $request)
    {
        $searchText = $request->query->get('searchText');
        if (empty($searchText)) {
            throw new InvalidArgumentException('Search text cannot be empty');
        }

        $pager = $this->buildPager(
            new ValueSearchAdapter(
                $this->backend,
                $searchText
            ),
            $request
        );

        $data = array(
            'children_count' => $pager->getNbResults(),
            'children' => array_map(
                function (ValueInterface $value) {
                    return $this->valueSerializer->serializeValue($value);
                },
                $pager->getCurrentPageResults()
            ),
        );

        return new JsonResponse($data);
    }
}
