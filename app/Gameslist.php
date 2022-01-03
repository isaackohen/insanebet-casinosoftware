<?php

namespace App;

use App\Games\Kernel\Game;
use App\Games\Kernel\Module\General\HouseEdgeModule;
use App\Modules;
use App\Settings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Jenssegers\Mongodb\Eloquent\Model;

class Gameslist extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'gameslist';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'desc', 'provider', 'd', 'demo', 'category', 'image', 'rtp', 'image_sq'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'json',
    ];

    public static function optimizedList($details = null)
    {
        $list = [];
        $games = self::where('d', '!=', '1')->get();

        if ($details === null) {
            $cachedList = Cache::get('listAll');
            if (! $cachedList) {
                foreach (Game::list() as $game) {
                    $houseEdgeModule = new HouseEdgeModule($game, null, null, null);
                    array_push($list, (object) [
                        'isDisabled' => $game->isDisabled(),
                        'isPlaceholder' => $game->metadata()->isPlaceholder(),
                        'ext' => false,
                        'name' => $game->metadata()->name(),
                        'id' => $game->metadata()->id(),
                        'icon' => $game->metadata()->icon(),
                        'cat' => 'inhouse',
                        'p' => env('MIX_INHOUSEPROVIDER'),
                        'type' => 'local',
                        'houseEdge' => ! Modules::get($game, false)->isEnabled($houseEdgeModule) ? null : floatval(Modules::get($game, false)->get($houseEdgeModule, 'house_edge_option')),
                    ]);
                }
 

                foreach ($games as $game) {


                    /* Hard sq 

                    $imageType = $game->image;
                    if(env('MIX_CDNIMAGERATIO') === 'sq') {
                        $imageType = $game->image_sq;
                    }*/
                        $imageType = $game->image_sq;


                    $cat = $game->category;
                    if($game->id === 'bg_LuckySweets' || $game->id === 'bg_ScrollOfAdventure' || $game->id === 'bg_SlotomonGo' || $game->id === 'bg_Domnitors' || $game->id === 'bg_DomnitorsDeluxe' || $game->id === 'bg_BookOfPyramids' || $game->id === 'bg_BraveViking' || $game->id === 'bg_CherryFiesta' || $game->id === 'bg_DesertTreasure' || $game->id === 'bg_HawaiiCocktails' || $game->id === 'bg_LuckyBlue' || $game->id === 'bg_LuckyLadyClover' || $game->id === 'bg_PrincessOfSky' || $game->id === 'bg_PrincessRoyal' || $game->id === 'bg_WestTown' || $game->id === 'bg_BobsCoffeeShop' || $game->id === 'bg_AztecMagicDeluxe' || $game->id === 'bg_AztecMagic' || $game->id === 'bg_FireLightning' || $game->id === 'bg_Avalon' || $game->id === 'bg_CrazyStarter' || $game->id === 'bg_FantasyPark' || $game->id === 'bg_PlatinumLightningDeluxe' || $game->id === 'bg_PlatinumLightning' || $game->id === 'bg_Baccarat' || $game->id === 'bg_MultihandBlackjack' || $game->id === 'bg_CasinoHoldem' || $game->id === 'bg_TexasHoldem' || $game->id === 'bg_LetItRide' || $game->id === 'bg_EuropeanRoulette') {
                        $cat = 'inhouse';
                    }
                    array_push($list, (object) [
                        'ext' => true,
                        'name' => $game->name,
                        'id' => $game->id,
                        'icon' => $imageType,
                        'cat' => $cat,
                        'p' => $game->provider,
                        'houseEdge' => $game->rtp,
                        'type' => 'external',
                    ]);
                }
                $cachedList = $list;
                Cache::put('listAll', $list, Carbon::now()->addMinutes(120));
            }
        } else {
            $cachedList = Cache::get('cachedList:'.$details);
            if (! $cachedList) {
                $var = 'category_'.$details;
                $category = Settings::get($var);
                foreach ($games as $game) {
                    $imageType = $game->image;
                    if(env('MIX_CDNIMAGERATIO') === 'sq') {
                        $imageType = $game->image_sq;
                    }
                    if (in_array($game['id'], explode(',', $category))) {
                        array_push($list, (object) [
                            'ext' => true,
                            'name' => $game->name,
                            'id' => $game->id,
                            'icon' => $imageType,
                            'cat' => $details,
                            'p' => $game->provider,
                            'houseEdge' => $game->rtp,
                            'type' => 'external',
                        ]);
                    }
                }
                $cachedList = $list;
                Cache::put('cachedList:'.$details, $list, Carbon::now()->addMinutes(120));
            }
        }

        return $cachedList;
    }

    public static function cachedList()
    {
        $cachedList = Cache::get('cachedList');

        if (! $cachedList) {
            $cachedList = self::where('d', '!=', '1')->get();
            Cache::put('cachedList', $cachedList, Carbon::now()->addMinutes(120));
        }

        return $cachedList;
    }
}
