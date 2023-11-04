<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class PaginationHelper
{
    /**
     * Paginate the query based on the request parameters. If the paginate parameter is not set, paginate the query.
     * If the paginate parameter is set to false, return all the results. If the paginate parameter is set to true,
     * paginate the query. If the per_page parameter is set, paginate the query with the given number of results per
     * page.
     *
     * @param QueryBuilder $outQuery
     * @param Request $request
     * @return void
     */
    public static function Paginate(QueryBuilder &$query, Request $request): void
    {
        $shouldPaginate = $request->input('paginate', true);
        $shouldPaginate = ($shouldPaginate === 'false') ? false : (bool)$shouldPaginate;

        $perPage = $request->input('per_page', 15);

        if ($shouldPaginate) {
            $query = $query->paginate($perPage);
        } else {
            $query = $query->get();
        }
    }
}
