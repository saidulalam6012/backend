<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::orderBy('id', 'DESC')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = $request->validate([
            'title' => 'bail|required|unique:products|max:255',
            'description' => 'bail|required',
            'price' => 'bail|required|integer',
            'image' => 'required|base64image',
        ]);
        $product['image'] = uploadBase64File($request->image);
        Product::create($product);
        return response('Product created successfully', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Product::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = $request->validate([
            'title' => 'bail|required|max:255|unique:products,title,'.$id,
            'description' => 'bail|required',
            'price' => 'bail|required|integer',
            'image' => 'required',
        ]);
        
        if(preg_match('#^data:image/\w+;base64,#i', $request->image) == 1){
            $product['image'] = uploadBase64File($request->image);
        } else {
            unset($product['image']);
        }
        Product::find($id)->update($product);
        return response('Product updated successfully', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        Storage::delete(str_replace("storage", "public",$product->image));
        $product->delete();
        return response('Product delete successfully', 200);
    }
}
