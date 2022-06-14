<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Models\Product;

class ProductController extends Controller
{
    //membuat filtering
    public function all(Request $request)
    {
        //membuat variabel yang dibutuhkan
        $id = $request->input('id');
        $limit = $request->input('limit', 6);
        $name = $request->input('name');
        $types = $request->input('types');

        $price_from = $request->input('price_from');
        $price_to = $request->input('price_to');

        $rate_from = $request->input('rate_from');
        $rate_to = $request->input('rate_to');

        //kondisi untuk mengambil id saja
        if($id)
        {
            $product = Product::find($id);

            if($product)
                return ResponseFormatter::success(
                    $product,
                    'Data produk berhasil diambil'
                );
            else
                return ResponseFormatter::error(
                    null,
                    'Data produk tidak ada',
                    404
                );
        }
        
        //kondisi untuk mengambil selain id
        $product = Product::query();

        if($name)
            $product->where('name', 'like', '%' . $name . '%');

        if($types)
            $product->where('types', 'like', '%' . $types . '%');

        if($price_from)
            $product->where('price', '>=', $price_from);

        if($price_to)
            $product->where('price', '<=', $price_to);

        if($rate_from)
            $product->where('rate', '>=', $rate_from);

        if($rate_to)
            $product->where('rate', '<=', $rate_to);

        return ResponseFormatter::success(
            $product->paginate($limit),
            'Data list produk berhasil diambil'
        );
    }
}
