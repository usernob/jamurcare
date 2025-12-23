<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DeviceController extends Controller
{
    public function form(): View
    {
        return view("device.add");
    }


    public function show(string $ulid): View
    {
        /** @var User $user */
        $user = Auth::user();

        $device = $user->devices()->where("ulid", $ulid)->first();

        return view("device.show", [
            "device" => $device
        ]);
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            "device-name" => ["required", "ascii", "min:3", "max:255"]
        ]);

        /** @var User $user */
        $user = $request->user();
        $new_ulid = strtolower(Str::ulid()->toString());
        $device = $user->devices()->make([
            'ulid' => $new_ulid,
            'name' => $validated['device-name'],
        ]);
        $device->save();

        return redirect()->route("device.show", ["ulid" => $new_ulid]);
    }
}
