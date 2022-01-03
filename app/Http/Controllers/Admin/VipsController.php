<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateVipRequest;
use App\Utils\APIResponse;
use App\VIPLevels;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VipsController extends Controller
{
    public function get()
    {
        return APIResponse::success(VIPLevels::get()->toArray());
    }

    public function vip(Request $request)
    {
        $vip = VIPLevels::where('_id', $request->id)->first();

        return APIResponse::success([
            'vip' => $vip->toArray(),
        ]);
    }

    public function remove(Request $request)
    {
        VIPLevels::where('_id', $request->get('id'))->delete();

        return APIResponse::success();
    }

    public function create(CreateVipRequest $request)
    {
        $vip = VIPLevels::where('level', $request->level)->first();
        if ($vip) {
            return APIResponse::reject(1, 'Level number is not unique');
        }

        VIPLevels::create([
            'start' => $request->start,
            'level_name' => $request->level_name,
            'level' => $request->level,
            'promocode_bonus' => 0,
            'rake_percent' => 0,
            'faucet_bonus' => 0,
            'fs_bonus' => 0,
            'fs_superspin' => 0,
            'challenges_bonus' => 0,
        ]);

        return APIResponse::success();
    }

    public function save(Request $request)
    {
        VIPLevels::where('_id', $request->id)->first()->update([
            'level_name' => $request->level_name === 'null' ? null : $request->level_name,
            'promocode_bonus' => $request->promocode_bonus === 'null' ? null : $request->promocode_bonus,
            'rake_percent' => $request->rake_percent === 'null' ? null : $request->rake_percent,
            'faucet_bonus' => $request->faucet_bonus === 'null' ? null : $request->faucet_bonus,
            'fs_bonus' => $request->fs_bonus === 'null' ? null : $request->fs_bonus,
            'fs_superspin' => $request->fs_superspin === 'null' ? null : $request->fs_superspin,
            'challenges_bonus' => $request->challenges_bonus === 'null' ? null : $request->challenges_bonus,
        ]);

        return APIResponse::success();
    }
}
