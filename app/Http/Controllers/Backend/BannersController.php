<?php
/**
 * Created by NiNaCoder.
 * Date: 2019-05-25
 * Time: 09:01
 */

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use View;
use App\Models\Banner;
use Image;
use Cache;

class BannersController
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index(Request $request)
    {
        $banners = Banner::paginate(20);

        return view('backend.banners.index')->with('banners', $banners);
    }

    public function delete()
    {
        Banner::where('id', '=', $this->request->route('id'))->delete();
        return redirect()->back()->with('status', 'success')->with('message', 'banners successfully deleted!');
    }

    public function add()
    {
        return view('backend.banners.form');
    }

    public function addPost()
    {
        $this->request->validate([
            'banner_tag' => 'required|string|alpha_dash|regex:/^[a-z0-9_]+$/|min:4|max:30',
            'description' => 'required|string',
            'started_at' => 'nullable|date_format:Y/m/d H:i',
            'ended_at' => 'nullable|date_format:Y/m/d H:i|after:' . Carbon::now(),
            'code' => 'nullable|string',
        ]);

        $banner = new Banner();

        $banner->banner_tag = $this->request->input('banner_tag');
        $banner->description= $this->request->input('description');

        if($this->request->input('started_at'))
        {
            $banner->started_at = Carbon::parse($this->request->input('started_at'));
        }

        if($this->request->input('type') && intval($this->request->input('type')) > 0)
        {
            $banner->type = intval($this->request->input('type'));
            $banner->addMedia($this->request->file('file')->getPathName())->usingFileName($this->request->file('file')->getClientOriginalName(), PATHINFO_FILENAME)->toMediaCollection('file');
        }

        if($this->request->input('ended_at'))
        {
            $banner->ended_at = Carbon::parse($this->request->input('ended_at'));
        }

        $banner->approved = $this->request->input('disabled') ? 0 : 1;
        $banner->code = $this->request->input('code');
        $banner->save();

        Cache::forget('banners');

        return redirect()->route('backend.banners')->with('status', 'success')->with('message', 'Banner successfully added!');
    }

    public function edit()
    {
        $banner = Banner::withoutGlobalScopes()->findOrFail($this->request->route('id'));
        return view('backend.banners.form')
            ->with('banner', $banner);
    }

    public function editPost()
    {
        $this->request->validate([
            'banner_tag' => 'required|string|alpha_dash|regex:/^[a-z0-9_]+$/|min:4|max:30',
            'description' => 'required|string',
            'started_at' => 'nullable|date_format:Y/m/d H:i',
            'ended_at' => 'nullable|date_format:Y/m/d H:i|after:' . Carbon::now(),
            'code' => 'nullable|string',
        ]);

        $banner = Banner::findOrFail($this->request->route('id'));

        $banner->banner_tag = $this->request->input('banner_tag');
        $banner->description= $this->request->input('description');

        if($this->request->input('type') && intval($this->request->input('type')) > 0)
        {
            $banner->type = intval($this->request->input('type'));
            $banner->clearMediaCollection('file');
            $banner->addMedia($this->request->file('file')->getPathName())->usingFileName($this->request->file('file')->getClientOriginalName(), PATHINFO_FILENAME)->toMediaCollection('file');
        } else {
            $banner->type = 0;
        }

        if($this->request->input('started_at'))
        {
            $banner->started_at = Carbon::parse($this->request->input('started_at'));
        }

        if($this->request->input('ended_at'))
        {
            $banner->ended_at = Carbon::parse($this->request->input('ended_at'));
        }

        $banner->approved = $this->request->input('disabled') ? 0 : 1;
        $banner->code = $this->request->input('code');
        $banner->save();

        Cache::forget('banners');

        return redirect()->route('backend.banners')->with('status', 'success')->with('message', 'Banner successfully edited!');
    }

    public function disable()
    {
        $banner = Banner::findOrFail($this->request->route('id'));
        $banner->approved = ! $banner->approved;
        $banner->save();

        Cache::forget('banners');
        return redirect()->route('backend.banners')->with('status', 'success')->with('message', 'Banner successfully edited!');
    }
}