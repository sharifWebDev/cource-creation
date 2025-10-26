<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Determine the user's role and redirect accordingly
        if (auth()->user()) {
            return view('admin.home'); // View for admin dashboard
        }

        return redirect('/'); // Redirect to home or login page if the role is not recognized
    }

    public function optimize()
    {
        Artisan::call('optimize');

        return response()->json(['message' => 'Application optimized successfully']);
    }

    public function index()
    {
        return view('admin.home');
    }

    public function toggleStatus0(Request $request, $id)
    {
        try {
            $brand = Builder()::findOrFail($id);
            $brand->status = $request->status;
            $brand->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error toggling status for brand ID '.$id.': '.$e->getMessage());

            return response()->json(['success' => false, 'error' => 'Failed to toggle status'], 500);
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $updateColumn = $request->columnName;
            $getModel = $this->findUrlInfo($request->url);
            $modelInstance = new $getModel;
            $query = $modelInstance->findOrFail($id);
            $query->$updateColumn = $request->status;
            $query->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error toggling status for ID '.$id.': '.$e->getMessage());

            return response()->json(['success' => false, 'error' => 'Failed to toggle status'], 500);
        }
    }

    private function findUrlInfo($url)
    {
        // Extract the last segment of the URL path
        $segments = explode('/', rtrim(parse_url($url, PHP_URL_PATH), '/'));
        $lastSegment = end($segments);

        // Convert the URL segment to a model name (assuming singular and capitalized)
        $modelName = '\\App\\Models\\'.\Illuminate\Support\Str::studly(\Illuminate\Support\Str::singular($lastSegment));

        // Return the full model class name
        return $modelName;
    }
}
