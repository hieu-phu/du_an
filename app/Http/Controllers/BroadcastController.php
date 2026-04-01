<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BroadcastController extends Controller
{
    /**
     * Danh sách subdomain hợp lệ
     */
    private const VALID_SUBDOMAINS = [
        'ban-hang',
        'kho',
        'thu-chi',
        'mua-hang',
        'main'
    ];

    /**
     * Xác thực kênh user cá nhân theo subdomain
     */
    public function authorizeUserChannel($user, $id, $sub)
    {
        if (!$this->checkAuthenticated($user)) {
            return false;
        }

        if (!$this->checkUserId($user, $id)) {
            return false;
        }

        if (!$this->checkSubdomain($user, $sub)) {
            return false;
        }

        if (!$this->checkOrigin($user)) {
            return false;
        }

        return true;
    }

    /**
     * Kiểm tra user đã đăng nhập
     */
    private function checkAuthenticated($user)
    {
        if (!$user) {
            Log::warning('Broadcast authentication failed: User not authenticated');
            return false;
        }
        return true;
    }

    /**
     * Kiểm tra user id có khớp không
     */
    private function checkUserId($user, $id)
    {
        if ($user->id !== (int) $id) {
            Log::warning('Broadcast authentication failed: User ID mismatch', [
                'user_id' => $user->id,
                'requested_id' => $id
            ]);
            return false;
        }
        return true;
    }

    /**
     * Kiểm tra subdomain có hợp lệ không
     */
    private function checkSubdomain($user, $subdomain)
    {
        if (!in_array($subdomain, self::VALID_SUBDOMAINS)) {
            Log::warning('Broadcast authentication failed: Invalid subdomain', [
                'user_id' => $user->id,
                'subdomain' => $subdomain,
                'valid_subdomains' => self::VALID_SUBDOMAINS
            ]);
            return false;
        }
        return true;
    }

    /**
     * Kiểm tra origin có hợp lệ không
     */
    private function checkOrigin($user)
    {
        $request = request();
        $origin = $request->header('Origin');
        $referer = $request->header('Referer');

        $statefulDomains = config('sanctum.stateful');
        $allowedDomains = is_array($statefulDomains)
            ? $statefulDomains
            : array_map('trim', explode(',', $statefulDomains));

        if (!$origin) {
            Log::warning('Broadcast authentication failed: No origin header', [
                'user_id' => $user->id,
                'ip' => $request->ip()
            ]);
            return false;
        }

        $originHost = parse_url($origin, PHP_URL_HOST);
        $originPort = parse_url($origin, PHP_URL_PORT);
        $originHostWithPort = $originHost . ($originPort ? ':' . $originPort : '');

        foreach ($allowedDomains as $allowedDomain) {
            if ($originHost === $allowedDomain || $originHostWithPort === $allowedDomain) {
                return true;
            }
        }

        Log::warning('Broadcast authentication failed: Invalid origin', [
            'user_id' => $user->id,
            'origin' => $origin,
            'referer' => $referer,
            'ip' => $request->ip(),
            'allowed_domains' => $allowedDomains
        ]);

        return false;
    }
}
