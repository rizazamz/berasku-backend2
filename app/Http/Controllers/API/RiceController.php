<?php

namespace App\Http\Controllers\API;

use App\Models\Rice;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;

class RiceController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 6);
        $name = $request->input('name');
        $types = $request->input('types');

        $price_from = $request->input('price_from');
        $price_to = $request->input('price_to');

        $rate_from = $request->input('rate_from');
        $rate_to = $request->input('rate_to');

        if ($id)
        {
            $rice = Rice::find($id);

            if ($rice)
            {
                return ResponseFormatter::success(
                    $rice,
                    'Data produk berhasil diambil'
                );
            }
                else
                {
                    return ResponseFormatter::error(
                        null,
                        'Data produk tidak ada',
                        404
                    );
            }
        }

        $rice = Rice::query();

        if($name)
            $rice->where('name', 'like', '%' . $name . '%');

        if($types)
            $rice->where('types', 'like', '%' . $types . '%');

        if($price_from)
            $rice->where('price', '>=', $price_from);

        if($price_to)
            $rice->where('price', '<=', $price_to);

        if($rate_from)
            $rice->where('rate', '>=', $rate_from);

        if($rate_to)
            $rice->where('rate', '<=', $rate_to);

        return ResponseFormatter::success(
            $rice->paginate($limit),
            'Data list produk berhasil diambil'
        );
    }
}
