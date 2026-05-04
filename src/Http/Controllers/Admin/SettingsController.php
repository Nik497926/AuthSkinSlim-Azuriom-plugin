<?php

namespace Azuriom\Plugin\AuthSkinSlim\Http\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('auth-skin-slim::admin.settings', [
            'relaxSkinApiDimensions' => filter_var(
                setting('auth-skin-slim.relax_skin_api_dimensions', '0'),
                FILTER_VALIDATE_BOOLEAN
            ),
            'skinApiEnabled' => plugins()->isEnabled('skin-api'),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'relax_skin_api_dimensions' => 'nullable|boolean',
        ]);

        Setting::updateSettings(
            'auth-skin-slim.relax_skin_api_dimensions',
            $request->boolean('relax_skin_api_dimensions') ? '1' : '0'
        );

        return redirect()->route('auth-skin-slim.admin.settings')
            ->with('success', trans('admin.settings.updated'));
    }
}
