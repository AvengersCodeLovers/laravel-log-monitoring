<?php

namespace AvengersGroup;

use Exception;
use wataridori\ChatworkSDK\ChatworkSDK;
use wataridori\ChatworkSDK\ChatworkRoom;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use wataridori\ChatworkSDK\Exception\RequestFailException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class AbstractHandler extends ExceptionHandler
{
    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        $this->sendExceptionToChatWork($exception);

        parent::report($exception);
    }

        /**
     * Send Exception To ChatWork
     *
     * @param \Exception $exception
     *
     * @return mixed
     */
    protected function sendExceptionToChatWork($exception)
    {
        try {
            // If have more case should use switch case
            if ($exception instanceof ValidationException || $exception instanceof AuthenticationException) {
                return false;
            }
            $message = $this->getTemplateMessage($exception);
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
            throw $ex->getMessage();
        }
    }

    /**
     * Get Template Message Exception
     *
     * @param \Exception $e
     *
     * @return string
     */
    protected function getTemplateMessage($e)
    {
        return '[info][title]Bug in ' . env('APP_ENV') . '[/title][code]Message: ' . $e->getMessage()
        . ' in file ' . $e->getFile()
        . ' line ' . $e->getLine()
        . '[/code][/info]';
    }
}
