<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;
class PruebaController extends Controller
{
    public function prueba(){
        $categories = Category::all();
        
        foreach($categories as $category){
            echo "<h2>{$category->name}</h2>";
            foreach($category->posts as $post){
                echo "<span>{$post->content}</span>";
            }
        }
        
        die();
    }
}
