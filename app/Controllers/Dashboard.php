<?php

namespace App\Controllers;

use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();

        $data = [
            'totalUser' => $userModel->where('isdeleted', 0)->countAllResults()
        ];

        return view('dashboard/index', $data);
    }
}