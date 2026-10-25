<?php

namespace App\Http\Controllers\Admin;

//use App\Event\Notification;
use App\Models\User;
use App\Models\Major;
use App\Models\Teacher;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use App\Events\Category1Notification;
use App\Notifications\CategoryNotification;
use Illuminate\Support\Facades\Notification;
//use Illuminate\App\Notifications\Dispatch;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users=User::find(1);
      // $users = User::where('id', '1')->first();
        Gate::authorize('all_categories');
        $categories = Category::orderByDesc('id')->paginate(5);
        //dd($categories->teacher);

        return view('admin.categories.index', compact('categories','users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
                 //  ->WhereNull('parent_id')->get()ازا انحزف احد البيرنت لا تعرضه بالسليكت وتضاف للايديت
        $users=User::find(1);
        $teachers = Teacher::all();
        return view('admin.categories.create', compact('teachers','users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $users = User::get();
         // Validate Data
         $request->validate([
            'name_major' => 'required',
            'image' => 'required',
            'title' => 'required',
            'description' => 'required',

        ]);



       $img = $request->file('image');
       $img_name = rand() . time() . $img->getClientOriginalName();
       $img->move(public_path('uploads/categories'), $img_name);




        // Insert To Database

           Category::create([
            'name_major' => $request->name_major,
           'image' => $img_name,
            'title' => $request->title,
           'description' => $request->description,


        ]);
           $name_major = Major::latest()->first(); //عشان نعرف اي تخصص انضاف ونضغط عليه نروحله

          // Notification::Send($users, new CategoryNotification($request->name_major));
          // Notification::dispatch('noyify updated by ' . Auth::user()->name);

          foreach ($users as $client){

            $client->notify(new CategoryNotification($request->name_major));
        }
        event(new Category1Notification($request->name_major));

        // Redirect
        return redirect()->route('admin.categories.index')->with('msg', __('dash.saved_new'))->with('type', 'success');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {      // ->WhereNull('parent_id')->get()الwhere ما يجيب للاساسي للاب يعنه نفسه بالسليكت
        $categories = Category::all();
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('categories', 'category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate Data
        $request->validate([
            'name_major' => 'required',
            'title' => 'required',
            'description' => 'required',
            
        ]);

        $category = Category::findOrFail($id);

        // Upload File

        $img_name = $category->image;
        if($request->hasFile('image')) {
           $img_name = rand() . time() . $request->file('image')->getClientOriginalName();
          $request->file('image')->move(public_path('uploads/categories'), $img_name);
       }


        // Insert To Database

        $category->update([
            'name_major' => $request->name_major,
           'image' => $img_name,
            'title' => $request->title,
           'description' => $request->description,


        ]);

        // Redirect
        return redirect()->route('admin.categories.index')->with('msg', __('dash.saved'))->with('type', 'info');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $teachers = Teacher::where('categorie_id',$id)->first();
        if($teachers)
        {
            return redirect()->back()->with('msg', __('dash.category_in_use'))->with('type', 'danger');
        }

        $category = Category::findOrFail($id);

        File::delete(public_path('uploads/categories/'.$category->image));


        $category->delete();

        return redirect()->route('admin.categories.index')->with('msg', __('dash.deleted'))->with('type', 'success');
    }
}
