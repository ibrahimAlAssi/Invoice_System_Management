<?php

namespace App\Http\Controllers;

use App\Models\products;
use App\Models\sections;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $sections=sections::all();
        $products=products::all();
       return view('products.products',compact('sections','products'));
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
        $validatedData = $request->validate([
            'product_name' => 'required|unique:products,product_name|min:2|max:255',
        ],[

            'product_name.required' =>'يرجي ادخال اسم المنتج',
            'product_name.unique' =>'اسم المنتج مسجل مسبقا',
            'product_name.min'=>'اسم المنتج يجب على الاقل من حرفين',


        ]);
        products::create([
            'product_name'=>$request->product_name,
            'description'=>$request->description,
            'section_id'=>$request->section_id ,
        ]);
        session()->flash('Add','تم إضافة المنتج بنجاح');
        return redirect('/products');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\products  $products
     * @return \Illuminate\Http\Response
     */
    public function show(products $products)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\products  $products
     * @return \Illuminate\Http\Response
     */
    public function edit(products $products)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\products  $products
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $validatedData=$request->validate([
            'product_name'=>'required|unique:products,product_name,'.$id,
        ],[
            'product_name.required' =>'يرجي ادخال اسم المنتج',
            'product_name.unique' =>'اسم المنتج مسجل مسبقا',
        ]);
      $id=sections::where('section_name',$request->section_name)->first()->id;
      $products=products::findOrFail($request->id);
      $products->update([
          'product_name'=>$request->product_name,
          'description'=>$request->description,
          'section_id'=>$id,
      ]);
      session()->flash('update','تم التعديل المنتج  بنجاح');
      return redirect('/products');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\products  $products
     * @return \Illuminate\Http\Response
     */
    public function destroy(request $request,$id)
    {
         $products=products::findOrFail($id);
        $products->delete();
        session()->flash('delete','تم حذف المنتج بنجاح');
        return back();
    }
}
