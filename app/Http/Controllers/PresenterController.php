<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesMediaUploads;
use App\Models\Presenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PresenterController extends Controller
{
    use HandlesMediaUploads;

    public function index(): JsonResponse
    {
        return response()->json(
            Presenter::query()->latest()->get()
        );
    }

    public function show(Presenter $presenter): JsonResponse
    {
        return response()->json($presenter);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'array:en,ka'],
            'name.en' => ['required', 'string', 'max:255'],
            'name.ka' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'array:en,ka'],
            'description.en' => ['required_with:description', 'string'],
            'description.ka' => ['required_with:description', 'string'],
            'city' => ['required', 'array:en,ka'],
            'city.en' => ['required', 'string', 'max:255'],
            'city.ka' => ['required', 'string', 'max:255'],
        ]);

        $data['profile_photo'] = $this->resolveSingleMediaPath(
            $request,
            'profile_photo',
            'presenters/profile-photos'
        );

        $presenter = Presenter::query()->create($data);

        return response()->json($presenter, 201);
    }

    public function update(Request $request, Presenter $presenter): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'array:en,ka'],
            'name.en' => ['required_with:name', 'string', 'max:255'],
            'name.ka' => ['required_with:name', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'array:en,ka'],
            'description.en' => ['required_with:description', 'string'],
            'description.ka' => ['required_with:description', 'string'],
            'city' => ['sometimes', 'required', 'array:en,ka'],
            'city.en' => ['required_with:city', 'string', 'max:255'],
            'city.ka' => ['required_with:city', 'string', 'max:255'],
        ]);

        $data['profile_photo'] = $this->resolveSingleMediaPath(
            $request,
            'profile_photo',
            'presenters/profile-photos',
            $presenter->profile_photo
        );

        $presenter->update($data);

        return response()->json($presenter);
    }

    public function destroy(Presenter $presenter): JsonResponse
    {
        $presenter->delete();

        return response()->json([
            'message' => 'Presenter deleted successfully.',
        ]);
    }
}
