<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
    public function show()
    {
        return view("device.add");
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

        return view("device.show", [
            "device" => $device
        ]);
    }
}
