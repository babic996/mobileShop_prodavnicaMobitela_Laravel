<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Cart;
use App\Models\Phone;
use App\Models\Tablet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;

class EquipmentIndexController extends Controller
{
    public function index($id)
    {

        $equipments=Equipment::where('category_equipment_id',$id)->paginate(3);
        $phones_nav=Phone::distinct()->get('category_id');
        $tablets_nav=Tablet::distinct()->get('category_id');
        $equipment_nav=Equipment::distinct()->get('category_equipment_id');


        return view('equipment-list',['equipments'=>$equipments,'phones_nav'=>$phones_nav,'tablets_nav'=>$tablets_nav,'equipment_nav'=>$equipment_nav,'id'=>$id]);
    }
    public function show(Equipment $equipment)
    {
        $equipment->all();
        return view('equipment-info',['equipment'=>$equipment]);
    }
    public function getAddToCart(Request $request,$id)
    {
        $equipment=Equipment::find($id);
        $oldCart=Session::has('cart') ? Session::get('cart'):null;
        $cart=new Cart($oldCart);
        $cart->add($equipment,$request['kolicina']);

        $request->session()->put('cart',$cart);
        return back();
    }
    public function filter(Request $request, $id)
    {
        $phones_nav=Phone::distinct()->get('category_id');
        $tablets_nav=Tablet::distinct()->get('category_id');
        $equipment_nav=Equipment::distinct()->get('category_equipment_id');

        if($request['filter']=="1")
        {
            $equipments=Search::add(Equipment::class,'category_equipment_id','id')
                ->orderByAsc()
                ->paginate(3)
                ->search($id);
        }
        elseif($request['filter']=="2")
        {
            $equipments=Search::add(Equipment::class,'category_equipment_id','id')
                ->orderByDesc()
                ->paginate(3)
                ->search($id);
        }
        elseif($request['filter']=="3")
        {
            $equipments=Search::add(Equipment::class,'category_equipment_id','price')
                ->orderByDesc()
                ->paginate(3)
                ->search($id);
        }
        elseif($request['filter']=="4")
        {
            $equipments=Search::add(Equipment::class,'category_equipment_id','price')
                ->orderByAsc()
                ->paginate(3)
                ->search($id);
        }
        return view('equipment-list',['equipments'=>$equipments,'phones_nav'=>$phones_nav,'tablets_nav'=>$tablets_nav,'equipment_nav'=>$equipment_nav,'id'=>$id]);
    }
}
