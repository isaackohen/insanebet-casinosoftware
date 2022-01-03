<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Settings;
use App\Utils\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function get()
    {
        return APIResponse::success([
            'mutable' => Settings::where('internal', '!=', true)->where('hidden', '!=', true)->orWhere('cat', '=', null)->orWhere('cat', 'general')->get()->toArray(),
            'immutable' => Settings::where('internal', true)->where('hidden', '!=', true)->where('cat', '=', null)->get()->toArray(),
            'bonus' => Settings::where('internal', '!=', true)->where('cat', 'bonus')->where('hidden', '!=', true)->get()->toArray(),
            'global' => [
                [
                    '_id' => '1',
                    'cat' => 'global',
                    'name' => 'nowpayments_apikey',
                    'value' => config('settings.nowpayments_id'),
                    'hidden' => false,
                    'internal' => true,
                    'updated_at' => '2021-10-16T16:22:48.008000Z',
                    'created_at' => '2021-02-13T20:12:36.772000Z',
                ],
                [
                    '_id' => '2',
                    'cat' => 'global',
                    'name' => 'dkapi_apikey',
                    'value' => config('settings.api_key'),
                    'hidden' => false,
                    'internal' => true,
                    'updated_at' => '2021-10-16T16:22:48.008000Z',
                    'created_at' => '2021-02-13T20:12:36.772000Z',
                ],
                [
                    '_id' => '3',
                    'cat' => 'global',
                    'name' => 'chaingateway_apikey',
                    'value' => config('settings.chaingateway_apikey'),
                    'hidden' => false,
                    'internal' => true,
                    'updated_at' => '2021-10-16T16:22:48.008000Z',
                    'created_at' => '2021-02-13T20:12:36.772000Z',
                ],
                [
                    '_id' => '4',
                    'cat' => 'global',
                    'name' => 'chaingateway_password',
                    'value' => config('settings.chaingateway_password'),
                    'hidden' => false,
                    'internal' => true,
                    'updated_at' => '2021-10-16T16:22:48.008000Z',
                    'created_at' => '2021-02-13T20:12:36.772000Z',
                ],
            ],
        ]);
    }

    public function create(Request $request)
    {
        Settings::create(
            [
                'name' => request('key'),
                'cat' => request('cat'),
                'description' => request('description'),
                'hidden' => false,
                'internal' => false,
                'value' => null,
            ]
        );

        return APIResponse::success();
    }

    public function edit(Request $request)
    {
        Settings::where('name', request('key'))->first()->update([
            'value' => request('value') === 'null' ? null : request('value'),
        ]);
        Cache::forget('settings:'.request('key'));

        return APIResponse::success();
    }

    public function remove(Request $request)
    {
        Settings::where('name', request('key'))->delete();

        return APIResponse::success();
    }
    public function currencyExtraSettings(Request $request)
    {
        return APIResponse::success([
            [
                'name' => 'withdraw_limit_daily',
                'value' => Settings::get('withdraw_limit_daily', 100, true),
            ],
            [
                'name' => 'withdraw_count_daily',
                'value' => Settings::get('withdraw_count_daily', 0, true),
            ],
            [
                'name' => 'withdraw_limit_3hrs',
                'value' => Settings::get('withdraw_limit_3hrs', 300, true),
            ],
            [
                'name' => 'withdraw_count_3hrs',
                'value' => Settings::get('withdraw_count_3hrs', 0, true),
            ],
        ]);
    }

    public function telegramSettings(Request $request)
    {
        return APIResponse::success([
            [
                'name' => 'telegram_public_notifications',
                'value' => Settings::get('telegram_public_notifications', 0, true),
            ],
            [
                'name' => 'telegram_public_channel',
                'value' => Settings::get('telegram_public_channel', -1001770969921, true),
            ],
            [
                'name' => 'telegram_internal_notifications',
                'value' => Settings::get('telegram_internal_notifications', 0, true),
            ],
            [
                'name' => 'telegram_internal_channel',
                'value' => Settings::get('telegram_internal_channel', -1001770969921, true),
            ],
        ]);
    }

    public function botSettings(Request $request)
    {
        return APIResponse::success([
            [
                'name' => 'create_new_bot_every_ms',
                'value' => Settings::get('create_new_bot_every_ms', 20000, true),
            ],
            [
                'name' => 'hidden_bets_probability',
                'value' => Settings::get('hidden_bets_probability', 20, true),
            ],
            [
                'name' => 'hidden_profile_probability',
                'value' => Settings::get('hidden_profile_probability', 20, true),
            ],
            [
                'name' => 'min_amount_of_games_from_one_bot',
                'value' => Settings::get('min_amount_of_games_from_one_bot', 20, true),
            ],
            [
                'name' => 'max_amount_of_games_from_one_bot',
                'value' => Settings::get('max_amount_of_games_from_one_bot', 50, true),
            ],
            [
                'name' => 'min_delay_between_games_from_one_bot_ms',
                'value' => Settings::get('min_delay_between_games_from_one_bot_ms', 1000, true),
            ],
            [
                'name' => 'max_delay_between_games_from_one_bot_ms',
                'value' => Settings::get('max_delay_between_games_from_one_bot_ms', 5000, true),
            ],
        ]);
    }

    public function startBot()
    {
        dispatch(new \App\Jobs\Bot\BotScheduler());

        return APIResponse::success();
    }
}
