<?php

namespace App\Http\Controllers\Api;

use App\Post;
use App\Client;
use App\Notification;

use App\Donation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostsController extends Controller
{


public function posts(Request $request)
    {

        $posts = Post::where(function ($query) use ($request) {

            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }
        })->get();


        return responsejson(1, 'success', $posts);
    }

    public function showpost(Request $request,Post $id)
    {
    
        return responsejson(1, 'success', $id);
    }


    public function filterposts(Request $request)
    {

        $posts = Post::where(function ($query) use ($request) {

            if ($request->has('id') and $request->has('category_id')) {
                $query->where(
                             [
                                 'id'=> $request->id ,
                                 'category_id'=> $request->category_id
                             ]);
                
            }
            else{  $query->where('id',$request->id);}
        })->get()->all();


        if($posts){return responsejson(1, 'success', $posts);}
        else{
            return responsejson(0, 'not exists search on another');
        }
  

        
    }

    public function toggle(Request $request,$id)
    {
        $userid = $request->user()->id;

        $client = Client::find($userid);
        $post   = Post::find($id);

        $client->posts()->toggle($post->id); 
        

        return responsejson(1, 'success');

    }

    public function ListFavourite(Request $request)
    {
        $client = $request->user();

        $favourite = $client->posts()->where('client_id', $client->id)->get();

        return responsejson(1, 'successfully list favourite', $favourite);
    }


 
}
    