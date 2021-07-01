<?php

namespace AvengersGroup;

use Exception;
use Illuminate\Support\Facades\Auth;
use wataridori\ChatworkSDK\ChatworkSDK;
use wataridori\ChatworkSDK\ChatworkRoom;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use wataridori\ChatworkSDK\Exception\RequestFailException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class AbstractHandler
{
    /**
     * Send Exception To ChatWork
     *
     * @param \Exception $exception
     *
     * @return mixed
     */
    public function sendExceptionToChatWork($exception, $request = null)
    {
        try {
            // If have more case should use switch case
            if ($exception instanceof ValidationException
                || $exception instanceof AuthenticationException
                || $exception instanceof NotFoundHttpException
                || $exception instanceof TokenMismatchException
                || $exception instanceof MethodNotAllowedHttpException
                || $exception instanceof ThrottleRequestsException) {
                return false;
            }
            $message = $this->getTemplateMessage($exception, $request);
            ChatworkSDK::setApiKey(config('services.chatwork.api_key'));
            $room = new ChatworkRoom(config('services.chatwork.room_id_sos'));
            $members = $room->getMembers();
            $admins = [];

            foreach ($members as $member) {
                if ($member->role === config('services.chatwork.role.admin')) {
                    array_push($admins, $member);
                }
            }

            return $room->sendMessageToList($admins, $message);
        } catch (RequestFailException $ex) {
            throw $ex;
        }
    }

    /**
     * Get Template Message Exception
     *
     * @param  \Exception  $e
     * @param  null|\Illuminate\Http\Request  $r
     *
     * @return string
     */
    protected function getTemplateMessage($e, $r)
    {
        $email = Auth::check() ? Auth::user()->email : 'N/A';
        $message = $e->getMessage() ? $e->getMessage() : 'N/A';

        if ($r) {
            return '[info][title]Bug in ' . config('app.env') . '[/title][code]Message: ' . $message . '
            In file: ' . $e->getFile() . '
            Line: ' . $e->getLine() . '
            Request uri: ' . $r->getRequestUri() . '
            Method: ' . $r->getMethod() . '
            Previous uri: ' . str_replace(url('/'), '', url()->previous()) . '
            User agent: ' . $r->header('User-Agent') . '
            User id: ' . $email
            . ' [/code][/info]';
        }

        return '[info][title]Bug in ' . config('app.env') . '[/title][code]Message: ' . $message . '
        In file: ' . $e->getFile() . '
        Line: ' . $e->getLine() . '
        Email: ' . $email
        . ' [/code][/info]';
    }
}
