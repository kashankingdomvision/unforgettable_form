<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\QuoteLog;
class QuoteController extends Controller
{
    public function quote_logs($id)
    {
        $data['quotes'] = QuoteLog::findOrFail(decrypt($id));
        dd($data);
    }
}
