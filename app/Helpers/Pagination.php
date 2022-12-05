<?php

namespace App\Helpers;

class Pagination {

    public static function links($request)
    {
        if ($request->input('page', 1) > 1) {
            $prevPage = url()->current() . '?page=' . ($request->input('page') - 1);
        } else {
            $prevPage = null;
        }

        if ($request->input('page', 1) * 15 < $request->input('total', 0)) {
            $nextPage = url()->current() . '?page=' . ($request->input('page', 1) + 1);
        } else {
            $nextPage = null;
        }

        return [
            'first' => url()->current() . '?page=1',
            'last' => url()->current() . '?page=' . ceil($request->input('total') / 15),
            'prev' => $prevPage,
            'next' => $nextPage,
        ];
    }

    public static function meta($request)
    {    
        return [
            'current_page' => (int) $request->input('page', 1),
            'total' => $request->input('total', 0),            
        ];
    }
}