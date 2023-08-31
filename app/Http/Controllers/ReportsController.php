<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportsController extends Controller
{
    //
    public function index()
    {

        return view('reports.invoices_report');
    }

    public function Search_invoices(Request $request)
    {
            
        $rdio = $request->rdio;
        // في حالة البحث بنوع الفاتورة
        if ($rdio == 1) {
            // في حالة عدم تحديد تاريخ
            if ($request->type && $request->start_at == '' && $request->end_at == '') {
                if($request->type=='الكل')
                $invoices = invoice::select('*')->get();
                else  $invoices = invoice::select('*')->where('status', '=', $request->type)->get();
                 $type = $request->type;
                 return view('reports.invoices_report', compact('type','invoices'));
                }
            // في حالة تحديد تاريخ استحقاق
            else {

                $start_at = date($request->start_at);
                $end_at = date($request->end_at);
                $type = $request->type;

                $invoices = invoice::whereBetween('invoice_Date', [$start_at, $end_at])->where('status', '=', $request->type)->get();
                return view('reports.invoices_report', compact('type', 'start_at', 'end_at','invoices'));
            }
        }

        //====================================================================

        // في البحث برقم الفاتورة
        else {

            $invoices = Invoice::select('*')->where('invoice_number', '=', $request->invoice_number)->get();
            return view('reports.invoices_report',compact('invoices'));
        }
    }
}
