<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;

class CategoryController extends Controller
{

    // Return all categories of the current user's company
    public function index()
    {
        try {
            //To be improved
            $userDepartment = current_user()->department()->first();
            $userCompany = $userDepartment->company()->first();

            return $userCompany
                ->categories()
                ->orderBy('name', 'asc')
                ->get();
        } catch (Exception $e) {
            return response_success(['error' => $e->getMessage()]);
        }
    }

    // Creating new Category
    public function store(Request $request)
    {
        try {
            // validate the input data
            $request->validate([
                'name' => 'required|string|max:64',
                'colorVal' => 'required|string|max:32',
            ]);

            // Get current user's company id
            $userDepartment = current_user()->department()->first();
            $companyId = $userDepartment->company_id;

            // Creating department
            $category = new Category([
                'name' => $request->name,
                'colorVal' => $request->colorVal,
                'company_id' => $companyId
            ]);

            $category->save();

            // return success response + name of new category
            $data['name'] = $request->name;
            return response_success(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    // Return category by id
    public function edit($id)
    {
        try {
            return Category::findOrFail($id);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    // Updating category
    public function update(Request $request, Category $category)
    {

        try {
            // validate the input data
            $attributes = $request->validate([
                'name' => 'required|string|max:64',
                'colorVal' => 'required|string|max:32',
            ]);

            //Updating category
            $category->update($attributes);

            // return a success response
            $data['name'] = $request->name;
            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    // Deleting a category
    public function destroy($id)
    {

        try {
            $category = Category::findOrFail($id);
            $data['name'] = $category->name;

            $category->delete();

            // return a success response
            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
