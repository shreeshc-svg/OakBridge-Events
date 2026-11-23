<?php

namespace App\Http\Controllers;

use App\Models\PassType;
use App\Models\Service;
use App\Support\Coupons;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/** The "Apply" button next to the coupon box: checks a code, never uses it. */
class CouponCheckController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:40',
            'event' => 'required|string|max:255',
            'pass_type_id' => 'nullable|integer',
            'quantity' => 'nullable|integer|min:1|max:50',
            'email' => 'nullable|string|max:100',
        ]);

        $event = Service::wherePublished('1')->where('title', $data['event'])->whereDate('date', '>=', today())->first();
        if (! $event) {
            return response()->json(['ok' => false, 'message' => 'Please choose the event first.'], 422);
        }
        $pass = isset($data['pass_type_id']) ? PassType::where('service_id', $event->id)->find($data['pass_type_id']) : null;
        $email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL) ? $data['email'] : null;

        try {
            $result = Coupons::check($data['code'], $event, $pass, (int) ($data['quantity'] ?? 1), $email);
        } catch (ValidationException $e) {
            return response()->json(['ok' => false, 'message' => $e->errors()['coupon_code'][0] ?? 'This code cannot be used.'], 422);
        }

        $coupon = $result['coupon'];

        return response()->json([
            'ok' => true,
            'code' => $result['code']->code,
            'label' => $result['label'],
            'type' => $coupon->discount_type,
            'value' => (float) $coupon->discount_value,
            'max_passes' => $coupon->max_passes,
            'pass_type_id' => $coupon->pass_type_id,
            'pass_name' => $coupon->passType?->name,
            'message' => ucfirst($coupon->label()) . ' applied' . ($coupon->max_passes === 1 ? ' (1 pass per code).' : ' (up to ' . $coupon->max_passes . ' passes).'),
        ]);
    }
}
