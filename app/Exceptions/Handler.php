<?php

namespace Corp\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {

        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {

        if($this->isHttpException($e)) {
            $statusCode = $e->getStatusCode();

            switch($statusCode) {
                case '404' :

                    $obj = new \Corp\Http\Controllers\SiteController(new \Corp\Repositories\MenusRepository(new \Corp\Menu));

                    $navigation = view(env('THEME').'.navigation')->with('menu',$obj->getMenu())->render();

                    \Log::alert('Страница не найдена - '. $request->url());

                    return response()->view(env('THEME').'.404',['bar' => 'no','title' =>'Страница не найдена','navigation'=>$navigation]);
            }
        }

        return parent::render($request, $e);




    }
}
