<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FavoriteController extends Controller
{
    public function favoriteTeamMember(Request $request) {
        $user = $request -> input('user');
        $favorite = $request -> input('favorite');
        $created = Carbon::now()->toDateTimeString();
        $updated = Carbon::now()->toDateTimeString();

        $current = DB::table('favorites')->where('user', '=', $user)->where('favorite', '=', $favorite)->first();
        if ($current) {
            return response() -> json(['message' => 'Team member already a favorite.']);
        }
        DB::table('favorites')->insert(
            ['user' => $user, 'favorite' => $favorite, 'created_at' => $created, 'updated_at' => $updated]
        );

        return response() -> json(['message' => 'Team member favorited.'], 200);
    }

    public function unFavoriteTeamMember(Request $request) {
        $user = $request -> input('user');
        $favorite = $request -> input('favorite');
        $current = DB::table('favorites')->where('user', '=', $user)->where('favorite', '=', $favorite)->delete();

        return response() -> json(['message' => 'Team member removed from favorites.'], 200);

    }

    public function getFavorites($id) {
        $favorites = DB::table('favorites')->where('user', '=', $id)->pluck('favorite');

        return response() -> json($favorites, 200);
    }

    public function getFavoriteEmails($id) {
        $favorites = DB::table('favorites')->join('users', 'favorites.favorite', '=', 'users.id')
        ->where('favorites.user', '=', $id)->pluck('email');
        return $favorites;
    }
}
