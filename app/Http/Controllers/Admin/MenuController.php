<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::orderBy('name')->get();

        return view('admin.menus.index', compact('menus'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:500'],
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        $data['image'] = $request->file('image')->store('menus', 'public');
        $data['is_active'] = true;

        Menu::create($data);

        return back()->with('success', 'Menu gorengan berhasil ditambahkan.');
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'price'     => ['required', 'integer', 'min:500'],
            'image'     => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'is_active' => ['required', 'in:0,1'],
        ]);

        $data['is_active'] = (bool) $data['is_active'];

        if ($request->hasFile('image')) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $data['image'] = $request->file('image')->store('menus', 'public');
        } else {
            unset($data['image']);
        }

        $menu->update($data);

        return back()->with('success', "Menu {$menu->name} berhasil diperbarui.");
    }

    public function destroy(Menu $menu)
    {
        if ($menu->transactionItems()->exists()) {
            $menu->update(['is_active' => false]);

            return back()->with('success', "Menu {$menu->name} dinonaktifkan (sudah pernah ditransaksikan).");
        }

        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }

        $menu->delete();

        return back()->with('success', 'Menu berhasil dihapus.');
    }
}
