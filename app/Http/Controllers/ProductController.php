<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateProduct;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
//    public function __construct(){
//        $this->middleware('roles:admin');
//    }

    public function getAllProduct()
    {


        $product = Product::all();


        return response()->json($product, 200);
    }

    public function search(Request $request)
    {
        $category_id = $request->get('category_id');

        $products = Product::query()
            ->whereHas('categories', function (Builder $query) use ($category_id) {
                $query->whereIn('category_id', [$category_id]);
            })->get();

        $response = [
            'Products:' => $products,
            'Param:' => 'category_id'
        ];


        return response()->json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createProduct(ValidateProduct $request)
    {
        $user = Auth::user();
        $products = Product::Create([

            'name' => $request->get('name'),
            'price' => $request->get('price'),
            'description' => $request->get('description'),
            'user_id' => $user->id,
        ]);

        $category_id = $request->get('category_id');


//        $cutCat = Category::where('name', 'cutiuta')->first();
//        $bucCat = Category::where('name', 'buchet')->first();
//        $cosCat = Category::where('name', 'cosulet')->first();

        $products->categories()->attach($category_id);


        $user->show = [
            'href' => '/api/product/' . $products->id . '?api_token=' . $user->api_token,
            'method' => 'Get',
        ];
        $user->delete = [
            'href' => '/api/product/' . $products->id . '?api_token=' . $user->api_token,
            'method' => 'delete',
        ];
        $user->update = [
            'href' => '/api/product/' . $products->id . '?api_token=' . $user->api_token,
            'method' => 'put',
        ];
        $response = [
            'msg' => 'Product Created',
            'Product' => $products,
            'Param:' => 'category_id, name, price, description',
            'Show Product' => $user->show,
            'Delete Product' => $user->delete,
            'Update Product' => $user->update
        ];

        return response()->json($response, 201);
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $product = Product::find($id);

        if (!$product) {
            abort(404, 'Product not found');
        }

        return response()->json($product->load('comments', 'user'), 200);


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);

        if (!$product) {
            abort(404, 'Product not found');
        }

        return response()->json($product->load('comments', 'user'), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        $product->name = $request->input('name');
        $product->price = $request->input('price');
        $product->description = $request->input('description');
        $product->save();

        return response()->json($product->load('user'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        $user = User::find($id);
        $adminRole = Role::where('name', 'admin')->first();

        if (!$user === $adminRole) {
            abort('401', 'You do not have permision for this action');
        }

        if (!Comment::where('product_id',$product->id)->delete()){
            abort('400', 'Comment was not deleted');
        }
        $product->destroy($product->id);

        return response()->json(['Succes' => 'Product and comment attachet was deleted']);

    }
}
