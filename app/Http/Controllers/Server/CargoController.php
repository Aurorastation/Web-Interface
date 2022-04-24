<?php

namespace App\Http\Controllers\Server;

use Grpc\Server;
use Illuminate\Http\Request;
use App\Models\ServerCargoItem;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

class CargoController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->user()->cannot('server_cargo_show')) {
                abort('403', 'You do not have the required permission');
            }
            return $next($request);
        });
    }

    public function index(){
        return view('server.cargo.index');
    }

    public function getItem($item_id){
        $item = ServerCargoItem::findOrFail($item_id);
        return view('server.cargo.item',["item"=>$item]);
    }

    public function getItemData(Request $request)
    {
        $chars = ServerCargoItem::select(['id', 'name', 'supplier', 'categories', 'price']);

        return Datatables::of($chars)
            ->removeColumn('id')
            ->editColumn('name', function( ServerCargoItem $item) {
                return '<a href="'.route('server.cargo.item.show',['item_id'=>$item->id]).'">'.$item->name.'</a>';
            })
            ->addColumn('action', function( ServerCargoItem $item) use ($request) {
                return '<a href="'.route('server.cargo.item.show',['item_id'=>$item->id]).'" class="btn btn-success" role="button">Show</a>';
            })
            ->rawColumns(['name', 'action'])
            ->make();
    }
}
