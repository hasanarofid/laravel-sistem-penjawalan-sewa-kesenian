<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Models\BookingList;
use App\Models\Room;
use App\Models\RoomStatus;

use App\Http\Requests\User\MyBookingListRequest;

use DataTables;

class MyBookingListController extends Controller
{
    public function json(){
        $data = BookingList::where('user_id', Auth::user()->id)->with([
            'room'
        ]);

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
        return view('pages.user.my-booking-list.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $rooms = Room::all();

        return view('pages.user.my-booking-list.create', [
            'rooms' => $rooms,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MyBookingListRequest $request)
    {
        $data               = $request->all();
        $data['user_id']    = Auth::user()->id;
        $data['status']     = 'PENDING';

        $room               = Room::select('name')->where('id', $data['room_id'])->get();

        if(
            BookingList::where([
                ['date', '=', $data['date']],
                ['room_id', '=', $data['room_id']],
            ])
            ->whereBetween('start_time', [$data['start_time'], $data['end_time']])
            ->count() <= 0 || 
            BookingList::where([
                ['date', '=', $data['date']],
                ['room_id', '=', $data['room_id']],
            ])
            ->whereBetween('end_time', [$data['start_time'], $data['end_time']])
            ->count() <= 0 ||
            BookingList::where([
                ['date', '=', $data['date']],
                ['room_id', '=', $data['room_id']],
                ['start_time', '<=', $data['start_time']],
                ['end_time', '>=', $data['end_time']],
            ])->count() <= 0
        ) {
            if(BookingList::create($data)) {
                $request->session()->flash('alert-success', 'Booking ruang '.$room[0]['name'].' berhasil ditambahkan');
            } else {
                $request->session()->flash('alert-failed', 'Booking ruang '.$room[0]['name'].' gagal ditambahkan');
            }
        } else {
            // dd($room);
            $request->session()->flash('alert-failed', 'Ruangan '.$room[0]['name'].' di waktu itu sudah dibooking');
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

        if($item->update($data)) {
            session()->flash('alert-success', 'Booking Ruang '.$item->name.' berhasil dibatalkan');
        } else {
            session()->flash('alert-failed', 'Booking Ruang '.$item->name.' gagal dibatalkan');
        }
        
        return redirect()->route('my-booking-list.index');
    }
}