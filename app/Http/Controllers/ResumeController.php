<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ResumeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $resumes = auth()->user()->resumes;
        
        return view('resumes.index', compact('resumes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data= 'test';
        return view('resumes.create', ['data' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth()->user();
        $resume = $user->resumes()->where('title', $request->title)->first();
            if($resume){
                return back()
                    ->withErrors(['title'=> 'You already have a resume with this name'])
                    ->withInput(['title' => $request->title]);
            }
            $resume= $user->resumes()->create([
            'title'=> $request['title'],
            'name' => $user->name,
            'email' => $user->email,
        ]);
        return redirect()->route('resumes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Resume  $resume
     * @return \Illuminate\Http\Response
     */
    public function show(Resume $resume)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Resume  $resume
     * @return \Illuminate\Http\Response
     */
    public function edit(Resume $resume)
    {
        //$resume->auth()->user()->resumes()->where('id', $request->resume);
        
        return view('resumes.edit', compact('resume'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Resume  $resume
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Resume $resume)
    {
        $data = $request->validate([
            'name'=>'required|string',
            'email'=> 'required|email',
            'website' => 'nullable|url',
            'picture' => 'nullable|image',
            'about'=> 'nullable|string',
            'title'=> Rule::unique('resumes')->where(function($query) use ($resume){
                return $query->where('user_id', $resume->user->id);
            })->ignore($resume->id)
        ]);
        dd($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Resume  $resume
     * @return \Illuminate\Http\Response
     */
    public function destroy(Resume $resume)
    {
        //
    }
}
