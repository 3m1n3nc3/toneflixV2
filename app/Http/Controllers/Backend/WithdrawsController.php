<?php
/**
 * Created by NiNaCoder.
 * Date: 2019-08-06
 * Time: 23:14
 */

namespace App\Http\Controllers\Backend;

use App\Models\Withdraw;
use Illuminate\Http\Request;
use DB;

class WithdrawsController
{
    private $request;
    private $select;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $withdraws = Withdraw::withoutGlobalScopes()->orderBy('id', 'desc');

        isset($_GET['q']) ? $term = $_GET['q'] : $term = '';

        if($term) {
            $withdraws = $withdraws->whereRaw('users.name LIKE "%' . $term . '%"');
        }

        $withdraws = $withdraws->paginate(20);

        return view('backend.withdraws.index')
            ->with('term', $term)
            ->with('withdraws', $withdraws);
    }

    public function process()
    {
        $this->request->validate([
            'action' => 'required|string|in:unPaid,markPaid,decline',
            'id' => 'required|integer',

        ]);

        $withdraw = Withdraw::findOrfail($this->request->input('id'));


        if($this->request->input('action') == 'unPaid') {
            $withdraw->paid = 0;
            $withdraw->save();

            return response()->json(array(
                'success' => true,
            ));
        } elseif($this->request->input('action') == 'markPaid') {
            $withdraw->paid = 1;
            $withdraw->save();

            return response()->json(array(
                'success' => true,
            ));
        } elseif($this->request->input('action')  == 'decline') {
            $withdraw->delete();

            return response()->json(array(
                'success' => true,
            ));
        }
    }
}