<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function index(): Response
    {
        $setting = $this->firstSetting();

        return Inertia::render('Settings/Index', [
            'setting' => [
                'id' => $setting->id,
                'site_name' => $setting->site_name,
                'logo_path' => $setting->logo_path,
                'favicon_path' => $setting->favicon_path,
                'header_content' => $setting->header_content,
                'footer_content' => $setting->footer_content,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'header_content' => ['nullable', 'string'],
            'footer_content' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'image', 'max:1024'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_favicon' => ['nullable', 'boolean'],
        ]);

        $setting = $this->firstSetting();

        $nextLogoPath = $setting->logo_path;
        $nextFaviconPath = $setting->favicon_path;

        if ((bool) ($validated['remove_logo'] ?? false)) {
            $this->deletePublicFile($setting->logo_path);
            $nextLogoPath = null;
        }
        if ((bool) ($validated['remove_favicon'] ?? false)) {
            $this->deletePublicFile($setting->favicon_path);
            $nextFaviconPath = null;
        }

        if ($request->hasFile('logo')) {
            $this->deletePublicFile($setting->logo_path);
            $nextLogoPath = $request->file('logo')->store('site-settings/logo', 'public');
        }
        if ($request->hasFile('favicon')) {
            $this->deletePublicFile($setting->favicon_path);
            $nextFaviconPath = $request->file('favicon')->store('site-settings/favicon', 'public');
        }

        $setting->update([
            'site_name' => $validated['site_name'],
            'header_content' => $validated['header_content'] ?? null,
            'footer_content' => $validated['footer_content'] ?? null,
            'logo_path' => $nextLogoPath,
            'favicon_path' => $nextFaviconPath,
            'updated_by' => $request->user()?->id,
        ]);

        return back()->with('success', 'Da cap nhat cau hinh website.');
    }

    private function firstSetting(): SiteSetting
    {
        return SiteSetting::query()->firstOrCreate(
            ['id' => 1],
            ['site_name' => config('app.name', 'HRM')]
        );
    }

    private function deletePublicFile(?string $path): void
    {
        if (!filled($path)) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}

