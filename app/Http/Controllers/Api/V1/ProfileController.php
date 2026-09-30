<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Http\Resources\ProfileResource;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = min(max((int) $request->query('per_page', 5), 1), 50);
        $profiles = Profile::query()
            ->when(
                $request->filled('search'),
                fn ($q) => $q->where('name', 'like', '%'.$request->search.'%')
            )
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        return ProfileResource::collection($profiles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data=$request->validate([
            'name'=>'required|string|max:255',
            'bio'=>'nullable|string',
            'cover_photo'=>'nullable|string|max:255',
            'profile_photo'=>'nullable|string|max:255'
        ]);
        return response()->json(Profile::create($data),201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Profile $profile)
    {
        return response()->json($profile);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Profile $profile)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'bio' => 'sometimes|nullable|string',
            'cover_photo' => 'sometimes|nullable|string|max:255',
            'profile_photo' => 'sometimes|nullable|string|max:255'
        ]);

        $profile->update($data);
        return response()->json($profile);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Profile $profile)
    {
        $profile->delete();
        return response()->noContent();
    }
}
