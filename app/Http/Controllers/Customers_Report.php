<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\sections;
use Illuminate\Http\Request;

class Customers_Report extends Controller
{

    public function index()
    {
        $sections=sections::all();
        return view('reports.customer_report',compact('sections'));
    }
    public function Search_customers(Request $request){
        // في حالة البحث بدون التاريخ
             if ($request->section && $request->product && $request->start_at =='' && $request->end_at=='') {
              $invoices = invoice::select('*')->where('section_id','=',$request->section)->where('product','=',$request->product)->get();
              $sections = sections::all();
               return view('reports.customer_report',compact('sections','invoices'));
             }
          // في حالة البحث بتاريخ
             else {
               $start_at = date($request->start_at);
               $end_at = date($request->end_at);

              $invoices = Invoice::whereBetween('invoice_Date',[$start_at,$end_at])->where('section_id','=',$request->section)->where('product','=',$request->product)->get();
               $sections = sections::all();
               return view('reports.customer_report',compact('sections','invoices'));
             }
            }
}
