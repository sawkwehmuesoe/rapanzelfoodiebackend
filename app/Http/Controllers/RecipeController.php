<?php
namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class RecipeController extends Controller
{
    protected $user;
 
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->user
            ->recipes()
            ->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validate data
        $data = $request->only('title', 'description','image');
        $validator = Validator::make($data, [
            'title' =>   'required|string',
            'description' => 'required|string',
            'image'=> 'required|string',
            // 'cookingtime'=>'string',
            // 'amount' => 'required|integer',
            // 'type'=> 'integer'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is valid, create new product
        $recipe = $this->user->recipes()->create([     

            'title' =>   $request->title,
            'description' => $request->description,
            'image' => $request->image,
            // 'cookingtime' => $request->cookingtime,
            // 'amount' => $request->amount,
            // 'type' => $request->type,
        ]);

        //Product created, return success response
        return response()->json([
            'success' => true,
            'message' => 'Recipe created successfully',
            'data' => $recipe
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $recipe = $this->user->recipes()->find($id);
    
        if (!$recipe) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, recipe not found.'
            ], 400);
        }
    
        return $recipe;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Recipe $recipe)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Recipe $recipe)
    {
        //Validate data
        $data = $request->only('title', 'description', 'image');
        $validator = Validator::make($data, [
            'title' =>   'required|string',
            'description' => 'required|string',
            'image' => 'required|string',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is valid, update product
        $product = $recipe->update([
            'title' =>   $request->title,
            'description' => $request->description,
            'image' => $request->image,
        ]);

        //Product updated, return success response
        return response()->json([
            'success' => true,
            'message' => 'Recipe updated successfully',
            'data' => $recipe
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Recipe $recipe)
    {
        $recipe->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Recipe deleted successfully'
        ], Response::HTTP_OK);
    }
}