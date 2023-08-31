<?php

namespace App\Http\Controllers;

use App\Models\invoice;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $count_all = invoice::count();
        $count_invoice1 = invoice::where('value_status', 1)->count();
        $count_invoice2 = invoice::where('value_status', 2)->count();
        $count_invoice3 = invoice::where('value_status', 3)->count();
        if ($count_invoice1 == 0) {
            $percent1 = 0;
        } else {
            $percent1 = $count_invoice1 / $count_all * 100;
        }
        if ($count_invoice2 == 0) {
            $percent2 = 0;
        } else {
            $percent2 = $count_invoice2 / $count_all * 100;
        }
        if ($count_invoice3 == 0) {
            $percent3 = 0;
        } else {
            $percent3 = $count_invoice3 / $count_all * 100;
        }
        $chartjs = app()->chartjs
            ->name('barChartTest')
            ->type('bar')
            ->size(['width' => 400, 'height' => 200])
            ->labels(['فواتير المدفوعة', 'الفواتير الغير المدفوعة', 'الفواتير المدفوعة جزئيا'])
            ->datasets([
                [
                    "label" => "فواتير المدفوعة",
                    'backgroundColor' => ['rgb(44,189,142)'],
                    'data' => [$percent1]
                ],
                [
                    "label" => "",
                    'backgroundColor' => ['white'],
                    'borderColor' => 'white',
                    'data' => [0],
                ],
                [
                    "label" => "الفواتير الغير المدفوعة",
                    'backgroundColor' => ['rgb(248,93,118)'],
                    'data' => [$percent2]
                ],
                [
                    "label" => "",
                    'backgroundColor' => ['white'],
                    'borderColor' => 'white',
                    'data' => [0]
                ],
                [
                    "label" => "الفواتيرالمدفوعة جزئيا",
                    'backgroundColor' => ['rgb(243,133,67)'],
                    'data' => [$percent3]
                ]
            ])
            ->options([]);


        $chartjs_2 = app()->chartjs
            ->name('pieChartTest')
            ->type('pie')
            ->size(['width' => 400, 'height' => 200])
            ->labels(['فواتير المدفوعة', 'الفواتير الغير المدفوعة', 'الفواتير المدفوعة جزئيا'])
            ->datasets([
                [
                    'backgroundColor' => ['#26b687', '#f86079', '#f38745'],
                    'hoverBackgroundColor' => ['#049868', '#f84865', '#f76e30'],
                    'data' => [$percent1, $percent2, $percent3]
                ]
            ])
            ->options([]);

        return view('home', compact('chartjs', 'chartjs_2'));
    }
}
