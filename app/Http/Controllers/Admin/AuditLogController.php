<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->aksi) {
            $query->where('aksi', $request->aksi);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->tanggal_mulai) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }

        if ($request->tanggal_selesai) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        if ($request->search) {
            $query->where('aksi', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(20);

        $aksiList = AuditLog::distinct()
            ->pluck('aksi')
            ->sort()
            ->values();

        return view('admin.audit-log.index', compact('logs', 'aksiList'));
    }
}
