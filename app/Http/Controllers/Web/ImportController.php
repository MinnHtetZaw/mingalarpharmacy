<?php

namespace App\Http\Controllers\Web;

use App\Imports\ItemsImport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function execelImport(Request $request)
    {
        $this->validate($request, [
            'select_file' => 'required|mimes:xls,xlsx'
         ]);

        $counts = Excel::import(new ItemsImport(),$request->file('select_file'));

        alert()->success("Success");
        return back();
    }
}
