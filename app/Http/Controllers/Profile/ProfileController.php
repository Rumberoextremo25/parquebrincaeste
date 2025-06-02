<?php

namespace App\Http\Controllers\Profile;

use App\Enums\OrderStatus;
use App\Helpers\Checkout;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\PaymentResource;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class ProfileController extends Controller
{
	public function my_account()
	{
		return Inertia::render('Profile/Dashboard');
	}

	public function account_details()
	{
		return Inertia::render('Profile/AccountDetails');
	}

	public function store_account_details(Request $request)
	{
		$user = auth()->user();
		$request->validate([
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'string', 'confirmed', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
			'phone' => ['required', 'string', 'max:255'],
		]);

		$user->forceFill([
			'name' => $request->name,
			'email' => $request->email,
			'phone' => $request->phone,
		])->save();

		return Redirect::route('profile.account_details')
			->with(
				'success',
				'Datos Actualizados correctamente'
			);
	}

	public function my_orders()
	{
		$user = auth()->user();
		$payments = $user->orders()->orderBy('id', 'DESC')->paginate(10);
		return Inertia::render('Profile/MyOrders', [
			'orders' => OrderResource::collection($payments),
		]);
	}

	public function order_details_pdf($code)
	{
		//Logica para PDF
	}

	public function change_password()
	{
		return Inertia::render('Profile/ChangePassword');
	}

	public function store_change_password(Request $request)
	{
		$user = auth()->user();

		Validator::make($request->all(), [
			'current_password' => ['required', 'string'],
			'password' => ['required', 'confirmed', Rules\Password::defaults()],
		])->after(function ($validator) use ($user, $request) {
			if (!isset($request->current_password) || !Hash::check($request->current_password, $user->password)) {
				$validator->errors()->add('current_password', __('La contraseña proporcionada no coincide con su contraseña actual. '));
			}
		})->validate();

		$user->forceFill([
			'password' => Hash::make($request->password),
		])->save();

		return Redirect::route('change_password')
			->with(
				'success',
				'Datos Actualizados Correctamente'
			);
	}
}
