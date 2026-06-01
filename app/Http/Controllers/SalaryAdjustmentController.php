<?php

namespace App\Http\Controllers;

use App\Models\SalaryAdjustment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalaryAdjustmentController extends Controller
{
    public function store(Request $request)
    {
        // Loại bỏ dấu chấm phân cách hàng nghìn khỏi trường số tiền
        if ($request->has('amount')) {
            $request->merge(['amount' => str_replace('.', '', $request->amount)]);
        }

        $validated = $request->validate([
            'trip_date' => 'required|date',
            'project_id' => 'required|exists:projects,id',
            'driver_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:addition,deduction',
            'note' => 'nullable|string',
        ]);

        SalaryAdjustment::create($validated);

        // Nếu nhấn "Lưu & Thêm mới"
        if ($request->has('save_and_new')) {
            return redirect()->route('trips.create', [
                'trip_date' => $validated['trip_date'],
                'project_id' => $validated['project_id'],
                'driver_id' => $validated['driver_id'],
                'tab' => 'adjustment', // Để giữ tab Phát sinh hoạt động
            ])->with('success', 'Đã lưu phát sinh lương. Tiếp tục thêm mới.');
        }

        // Redirect về trang chi tiết tháng của dự án tương ứng
        $date = Carbon::parse($validated['trip_date']);
        return redirect()->route('trips.by-month', [
            'project' => $validated['project_id'],
            'year' => $date->year,
            'month' => $date->month,
        ])->with('success', 'Đã thêm phát sinh lương thành công.');
    }

    public function destroy(SalaryAdjustment $salaryAdjustment)
    {
        $salaryAdjustment->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Đã xoá phát sinh lương.');
    }
}
