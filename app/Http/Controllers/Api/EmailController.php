<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Email;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmailController extends Controller {
    //======================================================================
    // ROUTER METHODS
    //======================================================================

    /**
     * Set the the given email as read.
     *
     * @param Request $request
     * @param Email   $email
     *
     * @return array
     */
    public function getEmailRead(Request $request, Email $email) {
        $email->read_at = $email->read_at ?: Carbon::now();
        $email->save();

        // Creates an empty 1x1 image
        $image = base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');

        return response($image)->header('Content-Type', 'image/gif');
    }
}
