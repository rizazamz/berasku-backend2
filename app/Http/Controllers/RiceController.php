<?php

namespace App\Http\Controllers;

use App\Http\Requests\RiceRequest;
use App\Models\Rice;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $rice = Rice::paginate(10);

        return view('rice.index', [
            'rice' => $rice
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('rice.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(RiceRequest $request)
    {
        $data = $request->all();

        $data['picturePath'] = $request->file('picturePath')->store('assets/rice', 'public');

        Rice::create($data);

        return redirect()->route('rice.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Rice  $rice
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show(Rice $rice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Rice  $rice
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Rice $rice)
    {
        return view('rice.edit',[
            'item' => $rice
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Rice  $rice
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Rice $rice)
    {
        $data = $request->all();

        if($request->file('picturePath'))
        {
            $data['picturePath'] = $request->file('picturePath')->store('assets/rice', 'public');
        }

        $rice->update($data);

        return redirect()->route('rice.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rice  $rice
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Rice $rice)
    {
        $rice->delete();

        return redirect()->route('rice.index');
    }
}