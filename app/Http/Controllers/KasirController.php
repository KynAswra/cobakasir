<?php

namespace App\Http\Controllers;

use App\Models\Menu;

class KasirController extends Controller
{
    public function index()
    {
        $menus = Menu::where('is_active', true)->orderBy('name')->get();

        return view('kasir.index', [
            'menus'        => $menus,
            'cashierName'  => auth()->user()?->name ?? 'Kasir',
        ]);
    }
}
