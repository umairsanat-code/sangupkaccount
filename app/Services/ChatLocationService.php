<?php

namespace App\Services;

use App\Enums\UserType;
use App\Http\Resources\Chat\ImageProductCollection;
use Exception;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\ProductImage;
use Google\Cloud\Firestore\Transaction;
use Kreait\Firebase\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use App\Traits\loggerExceptionTrait;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Chat\ChatCollection;
use App\Http\Resources\Chat\ProductCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;


/**
 * ChatService : get data from firestore
 * see @link https://github.com/kreait/laravel-firebase
 * model Inbox/currentUser/receiverUser/orders/order_id/chat/messages/message_id/fields
 */
class ChatLocationService
{
    use loggerExceptionTrait;

    public $database;
    public $refdb;

    public function __construct()
    {
        /* firebase authentication using php admin sdk */
        $factory = (new Factory())
            ->withServiceAccount(Config::get('firebase.projects.app.credentials.file'))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));
        $this->database = app('firebase.firestore')->database();
        $this->refdb = app('firebase.database');
    }


    /*
   * @method JsonResponse getData()
   * @param string $node as collection name
   * @param string  $user uid
   * @param string $role Seller/Buyer
   * @param array $products belong to user
   * @return array of chat and product
   */
    public function messageStatusUpdate($authUid, $receiverUid, $orderId, $ignoreMessageId)
    {
        try {
            $node = 'Inbox';
            $onNode = 'orders';
            $messageStatus = array("NONE", "NOT_RESPONDED", "PENDING");
            $senderMessages = $this->database->collection($node)->document($authUid)
                ->collection($receiverUid)->document($onNode)
                ->collection($orderId)->document('Chat')->collection('Messages')
                ->where('messageType', '=', 'LOCATION')
                ->documents();
            foreach ($senderMessages as $values) {
                if ($ignoreMessageId != $values->id()) {
                    if (in_array($values['requestStatus'], $messageStatus, TRUE)) {
                        $findMessageCollection = $this->database->collection($node)->document($authUid)
                            ->collection($receiverUid)->document($onNode)
                            ->collection($orderId)->document('Chat')->collection('Messages')
                            ->document($values->id());
                        $this->database->runTransaction(function (Transaction $transaction) use ($findMessageCollection) {
                            $transaction->update($findMessageCollection, [
                                ['path' => 'requestStatus', 'value' => "NOT_RESPONDED"]
                            ]);
                        });
                    }
                }
            }
            $receiverMessages = $this->database->collection($node)->document($receiverUid)
                ->collection($authUid)->document($onNode)
                ->collection($orderId)->document('Chat')->collection('Messages')
                ->where('messageType', '=', 'LOCATION')
                ->documents();
            foreach ($receiverMessages as $values) {
                if ($ignoreMessageId != $values->id()) {
                    if (in_array($values['requestStatus'], $messageStatus, TRUE)) {
                        $findMessageCollection = $this->database->collection($node)->document($receiverUid)
                            ->collection($authUid)->document($onNode)
                            ->collection($orderId)->document('Chat')->collection('Messages')
                            ->document($values->id());
                        $this->database->runTransaction(function (Transaction $transaction) use ($findMessageCollection) {
                            $transaction->update($findMessageCollection, [
                                ['path' => 'requestStatus', 'value' => "NOT_RESPONDED"]
                            ]);
                        });
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            /*
           * saving error logs
           */
            $this->saveExceptionLog($e, 'Firebase Location Update error');
            return false;
        }
    }

    public function unreadMessageCount($authUid)
    {
        try {
            $counter = 0;
            $node = 'Inbox';
            $onNode = 'orders';
            $receiverUids = $this->getFirebaseReceiverUids($authUid);
            foreach ($receiverUids as $receiverUid) {
                $orderIds = $this->database->collection($node)->document($authUid)
                    ->collection($receiverUid)->document($onNode)->collections();
                if (!is_null($orderIds)) {
                    foreach ($orderIds as $orderValue) {
                        $orderIdString = $orderValue->id();
                        $unreadMessageCounts = $this->database->collection($node)->document($authUid)
                            ->collection($receiverUid)->document($onNode)
                            ->collection($orderIdString)->document('Chat')->collection('Messages')
                            ->orderBy('time', 'desc')->limit(1)->documents();
                        foreach ($unreadMessageCounts as $lastMessageValue) {
                            $isRead = $lastMessageValue['isMessageRead'];
                            if (!$isRead) {
                                $counter = $counter + 1;
                            }
                        }
                    }
                }
            }
            return $counter;
        } catch (Exception $e) {
            $this->saveExceptionLog($e, 'Firebase Location Update error');
            return 0;
        }
    }

    public function getFirebaseReceiverUids($uid)
    {
        $node = 'Inbox';
        $receiverIds = array();
        $data = $this->database->collection($node)->document($uid)->collections();
        foreach ($data as $receiver) {
            $receiverIds[] = $receiver->id();
        }
        return array_values(array_diff($receiverIds, array("null", "")));
    }
}
