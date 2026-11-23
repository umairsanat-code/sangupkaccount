<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use Exception;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\FileUploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Team\PasswordUpdate;
use App\Traits\loggerExceptionTrait;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\Team\Store;
use App\Http\Requests\Admin\Team\Update;
use App\Models\Ticket;
use App\Models\CustomerFile;



class TeamController extends Controller
{
    use loggerExceptionTrait, FileUploadTrait;

    public function __construct()
    {
//        $this->middleware(['role:admin']);
        $this->middleware(['auth']);
    }

    /**
     * imageDirectory  path of folder where image will be stored on s3 e.g uploads/categories
     *
     * @var string
     */
    protected $imageDirectory = "uploads/teams";

    /**
     * listing  : list all roles with permissions using yajra/laravel-datatables-oracle: "~9.0"
     *
     * @param  mixed $request
     * @return void
     */
    public function listing(Request $request)
    {

        $roles = Role::whereNotIn('name', ['admin', 'seller', 'buyer'])->get();
        return view('admin.teams.listing', compact('roles'));
    }

    /**
     * teams : return json of all team members
     *
     * @return void
     */
    public function teams()
    {
        try {
            $teams=Customer::orderBy('created_at', 'desc')->get();
            return response()->json(['status' => true, 'data' => $teams], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'TeamController exception');
            return response()->json(['status' => false, 'data' => $e->getMessage()], JsonResponse::HTTP_OK);
        }
    }

    /**
     * store : store new team member
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
//            $serviceNumber = rand(10, 2000);

            // Handle file upload if present
            $filePath = null;
            // if ($request->hasFile('file')) {
            //     $filePath = $request->file('file')->store('uploads', 'public');
            // }


            // Create the customer
            $user = Customer::create([
                'name' => $request->firstName . ' ' . $request->lastName,
                'email' => $request->email,
                'phone' => $request->phoneNumber,
                'pnr' => $request->pnr,
                'total_customer' => $request->total_member,
//                'service_number' => $serviceNumber,
                'file' => $filePath,
                'customer_pnr'=>$request->customer_pnr,
                'id_card'=>$request->id_card,
                'passport_no'=>$request->passport_no,
            ]);
              if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/customer_files'), $fileName);

                    // File record save in customer_files table
                    $test=CustomerFile::create([
                        'customer_id' => $user->id,
                        'file_name'   => $fileName,
                        'file_path'   => 'uploads/customer_files/' . $fileName,
                    ]);
//                    dd($test);
                }
            }
//            $user->assignRole([$store->role]);
            DB::commit();

        return response()->json(['success' => 'Team member added successfully.']);




        } catch (Exception $e) {

            DB::rollBack();
            $this->saveExceptionLog($e, 'TeamController exception');
            return response()->json(['status' => false, 'data' => $e->getMessage()], JsonResponse::HTTP_OK);

        }
    }
    /**
     * edit : show a team member data for update
     *
     * @param  mixed $id
     * @return void
     */
    public function edit($id)
    {
        try {
            $team = User::with(['roles'])->find($id);
            $selected = $team->roles->pluck('name')->toArray();
            return response()->json(['status' => true, 'data' => $team, 'selected' => $selected], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'TeamController exception');
            return response()->json(['status' => false, 'data' => $e->getMessage()], JsonResponse::HTTP_OK);
        }
    }

    /**
     * update : update the given team member
     *
     * @param  mixed $updateRequest
     * @return void
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'pnr' => 'required|string|max:50',
            'total_customer' => 'required|integer|min:1',
            'customer_pnr' => 'nullable',
            'editId_card' => 'nullable',
            'editPassport_no' => 'nullable',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'pnr'=>$request->pnr,
            'total_customer'=>$request->total_customer,
             'customer_pnr'=>$request->customer_pnr,
            'id_card'=>$request->editId_card,
            'passport_no'=>$request->editPassport_no,
        ]);

        return response()->json(['status' => true, 'message' => 'Customer updated successfully.']);
    }

    /**
     * delete : delete given team member
     *
     * @param  mixed $id
     * @return void
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $user = User::find($id);
            $assignedRole = $user->roles;
            if (!empty($assignedRole)) {
                $user->syncRoles($assignedRole->pluck('name')->toArray());
            }
            $user->delete();
            DB::commit();
            toastr()->success('Team member deleted successfully!');
            return back();
        } catch (Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'TeamController exception');
            toastr()->error($e->getMessage());
            return back();
        }
    }

    public function password($id, PasswordUpdate $passwordUpdate)
    {
        try {
            DB::beginTransaction();
            $user = User::find($id);
            $user->update(['password' => Hash::make($passwordUpdate->password)]);
            DB::commit();
            toastr()->success('Team member password updated successfully!');
            return back();
        } catch (Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'TeamController exception');
            toastr()->error($e->getMessage());
            return back();
        }
    }
    public function passwordRest(){
        return view('admin.users.inc.reset-pwd');
    }
    public function passwordupdateM(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'password' => 'required|min:8|confirmed',
            ]);

            // Get the authenticated user's ID
            $user_id = Auth::id();
//            dd($user_id,$request->all());

            // Retrieve the user from the database
            $user = User::findOrFail($user_id);
//dd($user);
            // Update the user's password
            $user->password = Hash::make($request->input('password'));
            $user->save();

            // Redirect or return a success response
            return redirect()->back()->with('success', 'Password updated successfully.');

        } catch (\Exception $e) {
//            dd($e->getMessage());
            // Handle any exceptions and return an error response
            return redirect()->back()->with('error', 'An error occurred while updating the password.');
        }
    }
        public function destroy($id)
    {
        $team = Customer::find($id);
        if ($team) {
            $ticket=Ticket::where('customer_id',$team->id)->delete();
            $team->delete();
            return response()->json(['message' => 'Team deleted successfully!']);
        }
        return response()->json(['message' => 'Team not found!'], 404);
    }



public function uploadFiles(Request $request)
{
    $request->validate([
        'team_id'   => 'required|exists:customers,id',
        'files.*'   => 'required|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120'
    ]);

    $team = Customer::findOrFail($request->team_id);

    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {
            // Original file name without extension
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            // Extension
            $extension = $file->getClientOriginalExtension();

            // Safe file name (slugify + timestamp + uniqid)
            $safeName = Str::slug($originalName) . '_' . time() . '_' . uniqid() . '.' . $extension;

            // Move file
            $file->move(public_path('uploads/customer_files'), $safeName);

            // Save in DB
            CustomerFile::create([
                'customer_id' => $team->id,
                'file_name'   => $safeName,
                'file_path'   => 'uploads/customer_files/' . $safeName,
            ]);
        }
    }

    return response()->json([
        'status'  => 'success',
        'message' => 'Files uploaded successfully!'
    ]);
}

    public function getImage(Request $request)
    {
        $files = CustomerFile::where('customer_id', $request->customer_id)->get(['file_path']);
        return response()->json([
            'files' => $files
        ]);
    }
}
