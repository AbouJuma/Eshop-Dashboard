<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategoryResource;
use Illuminate\Http\Request;

use App\Http\Resources\ProductResource;

use App\Product; // Use the main Product model
use App\Category; // Use the main Category model
use App\BusinessLocation; // Use the BusinessLocation model

class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $per_page = 100;
        return $this->sendResponse(ProductResource::collection(Product::paginate($per_page)), 'RETRIEVE_SUCCESS');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!$product = Product::find($id)) return $this->sendError('NOT_FOUND', 404);
        else return $this->sendResponse(new ProductResource($product), 'RETRIEVE_SUCCESS')->response()->setStatusCode(200);
    }

    //get product categories
    public function getProductInCategories()
    {
        $product_categories = Category::with(['products' => function($query) {
            $query->whereHas('product_locations', function($subQuery) {
                $subQuery->where('product_locations.location_id', 9); // DOL SPARE SHOP NJIA YA NG'OMBE
            })->with(['variations', 'variations.product_variation', 'media']);
        }])->where('category_type', 'product')->get();
        
        // Filter out categories with empty products
        $filtered_categories = $product_categories->filter(function($category) {
            return $category->products->isNotEmpty();
        });
        
        return $this->sendResponse(ProductCategoryResource::collection($filtered_categories), 'RETRIEVE_SUCCESS');
    }

    //get products from DOL SPARE SHOP NJIA YA NG'OMBE (location ID 9) by categories
    public function getDolShopProductsByCategories()
    {
        try {
            $product_categories = Category::with(['products' => function($query) {
                $query->whereHas('product_locations', function($subQuery) {
                    $subQuery->where('product_locations.location_id', 9); // DOL SPARE SHOP NJIA YA NG'OMBE
                })->with(['variations', 'variations.product_variation', 'media']);
            }])->where('category_type', 'product')->get();
            
            // Filter out categories with empty products
            $filtered_categories = $product_categories->filter(function($category) {
                return $category->products->isNotEmpty();
            });
            
            return $this->sendResponse(ProductCategoryResource::collection($filtered_categories), 'RETRIEVE_SUCCESS');
        } catch (\Exception $e) {
            return $this->sendError('FAILED', $e->getMessage(), 500);
        }
    }

    //get all business locations
    public function getAllLocations()
    {
        try {
            $locations = BusinessLocation::select('id', 'name', 'location_id', 'city', 'country', 'state', 'zip_code', 'mobile')->get();
            
            return $this->sendResponse($locations, 'RETRIEVE_SUCCESS');
        } catch (\Exception $e) {
            return $this->sendError('FAILED', $e->getMessage(), 500);
        }
    }

    //get product by category
    public function getProductsByCategory($id)
    {
        if (!$product_category = Category::with('products.variations')->where('category_type', 'product')->find($id)) return $this->sendError('NOT_FOUND', 404);
        else return $this->sendResponse(new ProductCategoryResource($product_category), 'RETRIEVE_SUCCESS');
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
        //
    }

    //get products by location with categories
    public function getProductsByLocation($location_id)
    {
        try {
            $product_categories = Category::with(['products' => function($query) use ($location_id) {
                $query->whereHas('product_locations', function($subQuery) use ($location_id) {
                    $subQuery->where('product_locations.location_id', $location_id);
                })->with(['variations', 'variations.product_variation', 'media']);
            }])->where('category_type', 'product')->get();
            
            return $this->sendResponse(ProductCategoryResource::collection($product_categories), 'RETRIEVE_SUCCESS');
        } catch (\Exception $e) {
            return $this->sendError('FAILED', $e->getMessage(), 500);
        }
    }

    //get products by location name with categories
    public function getProductsByLocationName($location_name)
    {
        try {
            $product_categories = Category::with(['products' => function($query) use ($location_name) {
                $query->whereHas('product_locations', function($subQuery) use ($location_name) {
                    $subQuery->where('name', $location_name);
                })->with(['variations', 'variations.product_variation', 'media']);
            }])->where('category_type', 'product')->get();
            
            return $this->sendResponse(ProductCategoryResource::collection($product_categories), 'RETRIEVE_SUCCESS');
        } catch (\Exception $e) {
            return $this->sendError('FAILED', $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
