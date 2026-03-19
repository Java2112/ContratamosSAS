<?php

namespace App\Domains\Company\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $client = $request->user()->client;

        if (!$client) {
            abort(403, 'User is not associated with any company client.');
        }

        return Inertia::render('Company/Profile/Edit', [
            'client' => $client
        ]);
    }

    public function update(Request $request)
    {
        $client = $request->user()->client;

        if (!$client) {
            abort(403);
        }

        $validated = $request->validate([
            'document_type' => 'required|string|max:10',
            'document_number' => 'required|string|max:50',
            'business_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'required|string|max:255',
            'industry_sector' => 'nullable|string|max:255',
        ]);

        // Log the change for admin audit
        activity()
            ->performedOn($client)
            ->causedBy($request->user())
            ->log('Company profile updated from Company Portal');

        $client->update($validated);

        return redirect()->route('company.profile.edit')->with('success', 'Perfil actualizado correctamente.');
    }
}
