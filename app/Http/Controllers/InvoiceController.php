<?php

namespace App\Http\Controllers;

use App\Exports\InvoicesExport;
use App\Models\User;
use App\Models\Invoice;
use App\Models\sections;
use Illuminate\Http\Request;
use App\Models\invoices_details;
use App\Notifications\Add_invoice;
use Illuminate\Support\Facades\DB;
use App\Models\invoice_attachments;
use App\Notifications\Add_invoice_notification;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = Invoice::all();
        return view('invoices.invoices', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sections = sections::all();
        return view('invoices.add_invoice', compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'Amount_Commission' => 'required|lt:Amount_collection',
        ], [
            'Amount_Commission.lt' => 'مبلغ العمولة يجب ان يكون أقل من مبلغ التحصيل s',
        ]);
        Invoice::create([
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_Date,
            'due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'amount_collection' => $request->Amount_collection,
            'amount_comission' => $request->Amount_Commission,
            'discount' => $request->Discount,
            'value_vat' => $request->Value_VAT,
            'rate_vat' => $request->Rate_VAT,
            'total' => $request->Total,
            'status' => 'غير مدفوعة',
            'value_status' => 2,
            'note' => $request->note,
        ]);
        $invoice_id = invoice::latest()->first()->id;
        invoices_details::create([
            'id_invoices' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'section' => $request->Section,
            'status' => 'غير مدفوعة',
            'value_status' => 2,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);

        if ($request->hasFile('pic')) {

            $invoice_id = Invoice::latest()->first()->id;
            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;

            $attachments = new invoice_attachments();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $invoice_number;
            $attachments->Created_by = Auth::user()->name;
            $attachments->invoice_id = $invoice_id;
            $attachments->save();

            // move pic
            $imageName = $request->pic->getClientOriginalName();
            $request->pic->move(public_path('Attachments/' . $invoice_number), $imageName);
        }
        // $user=User::first();
        // Notification::send($user, new Add_invoice( $invoice_id));

        $user = User::get();
        $invoices = Invoice::latest()->first();
        Notification::send($user, new Add_invoice_notification($invoices));
        // Session()->flash('add', 'تم اضافة الفاتورة بنجاح');
        return redirect('/invoices')->with('add', 'تم اضافة الفاتورة بنجاح');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $invoices = Invoice::where('id', $id)->first();
        return view('invoices.status_update', compact('invoices'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $invoice = invoice::where('id', $id)->first();
        $sections = sections::all();
        return view('invoices.edit_invoice', compact('invoice', 'sections'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $invoice = Invoice::FindOrFail($request->invoice_id);
        $invoice->update([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'product' => $request->product,
            'section_id' => $request->section,
            'amount_collection' => $request->amount_collection,
            'amount_comission' => $request->amount_commission,
            'discount' => $request->discount,
            'value_vat' => $request->value_vat,
            'rate_vat' => $request->rate_vat,
            'total' => $request->total,
            'note' => $request->note,
        ]);
        // session()->flash('edit', 'تمت التعديل الفاتورة بنجاح');
        return redirect()->route('invoices.index')->with('edit','تمت التعديل الفاتورة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->invoice_id;
        $invoice = Invoice::where('id', $id)->first();
        $details = invoice_attachments::where('invoice_id', $id)->first();
        // $invoice->forceDelete();              // to delete from all database
        $id_page = $request->id_page;
        if (!$id_page == 2) {
            if (!empty($details->invoice_number)) {
                Storage::disk('public_uploads')->delete($details->invoice_number . '/' . $details->file_name);
            }

            $invoice->forceDelete();
            session()->flash('delete_invoice');
            return redirect('/invoices');
        } else {
            $invoice->delete();
            session()->flash('archive_invoice');
            return redirect('/Archive');
        }
    }
    public function getproducts($id)
    {
        $products = DB::table("products")->where("section_id", $id)->pluck("Product_name", "id");
        return json_encode($products);
    }

    public function Status_Update(Request $request, $id)
    {
        $invoices = Invoice::findOrFail($id);

        if ($request->status === 'مدفوعة') {

            $invoices->update([
                'value_status' => 1,
                'status' => $request->status,
                'payment_date' => $request->payment_date,
            ]);

            invoices_Details::create([
                'id_invoices' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'section' => $request->section,
                'status' => $request->status,
                'value_status' => 1,
                'payment_date'=> $request->payment_date,
                'note' => $request->note,
                'user' => (Auth::user()->name),
            ]);
        } else {
            $invoices->update([
                'value_status' => 3,
                'status' => $request->status,
                'payment_date' => $request->payment_date,
            ]);
            invoices_Details::create([
                'id_invoices' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'section' => $request->section,
                'status' => $request->status,
                'value_status' => 3,
                'note' => $request->note,
                'payment_date'=> $request->payment_date,
                'user' => (Auth::user()->name),
            ]);
        }
        session()->flash('Status_Update');
        return redirect('/invoices');
    }
    public function paid()
    {
        $invoices = Invoice::where('value_status', 1)->get();
        return view('invoices.invoices_paid', compact('invoices'));
    }
    public function unpaid()
    {
        $invoices = Invoice::where('value_status', 2)->get();
        return view('invoices.invoices_unpaid', compact('invoices'));
    }
    public function partial()
    {
        $invoices = Invoice::where('value_status', 3)->get();
        return view('invoices.invoices_partial', compact('invoices'));
    }
    public function Print($id)
    {
        $invoices = Invoice::where('id', $id)->first();
        return view('invoices.print_invoices', compact('invoices'));
    }
    public function export()
    {
        return Excel::download(new InvoicesExport, 'قائمة الفواتير.xlsx');
    }
    public function mark_All_As_Read()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back();
    }
    public function markAsRead($id)
    {
        foreach (auth()->user()->unreadNotifications as $notification) {
            if ($notification->data['id'] == $id)
                $notification->markAsRead();
        }
        return redirect()->route('InvoicesDetails', ['id' => $id]);
    }
}
