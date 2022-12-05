<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransaction;

class TransactionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.user');
        $this->middleware('auth:user');
    }

    public function store(StoreTransaction $request)
    {

    }
}
