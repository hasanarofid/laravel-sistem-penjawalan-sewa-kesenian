<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

use App\Models\BookingList;
use App\Models\Room;
use App\Models\RoomStatus;
use App\Models\User;

use App\Jobs\SendEmail;

use App\Http\Requests\User\MyBookingListRequest;
use App\Models\BarangkesenianM;
use DataTables;
use Illuminate\Support\Facades\Validator;

class MyBookingListController extends Controller
{
    public function json(){
        $data = BookingList::where('user_id', Auth::user()->id)->with([
            'kesenian'
        ]);
        // dd($data->get());
        // foreach($data->get() as $dat ){
        //     dd($dat->kesenian->foto);
        // } 
        return DataTables::of($data)
        ->addIndexColumn()
        ->make(true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $model =  BookingList::where('user_id', Auth::user()->id)->with([
            'kesenian'
        ])
        ->orderBy('created_at', 'desc')
        ->get();
        return view('pages.user.my-booking-list.index2',
        compact('model')
    );
    }

    public function bayar(Request $request){
        // dd($request->item_id);
        // $request->validate([
        //     'item_id' => 'required|exists:your_table,id', // Validate that item_id exists in your table
        //     'proof' => 'required|mimes:jpeg,png,jpg,pdf|max:2048' // Validate file type and size
        // ]);

        $filePath = $request->file('proof')->store('payment_proofs', 'public');

        $model = BookingList::find($request->item_id);
        // dd($model);
        $model->bukti_pembayaran = $filePath;
        $model->status = 'DIBAYAR'; // Update status to DIBAYAR
        $model->save();

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diupload.');
    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $rooms = Room::orderBy('name')->get();
        $kesenian = BarangkesenianM::orderBy('nama')->get();

        return view('pages.user.my-booking-list.create', [
            'rooms' => $rooms,
            'kesenians' => $kesenian,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(MyBookingListRequest $request)
    public function store(Request $request)
    {
        

        $data               = $request->all();

        $data['user_id']    = Auth::user()->id;
        $data['status']     = 'PENDING';

        $room               = BarangkesenianM::select('nama')->where('id', $data['kesenian_id'])->firstOrFail();

        if(
            BookingList::where([
                ['date', '=', $data['date']],
                ['barangkesenian_id', '=', $data['kesenian_id']],
                ['status', '=', 'DISETUJUI'],
            ])
            ->count() <= 0 || 
            BookingList::where([
                ['date', '=', $data['date']],
                ['barangkesenian_id', '=', $data['kesenian_id']],
                ['status', '=', 'DISETUJUI'],
            ])
            ->count() <= 0 ||
            BookingList::where([
                ['date', '=', $data['date']],
                ['barangkesenian_id', '=', $data['kesenian_id']],
                ['status', '=', 'DISETUJUI'],
            ])->count() <= 0
        ) {
            $model = new BookingList();
            $model->barangkesenian_id =  $data['kesenian_id'];
            $model->date =  $data['date'];
            $model->alamat =  $data['purpose'];
            $model->user_id = Auth::user()->id;
            $model->status =  'PENDING';
            if($model->save()) {
                $request->session()->flash('alert-success', 'Kesenian '.$room->nama.' berhasil ditambahkan');
                
                $user_name          = $this->getUserName();
                $user_email         = $this->getUserEmail();
                
                $admin      = $this->getAdminData();
                $status     = 'DIBUAT';

                $to_role    = 'USER';

                // use URL::to('/') for the url value

                // URL::to('/my-booking-list)
                // dispatch(new SendEmail($user_email, $user_name, $room->name, $data['date'], $data['start_time'], $data['end_time'], $data['purpose'], $to_role, $user_name, 'https://google.com', $status));

                // $to_role    = 'ADMIN';

                // // URL::to('/admin/booking-list)
                // dispatch(new SendEmail($admin->email, $user_name, $room->name, $data['date'], $data['start_time'], $data['end_time'], $data['purpose'], $to_role, $admin->name, 'https://google.com', $status));

            } else {
                $request->session()->flash('alert-failed', 'Booking ruang '.$room->name.' gagal ditambahkan');
                return redirect()->route('my-booking-list.create');
            }
        } else {
            $request->session()->flash('alert-failed', 'Ruangan '.$room->name.' di waktu itu sudah dibooking');
            return redirect()->route('my-booking-list.create');
        }

        return redirect()->route('my-booking-list.index');
    }

    /**
     * Cancel the specified data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        $item           = BookingList::findOrFail($id);
        $data['status'] = 'BATAL';

        $room               = Room::select('name')->where('id', $item->kesenian_id)->firstOrFail();

        if($item->update($data)) {
            session()->flash('alert-success', 'Booking Ruang '.$room->name.' berhasil dibatalkan');

            $user_name          = $this->getUserName();
            $user_email         = $this->getUserEmail();

            $admin      = $this->getAdminData();
            $status     = $data['status'];

            $to_role    = 'USER';

            dispatch(new SendEmail($user_email, $user_name, $room->name, $item->date, $item->start_time, $item->end_time, $item->purpose, $to_role, $user_name, 'https://google.com', $status));
            
            $to_role    = 'ADMIN';

            dispatch(new SendEmail($admin->email, $user_name, $room->name, $item->date, $item->start_time, $item->end_time, $item->purpose, $to_role, $admin->name, 'https://google.com', $status));
            
        } else {
            session()->flash('alert-failed', 'Booking Ruang '.$room->name.' gagal dibatalkan');
        }
        
        return redirect()->route('my-booking-list.index');
    }

    public function getAdminData() {
        return User::select('name','email')->where('role', 'ADMIN')->firstOrFail();
    }

    public function getUserName() {
        return Auth::user()->name;
    }

    public function getUserEmail() {
        return Auth::user()->email;
    }
}
