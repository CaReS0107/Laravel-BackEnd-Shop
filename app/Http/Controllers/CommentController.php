<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeComment(Request $request, $id)
    {
        $user = Auth::user();
        $product = Product::find($id);

        $comment = Comment::create([
            'comment' => $request->get('comment'),
            'product_id' => $product->id,
            'user_id' => $user->id,

        ]);
        $response = [
            'msg' => 'Coment was created',
            'comment' => $comment->comment,
        ];


        return response()->json($response, 201);

    }


}
