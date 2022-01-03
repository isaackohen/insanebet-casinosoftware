<?php

namespace App\Http\Controllers\Admin;

use App\Games\Kernel\Game;
use App\Games\Kernel\Module\Module;
use App\Http\Controllers\Controller;
use App\Modules;
use App\Utils\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ModuleController extends Controller
{
    public function toggle(Request $request)
    {
        $game = Game::find(request('api_id'));
        $module = Module::find(request('module_id'));
        Modules::get($game, filter_var(request('demo'), FILTER_VALIDATE_BOOLEAN))->toggleModule($module)->save();

        return APIResponse::success();
    }

    public function setValue(Request $request)
    {
        $game = Game::find(request('api_id'));
        $module = Module::find(request('module_id'));
        Modules::get($game, filter_var(request('demo'), FILTER_VALIDATE_BOOLEAN))->set($module, request('option_id'), request('value') ?? '')->save();

        return APIResponse::success();
    }

    public function setData(Request $request)
    {
        $demo = $request->boolean('demo');
        $game = Game::find($request->game);

        $supportedModules = [];

        foreach (Module::modules() as $module) {
            $instance = new $module($game, null, null, null);

            $settings = [];
            foreach ($instance->settings() as $setting) {
                if($setting->id() !== 'house_edge_option' && env('SHOW_GAME_MODULES') === false) {

                } else {
                array_push($settings, [
                    'id' => $setting->id(),
                    'name' => $setting->name(),
                    'description' => $setting->description(),
                    'defaultValue' => $setting->defaultValue(),
                    'type' => $setting->type(),
                    'value' => Modules::get($game, $demo)->get($instance, $setting->id()),
                ]);
                }
            }

            if ($instance->supports()) {
                Log::notice($instance->id());
                if($instance->id() !== 'house_edge' && env('SHOW_GAME_MODULES') === false) {

                } else {
                array_push($supportedModules, [
                    'id' => $instance->id(),
                    'name' => $instance->name(),
                    'description' => $instance->description(),
                    'supports' => $instance->supports(),

                    'isEnabled' => Modules::get($game, $demo)->isEnabled($instance),
                    'settings' => $settings,
                ]);
            }

        }
        }

        return APIResponse::success($supportedModules);
    }
}
