<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class PaginationHelper
{
    /**
     * Paginate the query based on the request parameters. If the paginate parameter is set to false do not paginate
     * the results (pagination is enabled by default). If the per_page parameter is set, paginate the query with the
     * given number of results per page (the default value is 15).
     *
     * @param QueryBuilder $outQuery
     * @param Request $request
     * @return void
     */
    public static function Paginate(QueryBuilder &$outQuery, Request $request): void
    {
        $paginate = $request['paginate'];
        $perPage = $request['per_page'] ?? 15;

        if ($paginate === null) {
            $outQuery = $outQuery->paginate($perPage);
        } else if ($paginate === 'false') {
            $outQuery = $outQuery->get();
        } else {
            $outQuery = $outQuery->paginate($perPage);
        }
    }
}
