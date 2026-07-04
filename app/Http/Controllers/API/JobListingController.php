<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobListingResource;
use App\Models\JobListing;
use Illuminate\Http\Request;

class JobListingController extends Controller
{
    public function index(Request $request)
    {
        $query = JobListing::with('company');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('min_salary')) {
            $query->where('salary', '>=', $request->min_salary);
        }

        if ($request->has('max_salary')) {
            $query->where('salary', '<=', $request->max_salary);
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $listings = $query->paginate($request->get('per_page', 10));

        return JobListingResource::collection($listings);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'type' => 'required|in:full_time,part_time,internship,contract',
            'company_id' => 'required|exists:companies,id',
        ]);

        $company = \App\Models\Company::find($validated['company_id']);
        if ($request->user()->id !== $company->user_id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Nemate dozvolu.'], 403);
        }

        $listing = JobListing::create($validated);

        return response()->json([
            'message' => 'Oglas kreiran.',
            'data' => new JobListingResource($listing->load('company')),
        ], 201);
    }

    public function show(JobListing $jobListing)
    {
        $jobListing->load('company', 'applications');

        return new JobListingResource($jobListing);
    }

    public function update(Request $request, JobListing $jobListing)
    {
        $company = $jobListing->company;
        if ($request->user()->id !== $company->user_id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Nemate dozvolu.'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'location' => 'sometimes|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'type' => 'sometimes|in:full_time,part_time,internship,contract',
        ]);

        $jobListing->update($validated);

        return response()->json([
            'message' => 'Oglas ažuriran.',
            'data' => new JobListingResource($jobListing),
        ]);
    }

    public function destroy(Request $request, JobListing $jobListing)
    {
        $company = $jobListing->company;
        if ($request->user()->id !== $company->user_id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Nemate dozvolu.'], 403);
        }

        $jobListing->delete();

        return response()->json([
            'message' => 'Oglas obrisan.',
        ]);
    }
}
