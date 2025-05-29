<?php

namespace App\Http\Controllers;


use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

 /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Feedsmax(Closed Feedback Loop)",
     *      description="Swagger documentation for Feedsmax endpoint initiated in app & web",
     *      @OA\Contact(
     *          email="mohammad.moniruzzaman@brainstation-23.com"
     *      ),
     *     @OA\License(
     *         name="Apache 2.0",
     *     )
     * ),
     * @OA\Components(
     *    @OA\SecurityScheme(
     *        type="http",
     *        description="Use your credential to obtain a token",
     *        name="Authorization",
     *        in="header",
     *        scheme="bearer",
     *        securityScheme="bearerAuth",
     * )
     *)
*/
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     */
    public function info(){
        $info = [
            'application' => "Feedsmax Backend",
            'version' => 'v1',
        ];
        return response()->json($info, Response::HTTP_OK);
    }
}
