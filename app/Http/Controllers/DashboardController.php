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

                    $actions = '<div class="relative inline-block text-left" x-data="{ open: false }">
                        <button @click="open = !open" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
                            '.__('Actions').'
                            <i class="fa-solid fa-chevron-down ml-2"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10" style="display: none;">
                            <div class="py-1" role="menu" aria-orientation="vertical">
                                <a href="'.$viewUrl.'" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                    <i class="fa-solid fa-eye mr-2 text-blue-500"></i> '.__('View').'
                                </a>';

                    if ($row->status === 'completed') {
                        $actions .= '<a href="'.$downloadUrl.'" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                        <i class="fa-solid fa-download mr-2 text-green-500"></i> '.__('Download').'
                                    </a>';
                    }

                    $actions .= '<form action="'.route('reports.destroy', $row->id).'" method="POST" class="inline" id="delete-form-'.$row->id.'">
                                    '.csrf_field().'
                                    '.method_field('DELETE').'
                                    <button type="button" onclick="swalDelete(document.getElementById(\'delete-form-'.$row->id.'\'))" class="w-full text-left block px-4 py-2 text-sm text-red-700 hover:bg-gray-100" role="menuitem">
                                        <i class="fa-solid fa-trash mr-2 text-red-500"></i> '.__('Delete').'
                                    </button>
                                </form>
                            </div>
                        </div>
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
