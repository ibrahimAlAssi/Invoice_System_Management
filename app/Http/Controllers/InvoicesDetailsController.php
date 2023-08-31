<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\invoices_details;
use App\Models\invoice_attachments;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Controller;

class InvoicesDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\invoices_details  $invoices_details
     * @return \Illuminate\Http\Response
     */


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\invoices_details  $invoices_details
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $invoices = Invoice::where('id', $id)->first();
        $details = invoices_details::where('id_invoices', $id)->get();
        $attachments = invoice_attachments::where('invoice_id', $id)->get();
        return view('invoices.invoice_details', compact('invoices', 'attachments', 'details'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\invoices_details  $invoices_details
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, invoices_details $invoices_details)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\invoices_details  $invoices_details
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {

        $invoices = invoice_attachments::findOrFail($id);
        $invoices->delete();
        // Storage::disk('public_uploads')->deleteDirectory($request->invoice_number);//delete folder
        Storage::disk('public_uploads')->delete($request->invoice_number.'/'.$request->invoice_name);//delete file
        session()->flash('delete', 'تم حذف المرفق بنجاح');
        return back();
    }


    public function openFile($invoice_number,$file_name)
    {
        //  $files = Storage::disk('public_uploads')->path($invoice_number.'/'.$file_name); //it's work but there is error about path
        $pathFile = public_path('Attachments/'.$invoice_number.'/'. $file_name);
        return response()->file($pathFile);

    }
    public function getFile($invoice_number,$file_name)
    {
        $pathFile = public_path('Attachments/'.$invoice_number.'/'. $file_name);
        return response()->download($pathFile);
    }
}
