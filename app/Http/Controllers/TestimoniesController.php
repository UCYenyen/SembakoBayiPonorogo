<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Testimony;
class TestimoniesController extends Controller
{
    public function show(Testimony $Testimony)
    {
        return view('admin.testimonies.show', compact('Testimony'));
    }
    public function create()
    {
        return view('admin.testimonies.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'rating' => 'required|integer|min:1|max:5',
            'description' => 'required|string',
            'image_url' => 'nullable|url',
        ]);

        Testimony::create($request->all());

        return redirect()->route('admin.testimonies.index')->with('success', 'Testimony created successfully!');
    }
    public function edit(Testimony $Testimony)
    {
        return view('testimonies.edit', compact('Testimony'));
    }
    public function update(Request $request, Testimony $Testimony)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'description' => 'required|string',
            'image_url' => 'nullable|url',
        ]);

        $Testimony->update($request->all());

        return redirect()->route('testimonies.index')->with('success', 'Testimony updated successfully!');
    }
}
