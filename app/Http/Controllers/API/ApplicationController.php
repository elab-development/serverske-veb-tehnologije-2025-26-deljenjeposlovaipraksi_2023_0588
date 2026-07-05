<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $applications = Application::with('user', 'jobListing.company')
                ->paginate($request->get('per_page', 10));
        } elseif ($user->isEmployer()) {
            $applications = Application::with('user', 'jobListing.company')
                ->whereHas('jobListing.company', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->paginate($request->get('per_page', 10));
        } else {
            $applications = Application::with('jobListing.company')
                ->where('user_id', $user->id)
                ->paginate($request->get('per_page', 10));
        }

        return ApplicationResource::collection($applications);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_listing_id' => 'required|exists:job_listings,id',
            'cover_letter' => 'nullable|string',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $existing = Application::where('user_id', $request->user()->id)
            ->where('job_listing_id', $validated['job_listing_id'])
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Već ste se prijavili na ovaj oglas.',
            ], 422);
        }

        $cvPath = null;
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('cvs', 'public');
        }

        $application = Application::create([
            'user_id' => $request->user()->id,
            'job_listing_id' => $validated['job_listing_id'],
            'cover_letter' => $validated['cover_letter'] ?? null,
            'cv_path' => $cvPath,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Prijava poslata.',
            'data' => new ApplicationResource($application->load('jobListing')),
        ], 201);
    }

    public function show(Application $application, Request $request)
    {
        $user = $request->user();

        if ($user->id !== $application->user_id
            && $user->id !== $application->jobListing->company->user_id
            && !$user->isAdmin()) {
            return response()->json(['message' => 'Nemate dozvolu.'], 403);
        }

        $application->load('user', 'jobListing.company');

        return new ApplicationResource($application);
    }

    public function update(Request $request, Application $application)
    {
        $user = $request->user();

        if ($user->id !== $application->jobListing->company->user_id && !$user->isAdmin()) {
            return response()->json(['message' => 'Nemate dozvolu.'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        $application->update($validated);

        return response()->json([
            'message' => 'Status prijave ažuriran.',
            'data' => new ApplicationResource($application),
        ]);
    }

    public function destroy(Request $request, Application $application)
    {
        $user = $request->user();

        if ($user->id !== $application->user_id && !$user->isAdmin()) {
            return response()->json(['message' => 'Nemate dozvolu.'], 403);
        }

        $application->delete();

        return response()->json([
            'message' => 'Prijava obrisana.',
        ]);
    }
    public function exportCsv(Request $request)
{
    $user = $request->user();

    if ($user->isAdmin()) {
        $applications = Application::with('user', 'jobListing.company')->get();
    } elseif ($user->isEmployer()) {
        $applications = Application::with('user', 'jobListing.company')
            ->whereHas('jobListing.company', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->get();
    } else {
        $applications = Application::with('jobListing.company')
            ->where('user_id', $user->id)
            ->get();
    }

    $csvHeader = ['ID', 'Korisnik', 'Email', 'Oglas', 'Kompanija', 'Status', 'Datum prijave'];
    $csvRows = [];

    foreach ($applications as $app) {
        $csvRows[] = [
            $app->id,
            $app->user ? $app->user->name : $user->name,
            $app->user ? $app->user->email : $user->email,
            $app->jobListing->title ?? '',
            $app->jobListing->company->name ?? '',
            $app->status,
            $app->createdat->format('Y-m-d H:i'),
        ];
    }

    $callback = function () use ($csvHeader, $csvRows) {
        $file = fopen('php://output', 'w');
        fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
        fputcsv($file, $csvHeader);
        foreach ($csvRows as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
    };

    $filename = 'prijave' . now()->format('Y_m_d_His') . '.csv';

    return response()->stream($callback, 200, [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ]);
}
}
