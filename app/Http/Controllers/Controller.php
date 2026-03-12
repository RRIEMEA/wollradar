<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function quickCreateRedirectTarget(Request $request, string $fallbackRoute): string
    {
        $redirectTo = trim((string) $request->input('redirect_to', ''));

        if ($redirectTo !== '') {
            $appOrigin = rtrim(url('/'), '/');
            $configuredOrigin = rtrim((string) config('app.url'), '/');

            if (Str::startsWith($redirectTo, '/')) {
                return $appOrigin . $redirectTo;
            }

            foreach (array_filter([$appOrigin, $configuredOrigin]) as $origin) {
                if ($redirectTo === $origin || Str::startsWith($redirectTo, [$origin . '/', $origin . '#'])) {
                    return $redirectTo;
                }
            }
        }

        return route($fallbackRoute);
    }

    protected function quickCreateValidationRedirect(
        Request $request,
        string $fallbackRoute,
        Validator $validator,
        string $errorBag,
        string $openModal
    ): RedirectResponse {
        return redirect()
            ->to($this->quickCreateRedirectTarget($request, $fallbackRoute))
            ->withErrors($validator, $errorBag)
            ->withInput()
            ->with('quick_add_open', $openModal);
    }

    protected function quickCreateSuccessRedirect(
        Request $request,
        string $fallbackRoute,
        string $status,
        string $selectField,
        int $selectId
    ): RedirectResponse {
        return redirect()
            ->to($this->quickCreateRedirectTarget($request, $fallbackRoute))
            ->with('status', $status)
            ->with('quick_add_selected', [$selectField => $selectId]);
    }
}
