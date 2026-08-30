<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\EnquiryStatus;
use App\Models\User;
use App\Services\EnquiryService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EnquiryController extends Controller
{
    public function __construct(protected EnquiryService $enquiries) {}

    public function index()
    {
        $statuses = EnquiryStatus::active()->get();
        $salespeople = User::role('Sales Executive')->orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.enquiry.index', compact('statuses', 'salespeople'));
    }

    public function list(Request $request)
    {
        $query = Enquiry::query()
            ->with(['status', 'assignee'])
            ->withCount('items')
            ->latest();

        if ($request->filled('status_id')) {
            $query->where('enquiry_status_id', $request->status_id);
        }
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('keyword')) {
            $search = $request->keyword;
            $query->where(function ($q) use ($search) {
                $q->where('enquiry_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhereHas('items', fn ($i) => $i->where('product_title', 'like', "%{$search}%"));
            });
        }

        return DataTables::of($query)
            ->addColumn('status_badge', function ($row) {
                $color = $row->status->color ?? '#6b7280';
                $name = e($row->status->name ?? '-');

                return '<span class="badge" style="background:'.$color.';color:#fff;">'.$name.'</span>';
            })
            ->addColumn('assignee_name', fn ($row) => e($row->assignee->name ?? 'Unassigned'))
            ->addColumn('action', function ($row) {
                return '<a href="'.route('admin.enquiries.show', $row->id).'" class="btn btn-sm btn-primary">View</a>';
            })
            ->editColumn('created_at', fn ($row) => $row->created_at?->format('d M Y H:i'))
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function show($id)
    {
        $enquiry = Enquiry::with([
            'status',
            'assignee',
            'items.product',
            'activities.user',
        ])->findOrFail($id);

        $statuses = EnquiryStatus::active()->get();
        $salespeople = User::role('Sales Executive')->orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.enquiry.show', compact('enquiry', 'statuses', 'salespeople'));
    }

    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'enquiry_status_id' => 'required|exists:enquiry_statuses,id',
            'note' => 'nullable|string|max:1000',
        ]);

        $enquiry = Enquiry::findOrFail($id);
        $this->enquiries->updateStatus($enquiry, (int) $data['enquiry_status_id'], $data['note'] ?? null);

        return back()->with('success', 'Enquiry status updated.');
    }

    public function assign(Request $request, $id)
    {
        $data = $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $enquiry = Enquiry::findOrFail($id);
        $this->enquiries->assign($enquiry, $data['assigned_to'] ?? null);

        return back()->with('success', 'Enquiry assignment updated.');
    }

    public function addNote(Request $request, $id)
    {
        $data = $request->validate([
            'note' => 'required|string|max:2000',
        ]);

        $enquiry = Enquiry::findOrFail($id);
        $this->enquiries->addNote($enquiry, $data['note']);

        return back()->with('success', 'Note added.');
    }
}
