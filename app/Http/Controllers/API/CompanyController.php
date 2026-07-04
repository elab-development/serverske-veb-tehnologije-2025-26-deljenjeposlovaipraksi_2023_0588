<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::with('user');

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('address')) {
            $query->where('address', 'like', '%' . $request->address . '%');
        }

        $companies = $query->paginate($request->get('per_page', 10));

        return CompanyResource::collection($companies);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        $validated['user_id'] = $request->user()->id;

        $company = Company::create($validated);

        return response()->json([
            'message' => 'Kompanija kreirana.',
            'data' => new CompanyResource($company),
        ], 201);
    }

    public function show(Company $company)
    {
        $company->load('user', 'jobListings');

        return new CompanyResource($company);
    }

    public function update(Request $request, Company $company)
    {
        if ($request->user()->id !== $company->user_id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Nemate dozvolu.'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        $company->update($validated);

        return response()->json([
            'message' => 'Kompanija ažurirana.',
            'data' => new CompanyResource($company),
        ]);
    }

    public function destroy(Request $request, Company $company)
    {
        if ($request->user()->id !== $company->user_id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Nemate dozvolu.'], 403);
        }

        $company->delete();

        return response()->json([
            'message' => 'Kompanija obrisana.',
        ]);
    }
}
