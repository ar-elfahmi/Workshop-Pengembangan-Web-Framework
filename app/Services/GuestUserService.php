<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GuestUserService
{
    public function createGuestUser(): User
    {
        return DB::transaction(function () {
            $row = DB::table('users')
                ->selectRaw("MAX(CAST(SUBSTRING(name, 7) AS UNSIGNED)) AS max_guest")
                ->where('name', 'like', 'Guest_%')
                ->lockForUpdate()
                ->first();

            $nextNumber = ((int) ($row->max_guest ?? 0)) + 1;
            $guestName = 'Guest_' . str_pad((string) $nextNumber, 7, '0', STR_PAD_LEFT);

            return User::create([
                'name' => $guestName,
                'role' => 'guest',
                'email' => strtolower($guestName) . '_' . Str::lower(Str::random(6)) . '@guest.local',
                'password' => null,
                'provider' => 'guest',
            ]);
        });
    }
}
