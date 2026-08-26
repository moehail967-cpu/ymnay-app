<?php

namespace Modules\SocialAuth\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SocialAuthSettingsController extends Controller
{
    public function settings()
    {
        return view('socialauth::admin.settings');
    }

    public function update(Request $request)
    {
        $request->validate([
            'google_client_id'     => 'required|string',
            'google_client_secret' => 'required|string',
        ]);

        update_static_option('google_client_id',     $request->google_client_id);
        update_static_option('google_client_secret', $request->google_client_secret);
        update_static_option('google_login_enable',  $request->has('google_login_enable') ? 'on' : null);

        return back()->with(['msg' => __('Social Auth settings updated successfully.'), 'type' => 'success']);
    }
}
