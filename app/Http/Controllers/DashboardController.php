<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Report::where('user_id', auth()->id())->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $viewUrl = route('reports.show', $row->id);
                    $downloadUrl = $row->status === 'completed' ? route('reports.download', $row->id) : '#';

                    $actions = '<div class="relative inline-block text-center">
                        <button type="button" onclick="toggleActionMenu(this)" class="action-btn inline-flex items-center justify-center w-8 h-8 rounded-full border border-gray-200 bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition-all duration-150" title="'.__('Actions').'">
                            <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                        </button>
                        <div class="action-menu hidden fixed z-[9999] w-44 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-10 py-1" role="menu" aria-orientation="vertical">
                                <a href="'.$viewUrl.'" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors duration-100" role="menuitem">
                                    <i class="fa-solid fa-eye mr-2 text-indigo-400 w-4"></i> '.__('View').'
                                </a>';

                    if ($row->status === 'completed') {
                        $actions .= '<a href="'.$downloadUrl.'" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors duration-100" role="menuitem">
                                        <i class="fa-solid fa-download mr-2 text-green-500 w-4"></i> '.__('Download').'
                                    </a>';
                    }

                    // $actions .= '<form action="'.route('reports.destroy', $row->id).'" method="POST" class="block" id="delete-form-'.$row->id.'">
                    //                 '.csrf_field().'
                    //                 '.method_field('DELETE').'
                    //                 <button type="button" onclick="swalDelete(document.getElementById(\'delete-form-'.$row->id.'\'))" class="w-full text-left flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors duration-100" role="menuitem">
                    //                     <i class="fa-solid fa-trash mr-2 text-red-400 w-4"></i> '.__('Delete').'
                    //                 </button>
                    //             </form>
                        '</div>
                    </div>';
                    return $actions;
                })
                ->editColumn('status', function($row) {
                    $colors = [
                        'completed' => 'bg-green-100 text-green-800',
                        'generating' => 'bg-yellow-100 text-yellow-800',
                        'failed' => 'bg-red-100 text-red-800',
                        'pending' => 'bg-gray-100 text-gray-800',
                    ];
                    $color = $colors[$row->status] ?? 'bg-gray-100 text-gray-800';
                    return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full '.$color.'">'.__(ucfirst($row->status)).'</span>';
                })
                ->editColumn('created_at', function($row) {
                    return $row->created_at->format('Y-m-d H:i');
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        $statusStats = Report::where('user_id', auth()->id())
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('dashboard', compact('statusStats'));
    }
}
