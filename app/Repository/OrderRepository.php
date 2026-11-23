<?php


namespace App\Repository;

use App\Enums\CardTypeEnum;
use App\Enums\NotificationType;
use App\Enums\OrderStatus;
use App\Enums\RequestStatusEnum;
use App\Enums\TrackingStatusEnum;
use App\Enums\WithdrawAmountTypeEnum;
use App\Http\Resources\Api\Order\BuyerOrderResource;
use App\Http\Resources\Api\Order\OrderProductResurce;
use App\Http\Resources\Api\Order\OrderResource;
use App\Http\Resources\Api\Order\UserOrderResource;
use App\Models\Card;
use App\Models\Cart;
use App\Models\CommissionSetting;
use App\Models\FavouriteProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use App\Models\UserBlock;
use App\Services\BrainTreeService;
use App\Services\ChatLocationService;
use App\Traits\FirebaseNotification;
use App\Traits\loggerExceptionTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderRepository
{
    use loggerExceptionTrait, FirebaseNotification;

    public function OrderRep($request)
    {
        if ($request->isCartCheckout) {
            return $this->cartCheckOut($request);
        } else {
            return $this->productCheckOut($request);
        }
    }

    public function cartCheckOut($request)
    {
        try {
            DB::beginTransaction();
            $cartItems = Cart::where('buyerId', Auth::id())->get();
            if ($cartItems->isEmpty()) {
                return response()->json(['response' => ['status' => false, 'message' => "Your carts is empty , Kindly add at least one product in cart"]], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            $total = 0;
            $itemDiscounts = 0;
            $sellerId = null;
            $orderItems = array();
            foreach ($cartItems as $cartItem) {
                $productDetail = Product::find($cartItem->productId);
                if ($productDetail) {
                    $orderedQuantity = $cartItem->quantity;
                    if ($productDetail->inStockQuantity == 0) {
                        return response()->json(['response' => ['status' => false, 'message' => 'Product is out of stock!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                    }
                    $remainingStock = $productDetail->inStockQuantity - $orderedQuantity;
                    if ($remainingStock >= 0) {
                        $productDetail->update([
                            'inStockQuantity' => $remainingStock
                        ]);
                        if ($remainingStock == 0) {
                            Cart::where('productId', $productDetail->id)->delete();
                            FavouriteProduct::where('productId', $productDetail->id)->delete();
                        } else {
                            Cart::where('productId', $productDetail->id)->where('quantity', '>', $remainingStock)->update([
                                'quantity' => $remainingStock
                            ]);
                        }

                    } else {
                        return response()->json(['response' => ['status' => false, 'message' => 'Selected product quantity is unavailable!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                    }
                    $productImagePath = $productDetail->productOneImage ? $productDetail->productOneImage->imageUrl : null;
                    $productSalePrice = $productDetail->productPrice;
                    $discountAmount = 0;
                    if ($productDetail->salePrice > 0) {
                        $productSalePrice = $productDetail->salePrice;
                        $discountAmount = $productDetail->productPrice - $productDetail->salePrice;
                    }
                    $itemTotalPrice = round($orderedQuantity * $productSalePrice, 2);
                    $itemDiscounts = round($itemDiscounts + ($orderedQuantity * $discountAmount), 2);
                    $total = $total + $itemTotalPrice;
                    $sellerId = $productDetail->sellerId;
                    $orderItems[] = [
                        'productId' => $productDetail->id,
                        'quantity' => $orderedQuantity,
                        'productPrice' => $productDetail->productPrice,
                        'productDescription' => $productDetail->description,
                        'productName' => $productDetail->name,
                        'productImage' => $productImagePath,
                        'productSalePrice' => $productDetail->salePrice ? $productDetail->salePrice : 0,
                        'createdAt' => strtotime(now()),
                        'updatedAt' => strtotime(now())
                    ];

                } else {
                    return response()->json(['response' => ['status' => false, 'message' => 'Product not found!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                }
            }
            $eloumoCharges = $this->getEloumoCharges($total);
            $transactionId = null;
            $transactions = $this->makePayment($request, $total);
            if ($transactions instanceof \Exception) {
                return response()->json(['response' => ['status' => false, 'message' => $transactions->getMessage()]], JsonResponse::HTTP_BAD_REQUEST);
            }
            if ($transactions['status'] == false) {
                return response()->json(['response' => ['status' => false, 'message' => $transactions['message']]], JsonResponse::HTTP_BAD_REQUEST);
            } else {
                if ($transactions['paymentId']) {
                    $transactionId = $transactions['paymentId'];
                } else {
                    return response()->json(['response' => ['status' => false, 'message' => "Payment not processed"]], JsonResponse::HTTP_BAD_REQUEST);
                }
            }
            $paidAmount = $total;
            $order = Order::create([
                'buyerId' => Auth::id(),
                'sellerId' => $sellerId,
                'paymentMethod' => $request->paymentMethod,
                'transactionId' => $transactionId,
                'status' => OrderStatus::ACCEPTED,
                'eloumoCharges' => $eloumoCharges,
                'totalAmount' => $total + $itemDiscounts,
                'discountAmount' => $itemDiscounts,
                'paidAmount' => $paidAmount,
                'deliveryType'=>$request->deliveryType,
                'latitude'=>$request->latitude,
                'longitude'=>$request->longitude,
                "address"=>$request->address
            ]);
            $orderId = $order->id;
            $orderItems = collect($orderItems)->map(function ($orderItem) use ($orderId) {
                $orderItem['orderId'] = $orderId;
                return $orderItem;
            })->toArray();
            OrderItem::insert($orderItems);
            Cart::where('buyerId', Auth::id())->delete();
            $orderDetail = Order::with('orderItems', 'orderItems.product', 'orderItems.product.imageProducts', 'seller', 'buyer')->where('id', $orderId)->first();
            DB::commit();
            $orderData = $this->orderDetailNotificationSetting('SELLER', $orderDetail);
            $title = 'Order Accepted';
            $message = 'Order Accepted';
            $fcm['title'] = $title;
            $fcm['body'] = $message;
            $fcm['data'] = [
                'title' => $title,
                'body' => $message,
                "notificationType" => NotificationType::ORDER_NOTIFICATION,
                'notification' => json_encode($orderData)
            ];
            $this->sendNotificationToBuyer($fcm, $sellerId);
            return response()->json(['response' => ['status' => true, 'data' => ['userDetail'=>new UserOrderResource($orderDetail),'orderDetail' => new OrderResource($orderDetail),"products" =>OrderProductResurce::collection($orderDetail->orderItems)]]], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'Save order Exception');
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    private function orderDetailNotificationSetting($role, $orderDetail)
    {
        $userAvatar = asset('img/default-avatar.svg');
        $userTotalReview = 0;
        $userType = $userUID = $userName = null;
        $rating = 0;
        $orderStatus = $orderDetail->status;
        if ($role == "BUYER") {
            //role type buyer
            $userType = "BUYER";
            if ($orderDetail->seller) {
                $userTotalReview = (float)$orderDetail->seller->sellerReviews;
                $rating = (float)$orderDetail->seller->sellerRating;
                $userName = $orderDetail->seller->name;
                $userUID = $orderDetail->seller->uid;
                $userAvatar = $orderDetail->seller->avatar ? Storage::disk('s3')->url($orderDetail->seller->avatar) : $userAvatar;

            }
            if ($orderDetail->isBuyerRatingCompleted) {
                $orderStatus = OrderStatus::COMPLETED;
            }
        } elseif ($role == "SELLER") {
            //role type seller
            $userType = "SELLER";
            if ($orderDetail->buyer) {
                $userTotalReview = (float)$orderDetail->buyer->buyerReviews;
                $rating = (float)$orderDetail->buyer->buyerRating;
                $userName = $orderDetail->buyer->name;
                $userUID = $orderDetail->buyer->uid;
                $userAvatar = $orderDetail->buyer->avatar ? Storage::disk('s3')->url($orderDetail->buyer->avatar) : $userAvatar;
            }
            if ($orderDetail->isSellerRatingCompleted) {
                $orderStatus = OrderStatus::COMPLETED;
            }

        }
        $deliverStatus = null;
        if ($orderDetail->deliveryPerson) {
            $deliverStatus = $orderDetail->deliveryPerson == Auth::id() ? TrackingStatusEnum::DELIVERING : TrackingStatusEnum::NOT_DELIVERING;
        }

        return [
            "orderId" => (int)$orderDetail->id,
            "orderTotalPrice" => (float)$orderDetail->paidAmount,
            "orderStatus" => $orderStatus,
            "userType" => $userType,
            "orderDate" => (int)strtotime($orderDetail->createdAt),
            "userName" => $userName,
            "userUID" => $userUID,
            "userAvatar" => $userAvatar,
            "userRating" => $rating,
            "userTotalReview" => $userTotalReview,
            "paymentInvoice" => sprintf("%05d", $orderDetail->id),
            "trackingStatus" => $deliverStatus,
            "agreedLatitude" => $orderDetail->latitude,
            "agreedLongitude" => $orderDetail->longitude,
            "agreedAddress" => $orderDetail->address,
            "products" => OrderProductResurce::collection($orderDetail->orderItems),
            "totalOrderProducts" => (int)count($orderDetail->orderItems),
        ];

    }

    public function productCheckOut($request)
    {
        try {
            DB::beginTransaction();
            $productDetail = Product::where('id',$request->productId)->where('isSellerBlocked',0)->first();
            $itemDiscounts = 0;
            if ($productDetail) {
                if ($productDetail->sellerId == Auth::id()) {
                    return response()->json(['response' => ['status' => false, 'message' => 'Sorry! Do not add to cart own product!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                }
                $orderedQuantity = $request->productQuantity;
                if ($productDetail->inStockQuantity == 0) {
                    return response()->json(['response' => ['status' => false, 'message' => 'Product is out of stock!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                }
                $remainingStock = $productDetail->inStockQuantity - $orderedQuantity;
                if ($remainingStock >= 0) {
                    $productDetail->update([
                        'inStockQuantity' => $remainingStock
                    ]);
                    if ($remainingStock == 0) {
                        Cart::where('productId', $productDetail->id)->delete();
                        FavouriteProduct::where('productId', $productDetail->id)->delete();
                    } else {
                        Cart::where('productId', $productDetail->id)->where('quantity', '>', $remainingStock)->update([
                            'quantity' => $remainingStock
                        ]);
                    }
                } else {
                    return response()->json(['response' => ['status' => false, 'message' => 'Selected product quantity is unavailable!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                }
                $productImagePath = $productDetail->productOneImage ? $productDetail->productOneImage->imageUrl : null;

                $productSalePrice = $productDetail->productPrice;
                $discountAmount = 0;
                if ($productDetail->salePrice > 0) {
                    $productSalePrice = $productDetail->salePrice;
                    $discountAmount = $productDetail->productPrice - $productDetail->salePrice;
                }
                $itemTotalPrice = round($orderedQuantity * $productSalePrice, 2);
                $itemDiscounts = round($itemDiscounts + ($orderedQuantity * $discountAmount), 2);
                $total = $itemTotalPrice;
                $sellerId = $productDetail->sellerId;
                $orderItems[] = [
                    'productId' => $productDetail->id,
                    'quantity' => $orderedQuantity,
                    'productPrice' => $productDetail->productPrice,
                    'productDescription' => $productDetail->description,
                    'productName' => $productDetail->name,
                    'productImage' => $productImagePath,
                    'productSalePrice' => $productDetail->salePrice ? $productDetail->salePrice : 0,
                    'createdAt' => strtotime(now()),
                    'updatedAt' => strtotime(now())
                ];

            } else {
                return response()->json(['response' => ['status' => false, 'message' => 'Product not found!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            $eloumoCharges = $this->getEloumoCharges($total);
            $transactionId = null;
            $transactions = $this->makePayment($request, $total);
            if ($transactions instanceof \Exception) {
                return response()->json(['response' => ['status' => false, 'message' => $transactions->getMessage()]], JsonResponse::HTTP_BAD_REQUEST);
            }
            if ($transactions['status'] == false) {
                return response()->json(['response' => ['status' => false, 'message' => $transactions['message']]], JsonResponse::HTTP_BAD_REQUEST);
            } else {
                if ($transactions['paymentId']) {
                    $transactionId = $transactions['paymentId'];
                } else {
                    return response()->json(['response' => ['status' => false, 'message' => "Payment not processed"]], JsonResponse::HTTP_BAD_REQUEST);
                }
            }
            $paidAmount = $total;
            $order = Order::create([
                'buyerId' => Auth::id(),
                'sellerId' => $sellerId,
                'paymentMethod' => $request->paymentMethod,
                'transactionId' => $transactionId,
                'status' => OrderStatus::ACCEPTED,
                'eloumoCharges' => $eloumoCharges,
                'totalAmount' => $total + $itemDiscounts,
                'discountAmount' => $itemDiscounts,
                'paidAmount' => $paidAmount,
                'deliveryType'=>$request->deliveryType,
                'latitude'=>$request->latitude,
                'longitude'=>$request->longitude,
                "address"=>$request->address
            ]);
            $orderId = $order->id;
            $orderItems = collect($orderItems)->map(function ($orderItem) use ($orderId) {
                $orderItem['orderId'] = $orderId;
                return $orderItem;
            })->toArray();
            OrderItem::insert($orderItems);
            $orderDetail = Order::with('orderItems', 'orderItems.product', 'orderItems.product.imageProducts', 'seller', 'buyer')->where('id', $orderId)->first();
            Cart::where('buyerId', Auth::id())->where('productId', $productDetail->id)->delete();
            DB::commit();
            $orderData = $this->orderDetailNotificationSetting('SELLER', $orderDetail);
            $title = 'Order Accepted';
            $message = 'Order Accepted';
            $fcm['title'] = $title;
            $fcm['body'] = $message;
            $fcm['data'] = [
                'title' => $title,
                'body' => $message,
                "notificationType" => NotificationType::ORDER_NOTIFICATION,
                'notification' => json_encode($orderData)
            ];
            $this->sendNotificationToBuyer($fcm, $sellerId);
            return response()->json(['response' => ['status' => true, 'data' => ['userDetail'=>new UserOrderResource($orderDetail),'orderDetail' => new OrderResource($orderDetail),"products" =>OrderProductResurce::collection($orderDetail->orderItems)]]], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'Save order Exception');
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function getEloumoCharges($total)
    {
        $commission = CommissionSetting::first();
        if ($commission) {
            return $total * ($commission->amount / 100);
        }
        return 0;
    }

    public function makePayment($req, $amount)
    {
        $brainTreeService = new BrainTreeService();
        $customerId = Auth::user()->paypalCustomerId;
        if (is_null(Auth::user()->paypalCustomerId)) {
            $customerId = $brainTreeService->createCustomer(Auth::user()->firstName, Auth::user()->lastName, Auth::user()->email, Auth::user()->phoneNumber);
            User::find(Auth::id())->update(['paypalCustomerId' => $customerId]);
        }
        try {
            $response['paymentId'] = null;
            $response['status'] = true;
            $response['message'] = null;
            $cardType = $req->cardType;
            $nonceToken = $req->nonceToken;
            if ($cardType == CardTypeEnum::EXISTING) {
                $cardDetail = Card::where('userId', Auth::id())->where('id', $req->cardId)->orderBy('isDefault', 'desc')->first();
                if ($cardDetail) {
                    $paymentMethodId = $cardDetail->cardId;
                    $paymentMethodNonce = $brainTreeService->createPaymentMethodNonce($paymentMethodId);
                    if ($paymentMethodNonce) {

                        if ($paymentMethodNonce->success) {

                            $transactions = $brainTreeService->createTransaction($paymentMethodNonce->paymentMethodNonce->nonce, $amount);
                            if ($transactions) {
                                if ($transactions->success) {
                                    $response['paymentId'] = $transactions->transaction->id;
                                    return $response;
                                } else {
                                    $response['paymentId'] = null;
                                    $response['status'] = false;
                                    $response['message'] = $transactions->message;
                                    return $response;
                                }
                            } else {
                                $response['paymentId'] = null;
                                $response['status'] = false;
                                $response['message'] = "Transaction not completed";
                                return $response;
                            }

                        } else {
                            $response['paymentId'] = null;
                            $response['status'] = false;
                            $response['message'] = $paymentMethodNonce->message;
                            return $response;
                        }


                    } else {
                        $response['paymentId'] = null;
                        $response['status'] = false;
                        $response['message'] = "Invalid payment method detail";
                        return $response;
                    }
                } else {
                    $response['paymentId'] = null;
                    $response['status'] = false;
                    $response['message'] = "invalid card id";
                    return $response;
                }
            } elseif ($cardType == CardTypeEnum::NEW) {
                if ($req->isSaveCard) {
                    $paymentResponse = $brainTreeService->createPaymentMethod($nonceToken, $customerId);
                    if ($paymentResponse) {
                        if ($paymentResponse->success) {
                            $isDefault = 1;
                            $cardHolderName = $req->cardHolderName;
                            if (Card::where('userId', Auth::id())->where('isDefault', 1)->exists()) {
                                $isDefault = 0;
                            }
                            $cardDetail = [
                                'userId' => Auth::id(),
                                'lastDigits' => $paymentResponse->paymentMethod->last4,
                                'cardId' => $paymentResponse->paymentMethod->token,
                                'cardHolderName' => $cardHolderName,
                                'cardBrandName' => $paymentResponse->paymentMethod->cardType,
                                'cardBrandUrl' => $paymentResponse->paymentMethod->imageUrl,
                                'isDefault' => $isDefault
                            ];
                            Card::create($cardDetail);

                            $paymentMethodNonce = $brainTreeService->createPaymentMethodNonce($paymentResponse->paymentMethod->token);
                            if ($paymentMethodNonce) {

                                if ($paymentMethodNonce->success) {
                                    $transactions = $brainTreeService->createTransaction($paymentMethodNonce->paymentMethodNonce->nonce, $amount);
                                    if ($transactions) {
                                        if ($transactions->success) {
                                            $response['paymentId'] = $transactions->transaction->id;
                                            return $response;
                                        } else {
                                            $response['paymentId'] = null;
                                            $response['status'] = false;
                                            $response['message'] = $transactions->message;
                                            return $response;
                                        }
                                    } else {
                                        $response['paymentId'] = null;
                                        $response['status'] = false;
                                        $response['message'] = "Transaction not completed";
                                        return $response;
                                    }

                                } else {
                                    $response['paymentId'] = null;
                                    $response['status'] = false;
                                    $response['message'] = $paymentMethodNonce->message;
                                    return $response;
                                }


                            } else {
                                $response['paymentId'] = null;
                                $response['status'] = false;
                                $response['message'] = "Payment is saved successfully but payment is not processed";
                                return $response;
                            }
                        } else {
                            $response['paymentId'] = null;
                            $response['status'] = false;
                            $response['message'] = $paymentResponse->message;
                            return $response;
                        }
                    } else {
                        $response['paymentId'] = null;
                        $response['status'] = false;
                        $response['message'] = "Payment method is not saved";
                        return $response;
                    }

                } else {
                    $transactions = $brainTreeService->createTransaction($nonceToken, $amount);
                    if ($transactions) {
                        if ($transactions->success) {
                            $response['paymentId'] = $transactions->transaction->id;
                            return $response;
                        } else {
                            $response['paymentId'] = null;
                            $response['status'] = false;
                            $response['message'] = $transactions->message;
                            return $response;
                        }
                    } else {
                        $response['paymentId'] = null;
                        $response['status'] = false;
                        $response['message'] = "Transaction not completed";
                        return $response;
                    }
                }
            }
            return $response;
        } catch (\Exception $exception) {
            $this->saveExceptionLog($exception, 'Make Payment Exception');
            return $exception;
        }

    }

    public function updateOrder($request)
    {
        if ($request->orderStatus == RequestStatusEnum::TRACKING) {
            return $this->trackingOrder($request);
        }
        if ($request->orderStatus == RequestStatusEnum::CANCEL) {
            return $this->CancelOrder($request);
        }
        if ($request->orderStatus == RequestStatusEnum::COMPLETE) {
            return $this->completeOrder($request);
        }
        if ($request->orderStatus == RequestStatusEnum::RATING) {
            return $this->ratingOrder($request);
        }
    }

    public function CancelOrder($request)
    {
        try {
            DB::beginTransaction();
            $order = Order::with('orderItems', 'orderItems.product', 'orderItems.product.imageProducts', 'seller', 'buyer')->where('id', $request->orderId)->first();
            if ($order) {
                if ($order->buyerId != Auth::id()) {
                    if ($order->sellerId != Auth::id()) {
                        return response()->json(['response' => ['status' => false, 'message' => "You don't have permission!"]], JsonResponse::HTTP_FORBIDDEN);
                    }
                }
                if ($order->status == OrderStatus::CANCELED) {
                    return response()->json(['response' => ['status' => false, 'message' => "Order is already canceled"]], JsonResponse::HTTP_CONFLICT);
                }
                if ($order->status == OrderStatus::RATING) {
                    return response()->json(['response' => ['status' => false, 'message' => "Sorry!You can't cancel order because your order is already completed."]], JsonResponse::HTTP_CONFLICT);
                }
                if ($order->status == OrderStatus::COMPLETED) {
                    return response()->json(['response' => ['status' => false, 'message' => "Sorry!You can't cancel order because your order is already completed."]], JsonResponse::HTTP_CONFLICT);
                }
                $order->update([
                    'cancelReason' => $request->reason,
                    'status' => OrderStatus::CANCELED,
                    'cancelBy' => Auth::id(),
                ]);
                $amount = $order->paidAmount;
                $userId = $order->sellerId;
                if ($order->buyerId == Auth::id()) {
                    $userId = $order->buyerId;
                    $deductedAmount = env('CANCELED_ORDER_AMOUNT_PERCENTAGE');
                    if (strtotime(Carbon::now()) <= strtotime(Carbon::parse($order->createdAt)->addMinutes(env('CANCELED_ORDER_TIME')))) {
                        $amount = $order->paidAmount;
                    } else {
                        $amountDeduction = $order->paidAmount * ($deductedAmount / 100);
                        $amount = $order->paidAmount - $amountDeduction;
                    }
                }
                Payment::create([
                    'orderId' => $order->id,
                    'userId' => $order->buyerId,
                    'paymentType' => WithdrawAmountTypeEnum::REFUNDED(),
                    'clearedAmount' => $amount
                ]);
                // stock return
                $items = OrderItem::where('orderId', $order->id)->get();
                foreach ($items as $value) {
                    $product = Product::where('id', $value->productId)->first();
                    if ($product) {
                        $product->inStockQuantity = $product->inStockQuantity + $value->quantity;
                        $product->save();
                    }
                }
                DB::commit();
                $orderDetail = $order->sellerId == Auth::id() ? $this->orderDetailNotificationSetting('BUYER', $order) : $this->orderDetailNotificationSetting('SELLER', $order);
                $senderUserId = $order->sellerId == Auth::id() ? $order->buyerId : $order->sellerId;
                $title = 'Order Canceled';
                $message = 'Order Canceled';
                $fcm['title'] = $title;
                $fcm['body'] = $message;
                $fcm['data'] = [
                    'title' => $title,
                    'body' => $message,
                    "notificationType" => NotificationType::ORDER_NOTIFICATION,
                    'notification' => json_encode($orderDetail)
                ];
                $this->sendNotificationToBuyer($fcm, $senderUserId);
                return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
            } else {
                return response()->json(['response' => ['status' => false, 'message' => 'Order not found!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'Cancel order Exception');
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function completeOrder($request)
    {
        try {
            DB::beginTransaction();
            $orderId = $request->orderId;
            $order = Order::with('orderItems', 'orderItems.product', 'orderItems.product.imageProducts', 'seller', 'buyer')->where('id', $orderId)->first();
            if ($order) {
                if ($order->status == OrderStatus::TRACKING) {
                    if ($order->buyerId != Auth::id()) {
                        return response()->json(['response' => ['status' => false, 'message' => 'Sorry! Do not have permission to seller for order mark as completed']], JsonResponse::HTTP_FORBIDDEN);
                    }
                    $order->update([
                        'status' => OrderStatus::RATING,
                    ]);
                    Payment::create([
                        'orderId' => $order->id,
                        'userId' => $order->sellerId,
                        'paymentType' => WithdrawAmountTypeEnum::EARNED(),
                        'clearedAmount' => $order->paidAmount - $order->eloumoCharges
                    ]);
                    DB::commit();
                    $orderDetail = $order->sellerId == Auth::id() ? $this->orderDetailNotificationSetting('BUYER', $order) : $this->orderDetailNotificationSetting('SELLER', $order);
                    $senderUserId = $order->sellerId == Auth::id() ? $order->buyerId : $order->sellerId;
                    $title = 'Order Completed';
                    $message = 'Order Completed';
                    $fcm['title'] = $title;
                    $fcm['body'] = $message;
                    $fcm['data'] = [
                        'title' => $title,
                        'body' => $message,
                        "notificationType" => NotificationType::ORDER_NOTIFICATION,
                        'notification' => json_encode($orderDetail)
                    ];
                    $this->sendNotificationToBuyer($fcm, $senderUserId);
                    return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
                } else {
                    if ($order->buyerId == Auth::id() && $order->status == OrderStatus::RATING) {
                        return response()->json(['response' => ['status' => false, 'message' => 'Order is already completed!']], JsonResponse::HTTP_CONFLICT);
                    }
                    return response()->json(['response' => ['status' => false, 'message' => 'Order is not tracking state!']], JsonResponse::HTTP_CONFLICT);
                }
            } else {
                return response()->json(['response' => ['status' => false, 'message' => 'Order id is not found!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'Accepted order Exception');
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function trackingOrder($request)
    {
        try {
            DB::beginTransaction();
            $orderId = $request->orderId;
            $order = Order::with('orderItems', 'orderItems.product', 'orderItems.product.imageProducts', 'seller', 'buyer')->where('id', $orderId)->first();
            if ($order) {
                if ($order->status == OrderStatus::ACCEPTED) {
                    if ($order->sellerId == Auth::id() || $order->buyerId == Auth::id()) {
                        $chatLocationService = new ChatLocationService();
                        $userId = $receiverUid = null;
                        $orderNodeId = "Order_" . $orderId;
                        $receiverUid = $order->seller ? $order->seller->uid : "";
                        $userId = $order->buyer ? $order->buyer->uid : "";
                        $ignoredMessageId = $request->firebaseMessageId;
                        $isUpdated = $chatLocationService->messageStatusUpdate($userId, $receiverUid, $orderNodeId, $ignoredMessageId);
                        if ($isUpdated) {
                            $order->update([
                                "latitude" => $request->latitude,
                                "longitude" => $request->longitude,
                                "address" => $request->address,
                                "firebaseMessageId" => $request->firebaseMessageId,
                                "status" => OrderStatus::TRACKING,
                                "deliveryPerson" => Auth::id(),
                            ]);
                            DB::commit();
                            $orderDetail = $order->sellerId == Auth::id() ? $this->orderDetailNotificationSetting('BUYER', $order) : $this->orderDetailNotificationSetting('SELLER', $order);
                            $senderUserId = $order->sellerId == Auth::id() ? $order->buyerId : $order->sellerId;
                            $title = 'Order Tracking';
                            $message = 'Order Tracking';
                            $fcm['title'] = $title;
                            $fcm['body'] = $message;
                            $fcm['data'] = [
                                'title' => $title,
                                'body' => $message,
                                "notificationType" => NotificationType::ORDER_NOTIFICATION,
                                'notification' => json_encode($orderDetail)
                            ];
                            $this->sendNotificationToBuyer($fcm, $senderUserId);
                            return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
                        } else {
                            return response()->json(['response' => ['status' => false, 'message' => "Firebase chat error"]], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                        }
                    }
                    return response()->json(['response' => ['status' => false, 'message' => "Don't have permission!"]], JsonResponse::HTTP_FORBIDDEN);

                } else {
                    if ($order->status == OrderStatus::TRACKING) {
                        return response()->json(['response' => ['status' => false, 'message' => 'Order is already tracking!']], JsonResponse::HTTP_CONFLICT);
                    }
                    return response()->json(['response' => ['status' => false, 'message' => 'Order is not accepted state!']], JsonResponse::HTTP_CONFLICT);
                }

            } else {
                return response()->json(['response' => ['status' => false, 'message' => 'Order not found!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'Tracking order Exception');
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function ratingOrder($request)
    {
        try {
            DB::beginTransaction();
            $orderId = $request->orderId;
            $order = Order::where('id', $orderId)->first();
            if ($order) {
                if ($order->status == OrderStatus::RATING) {
                    if ($order->sellerId == Auth::id()) {
                        if ($order->isSellerRatingCompleted) {
                            return response()->json(['response' => ['status' => false, 'message' => 'Order rating is already given!']], JsonResponse::HTTP_CONFLICT);
                        }
                        $ratingData = [
                            "sellerRating" => $request->rating,
                            "sellerFeedback" => $request->ratingMessage,
                            "isSellerRatingCompleted" => 1,
                        ];
                        if ($order->isBuyerRatingCompleted) {
                            $ratingData["status"] = OrderStatus::COMPLETED;
                        }
                        $order->update($ratingData);
                        $this->updateRating($order->buyerId, "BUYER");
                        DB::commit();
                        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
                    }
                    elseif ($order->buyerId == Auth::id()) {
                        if ($order->isBuyerRatingCompleted) {
                            return response()->json(['response' => ['status' => false, 'message' => 'Order rating is already given!']], JsonResponse::HTTP_CONFLICT);
                        }
                        $ratingData = [
                            "buyerRating" => $request->rating,
                            "buyerFeedback" => $request->ratingMessage,
                            'isBuyerRatingCompleted' => 1
                        ];
                        if ($order->isSellerRatingCompleted) {
                            $ratingData["status"] = OrderStatus::COMPLETED;
                        }
                        $order->update($ratingData);
                        $this->updateRating($order->sellerId, "SELLER");
                        DB::commit();
                        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
                    }
                } else {
                    if ($order->status == OrderStatus::COMPLETED) {
                        return response()->json(['response' => ['status' => false, 'message' => 'Order is already completed!']], JsonResponse::HTTP_CONFLICT);
                    }
                    return response()->json(['response' => ['status' => false, 'message' => 'Order is not completed state!']], JsonResponse::HTTP_CONFLICT);
                }
            } else {
                return response()->json(['response' => ['status' => false, 'message' => 'Order id is not found!!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            return response()->json(['response' => ['status' => false, 'message' => "Don't have permission to change status!"]], JsonResponse::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'Rating  order update Exception');
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    private function updateRating($userId, $role)
    {
        $rating = 0;
        $reviews = 0;
        if ($role == "BUYER") {
            $rating = Order::where('buyerId', $userId)->avg('sellerRating');
            $reviews = Order::where('buyerId', $userId)->whereNotNull('sellerRating')->count();
            User::where('id', $userId)->first()->update(['buyerRating' => $rating, 'buyerReviews' => $reviews]);
        }
        if ($role == "SELLER") {
            $rating = Order::where('sellerId', $userId)->avg('buyerRating');
            $reviews = Order::where('sellerId', $userId)->whereNotNull('buyerRating')->count();
            User::where('id', $userId)->first()->update(['sellerRating' => $rating, 'sellerReviews' => $reviews]);
        }
    }

    public function getHistory(array $data)
    {
        $query = Order::select('*');
        /* Get order seller OR Buyer  depends on login  */
        if ($data["userType"] == 'seller') {
            $query = $query->where('sellerId', Auth::id());
        } elseif ($data['userType'] == 'buyer') {
            $query = $query->where('buyerId', Auth::id());
        } else {
            $query = $query->where('sellerId', Auth::id())
                ->where(function ($q) {
                    $q->whereNotIn('status', [OrderStatus::RATING, OrderStatus::COMPLETED, OrderStatus::CANCELED]);
                })->orWhere('buyerId', Auth::id());
        }
        if ($data["status"] == "ACTIVE") {
            $query = $query->whereIn('status', [OrderStatus::TRACKING, OrderStatus::ACCEPTED]);
        } elseif ($data['status'] == "COMPLETED") {
            $query = $query->whereIn('status', [OrderStatus::COMPLETED, OrderStatus::RATING]);
        } elseif ($data['status'] == "CANCELED") {
            $query = $query->whereIn('status', [OrderStatus::CANCELED]);
        }
        $query->with('orderItems', 'orderItems.product', 'orderItems.product.favouriteProduct', 'orderItems.product.imageProducts', 'seller', 'buyer')->latest();
        return $query->paginate($data['limit']);
    }

}
