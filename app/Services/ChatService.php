<?php
/*
 * This file is responsible to get data from firestore
 */

namespace App\Services;

use App\Enums\TrackingStatusEnum;
use App\Enums\UserType;
use App\Http\Resources\Api\Order\OrderDetailResource;
use App\Http\Resources\Api\Order\UserOrderResource;
use App\Http\Resources\Chat\ImageProductCollection;
use App\Http\Resources\Chat\OrderChatResource;
use App\Models\UserBlock;
use Exception;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\ProductImage;
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
class ChatService
{
    use loggerExceptionTrait;

    /**
     * database : firestore database
     *
     * @var mixed
     */
    public $database;
    /**
     * refdb : reference database
     *
     * @var mixed
     */
    public $refdb;

    /**
     * node : starting collection of firestore
     *
     * @var mixed
     */
    protected string $node;
    /**
     * uid : currently login user  uid
     *
     * @var mixed
     */
    protected string $uid;
    /**
     * page : pagination page number
     *
     * @var int
     */
    protected int $page;
    /**
     * limit : pagination records limit
     *
     * @var int
     */
    protected int $limit;

    /**
     * on : the module name where user doing chat in our case is orders
     *
     * @var mixed
     */
    protected string $on;
    /**
     * responseBag : array of response returned by firebase
     *
     * @var array
     */
    protected array $responseBag;


    /**
     * __construct :  initializing the object's properties upon creation of the object
     *
     * @return void
     */
    public function __construct()
    {
        /* firebase authentication */
        $this->init();
        /* setting default values */
        $this->withDefault();
    }

    /**
     * init : load third party  authentications
     *
     * @return void
     */
    protected function init()
    {

        $factory = (new Factory())
            ->withServiceAccount(Config::get('firebase.projects.app.credentials.file'))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));
    }

    /**
     * withDefault : set values for class properties
     *
     * @return void
     */
    protected function withDefault()
    {

        $this->database = app('firebase.firestore')->database();
        $this->refdb = app('firebase.database');
        $this->limit = env('DEFAULT_PAGINATION_LIMIT');
        $this->page = 1;
        $this->node = 'Inbox';
        $this->uid = auth()->user()->uid;
        $this->on = 'orders';
    }

    /**
     * node : set the node value of firestore
     *
     * @param mixed $node
     * @return void
     */
    public function node($node = null)
    {
        $this->node = $node ? $node : 'Inbox';
        return $this;
    }

    /**
     * on : set the document value of path
     *
     * Inbox/currentUser/reciverUser/orders
     * @param mixed $node
     * @return void
     */
    public function on($on = null)
    {
        $this->on = $on ? $on : 'orders';
        return $this;
    }

    /**
     * uid : set the current user uid
     *
     * @param mixed $uid
     * @return void
     */
    public function uid($uid = null)
    {
        $this->uid = $uid ? $uid : auth()->user()->uid;
        return $this;
    }

    /**
     * withPaginate : set the pagination value
     *
     * @param int $limit
     * @param int $page
     * @return void
     */
    public function withPagination($limit = null, $page = null)
    {
        $this->limit = $limit ? $limit : env('DEFAULT_PAGINATION_LIMIT');
        $this->page = $page ? $page : 1;
        return $this;
    }

    /**
     * getReceiver : store all receiver ids into array
     *
     * Inbox/currentUser/reciverUser
     * @return array
     */
    public function getFirebaseReceiverUids(): array
    {
        $receiverIds = array();
        $data = $this->database->collection($this->node)->document($this->uid)->collections();
        foreach ($data as $receiver) {
            $receiverIds[] = $receiver->id();
        }
        return array_values(array_diff($receiverIds, array("null", "")));
    }


    /**
     * hasOrder : check if request have order string
     *
     * @param mixed $orderString
     * @return bool
     */
    public function hasOrder($orderString): bool
    {
        $type = explode("_", $orderString);
        $typeName = reset($type);
        if ($typeName == "Order") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * getOrderId: expolode the string and get the numeric value of oderId
     *
     * @param mixed $orderString
     * @return string
     */
    public function getOrderId($orderString): string
    {
        $id = explode("_", $orderString);
        return end($id);
    }


    /**
     * paginate: paginate the collection
     *
     * @param mixed $data
     * @return LengthAwarePaginator
     */
    public function paginate($data): LengthAwarePaginator
    {
        $page = $this->page ?: (Paginator::resolveCurrentPage() ?: 1);
        $data = $data instanceof Collection ? $data : Collection::make($data);
        return new LengthAwarePaginator($data->forPage($page, $this->limit), $data->count(), $this->limit, $page, []);
    }

    /**
     * getProductCollection
     *
     * @param mixed $ids
     * @return ResourceCollection
     */
    public function getProductCollection(array $ids): ResourceCollection
    {
        $products = Product::with('imageProducts')->whereKey($ids)->get();
        return new ProductCollection($products);
    }

    /**
     * getReviews
     *
     * @param mixed $id
     * @param mixed $userType
     * @return void
     */
    public function getReviewsCount($id, $userType)
    {
        if ($userType == UserType::BUYER) {
            $count = Order::where('buyerId', $id)->whereNotNull('buyerFeedback')->count('buyerFeedback');
            return (float)$count;
        } elseif ($userType == UserType::SELLER) {
            $count = Order::where('sellerId', $id)->whereNotNull('sellerFeedback')->count('sellerFeedback');
            return (float)$count;
        } else {
            return 0;
        }
    }

    /**
     * getRatingsAvg
     *
     * @param mixed $id
     * @param mixed $userType
     * @return void
     */
    public function getRatingsAvg($id, $userType)
    {
        if ($userType == UserType::BUYER) {
            $avg = Order::where('buyerId', $id)->whereNotNull('buyerRating')->avg('buyerRating');
            return (float)$avg;
        }
        if ($userType == UserType::SELLER) {
            $avg = Order::where('sellerId', $id)->whereNotNull('sellerRating')->avg('sellerRating');
            return (float)$avg;
        } else {
            return 0;
        }
    }


    /**
     * getOrderCollections: return order strings  like order_89
     *
     * Inbox/currentUser/reciverUser/orders/order_id
     * @param mixed $receiverUserData
     * @return object
     */
    public function getOrderCollections($receiverUserData): object
    {
        $orders = $this->database->collection($this->node)->document($this->uid)
            ->collection($receiverUserData->uid)->document($this->on)->collections();
        return $orders;
    }

    /**
     * getLastMessage : return lastMessage from firebase
     * Inbox/currentUser/reciverUser/orders/order_id/chat/messages/message_id/fields
     * @param mixed $receiverUserData
     * @param mixed $orderValue
     * @return object
     */

    public function getLastMessage($receiverUserData, $orderValue): object
    {

        $lastMesaage = $this->database->collection($this->node)->document($this->uid)
            ->collection($receiverUserData->uid)->document($this->on)->collection($orderValue->id())
            ->document('Chat')->collection('Messages')->orderBy('time', 'desc')->limit(1)->documents();
        return $lastMesaage;
    }

    /**
     * get : get data from firestore and return all recent messages
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function get(): JsonResponse
    {
        try {
            /*
             * getting receiver uid
             */
            $receiverUIDs = $this->getFirebaseReceiverUids();
            if (!is_null($receiverUIDs)) {
                $getReceiverUsers = User::whereIn('uid', $receiverUIDs)->get();
                if (!$getReceiverUsers->isEmpty()) {

                    foreach ($getReceiverUsers as $receiverUserData) {

                        /* getting order string e.g order_89 */
                        $orders = $this->getOrderCollections($receiverUserData);

                        if (!is_null($orders)) {

                            foreach ($orders as $orderValue) {

                                $orderIdString = $this->hasOrder($orderValue->id());
                                $orderId = $this->getorderId($orderValue->id());

                                if ($orderIdString && $orderId) {
                                    /*
                                    * Fetching last message based on type with order id
                                    */
                                    $lastMessageData = $this->getLastMessage($receiverUserData, $orderValue);
                                   // Message unnread count
                                    $messageUnReadCount = $this->database->collection($this->node)->document($this->uid)
                                        ->collection($receiverUserData->uid)->document($this->on)->collection($orderValue->id())
                                        ->document('Chat')->collection('Messages')->where('isMessageRead','=', false)->documents()->size();


                                    $orderDetails = Order::with('orderItems', 'orderItems.product', 'orderItems.product.favouriteProduct', 'orderItems.product.imageProducts')->where('id', $orderId)->first();
                                    if ($orderDetails) {
                                        $orderItem = $orderDetails->orderItems;
                                        $deliverStatus = null;
                                        if ($orderDetails->deliveryPerson) {
                                            $deliverStatus = $orderDetails->deliveryPerson == Auth::id() ? TrackingStatusEnum::DELIVERING : TrackingStatusEnum::NOT_DELIVERING;
                                        }
                                        /*
                                       * Setting response based on last message
                                       */
                                        if (!$orderItem->isEmpty()) {

                                            $products = $orderItem->map(function ($v) {
                                                return [
                                                    'productId' => (int)$v->productId,
                                                    'productName' => $v->productName,
                                                    'productPrice' => (float)$v->productPrice,
                                                    'productSalePrice' => (float)$v->productSalePrice,
                                                    'productPictures' => $v->product ? new ImageProductCollection($v->product->imageProducts) : [asset('img/no_image.png')],
                                                    "isFavorite" => $v->product?count($v->product->favouriteProduct->where('userId', Auth::id())) > 0 ? true : false: false,
                                                    "isProductOnSale"=>$v->isProductonSale?true:false,
                                                ];
                                            });

                                            foreach ($lastMessageData as $lastMessageValue) {
                                                $lastMessage = $lastMessageValue['message'];
                                                $isRead = $lastMessageValue['isMessageRead'];
                                                $time = $lastMessageValue['time'];
                                                /* formatting required data for response  */
                                                $userProfile = $receiverUserData->avatar ? Storage::disk('s3')->url($receiverUserData->avatar) : $receiverUserData->avatarFullUrl;
                                                $reviews = $this->getReviewsCount($receiverUserData->id, strtoupper($lastMessageValue['userType']));
                                                $ratings = $this->getRatingsAvg($receiverUserData->id, strtoupper($lastMessageValue['userType']));

                                                $userblock=UserBlock::where('blockedUserId',$receiverUserData->id)->where('blockedBy',Auth::id())->where('status','BLOCK')->first();
                                                if ($userblock){
                                                    $blocked='BLOCK';
                                                }else{
                                                    $blocked='UNBLOCK';

                                                }
                                               $orderDetailData []= [
                                                    'userDetail'=>new UserOrderResource($orderDetails),
                                                    'orderData'=>new  OrderChatResource($orderDetails),
                                                    "products" => $products,
                                                ];
                                                /*
                                                * @link http://specifications.techswivel.com/p0025g/#inbox-inbox-get
                                                * return response to app
                                                */
                                                $this->responseBag[] = [

                                                    "isMessageRead" => $isRead,
                                                    "lastMessage" => $lastMessage,
                                                    "unreadMessageCount" => (int)$messageUnReadCount,
                                                    "orderId" => (int)$orderId,
                                                    "time" => $time,
                                                    "userType" => strtoupper($lastMessageValue['userType']),
                                                    "userAvatar" => $userProfile,
                                                    "userName" => $receiverUserData->firstName . ' ' . $receiverUserData->lastName,
                                                    "userUID" => $receiverUserData->uid,
                                                    "chatUserId"=>$receiverUserData->id,
                                                    "orderDetails"=>$orderDetailData,
                                                     "userStatus"=>$blocked,
                                                ];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            /*
            * sorting all data in descending order
            */
            $sortDataDescending = collect($this->responseBag ?? [])->sortByDesc('time')->values();
            /*
            * getting pagination in Illuminate\Http\JsonResponse
            */
            $paginateData = $this->paginate($sortDataDescending);
            return response()->json(['response' => ['status' => true, 'data' => new ChatCollection($paginateData)]], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            /*
            * saving error logs
            */
            $this->saveExceptionLog($e, 'App\Services\ChatService');
            return response()->json(['response' => ['status' => false, 'message' => $e->getMessage()]], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
