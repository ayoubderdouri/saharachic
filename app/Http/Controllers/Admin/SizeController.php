<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SizeController extends Controller
{
    public function index()
    {
        return view('admin.sizes.index', ['sizes' => Size::orderBy('name')->paginate(20)]);
    }

    public function create()
    {
        return view('admin.sizes.form', ['size' => new Size]);
    }

    public function store(Request $request)
    {
        Size::create($this->validated($request));

        return redirect()->route('admin.sizes.index')->with('status', 'Taille créée.');
    }

    public function edit(Size $size)
    {
        return view('admin.sizes.form', compact('size'));
    }

    public function update(Request $request, Size $size)
    {
        $size->update($this->validated($request, $size));

        return redirect()->route('admin.sizes.index')->with('status', 'Taille mise à jour.');
    }

    public function destroy(Size $size)
    {
        $size->delete();

        return back()->with('status', 'Taille supprimée.');
    }

    private function validated(Request $request, ?Size $size = null): array
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('name'))]);

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('sizes')->ignore($size)],
        ]);
    }
}
