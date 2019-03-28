<?php
/**
 * Created by PhpStorm.
 * User: shadi
 * Date: 3/23/17
 * Time: 2:11 PM
 */

namespace App\Http\Controllers;


use App\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as illuminateResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Route;
use Request;

class ApiController extends Controller
{
    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @param mixed $statusCode
     * @return this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }


    public function resourceCreated($message = "Resource Created.")
    {
        return $this->setStatusCode(illuminateResponse::HTTP_CREATED)->respond(['message' => $message]);
    }
    public function resourceUpdated($message = "Resource Updated.")
    {
        return $this->setStatusCode(illuminateResponse::HTTP_CREATED)->respond(['message' => $message]);
    }
    public function resourceDeleted($message = "Resource Deleted.")
    {
        return $this->setStatusCode(illuminateResponse::HTTP_CREATED)->respond(['message' => $message]);
    }
    public function respondWithPagination($paginator, $data){
        return $this->respond([
            'data' => $data,
            'paginator' => [
                'total_count' => $paginator->toArray()['total'],
                'current_page' => $paginator->toArray()['current_page'],
                'total_pages' => ceil($paginator->toArray()['total'] / 5),
                "next_page_url" => $paginator->toArray()['next_page_url'],
                "path" =>  $paginator->toArray()['path'],
            ]
        ]);
    }


    public function respondNotFound($message = "Not Found!")
    {
        return $this->setStatusCode(illuminateResponse::HTTP_NOT_FOUND)->respondWithError($message);

    }

    public function parameterInvalid($message = "Invalid parameters")
    {
        return $this->setStatusCode(illuminateResponse::HTTP_UNPROCESSABLE_ENTITY)->respondWithError($message);
    }

    public function respondWithInternalError($message = "Internal Error!")
    {
        return $this->setStatusCode(illuminateResponse::HTTP_INTERNAL_SERVER_ERROR)->respondWithError($message);

    }
    public function unAuthorizedAccess($message = "ِYou don't have access to this resource")
    {
        return $this->setStatusCode(illuminateResponse::HTTP_FORBIDDEN)->respondWithError($message);

    }

    public function respond($data, $headers = [])
    {
        if(Route::getCurrentRoute()->getPrefix() == '/api/v1')
            return Response::json($data, $this->getStatusCode(), $headers);


        if(Request::segment(1) == 'categories')
        {
            return view('categories')->with($data);
        }
        if(Request::segment(1) == 'users')
        {
            return view('users')->with($data);
        }
    }

    public function respondWithError($message)
    {
        return $this->respond(["error" => ['message' => $message], 'status_code' => $this->getStatusCode()]);
    }

    public function addAndCheckScore(User $user, $scoreType, $scoreValue, $desc)
    {
        $user_id = $user->id;
        $type_id = $scoreType;
        $amount = $scoreValue;
        $type = ScoreType::find($type_id);
        if (!$type) {
            return Redirect::back()->withErrors("Type does not exist.");
        }


        $score = Score::create(['user_id' => $user_id, 'type_id' => $type_id, 'amount' => $amount
            , 'desc' => $desc]);
        if ($scoreType == 1) {
            // safir type increasing score
            $user->safir_score = $user->safir_score + $scoreValue;
            $user->save();
        }
        if ($scoreType == 2) {
            // safir type increasing score
            $user->reader_score = $user->reader_score + $scoreValue;
            $user->save();
        }
        $this->checkForBadge($user);

    }

    private function checkForBadge(User $user)
    {
        $operations = Config::get('constants.operation_types');
        $badges = $user->availableBadge();
        foreach ($badges as $badge){
            if ($badge->operation_type == 1){
                if ($badge->type_id == 1) {
                    // safir type increasing score

                    if ($user->safir_score > $badge->value){
                        DB::table('badge_user')->insert(
                            ['user_id' => $user->id, 'badge_id' => $badge->id]
                        );
                    }

                }
                if ($badge->type_id == 2) {
                    // safir type increasing score
                    if ($user->reader_score > $badge->value){
                        DB::table('badge_user')->insert(
                            ['user_id' => $user->id, 'badge_id' => $badge->id]
                        );
                    }

                }


            }
        }
    }
}