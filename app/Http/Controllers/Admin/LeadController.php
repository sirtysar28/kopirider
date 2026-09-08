<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Payment;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::with(['package', 'payments'])
            ->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('complete')) {
            $query->where('is_complete', $request->boolean('complete'));
        }

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('whatsapp', 'like', "%{$s}%")
                    ->orWhere('reference', 'like', "%{$s}%");
            });
        }

        return view('admin.leads.index', [
            'leads' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['status', 'complete', 'search']),
        ]);
    }

    public function show(Lead $lead)
    {
        $lead->load(['package', 'payments']);

        return view('admin.leads.show', compact('lead'));
    }

    public function update(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,contacted,negotiating,confirmed,lost'],
            'name' => ['nullable', 'string', 'max:120'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'note' => ['nullable', 'string'],
        ]);

        $lead->update([
            'status' => $data['status'],
            'name' => $data['name'] ?: $lead->name,
            'whatsapp' => $data['whatsapp'] ?: $lead->whatsapp,
        ]);

        return back()->with('success', 'Lead '.$lead->reference.' updated.');
    }
}
