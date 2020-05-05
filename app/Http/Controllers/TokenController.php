<?php
declare(strict_types=1);
/**
 * TokenController.php
 * Copyright (c) 2020 james@firefly-iii.org
 *
 * This file is part of the Firefly III CSV importer
 * (https://github.com/firefly-iii/csv-importer).
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace App\Http\Controllers;

use App\Services\Spectre\Request\ListCustomersRequest;
use GrumpyDictator\FFIIIApiSupport\Exceptions\ApiHttpException;
use GrumpyDictator\FFIIIApiSupport\Request\SystemInformationRequest;
use GrumpyDictator\FFIIIApiSupport\Response\SystemInformationResponse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

/**
 * Class TokenController
 */
class TokenController extends Controller
{
    /**
     * Check if the Firefly III API responds properly.
     *
     * @return JsonResponse
     */
    public function doValidate(): JsonResponse
    {
        $response = ['result' => 'OK', 'message' => null];
        $uri      = (string) config('spectre.uri');
        $token    = (string) config('spectre.access_token');
        $request  = new SystemInformationRequest($uri, $token);
        try {
            $result = $request->get();
        } catch (ApiHttpException $e) {
            return respnse()->json(['result' => 'NOK', 'message' => $e->getMessage()]);
        }
        // -1 = OK (minimum is smaller)
        // 0 = OK (same version)
        // 1 = NOK (too low a version)

        $minimum = (string) config('spectre.minimum_version');
        $compare = version_compare($minimum, $result->version);
        if (1 === $compare) {
            $errorMessage = sprintf(
                'Your Firefly III version %s is below the minimum required version %s',
                $result->version, $minimum
            );
            $response     = ['result' => 'NOK', 'message' => $errorMessage];
        }
        $message = $this->verifySpectre();
        if (null !== $message) {
            $response = ['result' => 'NOK', 'message' => $message];
        }

        return response()->json($response);
    }

    /**
     * Same thing but not over JSON.
     *
     * @return Factory|RedirectResponse|Redirector|View
     */
    public function index()
    {
        $uri          = (string) config('spectre.uri');
        $token        = (string) config('spectre.access_token');
        $request      = new SystemInformationRequest($uri, $token);
        $errorMessage = 'No error message.';
        $isError      = false;
        $result       = null;
        $compare      = 1;
        $minimum      = '';
        try {
            /** @var SystemInformationResponse $result */
            $result = $request->get();
        } catch (ApiHttpException $e) {
            $errorMessage = $e->getMessage();
            $isError      = true;
        }
        // -1 = OK (minimum is smaller)
        // 0 = OK (same version)
        // 1 = NOK (too low a version)
        if (false === $isError) {
            $minimum = config('spectre.minimum_version');
            $compare = version_compare($minimum, $result->version);
        }
        if (false === $isError && 1 === $compare) {
            $errorMessage = sprintf('Your Firefly III version %s is below the minimum required version %s', $result->version, $minimum);
            $isError      = true;
        }

        // continue with Spectre check.
        if (false === $isError) {
            $message = $this->verifySpectre();
            if (null !== $message) {
                $isError      = true;
                $errorMessage = $message;
            }
        }

        if (false === $isError) {
            return redirect(route('index'));
        }
        $pageTitle = 'Token error';

        return view('token.index', compact('errorMessage', 'pageTitle'));
    }

    /**
     * @return string|null
     */
    private function verifySpectre(): ?string {
        $uri = config('spectre.spectre_uri');
        $appId = config('spectre.spectre_app_id');
        $secret = config('spectre.spectre_secret');
        $request=  new ListCustomersRequest();
    }

}
