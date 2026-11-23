<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TopicsEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Notifications\NotificationRequest;
use App\Models\FcmToken;
use Illuminate\Http\Request;
use Facades\App\Helpers\FcmHelper;

class NotificationController extends Controller
{

     /**
     * __construct  load default values in class
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware(['permission:view notifications|send notification']);
        $this->middleware(['auth']);
    }
    /**
     * index show create form for notifictions
     *
     * @return void
     */
    public function index()
    {
        return view('admin.notifications.create');
    }
    public function send(NotificationRequest $request)
    {

        $data = $request->description;
        $title = $request->title;
        /* send notification to both users  */
        if ($request->has('Android_Users') && $request->has('iOS_Users')) {
            if (FcmHelper::topic($data,$title,TopicsEnum::ALL)) {
                toastr()->success('Notification sent!');
                return back();
            }
            toastr()->error('Something went wrong!');
            return back();
        }
        /* send notification to android  */
        if ($request->has('Android_Users')) {
            if (FcmHelper::topic($data, $title, TopicsEnum::ANDROID)) {
                toastr()->success('Notification sent!');
                return back();
            }
            toastr()->error('Something went wrong!');
            return back();
        }
        /* send notification to both ios  */
        if ($request->has('iOS_Users')) {
            if (FcmHelper::topic($data, $title, TopicsEnum::IOS)) {
                toastr()->success('Notification sent!');
                return back();
            }
            toastr()->error('Something went wrong!');
            return back();
        }
        toastr()->error('Please choose iOS or Android users to send notification!');
        return back();
    }
}
